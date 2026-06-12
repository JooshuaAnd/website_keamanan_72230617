import re
from typing import Any, List, Optional
from fastapi import APIRouter, Depends, HTTPException, Query, UploadFile, File
from sqlalchemy.orm import Session
from sqlalchemy import or_

from app.api import deps
from app.core.database import get_db
from app.models.user import User
from app.models.lms import Participant, Lecturer, Material
from app.schemas import lms as lms_schema

router = APIRouter()

XSS_PATTERN = re.compile(r'<[^>]*script|alert\s*\(|onerror\s*=|onload\s*=|onclick\s*=', re.IGNORECASE)
SQLI_PATTERN = re.compile(r"('|--|%27|%22|\bOR\b|\bAND\b|\bUNION\b|\bSELECT\b|\bDROP\b|\bDELETE\b|\bINSERT\b|\bUPDATE\b).*('|--|%27|%22)", re.IGNORECASE)

def sanitize_input(value: str) -> str:
    if not value or not isinstance(value, str):
        return value
    if XSS_PATTERN.search(value):
        raise HTTPException(status_code=400, detail="Input tidak valid")
    return value

def sanitize_output(value: Optional[str]) -> str:
    if not value:
        return ""
    value = value.replace("&", "&amp;")
    value = value.replace("<", "&lt;")
    value = value.replace(">", "&gt;")
    value = value.replace('"', "&quot;")
    value = value.replace("'", "&#x27;")
    return value

# ============ PARTICIPANT ENDPOINTS ============

@router.get("/participants", response_model=List[lms_schema.Participant])
def read_participants(
    db: Session = Depends(get_db),
    skip: int = 0,
    limit: int = 100,
    current_user: User = Depends(deps.get_current_dosen),
) -> Any:
    participants = db.query(Participant).offset(skip).limit(limit).all()
    return participants

@router.get("/participants/search", response_model=List[lms_schema.Participant])
def search_participants(
    q: str = Query(..., min_length=1, max_length=100),
    db: Session = Depends(get_db),
    current_user: User = Depends(deps.get_current_dosen),
) -> Any:
    sanitize_input(q)
    results = db.query(Participant).join(User).filter(
        or_(
            User.full_name.contains(q),
            Participant.nim.contains(q),
            Participant.kelas.contains(q),
            Participant.program_studi.contains(q),
        )
    ).all()
    return results

@router.post("/participants", response_model=lms_schema.Participant)
def create_participant(
    *,
    db: Session = Depends(get_db),
    participant_in: lms_schema.ParticipantCreate,
    current_user: User = Depends(deps.get_current_admin),
) -> Any:
    participant_in.nim = sanitize_input(participant_in.nim)
    participant_in.kelas = sanitize_input(participant_in.kelas)
    participant_in.program_studi = sanitize_input(participant_in.program_studi)
    participant = Participant(**participant_in.model_dump())
    db.add(participant)
    db.commit()
    db.refresh(participant)
    return participant

@router.put("/participants/{participant_id}", response_model=lms_schema.Participant)
def update_participant(
    *,
    db: Session = Depends(get_db),
    participant_id: int,
    participant_in: lms_schema.ParticipantUpdate,
    current_user: User = Depends(deps.get_current_admin),
) -> Any:
    participant = db.query(Participant).filter(Participant.id == participant_id).first()
    if not participant:
        raise HTTPException(status_code=404, detail="Participant not found")
    update_data = participant_in.model_dump(exclude_unset=True)
    for key, value in update_data.items():
        if value is not None:
            setattr(participant, key, sanitize_input(value) if isinstance(value, str) else value)
    db.add(participant)
    db.commit()
    db.refresh(participant)
    return participant

@router.delete("/participants/{participant_id}")
def delete_participant(
    *,
    db: Session = Depends(get_db),
    participant_id: int,
    current_user: User = Depends(deps.get_current_admin),
) -> Any:
    participant = db.query(Participant).filter(Participant.id == participant_id).first()
    if not participant:
        raise HTTPException(status_code=404, detail="Participant not found")
    
    # Also delete the associated User account and materials
    user = db.query(User).filter(User.id == participant.user_id).first()
    db.delete(participant)
    if user:
        # Delete materials created by this user
        from app.models.lms import Material
        db.query(Material).filter(Material.created_by == user.id).delete(synchronize_session=False)
        db.delete(user)
        
    db.commit()
    return {"msg": "Participant and user account deleted successfully"}

# ============ LECTURER ENDPOINTS ============

@router.get("/lecturers", response_model=List[lms_schema.Lecturer])
def read_lecturers(
    db: Session = Depends(get_db),
    skip: int = 0,
    limit: int = 100,
    current_user: User = Depends(deps.get_current_dosen),
) -> Any:
    lecturers = db.query(Lecturer).offset(skip).limit(limit).all()
    return lecturers

@router.get("/lecturers/search", response_model=List[lms_schema.Lecturer])
def search_lecturers(
    q: str = Query(..., min_length=1, max_length=100),
    db: Session = Depends(get_db),
    current_user: User = Depends(deps.get_current_dosen),
) -> Any:
    sanitize_input(q)
    results = db.query(Lecturer).join(User).filter(
        or_(
            User.full_name.contains(q),
            Lecturer.nidn.contains(q),
            Lecturer.bidang_keahlian.contains(q),
        )
    ).all()
    return results

@router.post("/lecturers", response_model=lms_schema.Lecturer)
def create_lecturer(
    *,
    db: Session = Depends(get_db),
    lecturer_in: lms_schema.LecturerCreate,
    current_user: User = Depends(deps.get_current_admin),
) -> Any:
    lecturer_in.nidn = sanitize_input(lecturer_in.nidn)
    lecturer_in.bidang_keahlian = sanitize_input(lecturer_in.bidang_keahlian)
    lecturer = Lecturer(**lecturer_in.model_dump())
    db.add(lecturer)
    db.commit()
    db.refresh(lecturer)
    return lecturer

@router.put("/lecturers/{lecturer_id}", response_model=lms_schema.Lecturer)
def update_lecturer(
    *,
    db: Session = Depends(get_db),
    lecturer_id: int,
    lecturer_in: lms_schema.LecturerUpdate,
    current_user: User = Depends(deps.get_current_admin),
) -> Any:
    lecturer = db.query(Lecturer).filter(Lecturer.id == lecturer_id).first()
    if not lecturer:
        raise HTTPException(status_code=404, detail="Lecturer not found")
    update_data = lecturer_in.model_dump(exclude_unset=True)
    for key, value in update_data.items():
        if value is not None:
            setattr(lecturer, key, sanitize_input(value) if isinstance(value, str) else value)
    db.add(lecturer)
    db.commit()
    db.refresh(lecturer)
    return lecturer

@router.delete("/lecturers/{lecturer_id}")
def delete_lecturer(
    *,
    db: Session = Depends(get_db),
    lecturer_id: int,
    current_user: User = Depends(deps.get_current_admin),
) -> Any:
    lecturer = db.query(Lecturer).filter(Lecturer.id == lecturer_id).first()
    if not lecturer:
        raise HTTPException(status_code=404, detail="Lecturer not found")
    
    # Also delete the associated User account and materials
    user = db.query(User).filter(User.id == lecturer.user_id).first()
    db.delete(lecturer)
    if user:
        # Delete materials created by this user
        from app.models.lms import Material
        db.query(Material).filter(Material.created_by == user.id).delete(synchronize_session=False)
        db.delete(user)
        
    db.commit()
    return {"msg": "Lecturer and user account deleted successfully"}

# ============ MATERIAL ENDPOINTS ============

@router.get("/materials", response_model=List[lms_schema.Material])
def read_materials(
    db: Session = Depends(get_db),
    skip: int = 0,
    limit: int = 100,
    current_user: User = Depends(deps.get_current_user),
) -> Any:
    materials = db.query(Material).offset(skip).limit(limit).all()
    return materials

@router.get("/materials/search", response_model=List[lms_schema.Material])
def search_materials(
    q: str = Query(..., min_length=1, max_length=100),
    db: Session = Depends(get_db),
    current_user: User = Depends(deps.get_current_user),
) -> Any:
    sanitize_input(q)
    results = db.query(Material).filter(
        or_(
            Material.title.contains(q),
            Material.description.contains(q),
        )
    ).all()
    return results

@router.post("/materials", response_model=lms_schema.Material)
def create_material(
    *,
    db: Session = Depends(get_db),
    material_in: lms_schema.MaterialCreate,
    current_user: User = Depends(deps.get_current_dosen),
) -> Any:
    material_in.title = sanitize_input(material_in.title)
    if material_in.description:
        material_in.description = sanitize_input(material_in.description)
    material = Material(
        title=material_in.title,
        description=material_in.description,
        file=material_in.file,
        created_by=current_user.id,
    )
    db.add(material)
    db.commit()
    db.refresh(material)
    return material

@router.put("/materials/{material_id}", response_model=lms_schema.Material)
def update_material(
    *,
    db: Session = Depends(get_db),
    material_id: int,
    material_in: lms_schema.MaterialUpdate,
    current_user: User = Depends(deps.get_current_dosen),
) -> Any:
    material = db.query(Material).filter(Material.id == material_id).first()
    if not material:
        raise HTTPException(status_code=404, detail="Material not found")
    if material.created_by != current_user.id and current_user.role != "admin":
        raise HTTPException(status_code=403, detail="Forbidden: You can only edit your own materials")
    update_data = material_in.model_dump(exclude_unset=True)
    for key, value in update_data.items():
        if value is not None:
            setattr(material, key, sanitize_input(value) if isinstance(value, str) else value)
    db.add(material)
    db.commit()
    db.refresh(material)
    return material

@router.delete("/materials/{material_id}")
def delete_material(
    *,
    db: Session = Depends(get_db),
    material_id: int,
    current_user: User = Depends(deps.get_current_dosen),
) -> Any:
    material = db.query(Material).filter(Material.id == material_id).first()
    if not material:
        raise HTTPException(status_code=404, detail="Material not found")
    if material.created_by != current_user.id and current_user.role != "admin":
        raise HTTPException(status_code=403, detail="Forbidden: You can only delete your own materials")
    db.delete(material)
    db.commit()
    return {"msg": "Material deleted successfully"}
