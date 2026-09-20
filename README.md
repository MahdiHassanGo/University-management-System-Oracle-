# Career Fair Management System - PHP + Oracle 10g XE

Final-term PHP user interface for the Career Fair Management System. This version connects directly to the existing Oracle 10g XE `CF_OWNER` schema using PHP OCI8. It does **not** use MySQL or phpMyAdmin.

## What stays in Oracle

The PHP website uses the same academic tables from the project report: ADMINISTRATOR, CAREER_FAIR, STUDENT, SKILL, STUDENT_SKILL, FAIR_REGISTRATION, COMPANY, COMPANY_PARTICIPATION, COMPANY_REPRESENTATIVE, BOOTH, BOOTH_ASSIGNMENT, JOB_VACANCY, JOB_REQUIRED_SKILL, APPLICATION, INTERVIEW, FAIR_SESSION, SESSION_ATTENDANCE, and FEEDBACK.

One small support table, `APP_USER`, is added for website login only.

## First-time setup summary

1. Start the Windows VM and Oracle 10g XE.
2. Confirm you can log in to Oracle APEX as `CF_OWNER`.
3. Run `database/02_verify_project_schema.sql` to verify the academic tables.
4. Run `database/01_web_support_setup.sql` once to create `APP_USER` and three demo logins.
5. Install/start Apache + PHP on the same Windows VM.
6. Enable a working PHP OCI8 extension and restart Apache.
7. Copy this folder to your Apache document root and keep the folder name `career_fair_management` OR update `BASE_URL` in `config/app.php`.
8. Copy `config/database.local.php.example` to `config/database.local.php` and set your real `CF_OWNER` password. (This file is ignored by git).
9. Open `http://localhost/career_fair_management/setup-check.php`.
10. When the page says **Oracle connection successful**, open the login page.

## Demo accounts

The initial demo accounts created by `database/01_web_support_setup.sql`:

- Admin: `admin@example.com`
- Student: `student@example.com`
- Company representative: `company@example.com`

The `related_id` values assume Administrator 1, Student 1 and Company Representative 1 exist, matching the sample project data. If your current IDs differ, update the three `APP_USER.related_id` values in Oracle.

## Oracle connection

Configure your credentials safely without committing them to version control:

1. Copy `config/database.local.php.example` to `config/database.local.php`.
2. Edit `config/database.local.php` with your local database password:
   - User: `CF_OWNER`
   - Service: `127.0.0.1:1521/XE`
   - Charset: `AL32UTF8`

Alternatively, you can set `DB_USER`, `DB_PASS`, and `DB_CONNECTION_STRING` via environment variables. `config/database.local.php` is already included in `.gitignore`.

## Included SQL

- `database/01_web_support_setup.sql` - creates web login support only.
- `database/02_verify_project_schema.sql` - checks your existing Oracle schema.
- `database/99_optional_fresh_schema_ONLY_IF_EMPTY.sql` - optional full academic schema for a completely empty schema; **do not run on your existing populated CF_OWNER database**.
- `coursework/Career_Fair_36_Queries_Oracle10g.sql` - the numbered Oracle coursework queries.

## Main UI features

- Admin: dashboard, fairs, students, company approvals, booths, jobs, applications, interviews, sessions, feedback.
- Student: dashboard, fair registration, job application, application history, sessions, feedback.
- Company representative: dashboard, participation requests, job management, application status management, interview scheduling/results.
- Setup Check page proves PHP -> OCI8 -> Oracle 10g XE connectivity and lists the Oracle tables.

## Verification already performed in the generated package

All PHP files pass `php -l` syntax validation in the generation environment. A live Oracle connection could not be executed from that environment, so `setup-check.php` is the required runtime verification on your Windows VM.

Read the included PDF guide before setup.
