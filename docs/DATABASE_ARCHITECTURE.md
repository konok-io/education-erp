# Database Architecture

## Overview

The Education ERP system uses MySQL 8.0 as the primary database with support for optional SQLite offline sync.

## Database Configuration

```sql
ENGINE: MySQL 8.0+
CHARSET: utf8mb4
COLLATION: utf8mb4_unicode_ci
STORAGE ENGINE: InnoDB
TIMEZONE: Asia/Dhaka
DATABASE NAME: education_erp
```

---

## Naming Conventions

### Tables
- Snake_case (lowercase with underscores)
- Plural nouns
- Examples: `students`, `teachers`, `attendance_records`

### Columns
- Snake_case
- Examples: `first_name`, `created_at`, `email_verified_at`

### Primary Keys
- Internal: `id` (auto-increment BIGINT)
- Public: `uuid` (UUID v4)

### Foreign Keys
- Pattern: `{table_singular}_id`
- Examples: `student_id`, `teacher_id`, `class_id`

### Indexes
- Pattern: `idx_{table}_{column}`
- Examples: `idx_students_email`, `idx_attendance_date`

---

## Master Schema

**See `DATABASE_SCHEMA.md` for complete table definitions.**

### Table Summary (61 Tables)

| Category | Tables |
|----------|--------|
| Core | campuses, academic_sessions, users, roles, permissions |
| Student | students, student_profiles, student_guardians, student_addresses, student_documents, student_sessions |
| Teacher | teachers, teacher_documents |
| Staff | staffs |
| Academic | academic_levels, departments, classes, sections, subjects, class_subjects |
| Attendance | attendance_sessions, student_attendance |
| Result | exam_types, exams, marks, grade_rules |
| Routine | routine_slots, class_routines |
| Payment | fee_heads, student_fees, invoices, invoice_items, payments, payment_methods |
| CMS | pages, menus, menu_items, notices, gallery_albums, gallery_images, settings |
| Library | book_categories, authors, publishers, books, book_issues |
| Hostel | hostels, rooms, hostel_allocations |
| Transport | vehicles, routes, route_stoppages, transport_allocations |
| Certificate | certificate_templates, certificates |
| System | activity_logs, notifications, personal_access_tokens |

---

## Key Design Decisions

### UUID Strategy
- Every primary table uses UUID as public identifier
- `id` (BIGINT) used for internal relationships only
- All external references use UUID

### Audit Columns
All business tables include:
```sql
created_at, updated_at, deleted_at
created_by, updated_by (UUID references)
```

### Soft Delete
- All tables support soft deletes
- Uses Laravel's `SoftDeletes` trait
- Data can be recovered

### Multi-Campus
- All tables include `campus_id` where applicable
- Supports future multi-campus expansion

### Academic Sessions
- All student/academic data references `session_id`
- Supports multi-year academic history

---

## Migration Execution Order

1. **System** - users, roles, permissions, settings
2. **Core** - campuses, academic_sessions
3. **Academic** - departments, classes, sections, subjects
4. **People** - students, teachers, staffs
5. **Operations** - attendance, exams, marks
6. **Finance** - fees, invoices, payments
7. **CMS** - pages, menus, notices, gallery
8. **Modules** - library, hostel, transport, certificates
9. **System Logs** - activity_logs, notifications

---

## Documentation

- [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md) - Complete schema with all table definitions
- [CODING_STANDARDS.md](CODING_STANDARDS.md) - Database coding standards

---

## Backup Strategy

```bash
# Database backup
mysqldump -u root -p education_erp > backup_$(date +%Y%m%d).sql

# Automated daily backup via cron
0 2 * * * mysqldump -u root -p$DB_PASS education_erp > /backup/education_erp_$(date +\%Y\%m\%d).sql
```

---

**Status:** Phase 006 Complete - Schema Finalized
