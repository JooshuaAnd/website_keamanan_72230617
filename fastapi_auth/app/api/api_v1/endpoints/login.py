import uuid
from datetime import datetime, timedelta, timezone
from typing import Any
from fastapi import APIRouter, Depends, HTTPException, Body
from fastapi.security import OAuth2PasswordRequestForm
from sqlalchemy.orm import Session
from jose import jwt

from app.core import security
from app.core.config import settings
from app.core.database import get_db
from app.core.mail import send_reset_password_email
from app.models.user import User
from app.schemas.token import Token, TokenPayload
from app.schemas.msg import Msg

router = APIRouter()

import logging

# Configure security logging
logger = logging.getLogger("security")
logger.setLevel(logging.INFO)
if not logger.handlers:
    fh = logging.FileHandler("security.log")
    formatter = logging.Formatter('%(asctime)s - %(levelname)s - %(message)s')
    fh.setFormatter(formatter)
    logger.addHandler(fh)

MAX_LOGIN_ATTEMPTS = 3
LOCKOUT_SECONDS = 10

def utcnow():
    return datetime.now(timezone.utc).replace(tzinfo=None)

@router.post("/login/access-token", response_model=Token)
def login_access_token(
    db: Session = Depends(get_db), form_data: OAuth2PasswordRequestForm = Depends()
) -> Any:
    user = db.query(User).filter(User.email == form_data.username).first()

    if user and user.locked_until:
        now = utcnow()
        if user.locked_until > now:
            remaining = int((user.locked_until - now).total_seconds())
            logger.warning(f"Blocked login attempt (Lockout active) - Email: {form_data.username} - Remaining: {remaining}s")
            raise HTTPException(
                status_code=400,
                detail=f"Terlalu banyak percobaan login gagal. Silakan tunggu {max(1, remaining)} detik atau hubungi admin."
            )
        else:
            user.login_attempts = 0
            user.locked_until = None
            db.add(user)
            db.commit()

    if not user or not security.verify_password(form_data.password, user.hashed_password):
        if user:
            user.login_attempts = (user.login_attempts or 0) + 1
            if user.login_attempts >= MAX_LOGIN_ATTEMPTS:
                user.locked_until = utcnow() + timedelta(seconds=LOCKOUT_SECONDS)
            db.add(user)
            db.commit()
            
            log_msg = f"Failed login attempt - Email: {form_data.username} - Attempt: {user.login_attempts}/{MAX_LOGIN_ATTEMPTS}"
            if user.login_attempts >= MAX_LOGIN_ATTEMPTS:
                log_msg += " - Account locked"
            logger.warning(log_msg)
        else:
            logger.warning(f"Failed login attempt - Email: {form_data.username} - Non-existent user")
            
        raise HTTPException(status_code=400, detail="Incorrect email or password")
    elif not user.is_active:
        logger.warning(f"Failed login attempt - Email: {form_data.username} - Inactive user")
        raise HTTPException(status_code=400, detail="Inactive user")
    elif not user.is_verified:
        logger.warning(f"Failed login attempt - Email: {form_data.username} - Unverified email")
        raise HTTPException(status_code=400, detail="Email not verified")

    # Reset attempts on success
    user.login_attempts = 0
    user.locked_until = None
    db.add(user)
    db.commit()
    logger.info(f"Successful login - Email: {user.email}")

    access_token_expires = timedelta(minutes=settings.ACCESS_TOKEN_EXPIRE_MINUTES)
    return {
        "access_token": security.create_access_token(
            user.email, expires_delta=access_token_expires
        ),
        "refresh_token": security.create_refresh_token(user.email),
        "token_type": "bearer",
    }

@router.post("/login/refresh-token", response_model=Token)
def refresh_token(
    refresh_token: str = Body(...), db: Session = Depends(get_db)
) -> Any:
    try:
        payload = jwt.decode(
            refresh_token, settings.SECRET_KEY, algorithms=[settings.ALGORITHM]
        )
        token_data = TokenPayload(**payload)
        if token_data.type != "refresh":
            raise HTTPException(status_code=400, detail="Invalid refresh token")
    except jwt.JWTError:
        raise HTTPException(status_code=400, detail="Invalid refresh token")

    user = db.query(User).filter(User.email == token_data.sub).first()
    if not user or not user.is_active:
        raise HTTPException(status_code=400, detail="User not found or inactive")

    access_token_expires = timedelta(minutes=settings.ACCESS_TOKEN_EXPIRE_MINUTES)
    return {
        "access_token": security.create_access_token(
            user.email, expires_delta=access_token_expires
        ),
        "refresh_token": security.create_refresh_token(user.email),
        "token_type": "bearer",
    }

@router.post("/password-recovery/{email}", response_model=Msg)
async def recover_password(email: str, db: Session = Depends(get_db)) -> Any:
    user = db.query(User).filter(User.email == email).first()
    if not user:
        raise HTTPException(
            status_code=404,
            detail="The user with this username does not exist in the system.",
        )
    action_token = uuid.uuid4().hex
    user.action_token = action_token
    db.add(user)
    db.commit()
    await send_reset_password_email(email=user.email, token=action_token)
    return {"msg": "Password recovery email sent"}

@router.post("/reset-password/", response_model=Msg)
def reset_password(
    token: str = Body(...),
    new_password: str = Body(...),
    db: Session = Depends(get_db),
) -> Any:
    user = db.query(User).filter(User.action_token == token).first()
    if not user:
        raise HTTPException(
            status_code=404,
            detail="Invalid or expired token",
        )
    elif not user.is_active:
        raise HTTPException(status_code=400, detail="Inactive user")
    hashed_password = security.get_password_hash(new_password)
    user.hashed_password = hashed_password
    user.action_token = None
    user.login_attempts = 0
    user.locked_until = None
    db.add(user)
    db.commit()
    return {"msg": "Password updated successfully"}
