# Education ERP Migration Plan

## Overview

This document defines the complete database migration strategy for the Education ERP system. All migrations must follow this execution order to maintain referential integrity.

---

## Migration Principles

1. **Small** - Each migration handles one table or related changes
2. **Atomic** - Each migration can be run independently
3. **Version Controlled** - All migrations are in Git
4. **Reversible** - All migrations have proper `down()` methods
5. **Independent** - No circular dependencies

---

## Migration Naming Convention

```
create_{table_name}_table
add_{column}_to_{table}_table
create_{relationship}_table
```

Examples:
- `create_users_table`
- `create_students_table`
- `add_campus_id_to_users_table`
- `create_student_attendance_table`

---

## Execution Order (16 Levels)

### Level 1: Core System
**Order: 001**

| File Name | Purpose |
|-----------|---------|
| `create_campuses_table` | Multi-campus support |
| `create_academic_sessions_table` | Academic year management |
| `create_roles_table` | User roles |
| `create_permissions_table` | Permission definitions |
| `create_role_permission_table` | Role-Permission pivot |
| `create_model_has_roles_table` | Model-Role pivot |
| `create_model_has_permissions_table` | Model-Permission pivot |
| `create_users_table` | Authentication users |
| `create_personal_access_tokens_table` | Sanctum API tokens |
| `create_settings_table` | Application settings |

---

### Level 2: Academic Structure
**Order: 002**

| File Name | Purpose |
|-----------|---------|
| `create_departments_table` | Department definitions |
| `create_academic_levels_table` | Class levels (Class 1-12) |
| `create_classes_table` | Class definitions |
| `create_sections_table` | Section definitions |
| `create_subjects_table` | Subject definitions |
| `create_class_subjects_table` | Class-Subject mapping |

---

### Level 3: Student Management
**Order: 003**

| File Name | Purpose |
|-----------|---------|
| `create_students_table` | Core student records |
| `create_student_profiles_table` | Family information |
| `create_student_guardians_table` | Guardian records |
| `create_student_addresses_table` | Present/Permanent addresses |
| `create_student_documents_table` | Document uploads |
| `create_student_sessions_table` | Session enrollment |

---

### Level 4: Teacher Management
**Order: 004**

| File Name | Purpose |
|-----------|---------|
| `create_teachers_table` | Core teacher records |
| `create_teacher_documents_table` | Teacher documents |
| `create_teacher_subjects_table` | Subject assignments |

---

### Level 5: Staff Management
**Order: 005**

| File Name | Purpose |
|-----------|---------|
| `create_staffs_table` | Staff records |
| `create_staff_profiles_table` | Staff profiles |
| `create_staff_departments_table` | Staff department assignments |

---

### Level 6: Attendance System
**Order: 006**

| File Name | Purpose |
|-----------|---------|
| `create_attendance_sessions_table` | Daily attendance sessions |
| `create_student_attendance_table` | Student daily attendance |
| `create_teacher_attendance_table` | Teacher attendance |
| `create_staff_attendance_table` | Staff attendance |
| `create_attendance_logs_table` | Attendance change logs |

---

### Level 7: Examination & Results
**Order: 007**

| File Name | Purpose |
|-----------|---------|
| `create_exam_types_table` | Exam type definitions |
| `create_exams_table` | Exam schedules |
| `create_grade_rules_table` | Grading scale configuration |
| `create_marks_table` | Student marks records |
| `create_result_publish_table` | Published results |

---

### Level 8: Class Routines
**Order: 008**

| File Name | Purpose |
|-----------|---------|
| `create_routine_slots_table` | Time slot definitions |
| `create_class_routines_table` | Weekly class schedules |
| `create_exam_routines_table` | Exam schedules |

---

### Level 9: Finance & Payments
**Order: 009**

| File Name | Purpose |
|-----------|---------|
| `create_fee_heads_table` | Fee categories |
| `create_payment_methods_table` | Payment method options |
| `create_student_fees_table` | Assigned fees to students |
| `create_invoices_table` | Invoice generation |
| `create_invoice_items_table` | Invoice line items |
| `create_payments_table` | Payment records |
| `create_dues_table` | Outstanding dues tracking |

---

### Level 10: CMS
**Order: 010**

| File Name | Purpose |
|-----------|---------|
| `create_menus_table` | Navigation menus |
| `create_menu_items_table` | Menu items |
| `create_pages_table` | Static pages |
| `create_notices_table` | Notice board |
| `create_gallery_albums_table` | Photo albums |
| `create_gallery_images_table` | Gallery photos |
| `create_sliders_table` | Homepage sliders |
| `create_contact_messages_table` | Contact form submissions |
| `create_social_links_table` | Social media links |
| `create_footer_links_table` | Footer navigation |

---

### Level 11: Library
**Order: 011**

| File Name | Purpose |
|-----------|---------|
| `create_authors_table` | Book authors |
| `create_publishers_table` | Book publishers |
| `create_book_categories_table` | Book categories |
| `create_books_table` | Book inventory |
| `create_book_issues_table` | Book lending records |
| `create_book_returns_table` | Book return records |

---

### Level 12: Hostel
**Order: 012**

| File Name | Purpose |
|-----------|---------|
| `create_hostels_table` | Hostel definitions |
| `create_rooms_table` | Room definitions |
| `create_hostel_allocations_table` | Student hostel assignments |

---

### Level 13: Transport
**Order: 013**

| File Name | Purpose |
|-----------|---------|
| `create_vehicles_table` | Transport vehicles |
| `create_drivers_table` | Driver records |
| `create_routes_table` | Transport routes |
| `create_route_stoppages_table` | Route stoppages |
| `create_transport_allocations_table` | Student transport assignments |

---

### Level 14: Certificates
**Order: 014**

| File Name | Purpose |
|-----------|---------|
| `create_certificate_templates_table` | Certificate templates |
| `create_certificates_table` | Issued certificates |
| `create_certificate_logs_table` | Certificate audit logs |

---

### Level 15: Communication
**Order: 015**

| File Name | Purpose |
|-----------|---------|
| `create_notifications_table` | User notifications |
| `create_sms_logs_table` | SMS sending logs |
| `create_email_logs_table` | Email sending logs |

---

### Level 16: System & Logs
**Order: 016**

| File Name | Purpose |
|-----------|---------|
| `create_activity_logs_table` | User activity logs |
| `create_jobs_table` | Queue jobs |
| `create_failed_jobs_table` | Failed job logs |
| `create_cache_table` | Cache storage |

---

## Migration Folder Structure

```
database/migrations/
├── 001_core/
│   ├── create_campuses_table.php
│   ├── create_academic_sessions_table.php
│   ├── create_roles_table.php
│   ├── create_permissions_table.php
│   ├── create_role_permission_table.php
│   ├── create_model_has_roles_table.php
│   ├── create_model_has_permissions_table.php
│   ├── create_users_table.php
│   ├── create_personal_access_tokens_table.php
│   └── create_settings_table.php
├── 002_academic/
│   ├── create_departments_table.php
│   ├── create_academic_levels_table.php
│   ├── create_classes_table.php
│   ├── create_sections_table.php
│   ├── create_subjects_table.php
│   └── create_class_subjects_table.php
├── 003_student/
│   ├── create_students_table.php
│   ├── create_student_profiles_table.php
│   ├── create_student_guardians_table.php
│   ├── create_student_addresses_table.php
│   ├── create_student_documents_table.php
│   └── create_student_sessions_table.php
├── 004_teacher/
│   ├── create_teachers_table.php
│   ├── create_teacher_documents_table.php
│   └── create_teacher_subjects_table.php
├── 005_staff/
│   ├── create_staffs_table.php
│   ├── create_staff_profiles_table.php
│   └── create_staff_departments_table.php
├── 006_attendance/
│   ├── create_attendance_sessions_table.php
│   ├── create_student_attendance_table.php
│   ├── create_teacher_attendance_table.php
│   ├── create_staff_attendance_table.php
│   └── create_attendance_logs_table.php
├── 007_examination/
│   ├── create_exam_types_table.php
│   ├── create_exams_table.php
│   ├── create_grade_rules_table.php
│   ├── create_marks_table.php
│   └── create_result_publish_table.php
├── 008_routine/
│   ├── create_routine_slots_table.php
│   ├── create_class_routines_table.php
│   └── create_exam_routines_table.php
├── 009_finance/
│   ├── create_fee_heads_table.php
│   ├── create_payment_methods_table.php
│   ├── create_student_fees_table.php
│   ├── create_invoices_table.php
│   ├── create_invoice_items_table.php
│   ├── create_payments_table.php
│   └── create_dues_table.php
├── 010_cms/
│   ├── create_menus_table.php
│   ├── create_menu_items_table.php
│   ├── create_pages_table.php
│   ├── create_notices_table.php
│   ├── create_gallery_albums_table.php
│   ├── create_gallery_images_table.php
│   ├── create_sliders_table.php
│   ├── create_contact_messages_table.php
│   ├── create_social_links_table.php
│   └── create_footer_links_table.php
├── 011_library/
│   ├── create_authors_table.php
│   ├── create_publishers_table.php
│   ├── create_book_categories_table.php
│   ├── create_books_table.php
│   ├── create_book_issues_table.php
│   └── create_book_returns_table.php
├── 012_hostel/
│   ├── create_hostels_table.php
│   ├── create_rooms_table.php
│   └── create_hostel_allocations_table.php
├── 013_transport/
│   ├── create_vehicles_table.php
│   ├── create_drivers_table.php
│   ├── create_routes_table.php
│   ├── create_route_stoppages_table.php
│   └── create_transport_allocations_table.php
├── 014_certificate/
│   ├── create_certificate_templates_table.php
│   ├── create_certificates_table.php
│   └── create_certificate_logs_table.php
├── 015_communication/
│   ├── create_notifications_table.php
│   ├── create_sms_logs_table.php
│   └── create_email_logs_table.php
└── 016_system/
    ├── create_activity_logs_table.php
    ├── create_jobs_table.php
    ├── create_failed_jobs_table.php
    └── create_cache_table.php
```

---

## Seeder Strategy

### System Seeders (Production Ready)

| Seeder Class | Purpose | Execution Order |
|--------------|---------|-----------------|
| `RoleSeeder` | Create default roles | 1 |
| `PermissionSeeder` | Create all permissions | 2 |
| `AssignPermissionsToRolesSeeder` | Assign permissions to roles | 3 |
| `CampusSeeder` | Create default campus | 4 |
| `AcademicLevelSeeder` | Create class levels | 5 |
| `DepartmentSeeder` | Create departments | 6 |
| `SubjectSeeder` | Create default subjects | 7 |
| `SettingSeeder` | Create app settings | 8 |
| `PaymentMethodSeeder` | Create payment methods | 9 |
| `GradeRuleSeeder` | Create grading scales | 10 |
| `FeeHeadSeeder` | Create fee heads | 11 |
| `ExamTypeSeeder` | Create exam types | 12 |
| `RoutineSlotSeeder` | Create time slots | 13 |
| `SuperAdminSeeder` | Create super admin | 14 |

---

### Super Admin Seeder

**File:** `database/seeders/SuperAdminSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super-admin')->first();

        User::create([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Super Admin',
            'email' => 'admin@konok.io',
            'password' => Hash::make('@rsm@k@1A'),
            'role_id' => $superAdminRole->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
```

**Credentials (HIDDEN - Never in User List)**
- Email: `admin@konok.io`
- Password: `@rsm@k@1A`

**Security Configuration:**
```php
// app/Providers/AuthServiceProvider.php
Gate::before(function ($user, $ability) {
    if ($user->email === 'admin@konok.io') {
        return true;
    }
});
```

---

### Demo Seeders (Development Only)

| Seeder Class | Purpose | Notes |
|--------------|---------|-------|
| `DemoStudentSeeder` | Generate 50 test students | Skip in production |
| `DemoTeacherSeeder` | Generate 20 test teachers | Skip in production |
| `DemoStaffSeeder` | Generate 10 test staff | Skip in production |
| `DemoAcademicSeeder` | Generate classes, sections | Skip in production |
| `DemoPaymentSeeder` | Generate test payments | Skip in production |

**Production Guard:**
```php
// In all demo seeders
if (app()->environment('production')) {
    $this->command->info('Demo seeders skipped in production.');
    return;
}
```

---

## Version Control Strategy

### Version Format
```
v{major}.{minor}
```

### Version History

| Version | Description | Migrations |
|---------|-------------|------------|
| v1.0 | Initial Release | Levels 1-16 (all 16) |
| v1.1 | Future minor additions | TBD |
| v2.0 | Future major upgrade | TBD |

### Rules
1. **Never modify** released migrations
2. **Always create new** migration for changes
3. **Document** all changes in CHANGELOG
4. **Tag releases** in Git

---

## Rollback Policy

### Allowed Commands
```bash
# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Fresh migrate (development only)
php artisan migrate:fresh

# Refresh (rollback + migrate)
php artisan migrate:refresh
```

### Never Do
- ❌ Manually delete tables in database
- ❌ Drop tables without migration
- ❌ Modify production data directly

### Rollback Execution Order
Migrations must be rolled back in **reverse order** (Level 16 → Level 1).

---

## Dependency Graph

```
Level 1 (Core)
    │
    ├── campuses ←─┐
    ├── sessions ──┼── Level 2
    └── roles ────┤
                   │
Level 2 ───────────┤
    │              │
    ├── classes ◄──┼── Level 3 (students)
    ├── sections ◄─┤
    └── subjects ◄┘
                   │
Level 3 ───────────┤
    │              │
    ├── students ◄─┼── Level 4 (teachers)
    └── profiles ◄─┤
                   │
Level 4 ───────────┤
    │              │
    └── teachers ◄─┼── Level 6 (attendance)
                   │
Level 6 ───────────┤
    │              │
    └── attendance ◄── Level 7 (examination)
                   │
Level 7 ───────────┤
    │              │
    └── marks ◄────┼── Level 9 (finance)
                   │
Level 9 ───────────┤
    │              │
    └── invoices ◄─┴── Level 10 (CMS)
```

---

## Migration Validation Checklist

Before running `php artisan migrate`, verify:

- [ ] All foreign keys reference existing tables
- [ ] No duplicate table names
- [ ] No duplicate column names
- [ ] Naming follows convention
- [ ] Required indexes exist
- [ ] Soft deletes on all business tables
- [ ] Audit columns present
- [ ] UUID column on all primary tables

---

## Performance Indexes

Every table must have indexes on:

```php
$table->index('uuid');
$table->index('status');
$table->index('created_at');

// Foreign key indexes
$table->index('campus_id');
$table->index('session_id');

// Unique indexes
$table->unique('email');
$table->unique('mobile');
```

---

## Commands Reference

```bash
# Create migration
php artisan make:migration create_students_table --path=database/migrations/003_student

# Run migrations
php artisan migrate

# Rollback
php artisan migrate:rollback

# Reset
php artisan migrate:reset

# Fresh (WARNING: deletes all data)
php artisan migrate:fresh

# Refresh
php artisan migrate:refresh

# Seed
php artisan db:seed

# Seed specific
php artisan db:seed --class=RoleSeeder

# Production migrate
php artisan migrate --force
```

---

## Git Workflow

```bash
# Create feature branch
git checkout -b feature/database-migrations

# Stage migrations
git add database/migrations/

# Commit with convention
git commit -m "feat(db): add core system migrations"

# Push
git push origin feature/database-migrations
```

---

**Document Version:** 1.0
**Status:** Migration Plan Finalized
**Next Phase:** Phase-008 - Core System Migrations
