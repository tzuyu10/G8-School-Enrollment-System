<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

# PUP School Enrollment System

A web-based enrollment system for the Polytechnic University of the Philippines (PUP) built with Laravel and Supabase (PostgreSQL).

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP Laravel 13 |
| Database | Supabase (PostgreSQL 17) |
| Auth | Laravel Sanctum |
| Frontend | Blade Templates + Bootstrap 5 |
| Assets | Vite |

## System Scope

- Online enrollment form (student registration + profile)
- Section/class assignment by registrar
- Subject enrollment per student
- Approval by registrar/admin
- Student records per semester

## User Roles

| Role | Description |
|---|---|
| Student | Registers, fills enrollment form, tracks application status |
| Faculty | Views assigned sections and class lists |
| Registrar | Reviews and approves/rejects enrollment applications, assigns sections |
| Admin | Manages users, system configuration |

## Prerequisites

Make sure the following are installed on your machine:

- PHP 8.2+
- Composer
- Node.js + npm
- Git
- A Supabase project (PostgreSQL)

### PHP Extensions Required

The following PHP extensions must be enabled in your `php.ini`:

```ini
extension=pdo_pgsql
extension=pgsql
extension=intl
```

To find your `php.ini` location:
```bash
php --ini
```

Then uncomment (remove the leading `;`) the lines above, save, and restart your local server.

To verify extensions are loaded:
```bash
# Windows PowerShell
php -m | Select-String pgsql

# Linux/Mac
php -m | grep pgsql
```

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/<username>/<repo>.git
cd g8-webdev-proj
```

### 2. Install PHP dependencies

```bash
composer install
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 3. Configure environment

Copy the example env file and fill in your Supabase credentials:

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your Supabase connection details:

```env
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-northeast-2.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.[your-project-ref]
DB_PASSWORD=[your-database-password]
```

> Get these from: Supabase Dashboard → Connect → Direct → Session Pooler

### 4. Run database migrations

```bash
php artisan migrate
```

> Note: Tables are created with `Schema::hasTable()` checks so existing tables are safely skipped.

### 5. Seed the database

```bash
php artisan db:seed
```

This populates:
- Roles (`student`, `faculty`, `registrar`, `admin`)
- Profile statuses (`pending`, `active`, `inactive`)
- Application statuses (`pending`, `approved`, `rejected`)
- Subject enrollment statuses (`enrolled`, `dropped`, `completed`, `failed`)
- Year levels (1st–4th Year)
- Academic Year 2025–2026 with semesters
- All 14 PUP colleges and 59 undergraduate programs

### 6. Install frontend dependencies and build assets

```bash
npm install
npm run build
```

## Running Locally

```bash
php artisan serve
```

Then open `http://127.0.0.1:8000` in your browser.

### Shortcut

```bash
composer run setup
```

## Database Schema

The system uses 17 tables organized into 4 groups:

**Auth & Users**
- `roles` — student, faculty, registrar, admin (DB-managed)
- `profile_statuses` — pending, active, inactive
- `profiles` — one row per user, linked to Laravel auth
- `student_profiles` — enrollment form data, 1:1 with profiles

**Academic Structure**
- `academic_years` — e.g. AY 2025-2026
- `semesters` — 1st Sem, 2nd Sem, Summer
- `colleges` — 14 PUP colleges
- `programs` — 59 undergraduate programs
- `year_levels` — 1st to 4th Year
- `sections` — class sections per semester/program/year level

**Subjects**
- `subjects` — subject master list
- `subject_offerings` — subjects offered per section with faculty and schedule

**Enrollment Flow**
- `application_statuses` — pending, approved, rejected
- `subject_enrollment_statuses` — enrolled, dropped, completed, failed
- `enrollment_applications` — one per student per semester
- `section_assignments` — section assigned after approval
- `subject_enrollments` — individual subjects per enrollment

## API Endpoints

All API routes are prefixed with `/api`.

### Public
| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/register` | Student registration + enrollment form |
| POST | `/api/login` | Login (all roles) |

### Authenticated (Bearer Token)
| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/logout` | Logout |
| GET | `/api/me` | Current user info |

### Lookup (all roles)
| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/lookup/semesters` | List semesters |
| GET | `/api/lookup/colleges` | List colleges with programs |
| GET | `/api/lookup/programs` | List programs |
| GET | `/api/lookup/year-levels` | List year levels |
| GET | `/api/lookup/sections` | List sections |
| GET | `/api/lookup/subjects` | List subjects |

### Student only
| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/enrollment` | Submit enrollment application |
| GET | `/api/enrollment/status` | Check own application status |

### Registrar + Admin
| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/applications` | List all applications (filter by `?status=pending`) |
| PUT | `/api/applications/{id}/approve` | Approve + assign section + auto-enroll subjects |
| PUT | `/api/applications/{id}/reject` | Reject with remarks |

### Admin only
| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/users` | List all users |
| POST | `/api/users` | Create faculty/registrar/admin account |
| PUT | `/api/users/{id}` | Update user role/status/password |

## Enrollment Flow

```
Student registers → profile created (role: student, status: pending)
        ↓
Student fills & submits enrollment form
        ↓
enrollment_application created (status: pending)
        ↓
Registrar reviews → approves/rejects
        ↓
On approval:
  - application status → approved
  - section assigned
  - subjects auto-enrolled from section offerings
  - profile status → active
```

## Bootstrapping Admin & Registrar Accounts

Since only students can self-register, admin and registrar accounts must be created manually or via the Admin API.

**Via Postman (recommended after first admin is created in DB):**
```
POST /api/users
Authorization: Bearer [admin token]
{
  "full_name": "...",
  "email": "...",
  "password": "...",
  "role": "registrar"
}
```

**Via Supabase SQL Editor (for initial bootstrap):**
```sql
-- Get role and status IDs
SELECT id, code FROM roles;
SELECT id, code FROM profile_statuses;

-- Then insert
INSERT INTO profiles (id, role_id, status_id, full_name, email, password)
VALUES (gen_random_uuid(), '[admin-role-id]', '[active-status-id]', 'Admin PUP', 'admin@pup.edu.ph', '[bcrypt-hash]');
```

Generate a bcrypt hash via:
```bash
php artisan tinker
echo Hash::make('yourpassword');
```

## Notes

- Student numbers (`20XX-XXXXX-MN-0`) are assigned by the registrar after approval, not during registration
- ITech diploma programs and OUS are not included in this version
- RLS is disabled on Supabase — Laravel handles all authorization via `RoleMiddleware`
- The `personal_access_tokens.tokenable_id` column must be `text` type (not `bigint`) to support UUID primary keys

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
