import random
import string
from app.core.database import SessionLocal
from app.models.user import User
from app.models.lms import Participant

def fill_missing_participants():
    db = SessionLocal()
    try:
        # Get all users with role 'peserta'
        peserta_users = db.query(User).filter(User.role == "peserta").all()
        print(f"Found {len(peserta_users)} users with role 'peserta'")
        
        updated_count = 0
        for user in peserta_users:
            # Check if participant profile exists
            participant = db.query(Participant).filter(Participant.user_id == user.id).first()
            if not participant:
                # Generate unique NIM
                while True:
                    nim = "".join(random.choices(string.digits, k=10))
                    if not db.query(Participant).filter(Participant.nim == nim).first():
                        break
                
                new_participant = Participant(
                    user_id=user.id,
                    nim=nim,
                    kelas="Reg-2026",
                    program_studi="Teknik Informatika"
                )
                db.add(new_participant)
                updated_count += 1
                print(f"Created participant profile for User ID {user.id} ({user.email}) with NIM {nim}")
        
        db.commit()
        print(f"Successfully created {updated_count} missing participant profiles.")
    finally:
        db.close()

if __name__ == "__main__":
    fill_missing_participants()
