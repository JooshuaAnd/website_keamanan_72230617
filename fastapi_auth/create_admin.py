from sqlalchemy.orm import Session
from app.core.database import SessionLocal, engine, Base
from app.models.user import User
from app.models.lms import Participant, Lecturer, Material
from app.core import security

Base.metadata.create_all(bind=engine)

def create_admin():
    db = SessionLocal()
    try:
        admin_email = "admin@gmail.com"
        admin = db.query(User).filter(User.email == admin_email).first()
        if not admin:
            admin = User(
                email=admin_email,
                hashed_password=security.get_password_hash("Admin123"),
                full_name="System Administrator",
                is_active=True,
                is_verified=True,
                is_superuser=True,
                role="admin",
            )
            db.add(admin)
            db.commit()
            print(f"Admin user created: {admin_email} / Admin123")
        else:
            print("Admin user already exists.")
    finally:
        db.close()

if __name__ == "__main__":
    create_admin()
