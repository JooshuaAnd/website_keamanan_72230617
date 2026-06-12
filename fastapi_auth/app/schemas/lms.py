from typing import Optional
from pydantic import BaseModel
from datetime import datetime

class UserMinimal(BaseModel):
    id: int
    email: str
    full_name: Optional[str] = None

    class Config:
        from_attributes = True

class ParticipantBase(BaseModel):
    nim: str
    kelas: str
    program_studi: str

class ParticipantCreate(ParticipantBase):
    user_id: int

class ParticipantUpdate(BaseModel):
    nim: Optional[str] = None
    kelas: Optional[str] = None
    program_studi: Optional[str] = None

class Participant(ParticipantBase):
    id: int
    user_id: int
    user: Optional[UserMinimal] = None
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True

class LecturerBase(BaseModel):
    nidn: str
    bidang_keahlian: str

class LecturerCreate(LecturerBase):
    user_id: int

class LecturerUpdate(BaseModel):
    nidn: Optional[str] = None
    bidang_keahlian: Optional[str] = None

class Lecturer(LecturerBase):
    id: int
    user_id: int
    user: Optional[UserMinimal] = None
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True

class MaterialBase(BaseModel):
    title: str
    description: Optional[str] = None
    file: Optional[str] = None

class MaterialCreate(MaterialBase):
    pass

class MaterialUpdate(BaseModel):
    title: Optional[str] = None
    description: Optional[str] = None
    file: Optional[str] = None

class Material(MaterialBase):
    id: int
    created_by: int
    creator: Optional[UserMinimal] = None
    created_at: datetime

    class Config:
        from_attributes = True
