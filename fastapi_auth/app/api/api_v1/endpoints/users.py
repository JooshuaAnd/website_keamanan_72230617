import uuid
from typing import Any, List
from fastapi import APIRouter, Depends, HTTPException, Body
from sqlalchemy.orm import Session
from pydantic import EmailStr

from app.api import deps
from app.core import security
from app.core.database import get_db
from app.core.mail import send_verification_email
from app.models.user import User
from app.schemas import user as user_schema
from app.schemas.msg import Msg

router = APIRouter()

@router.get("/", response_model=List[user_schema.UserResponse])
def read_users(
    db: Session = Depends(get_db),
    skip: int = 0,
    limit: int = 100,
    current_user: User = Depends(deps.get_current_admin),
) -> Any:
    users = db.query(User).offset(skip).limit(limit).all()
    return users

@router.post("/register", response_model=user_schema.UserResponse)
async def register_user(
    *,
    db: Session = Depends(get_db),
    password: str = Body(...),
    email: EmailStr = Body(...),
    full_name: str = Body(None),
    role: str = Body("peserta"),
) -> Any:
    user = db.query(User).filter(User.email == email).first()
    if user:
        raise HTTPException(
            status_code=400,
            detail="The user with this username already exists in the system.",
        )
    if role not in ["admin", "dosen", "peserta"]:
        raise HTTPException(status_code=400, detail="Invalid role. Must be: admin, dosen, or peserta")
    user_in = user_schema.UserCreate(email=email, password=password, full_name=full_name, role=role)
    db_obj = User(
        email=user_in.email,
        hashed_password=security.get_password_hash(user_in.password),
        full_name=user_in.full_name,
        role=role,
        is_active=True,
        is_verified=False,
        is_superuser=(role == "admin"),
    )
    db.add(db_obj)
    db.commit()
    db.refresh(db_obj)

    # Automatically create Participant or Lecturer profile
    import random
    import string
    from app.models.lms import Participant, Lecturer
    
    if role == "peserta":
        while True:
            nim = "".join(random.choices(string.digits, k=10))
            if not db.query(Participant).filter(Participant.nim == nim).first():
                break
        
        participant = Participant(
            user_id=db_obj.id,
            nim=nim,
            kelas="Reg-2026",
            program_studi="Teknik Informatika",
        )
        db.add(participant)
        db.commit()
        
    elif role == "dosen":
        while True:
            nidn = "".join(random.choices(string.digits, k=10))
            if not db.query(Lecturer).filter(Lecturer.nidn == nidn).first():
                break
        
        lecturer = Lecturer(
            user_id=db_obj.id,
            nidn=nidn,
            bidang_keahlian="Umum",
        )
        db.add(lecturer)
        db.commit()

    try:
        action_token = uuid.uuid4().hex
        db_obj.action_token = action_token
        db.add(db_obj)
        db.commit()
        await send_verification_email(email=email, token=action_token)
    except Exception as e:
        print(f"Error sending email: {e}")
        pass

    return db_obj

@router.post("/verify-email/{token}", response_model=Msg)
def verify_email(token: str, db: Session = Depends(get_db)) -> Any:
    user = db.query(User).filter(User.action_token == token).first()
    if not user:
        raise HTTPException(status_code=400, detail="Invalid or expired token")

    user.is_verified = True
    user.action_token = None
    db.add(user)
    db.commit()
    return {"msg": "Email verified successfully"}

@router.get("/me", response_model=user_schema.UserResponse)
def read_user_me(
    db: Session = Depends(get_db),
    current_user: User = Depends(deps.get_current_user),
) -> Any:
    return current_user

@router.put("/me", response_model=user_schema.UserResponse)
def update_user_me(
    *,
    db: Session = Depends(get_db),
    password: str = Body(None),
    full_name: str = Body(None),
    email: EmailStr = Body(None),
    current_user: User = Depends(deps.get_current_user),
) -> Any:
    if email:
        current_user.email = email
    if full_name:
        current_user.full_name = full_name
    if password:
        current_user.hashed_password = security.get_password_hash(password)
    db.add(current_user)
    db.commit()
    db.refresh(current_user)
    return current_user

@router.put("/{user_id}", response_model=user_schema.UserResponse)
def update_user(
    *,
    db: Session = Depends(get_db),
    user_id: int,
    user_in: user_schema.UserUpdate,
    current_user: User = Depends(deps.get_current_admin),
) -> Any:
    user = db.query(User).filter(User.id == user_id).first()
    if not user:
        raise HTTPException(
            status_code=404,
            detail="The user with this id does not exist in the system.",
        )
    if user_in.email:
        user.email = user_in.email
    if user_in.full_name:
        user.full_name = user_in.full_name
    if user_in.password:
        user.hashed_password = security.get_password_hash(user_in.password)
    if user_in.is_active is not None:
        user.is_active = user_in.is_active
    if user_in.is_superuser is not None:
        user.is_superuser = user_in.is_superuser
    if user_in.role is not None:
        user.role = user_in.role

    db.add(user)
    db.commit()
    db.refresh(user)
    return user

@router.delete("/{user_id}", response_model=user_schema.UserResponse)
def delete_user(
    *,
    db: Session = Depends(get_db),
    user_id: int,
    current_user: User = Depends(deps.get_current_admin),
) -> Any:
    user = db.query(User).filter(User.id == user_id).first()
    if not user:
        raise HTTPException(
            status_code=404,
            detail="The user with this id does not exist in the system.",
        )
    if user.id == current_user.id:
        raise HTTPException(
            status_code=400, detail="Super users are not allowed to delete themselves"
        )
        
    # Cascade delete participant profile, lecturer profile, and created materials
    from app.models.lms import Participant, Lecturer, Material
    
    # 1. Delete materials created by this user
    db.query(Material).filter(Material.created_by == user.id).delete(synchronize_session=False)
    
    # 2. Delete participant profile
    db.query(Participant).filter(Participant.user_id == user.id).delete(synchronize_session=False)
    
    # 3. Delete lecturer profile
    db.query(Lecturer).filter(Lecturer.user_id == user.id).delete(synchronize_session=False)
    
    # 4. Delete the user
    db.delete(user)
    db.commit()
    return user
