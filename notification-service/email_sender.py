import smtplib
from email.mime.text import MIMEText
import os

SMTP_HOST = os.getenv("SMTP_HOST")
SMTP_PORT = int(os.getenv("SMTP_PORT"))
SMTP_USER = os.getenv("SMTP_USER")
SMTP_PASSWORD = os.getenv("SMTP_PASSWORD")

def send_welcome_email(to_email: str, name: str):
    subject = "Bienvenido a la empresa"
    body = f"""
    <html>
    <body>
        <h2>Hola {name},</h2>
        <p>¡Bienvenido al equipo! Estamos encantados de tenerte con nosotros.</p>
    </body>
    </html>
    """

    msg = MIMEText(body, "html")
    msg["Subject"] = subject
    msg["From"] = SMTP_USER
    msg["To"] = to_email

    try:
        with smtplib.SMTP(SMTP_HOST, SMTP_PORT) as server:
            server.starttls()
            server.login(SMTP_USER, SMTP_PASSWORD)
            server.sendmail(SMTP_USER, [to_email], msg.as_string())

    except Exception as e:
        print(f"Email sending failed: {e}")
        raise