from app.core.database import SessionLocal, engine, Base
from app.models.user import User
from app.models.lms import Participant, Lecturer, Material
from app.core import security

Base.metadata.create_all(bind=engine)

def seed():
    db = SessionLocal()
    try:
        admin = db.query(User).filter(User.email == "admin@gmail.com").first()
        if not admin:
            admin = User(
                email="admin@gmail.com",
                hashed_password=security.get_password_hash("Admin123"),
                full_name="System Administrator",
                is_active=True,
                is_verified=True,
                is_superuser=True,
                role="admin",
            )
            db.add(admin)
            db.commit()
            db.refresh(admin)
            print("Admin created: admin@gmail.com / Admin123")
        else:
            print("Admin already exists")

        dosen = db.query(User).filter(User.email == "dosen@lms.com").first()
        if not dosen:
            dosen = User(
                email="dosen@lms.com",
                hashed_password=security.get_password_hash("Dosen123"),
                full_name="Dr. Budi Santoso",
                is_active=True,
                is_verified=True,
                role="dosen",
            )
            db.add(dosen)
            db.commit()
            db.refresh(dosen)

            lecturer = Lecturer(
                user_id=dosen.id,
                nidn="1234567890",
                bidang_keahlian="Teknik Informatika",
            )
            db.add(lecturer)
            db.commit()
            print("Dosen created: dosen@lms.com / Dosen123")
        else:
            print("Dosen already exists")

        peserta = db.query(User).filter(User.email == "peserta@lms.com").first()
        if not peserta:
            peserta = User(
                email="peserta@lms.com",
                hashed_password=security.get_password_hash("Peserta123"),
                full_name="Andi Pratama",
                is_active=True,
                is_verified=True,
                role="peserta",
            )
            db.add(peserta)
            db.commit()
            db.refresh(peserta)

            participant = Participant(
                user_id=peserta.id,
                nim="1234567890",
                kelas="A-2024",
                program_studi="Teknik Informatika",
            )
            db.add(participant)
            db.commit()
            print("Peserta created: peserta@lms.com / Peserta123")
        else:
            print("Peserta already exists")

        material_count = db.query(Material).count()
        if material_count == 0 and dosen:
            materials = [
                Material(title="Pengantar Pemrograman Web", description="Materi dasar tentang HTML, CSS, dan JavaScript", created_by=dosen.id),
                Material(title="Keamanan Aplikasi Web", description="Materi tentang OWASP Top 10 dan secure coding practices", created_by=dosen.id),
                Material(title="Database Design", description="Konsep perancangan database dan SQL", created_by=dosen.id),
            ]
            for m in materials:
                db.add(m)
            db.commit()
            print("Sample materials created")
        else:
            print("Materials already exist")

    finally:
        db.close()

if __name__ == "__main__":
    seed()
