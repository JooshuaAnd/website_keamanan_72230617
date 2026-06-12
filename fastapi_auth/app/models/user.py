from sqlalchemy import Column, Integer, String, Boolean, DateTime
from app.core.database import Base

class User(Base):
    __tablename__ = "users"

    id = Column(Integer, primary_key=True, index=True)
    email = Column(String, unique=True, index=True, nullable=False)
    hashed_password = Column(String, nullable=False)
    full_name = Column(String, index=True)
    role = Column(String, default="peserta")
    is_active = Column(Boolean(), default=True)
    is_verified = Column(Boolean(), default=False)
    is_superuser = Column(Boolean(), default=False)
    action_token = Column(String, unique=True, index=True, nullable=True)
    login_attempts = Column(Integer, default=0)
    locked_until = Column(DateTime, nullable=True)
