<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

<h1 align="center">PUP School Enrollment System</h1>

<p align="center">
  A web-based enrollment system for the Polytechnic University of the Philippines (PUP) built with Laravel and Supabase (PostgreSQL).
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-red?logo=laravel" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.2+-blue?logo=php" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Supabase-PostgreSQL%2017-3ECF8E?logo=supabase" alt="Supabase">
  <img src="https://img.shields.io/badge/Bootstrap-5-purple?logo=bootstrap" alt="Bootstrap 5">
  <img src="https://img.shields.io/badge/License-MIT-green" alt="MIT License">
</p>

---

## About

The **PUP School Enrollment System** is a full-stack web application that digitizes the student enrollment process for PUP. It supports the complete enrollment lifecycle — from student registration and form submission, to registrar review, section assignment, subject enrollment, and semester-based record keeping.

Built by **Group 8**:
- Florence Lee F. Cansino
- Elias Von Isaac R. Faeldonia
- Ken Audie S. Lucero
- Gabriel Andre E. Magtanong

In fulfillment for the Course: COMP 016: Web Development 

---

## Tech Stack

| Layer      | Technology                  |
|------------|-----------------------------|
| Backend    | PHP Laravel 13              |
| Database   | Supabase (PostgreSQL 17)    |
| Auth       | Laravel Sanctum (API tokens)|
| Frontend   | Blade Templates + Bootstrap 5 |
| Assets     | Vite                        |

---

## Scope Coverage

The system satisfies all five items in the defined project scope:

| # | Scope Item                        | Status | Implementation Detail |
|---|-----------------------------------|--------|-----------------------|
| 1 | **Online enrollment form**        | ✅ Done | Students register and submit an enrollment application via `POST /api/register` + `POST /api/enrollment`. The form captures semester, program, year level, and student type. |
| 2 | **Section/class assignment**      | ✅ Done | Registrar assigns a section upon approval via `PUT /api/applications/{id}/approve`. This creates a `section_assignments` record linking the enrollment to a specific section. |
| 3 | **Subject enrollment per student**| ✅ Done | On approval, all subjects from the assigned section's offerings are automatically enrolled via `subject_enrollments`, each with a status (`enrolled`, `dropped`, `completed`, `failed`). |
| 4 | **Approval by registrar/admin**   | ✅ Done | Registrar/Admin roles can list pending applications, then approve or reject them via protected API endpoints. Rejection requires remarks. |
| 5 | **Student records per semester**  | ✅ Done | Records are scoped per `semester_id` (linked to `academic_years`). Students can check their application history and subject enrollments across semesters via `GET /api/enrollment/status`. |

---

## System Architecture

```
┌─────────────────────────────────────┐
│           Client (Browser)          │
│     Blade Templates + Bootstrap 5   │
└──────────────────┬──────────────────┘
                   │ HTTP / API (Bearer Token)
┌──────────────────▼──────────────────┐
│         Laravel 13 Backend          │
│   Controllers · Middleware · Models │
│         Laravel Sanctum Auth        │
└──────────────────┬──────────────────┘
                   │ PDO / pgsql
┌──────────────────▼──────────────────┐
│     Supabase (PostgreSQL 17)        │
│       17 tables · UUID PKs          │
└─────────────────────────────────────┘
```

---

## User Roles

| Role           | Description |
|----------------|-------------|
| **Student**    | Registers, fills out the enrollment form, and tracks application status. |
| **Faculty**    | Views assigned sections and class lists. |
| **Registrar**  | Reviews and approves/rejects enrollment applications, assigns sections. |
| **Admin**      | Manages all users, roles, and system configuration. |

---

## Enrollment Flow

```
Student registers
        │
        ▼
Profile created (role: student, status: pending)
        │
        ▼
Student submits enrollment form
        │
        ▼
enrollment_application created (status: pending)
        │
        ▼
Registrar reviews application
        │
   ┌────┴────┐
   ▼         ▼
Approve     Reject (requires remarks)
   │
   ▼
Section assigned to student
   │
   ▼
Subjects auto-enrolled from section offerings
   │
   ▼
Student profile status → active
Student number generated (e.g. 2025-00001-MN-0)
```

---

## Database Schema

The system uses **17 tables** organized into 4 groups:

### Auth & Users
| Table              | Description |
|--------------------|-------------|
| `roles`            | student, faculty, registrar, admin |
| `profile_statuses` | pending, active, inactive |
| `profiles`         | One row per user (linked to Laravel auth) |
| `student_profiles` | Enrollment form data, 1:1 with profiles |

### Academic Structure
| Table           | Description |
|-----------------|-------------|
| `academic_years`| e.g. AY 2025–2026 |
| `semesters`     | 1st Sem, 2nd Sem, Summer |
| `colleges`      | 14 PUP colleges |
| `programs`      | 59 undergraduate programs |
| `year_levels`   | 1st to 4th Year |
| `sections`      | Class sections per semester/program/year level |

### Subjects
| Table               | Description |
|---------------------|-------------|
| `subjects`          | Subject master list |
| `subject_offerings` | Subjects offered per section with faculty and schedule |

### Enrollment Flow
| Table                        | Description |
|------------------------------|-------------|
| `application_statuses`       | pending, approved, rejected |
| `subject_enrollment_statuses`| enrolled, dropped, completed, failed |
| `enrollment_applications`    | One per student per semester |
| `section_assignments`        | Section assigned after approval |
| `subject_enrollments`        | Individual subjects per enrollment |

---

## API Endpoints

All API routes are prefixed with `/api`.

### Public

| Method | Endpoint        | Description                            |
|--------|-----------------|----------------------------------------|
| POST   | `/api/register` | Student registration + enrollment form |
| POST   | `/api/login`    | Login (all roles)                      |

### Authenticated (Bearer Token required)

| Method | Endpoint      | Description       |
|--------|---------------|-------------------|
| POST   | `/api/logout` | Logout            |
| GET    | `/api/me`     | Current user info |

### Lookup (all authenticated roles)

| Method | Endpoint                  | Description                 |
|--------|---------------------------|-----------------------------|
| GET    | `/api/lookup/semesters`   | List semesters              |
| GET    | `/api/lookup/colleges`    | List colleges with programs |
| GET    | `/api/lookup/programs`    | List programs               |
| GET    | `/api/lookup/year-levels` | List year levels            |
| GET    | `/api/lookup/sections`    | List sections               |
| GET    | `/api/lookup/subjects`    | List subjects               |

### Student only

| Method | Endpoint                 | Description                      |
|--------|--------------------------|----------------------------------|
| POST   | `/api/enrollment`        | Submit an enrollment application |
| GET    | `/api/enrollment/status` | Check own application status     |

### Registrar & Admin

| Method | Endpoint                         | Description                                      |
|--------|----------------------------------|--------------------------------------------------|
| GET    | `/api/applications`              | List all applications (filterable by `?status=`) |
| PUT    | `/api/applications/{id}/approve` | Approve + assign section + auto-enroll subjects  |
| PUT    | `/api/applications/{id}/reject`  | Reject with remarks                              |

### Admin only

| Method | Endpoint          | Description                            |
|--------|-------------------|----------------------------------------|
| GET    | `/api/users`      | List all users                         |
| POST   | `/api/users`      | Create faculty/registrar/admin account |
| PUT    | `/api/users/{id}` | Update user role/status/password       |

---

## Prerequisites

Make sure the following are installed:

- PHP 8.2+
- Composer
- Node.js + npm
- Git
- A [Supabase](https://supabase.com) project (PostgreSQL)

### Required PHP Extensions

Enable the following in your `php.ini`:

```ini
extension=pdo_pgsql
extension=pgsql
extension=intl
```

To find your `php.ini` location:
```bash
php --ini
```

Uncomment (remove the leading `;`) the lines above, save, and restart your server.

To verify the extensions are active:
```bash
# Windows PowerShell
php -m | Select-String pgsql

# Linux / macOS
php -m | grep pgsql
```

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/tzuyu10/G8-School-Enrollment-System.git
cd G8-School-Enrollment-System
git checkout dev
```

### 2. Install PHP dependencies

```bash
composer install
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 3. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your Supabase credentials:

```env
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-northeast-2.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.[your-project-ref]
DB_PASSWORD=[your-database-password]
```

> Get these from: **Supabase Dashboard → Connect → Direct → Session Pooler**

### 4. Run database migrations

```bash
php artisan migrate
```

> Tables are created with `Schema::hasTable()` checks — existing tables are safely skipped.

### 5. Seed the database

```bash
php artisan db:seed
```

This populates:
- Roles: `student`, `faculty`, `registrar`, `admin`
- Profile statuses: `pending`, `active`, `inactive`
- Application statuses: `pending`, `approved`, `rejected`
- Subject enrollment statuses: `enrolled`, `dropped`, `completed`, `failed`
- Year levels: 1st–4th Year
- Academic Year 2025–2026 with semesters
- All **14 PUP colleges** and **59 undergraduate programs**

### 6. Install frontend dependencies and build assets

```bash
npm install
npm run build
```

---

## Running Locally

```bash
php artisan serve
```

Then open `http://127.0.0.1:8000` in your browser.

### Shortcut

```bash
composer run setup
```

---

## Bootstrapping Admin & Registrar Accounts

Only students can self-register. Admin and registrar accounts must be created manually.

**Via API (recommended after first admin is seeded in the DB):**

```http
POST /api/users
Authorization: Bearer <admin_token>
Content-Type: application/json

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

-- Insert admin profile
INSERT INTO profiles (id, role_id, status_id, full_name, email, password)
VALUES (
  gen_random_uuid(),
  '[admin-role-id]',
  '[active-status-id]',
  'Admin PUP',
  'admin@pup.edu.ph',
  '[bcrypt-hash]'
);
```

Generate a bcrypt hash:
```bash
php artisan tinker
echo Hash::make('yourpassword');
```

---

---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
