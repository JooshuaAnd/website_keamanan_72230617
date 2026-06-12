from fastapi_mail import ConnectionConfig, FastMail, MessageSchema, MessageType
from pydantic import EmailStr
from app.core.config import settings

conf = ConnectionConfig(
    MAIL_USERNAME=settings.SMTP_USER,
    MAIL_PASSWORD=settings.SMTP_PASSWORD,
    MAIL_FROM=settings.EMAILS_FROM_EMAIL,
    MAIL_PORT=settings.SMTP_PORT,
    MAIL_SERVER=settings.SMTP_HOST,
    MAIL_STARTTLS=settings.SMTP_TLS,
    MAIL_SSL_TLS=settings.SMTP_SSL,
    USE_CREDENTIALS=True,
    VALIDATE_CERTS=True,
    MAIL_FROM_NAME=settings.EMAILS_FROM_NAME,
)

fastmail = FastMail(conf)

def get_html_template(title, body, button_text, url):
    return f"""
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
    </head>
    <body style="background-color: #f9fafb; padding: 20px; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        <div style="max-width: 500px; margin: 40px auto; padding: 40px; background-color: #ffffff; border: 1px solid #f3f4f6; border-radius: 24px; text-align: center; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
            <h2 style="color: #111827; margin-bottom: 16px; font-size: 24px;">{title}</h2>
            <p style="color: #4b5563; line-height: 1.6; font-size: 16px; margin-bottom: 24px;">{body}</p>
            <div style="margin: 20px 0;">
                <a href="{url}" style="background-color: #dc2626; color: #ffffff !important; padding: 14px 28px; text-decoration: none; display: inline-block; font-size: 16px; border-radius: 10px; font-weight: bold; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    {button_text}
                </a>
            </div>
        </div>
    </body>
    </html>
    """

async def send_verification_email(email: EmailStr, token: str):
    verification_url = f"{settings.FRONTEND_HOST}/verify/{token}"
    html = get_html_template(
        "Verify your email",
        "Welcome! Please confirm your email address to activate your account by clicking the button below.",
        "Verify Email",
        verification_url
    )
    message = MessageSchema(
        subject="Verify your email",
        recipients=[email],
        body=html,
        subtype=MessageType.html
    )
    try:
        await fastmail.send_message(message)
    except Exception as e:
        print(f"SMTP Error: {str(e)}")
        print(f"SIMULATION LINK: {verification_url}")

async def send_reset_password_email(email: EmailStr, token: str):
    reset_url = f"{settings.FRONTEND_HOST}/reset-password/{token}"
    html = get_html_template(
        "Password Reset Request",
        "We received a request to reset your password. Click the button below to choose a new password.",
        "Reset Password",
        reset_url
    )
    message = MessageSchema(
        subject="Password Reset Request",
        recipients=[email],
        body=html,
        subtype=MessageType.html
    )
    await fastmail.send_message(message)
