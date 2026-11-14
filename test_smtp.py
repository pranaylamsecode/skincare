import os
import re
import smtplib
import ssl
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

def load_env(env_path=".env"):
    """Load mail configuration directly from .env file if available."""
    config = {
        "MAIL_HOST": "smtp.gmail.com",
        "MAIL_PORT": "587",
        "MAIL_USERNAME": "fmz@myfmz.com",
        "MAIL_PASSWORD": "pykfhxccfyfmycoz",
        "MAIL_ENCRYPTION": "tls",
        "MAIL_FROM_ADDRESS": "fmz@myfmz.com",
        "MAIL_FROM_NAME": "MYFMZ"
    }
    
    if os.path.exists(env_path):
        with open(env_path, "r", encoding="utf-8") as f:
            for line in f:
                line = line.strip()
                if not line or line.startswith("#"):
                    continue
                match = re.match(r"^([A-Z0-9_]+)=(.*)$", line)
                if match:
                    key = match.group(1)
                    val = match.group(2).strip().strip('"\'')
                    if key in config:
                        config[key] = val
    return config

def run_smtp_test():
    config = load_env()
    host = config["MAIL_HOST"]
    port = int(config["MAIL_PORT"])
    username = config["MAIL_USERNAME"]
    password = config["MAIL_PASSWORD"]
    encryption = config["MAIL_ENCRYPTION"].lower()
    from_addr = config["MAIL_FROM_ADDRESS"]

    print("=" * 60)
    print("SMTP DIAGNOSTIC TEST (Python)")
    print("=" * 60)
    print(f"Host:       {host}")
    print(f"Port:       {port}")
    print(f"Encryption: {encryption}")
    print(f"Username:   {username}")
    print(f"Password:   {'*' * (len(password) - 4) + password[-4:] if len(password) >= 4 else '****'}")
    print(f"From:       {from_addr}")
    print("=" * 60)

    try:
        context = ssl.create_default_context()
        
        if port == 465 or encryption == "ssl":
            print(f"\n[1/3] Connecting via SSL to {host}:{port}...")
            server = smtplib.SMTP_SSL(host, port, context=context, timeout=15)
        else:
            print(f"\n[1/3] Connecting to {host}:{port}...")
            server = smtplib.SMTP(host, port, timeout=15)
            server.set_debuglevel(1)
            print("[2/3] Initiating STARTTLS...")
            server.starttls(context=context)

        print("[3/3] Authenticating credentials...")
        server.login(username, password)
        print("\n>>> AUTHENTICATION SUCCESSFUL! Your credentials are valid.")

        # Send test message
        print("\nSending a test email to itself...")
        msg = MIMEMultipart()
        msg["From"] = from_addr
        msg["To"] = from_addr
        msg["Subject"] = "SMTP Test Verification"
        msg.attach(MIMEText("Success! Your SMTP settings in .env are working properly.", "plain"))
        server.send_message(msg)
        print(f">>> TEST EMAIL SENT SUCCESSFULLY to {from_addr}!")
        
        server.quit()
        print("\nSTATUS: ALL TESTS PASSED.")

    except smtplib.SMTPAuthenticationError as e:
        print("\n" + "!" * 60)
        print("ERROR: Authentication Failed (Bad Credentials)")
        print(f"SMTP Code:  {e.smtp_code}")
        err_msg = e.smtp_error.decode("utf-8", errors="ignore") if isinstance(e.smtp_error, bytes) else str(e.smtp_error)
        print(f"SMTP Error: {err_msg}")
        print("\nPossible Causes:")
        print("1. The Google App Password is expired or mistyped.")
        print("2. 2-Step Verification was modified or turned off on the Google Account.")
        print("3. For Google Workspace, admin settings may restrict App Passwords.")
        print("!" * 60)

    except (smtplib.SMTPConnectError, TimeoutError, ConnectionRefusedError, OSError) as e:
        print("\n" + "!" * 60)
        print(f"ERROR: Connection Failed: {e}")
        print("\nPossible Causes:")
        print(f"1. Port {port} is blocked by your local ISP or Windows Firewall.")
        print("2. DNS cannot resolve smtp.gmail.com.")
        print("3. Try switching between Port 587 (TLS) and Port 465 (SSL).")
        print("!" * 60)

    except Exception as e:
        print("\n" + "!" * 60)
        print(f"UNEXPECTED ERROR: {type(e).__name__}: {e}")
        print("!" * 60)

if __name__ == "__main__":
    run_smtp_test()
