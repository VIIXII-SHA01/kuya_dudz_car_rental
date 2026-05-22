# Mailer configuration

This project uses PHPMailer (via `vendor/`) to send password reset emails.

Setup (recommended)

- Add the SMTP credentials as environment variables on your system (Windows):

  Open PowerShell as Administrator and run:

  ```powershell
  setx SMTP_HOST "smtp.gmail.com" /M
  setx SMTP_PORT "587" /M
  setx SMTP_USER "marketingj786@gmail.com" /M
  setx SMTP_PASS "your_smtp_password_here" /M
  setx FROM_EMAIL "marketingj786@gmail.com" /M
  setx FROM_NAME "KDCR Support" /M
  ```

  Then restart the Apache service (or your machine) so PHP/Apache picks up the new environment variables.

- For temporary session testing in the current PowerShell session use:

  ```powershell
  $env:SMTP_USER = 'marketingj786@gmail.com'
  $env:SMTP_PASS = 'your_smtp_password_here'
  ```

Alternative

- The mailer now loads a `.env` file from the project root if one exists, so you can keep your SMTP settings in that file instead of system environment variables.
- Optionally set `MAIL_DEBUG=1` in `.env` to surface SMTP errors during testing.

Gmail note

- If you use Gmail, you typically need an App Password instead of your regular account password. Create one in Google Account > Security > App passwords and put it in `SMTP_PASS`.

Security note

- Do NOT commit real credentials into the repository. Keep `.env` in `.gitignore` or use system env vars.
