# AI Based Vehicle Number Plate Recognition System (ANPR)

**Overview**
- A lightweight PHP/MySQL web application for recording and managing detected vehicle number plates. The repository was reorganized so the public web assets are under `public/` and configuration files are outside the web root in `config/`.

**Quick summary**
- Public web root: [public](public)
- Database config: [config/db_connect.php](config/db_connect.php)
- Sample database + data: [config/sample_data.sql](config/sample_data.sql)
- Third-party PDF library: `TCPDF-main/`
- Composer deps (PHPMailer): `vendor/`

**Project Features**
- User registration / login
- Admin dashboard and user management
- Vehicle record storage and display
- Email OTP registration using PHPMailer
- PDF generation using TCPDF (present in `TCPDF-main/`)

**Requirements**
- Windows (XAMPP) or any LAMP stack
- PHP 7.4+ (extensions: mysqli, mbstring, gd)
- MySQL or MariaDB
- Composer (optional, for managing PHP packages)

**Files of interest**
- Database init script: [config/init.php](config/init.php)
- Sample SQL (creates DB, tables and 5 sample rows): [config/sample_data.sql](config/sample_data.sql)
- Web frontend & pages: files inside [public](public)
- Stylesheets: [public/css](public/css)
- Images: [public/assets/images](public/assets/images)
- Simple DB connection: [config/db_connect.php](config/db_connect.php)

**Installation & Setup (local, XAMPP)**
1. Place the project inside your Apache document root (for example `C:/xampp/htdocs/simple`). Ensure the server is allowed to serve the `public/` folder as the site root. The easiest approach is to update Apache's DocumentRoot to `.../simple/public` or create a VirtualHost pointing to it.

2. Import the sample database to create tables and example data. From a terminal (or MySQL client):

```bash
# run from the project root
mysql -u root -p < config/sample_data.sql
```

3. Verify `config/db_connect.php` contains the correct DB credentials for your environment. Example connection values: host `localhost`, user `root`, password `""` (empty), dbname `number_plate_db`.

4. If you do not have Composer dependencies installed (i.e., `vendor/autoload.php` missing), run:

```bash
# in project root
composer install
```

(There is an included `vendor/` in the repository already; running composer install is only necessary if you removed it or want to update packages.)

5. Ensure `public/` is writable by PHP where file uploads or generated files are written (if any). Configure `php.ini` if you need larger upload limits.

**Passwords & Authentication notes**
- The sample SQL uses plain-text passwords to make the demo importable quickly. The application expects hashed passwords (it uses `password_verify()`), so you should replace the sample plain passwords with bcrypt hashes before trusting the data for real use.

To generate bcrypt hashes using PHP CLI and update the user in MySQL:

```bash
# generate hash (example)
php -r "echo password_hash('password123', PASSWORD_BCRYPT) . PHP_EOL;"
```

Then run an UPDATE in MySQL for that user:

```sql
UPDATE users SET password = '<bcrypt-hash>' WHERE email = 'admin@example.com';
```

I can create a small PHP helper script to hash the sample rows automatically if you'd like — tell me and I'll add it.

**How the code references the DB & assets**
- PHP pages include the DB connection like: `include __DIR__ . '/../config/db_connect.php';` (so `config/` is outside web root).
- CSS files are referenced inside `public/css/` and images inside `public/assets/images/`.
- If you change the web server root, double-check the relative paths in the top of the PHP files.

**TCPDF notes**
- `TCPDF-main/` is bundled for PDF generation. If your PDFs embed images, confirm `K_PATH_IMAGES` inside `TCPDF-main/config/tcpdf_config.php` points to an accessible images path (or use absolute paths).

**Composer & PHPMailer**
- Email OTP uses PHPMailer. `send_otp.php` expects `vendor/autoload.php`. If you need to re-install: run `composer require phpmailer/phpmailer`.

**Security & Production checklist**
- Move `config/` outside of the server's document root (it is already placed outside `public/` in this repo).
- Remove sample accounts and replace with secure passwords (bcrypt hashed).
- Use environment variables or a secure mechanism for email credentials rather than hard-coding in PHP files.
- Use TLS/SSL for SMTP and HTTPS for web access.

**Troubleshooting**
- Blank pages: enable `display_errors` in `php.ini` during development, or check `apache` error logs.
- Database errors: check `config/db_connect.php` values and ensure `number_plate_db` exists and the server is reachable.
- PHPMailer errors: check `send_otp.php` credentials and less‑secure app settings if using Gmail (prefer app password / OAuth).

**Suggested next improvements**
- Replace inline SQL with prepared statements everywhere (some already use prepared statements, keep auditing).
- Centralize configuration using `.env` and a small bootstrap loader.
- Implement password hashing on registration automatically (if not already), and migrate sample users to hashed passwords.
- Add unit/integration tests for critical flows.

**Where I put things (quick links)**
- Project root: see this `README.md` file
- DB connection: [config/db_connect.php](config/db_connect.php)
- Sample SQL: [config/sample_data.sql](config/sample_data.sql)
- Public site files: [public](public)

---
If you want, I can now:
- convert the sample users' plain passwords to bcrypt hashes and update `config/sample_data.sql` (so imports create hashed passwords), or
- add a small `scripts/hash_passwords.php` helper to convert existing DB rows to hashed passwords.

Tell me which one you prefer and I will implement it.