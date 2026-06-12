from typing import Optional
from pydantic import BaseModel, EmailStr

class UserBase(BaseModel):
    email: Optional[EmailStr] = None
    is_active: Optional[bool] = True
    full_name: Optional[str] = None
    is_superuser: bool = False
    role: str = "peserta"

class UserCreate(UserBase):
    email: EmailStr
    password: str

class UserUpdate(UserBase):
    password: Optional[str] = None

class UserInDBBase(BaseModel):
    id: Optional[int] = None
    email: Optional[EmailStr] = None
    is_active: Optional[bool] = True
    full_name: Optional[str] = None
    is_superuser: bool = False
    role: str = "peserta"

    class Config:
        from_attributes = True

class User(UserInDBBase):
    is_verified: bool
    hashed_password: str

class UserResponse(UserInDBBase):
    is_verified: bool
    hashed_password: Optional[str] = None

class UserInDB(UserInDBBase):
    hashed_password: str
