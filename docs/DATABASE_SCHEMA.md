# Education ERP Database Schema

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

## Core Conventions

### Table Naming
- Plural nouns
- snake_case
- Examples: `users`, `student_attendances`, `fee_payments`

### Column Naming
- snake_case
- Examples: `first_name`, `created_at`, `email_verified_at`

### Primary Key
- Column name: `id` (auto-increment for internal use)
- Public identifier: `uuid` (UUID v4)

### Foreign Key
- Pattern: `{table_singular}_id`
- Examples: `student_id`, `teacher_id`, `session_id`

### Index Naming
- Pattern: `idx_{table}_{column}`
- Examples: `idx_users_email`, `idx_students_uuid`

---

## Audit Columns (All Tables)

Every table includes:

```sql
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
uuid CHAR(36) UNIQUE NOT NULL
created_at TIMESTAMP NULL
updated_at TIMESTAMP NULL
deleted_at TIMESTAMP NULL  -- Soft delete
created_by CHAR(36) NULL
updated_by CHAR(36) NULL
```

---

## TABLE 1: campuses

Multi-campus support.

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| name | VARCHAR(255) | NOT NULL |
| code | VARCHAR(50) | UNIQUE, NOT NULL |
| address | TEXT | NULL |
| phone | VARCHAR(20) | NULL |
| email | VARCHAR(255) | NULL |
| logo | VARCHAR(255) | NULL |
| status | ENUM('active','inactive') | DEFAULT 'active' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

**Indexes:** `idx_campuses_status`, `idx_campuses_code`

---

## TABLE 2: academic_sessions

Academic year management.

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| name | VARCHAR(100) | NOT NULL |
| start_date | DATE | NOT NULL |
| end_date | DATE | NOT NULL |
| is_current | BOOLEAN | DEFAULT FALSE |
| status | ENUM('pending','active','closed') | DEFAULT 'pending' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

**Indexes:** `idx_sessions_is_current`, `idx_sessions_status`

---

## TABLE 3: users

Authentication and authorization.

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| campus_id | BIGINT UNSIGNED | FOREIGN KEY → campuses(id) |
| name | VARCHAR(255) | NOT NULL |
| email | VARCHAR(255) | UNIQUE, NOT NULL |
| mobile | VARCHAR(20) | UNIQUE, NULL |
| password | VARCHAR(255) | NOT NULL |
| role_id | BIGINT UNSIGNED | FOREIGN KEY → roles(id) |
| avatar | VARCHAR(255) | NULL |
| status | ENUM('active','inactive','suspended') | DEFAULT 'active' |
| email_verified_at | TIMESTAMP | NULL |
| last_login_at | TIMESTAMP | NULL |
| last_login_ip | VARCHAR(45) | NULL |
| remember_token | VARCHAR(100) | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |
| created_by | CHAR(36) | NULL |
| updated_by | CHAR(36) | NULL |

**Indexes:** `idx_users_email`, `idx_users_mobile`, `idx_users_campus_id`, `idx_users_role_id`

---

## TABLE 4: roles

Spatie Permission compatible.

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| name | VARCHAR(255) | UNIQUE, NOT NULL |
| guard_name | VARCHAR(255) | DEFAULT 'api' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

## TABLE 5: permissions

Spatie Permission compatible.

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| name | VARCHAR(255) | UNIQUE, NOT NULL |
| guard_name | VARCHAR(255) | DEFAULT 'api' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

---

## TABLE 6: role_permission

Pivot table for roles and permissions.

| Column | Type | Constraints |
|--------|------|-------------|
| role_id | BIGINT UNSIGNED | FOREIGN KEY → roles(id) |
| permission_id | BIGINT UNSIGNED | FOREIGN KEY → permissions(id) |

**Primary Key:** (role_id, permission_id)

---

## TABLE 7: model_has_roles

Spatie Permission compatible.

| Column | Type | Constraints |
|--------|------|-------------|
| role_id | BIGINT UNSIGNED | FOREIGN KEY → roles(id) |
| model_type | VARCHAR(255) | NOT NULL |
| model_id | BIGINT UNSIGNED | NOT NULL |

**Primary Key:** (role_id, model_id, model_type)

---

## TABLE 8: model_has_permissions

Spatie Permission compatible.

| Column | Type | Constraints |
|--------|------|-------------|
| permission_id | BIGINT UNSIGNED | FOREIGN KEY → permissions(id) |
| model_type | VARCHAR(255) | NOT NULL |
| model_id | BIGINT UNSIGNED | NOT NULL |

---

## STUDENT TABLES

### TABLE 9: students

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| campus_id | BIGINT UNSIGNED | FOREIGN KEY → campuses(id) |
| session_id | BIGINT UNSIGNED | FOREIGN KEY → academic_sessions(id) |
| admission_no | VARCHAR(50) | UNIQUE, NOT NULL |
| roll_no | VARCHAR(50) | NULL |
| first_name | VARCHAR(100) | NOT NULL |
| last_name | VARCHAR(100) | NULL |
| gender | ENUM('male','female','other') | NOT NULL |
| date_of_birth | DATE | NOT NULL |
| birth_place | VARCHAR(255) | NULL |
| religion | VARCHAR(50) | NULL |
| nationality | VARCHAR(100) | DEFAULT 'Bangladeshi' |
| blood_group | ENUM('A+','A-','B+','B-','O+','O-','AB+','AB-') | NULL |
| photo | VARCHAR(255) | NULL |
| status | ENUM('pending','approved','active','inactive','alumni','transferred') | DEFAULT 'pending' |
| admission_date | DATE | NOT NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |
| created_by | CHAR(36) | NULL |
| updated_by | CHAR(36) | NULL |

**Indexes:** `idx_students_admission_no`, `idx_students_roll_no`, `idx_students_session_id`, `idx_students_campus_id`, `idx_students_status`

---

### TABLE 10: student_profiles

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| student_id | BIGINT UNSIGNED | FOREIGN KEY → students(id) |
| father_name | VARCHAR(255) | NULL |
| father_occupation | VARCHAR(255) | NULL |
| father_mobile | VARCHAR(20) | NULL |
| father_email | VARCHAR(255) | NULL |
| father_annual_income | DECIMAL(12,2) | NULL |
| mother_name | VARCHAR(255) | NULL |
| mother_occupation | VARCHAR(255) | NULL |
| mother_mobile | VARCHAR(20) | NULL |
| mother_email | VARCHAR(255) | NULL |
| mother_annual_income | DECIMAL(12,2) | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

---

### TABLE 11: student_guardians

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| student_id | BIGINT UNSIGNED | FOREIGN KEY → students(id) |
| name | VARCHAR(255) | NOT NULL |
| relation | ENUM('father','mother','guardian','other') | NOT NULL |
| occupation | VARCHAR(255) | NULL |
| mobile | VARCHAR(20) | NOT NULL |
| email | VARCHAR(255) | NULL |
| nid | VARCHAR(50) | NULL |
| address | TEXT | NULL |
| is_primary | BOOLEAN | DEFAULT FALSE |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

**Indexes:** `idx_guardians_student_id`, `idx_guardians_mobile`

---

### TABLE 12: student_addresses

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| student_id | BIGINT UNSIGNED | FOREIGN KEY → students(id) |
| type | ENUM('present','permanent') | NOT NULL |
| address | TEXT | NOT NULL |
| district | VARCHAR(100) | NULL |
| division | VARCHAR(100) | NULL |
| country | VARCHAR(100) | DEFAULT 'Bangladesh' |
| postal_code | VARCHAR(20) | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

---

### TABLE 13: student_documents

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| student_id | BIGINT UNSIGNED | FOREIGN KEY → students(id) |
| type | VARCHAR(100) | NOT NULL |
| title | VARCHAR(255) | NOT NULL |
| file_path | VARCHAR(255) | NOT NULL |
| file_type | VARCHAR(50) | NULL |
| file_size | BIGINT | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

**Indexes:** `idx_student_documents_student_id`

---

### TABLE 14: student_sessions

Student enrollment in academic sessions.

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| student_id | BIGINT UNSIGNED | FOREIGN KEY → students(id) |
| session_id | BIGINT UNSIGNED | FOREIGN KEY → academic_sessions(id) |
| class_id | BIGINT UNSIGNED | FOREIGN KEY → classes(id) |
| section_id | BIGINT UNSIGNED | FOREIGN KEY → sections(id) |
| roll_no | VARCHAR(50) | NULL |
| status | ENUM('active','passed','failed','transferred') | DEFAULT 'active' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

**Indexes:** `idx_student_sessions_student_id`, `idx_student_sessions_session_id`

---

## TEACHER TABLES

### TABLE 15: teachers

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| campus_id | BIGINT UNSIGNED | FOREIGN KEY → campuses(id) |
| user_id | BIGINT UNSIGNED | FOREIGN KEY → users(id) |
| employee_id | VARCHAR(50) | UNIQUE, NOT NULL |
| designation | VARCHAR(100) | NOT NULL |
| department_id | BIGINT UNSIGNED | FOREIGN KEY → departments(id) |
| first_name | VARCHAR(100) | NOT NULL |
| last_name | VARCHAR(100) | NULL |
| gender | ENUM('male','female','other') | NOT NULL |
| date_of_birth | DATE | NOT NULL |
| joining_date | DATE | NOT NULL |
| religion | VARCHAR(50) | NULL |
| nationality | VARCHAR(100) | DEFAULT 'Bangladeshi' |
| blood_group | ENUM('A+','A-','B+','B-','O+','O-','AB+','AB-') | NULL |
| photo | VARCHAR(255) | NULL |
| status | ENUM('active','inactive','on_leave','retired','resigned') | DEFAULT 'active' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |
| created_by | CHAR(36) | NULL |
| updated_by | CHAR(36) | NULL |

**Indexes:** `idx_teachers_employee_id`, `idx_teachers_user_id`, `idx_teachers_campus_id`

---

### TABLE 16: teacher_documents

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| teacher_id | BIGINT UNSIGNED | FOREIGN KEY → teachers(id) |
| type | VARCHAR(100) | NOT NULL |
| title | VARCHAR(255) | NOT NULL |
| file_path | VARCHAR(255) | NOT NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

## STAFF TABLES

### TABLE 17: staffs

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| campus_id | BIGINT UNSIGNED | FOREIGN KEY → campuses(id) |
| user_id | BIGINT UNSIGNED | FOREIGN KEY → users(id) |
| employee_id | VARCHAR(50) | UNIQUE, NOT NULL |
| designation | VARCHAR(100) | NOT NULL |
| department_id | BIGINT UNSIGNED | FOREIGN KEY → departments(id) |
| first_name | VARCHAR(100) | NOT NULL |
| last_name | VARCHAR(100) | NULL |
| gender | ENUM('male','female','other') | NOT NULL |
| date_of_birth | DATE | NOT NULL |
| joining_date | DATE | NOT NULL |
| photo | VARCHAR(255) | NULL |
| status | ENUM('active','inactive','on_leave','retired') | DEFAULT 'active' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

## ACADEMIC TABLES

### TABLE 18: academic_levels

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| name | VARCHAR(100) | NOT NULL |
| code | VARCHAR(50) | NOT NULL |
| level | TINYINT | NOT NULL |
| description | TEXT | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 19: departments

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| name | VARCHAR(255) | NOT NULL |
| code | VARCHAR(50) | NOT NULL |
| type | ENUM('academic','administrative','support') | DEFAULT 'academic' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 20: classes

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| campus_id | BIGINT UNSIGNED | FOREIGN KEY → campuses(id) |
| academic_level_id | BIGINT UNSIGNED | FOREIGN KEY → academic_levels(id) |
| name | VARCHAR(100) | NOT NULL |
| code | VARCHAR(50) | NOT NULL |
| class_number | TINYINT | NOT NULL |
| description | TEXT | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

**Indexes:** `idx_classes_campus_id`, `idx_classes_academic_level_id`

---

### TABLE 21: sections

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| class_id | BIGINT UNSIGNED | FOREIGN KEY → classes(id) |
| name | VARCHAR(100) | NOT NULL |
| code | VARCHAR(50) | NOT NULL |
| capacity | INT | DEFAULT 40 |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

**Indexes:** `idx_sections_class_id`

---

### TABLE 22: subjects

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| campus_id | BIGINT UNSIGNED | FOREIGN KEY → campuses(id) |
| name | VARCHAR(255) | NOT NULL |
| code | VARCHAR(50) | NOT NULL |
| type | ENUM('theory','practical','both') | DEFAULT 'theory' |
| credit_hours | DECIMAL(3,1) | DEFAULT 0 |
| full_mark | DECIMAL(5,2) | NOT NULL |
| pass_mark | DECIMAL(5,2) | NOT NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 23: class_subjects

Subject assignments to classes.

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| class_id | BIGINT UNSIGNED | FOREIGN KEY → classes(id) |
| subject_id | BIGINT UNSIGNED | FOREIGN KEY → subjects(id) |
| teacher_id | BIGINT UNSIGNED | FOREIGN KEY → teachers(id) |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

**Indexes:** `idx_class_subjects_class_id`, `idx_class_subjects_subject_id`

---

## ATTENDANCE TABLES

### TABLE 24: attendance_sessions

Daily attendance session configuration.

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| session_id | BIGINT UNSIGNED | FOREIGN KEY → academic_sessions(id) |
| campus_id | BIGINT UNSIGNED | FOREIGN KEY → campuses(id) |
| date | DATE | NOT NULL |
| type | ENUM('student','teacher','staff') | NOT NULL |
| status | ENUM('open','closed') | DEFAULT 'open' |
| created_by | CHAR(36) | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

**Indexes:** `idx_attendance_sessions_date`, `idx_attendance_sessions_session_id`

---

### TABLE 25: student_attendance

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| attendance_session_id | BIGINT UNSIGNED | FOREIGN KEY → attendance_sessions(id) |
| student_id | BIGINT UNSIGNED | FOREIGN KEY → students(id) |
| class_id | BIGINT UNSIGNED | FOREIGN KEY → classes(id) |
| section_id | BIGINT UNSIGNED | FOREIGN KEY → sections(id) |
| status | ENUM('present','absent','late','excused') | NOT NULL |
| remarks | TEXT | NULL |
| marked_by | CHAR(36) | NULL |
| marked_at | TIMESTAMP | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

**Indexes:** `idx_student_attendance_session_id`, `idx_student_attendance_student_id`, `idx_student_attendance_date`

---

## RESULT TABLES

### TABLE 26: exam_types

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| name | VARCHAR(255) | NOT NULL |
| code | VARCHAR(50) | NOT NULL |
| type | ENUM('written','mcq','practical','assignment') | DEFAULT 'written' |
| weight | DECIMAL(5,2) | DEFAULT 0 |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 27: exams

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| session_id | BIGINT UNSIGNED | FOREIGN KEY → academic_sessions(id) |
| campus_id | BIGINT UNSIGNED | FOREIGN KEY → campuses(id) |
| exam_type_id | BIGINT UNSIGNED | FOREIGN KEY → exam_types(id) |
| name | VARCHAR(255) | NOT NULL |
| start_date | DATE | NOT NULL |
| end_date | DATE | NOT NULL |
| status | ENUM('draft','published','completed') | DEFAULT 'draft' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 28: marks

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| exam_id | BIGINT UNSIGNED | FOREIGN KEY → exams(id) |
| student_id | BIGINT UNSIGNED | FOREIGN KEY → students(id) |
| subject_id | BIGINT UNSIGNED | FOREIGN KEY → subjects(id) |
| class_id | BIGINT UNSIGNED | FOREIGN KEY → classes(id) |
| section_id | BIGINT UNSIGNED | FOREIGN KEY → sections(id) |
| written_mark | DECIMAL(5,2) | NULL |
| mcq_mark | DECIMAL(5,2) | NULL |
| practical_mark | DECIMAL(5,2) | NULL |
| assignment_mark | DECIMAL(5,2) | NULL |
| total_mark | DECIMAL(5,2) | NOT NULL |
| grade | VARCHAR(5) | NULL |
| point | DECIMAL(3,2) | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

**Indexes:** `idx_marks_exam_id`, `idx_marks_student_id`, `idx_marks_subject_id`

---

### TABLE 29: grade_rules

Grading scale configuration.

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| campus_id | BIGINT UNSIGNED | FOREIGN KEY → campuses(id) |
| name | VARCHAR(100) | NOT NULL |
| min_mark | DECIMAL(5,2) | NOT NULL |
| max_mark | DECIMAL(5,2) | NOT NULL |
| grade | VARCHAR(5) | NOT NULL |
| point | DECIMAL(3,2) | NOT NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

## ROUTINE TABLES

### TABLE 30: routine_slots

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| campus_id | BIGINT UNSIGNED | FOREIGN KEY → campuses(id) |
| name | VARCHAR(100) | NOT NULL |
| start_time | TIME | NOT NULL |
| end_time | TIME | NOT NULL |
| duration_minutes | INT | NOT NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

---

### TABLE 31: class_routines

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| session_id | BIGINT UNSIGNED | FOREIGN KEY → academic_sessions(id) |
| class_id | BIGINT UNSIGNED | FOREIGN KEY → classes(id) |
| section_id | BIGINT UNSIGNED | FOREIGN KEY → sections(id) |
| subject_id | BIGINT UNSIGNED | FOREIGN KEY → subjects(id) |
| teacher_id | BIGINT UNSIGNED | FOREIGN KEY → teachers(id) |
| day_of_week | TINYINT | NOT NULL |
| slot_id | BIGINT UNSIGNED | FOREIGN KEY → routine_slots(id) |
| room_no | VARCHAR(50) | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

## PAYMENT TABLES

### TABLE 32: fee_heads

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| campus_id | BIGINT UNSIGNED | FOREIGN KEY → campuses(id) |
| name | VARCHAR(255) | NOT NULL |
| code | VARCHAR(50) | NOT NULL |
| type | ENUM('mandatory','optional') | DEFAULT 'mandatory' |
| amount | DECIMAL(10,2) | NOT NULL |
| description | TEXT | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 33: student_fees

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| student_id | BIGINT UNSIGNED | FOREIGN KEY → students(id) |
| fee_head_id | BIGINT UNSIGNED | FOREIGN KEY → fee_heads(id) |
| session_id | BIGINT UNSIGNED | FOREIGN KEY → academic_sessions(id) |
| class_id | BIGINT UNSIGNED | FOREIGN KEY → classes(id) |
| amount | DECIMAL(10,2) | NOT NULL |
| waiver_amount | DECIMAL(10,2) | DEFAULT 0 |
| fine_amount | DECIMAL(10,2) | DEFAULT 0 |
| due_amount | DECIMAL(10,2) | NOT NULL |
| due_date | DATE | NULL |
| status | ENUM('pending','partial','paid','waiver') | DEFAULT 'pending' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 34: invoices

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| invoice_no | VARCHAR(50) | UNIQUE, NOT NULL |
| student_id | BIGINT UNSIGNED | FOREIGN KEY → students(id) |
| session_id | BIGINT UNSIGNED | FOREIGN KEY → academic_sessions(id) |
| subtotal | DECIMAL(12,2) | NOT NULL |
| discount | DECIMAL(10,2) | DEFAULT 0 |
| total | DECIMAL(12,2) | NOT NULL |
| paid_amount | DECIMAL(12,2) | DEFAULT 0 |
| due_amount | DECIMAL(12,2) | NOT NULL |
| status | ENUM('draft','issued','partial','paid','cancelled') | DEFAULT 'draft' |
| issue_date | DATE | NOT NULL |
| due_date | DATE | NOT NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

**Indexes:** `idx_invoices_invoice_no`, `idx_invoices_student_id`, `idx_invoices_status`

---

### TABLE 35: invoice_items

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| invoice_id | BIGINT UNSIGNED | FOREIGN KEY → invoices(id) |
| fee_head_id | BIGINT UNSIGNED | FOREIGN KEY → fee_heads(id) |
| description | VARCHAR(255) | NULL |
| amount | DECIMAL(10,2) | NOT NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

---

### TABLE 36: payments

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| invoice_id | BIGINT UNSIGNED | FOREIGN KEY → invoices(id) |
| student_id | BIGINT UNSIGNED | FOREIGN KEY → students(id) |
| amount | DECIMAL(10,2) | NOT NULL |
| payment_method | ENUM('cash','bank','mobile_banking','card','check') | DEFAULT 'cash' |
| transaction_id | VARCHAR(100) | NULL |
| bank_name | VARCHAR(100) | NULL |
| check_no | VARCHAR(50) | NULL |
| payment_date | DATE | NOT NULL |
| remarks | TEXT | NULL |
| created_by | CHAR(36) | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 37: payment_methods

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| name | VARCHAR(255) | NOT NULL |
| code | VARCHAR(50) | NOT NULL |
| status | ENUM('active','inactive') | DEFAULT 'active' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

## CMS TABLES

### TABLE 38: pages

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| title | VARCHAR(255) | NOT NULL |
| slug | VARCHAR(255) | UNIQUE, NOT NULL |
| content | LONGTEXT | NULL |
| meta_title | VARCHAR(255) | NULL |
| meta_description | TEXT | NULL |
| status | ENUM('draft','published') | DEFAULT 'draft' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 39: menus

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| name | VARCHAR(255) | NOT NULL |
| location | ENUM('header','footer','sidebar') | DEFAULT 'header' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 40: menu_items

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| menu_id | BIGINT UNSIGNED | FOREIGN KEY → menus(id) |
| parent_id | BIGINT UNSIGNED | NULL |
| title | VARCHAR(255) | NOT NULL |
| url | VARCHAR(500) | NULL |
| page_id | BIGINT UNSIGNED | NULL |
| target | ENUM('_self','_blank') | DEFAULT '_self' |
| sort_order | INT | DEFAULT 0 |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 41: notices

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| title | VARCHAR(255) | NOT NULL |
| content | TEXT | NOT NULL |
| type | ENUM('general','academic','event','holiday','urgent') | DEFAULT 'general' |
| publish_date | DATE | NOT NULL |
| expiry_date | DATE | NULL |
| status | ENUM('draft','published') | DEFAULT 'draft' |
| created_by | CHAR(36) | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 42: gallery_albums

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| title | VARCHAR(255) | NOT NULL |
| slug | VARCHAR(255) | UNIQUE, NOT NULL |
| description | TEXT | NULL |
| cover_image | VARCHAR(255) | NULL |
| event_date | DATE | NULL |
| status | ENUM('draft','published') | DEFAULT 'draft' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 43: gallery_images

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| album_id | BIGINT UNSIGNED | FOREIGN KEY → gallery_albums(id) |
| title | VARCHAR(255) | NULL |
| image_path | VARCHAR(255) | NOT NULL |
| caption | TEXT | NULL |
| sort_order | INT | DEFAULT 0 |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 44: settings

JSON-based application settings.

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| key | VARCHAR(255) | UNIQUE, NOT NULL |
| value | JSON | NULL |
| type | ENUM('string','number','boolean','json') | DEFAULT 'string' |
| group | VARCHAR(100) | DEFAULT 'general' |
| description | TEXT | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

---

## LIBRARY TABLES

### TABLE 45: book_categories

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| name | VARCHAR(255) | NOT NULL |
| code | VARCHAR(50) | NOT NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 46: authors

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| name | VARCHAR(255) | NOT NULL |
| email | VARCHAR(255) | NULL |
| phone | VARCHAR(20) | NULL |
| address | TEXT | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 47: publishers

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| name | VARCHAR(255) | NOT NULL |
| email | VARCHAR(255) | NULL |
| phone | VARCHAR(20) | NULL |
| address | TEXT | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 48: books

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| isbn | VARCHAR(50) | UNIQUE, NULL |
| title | VARCHAR(255) | NOT NULL |
| author_id | BIGINT UNSIGNED | FOREIGN KEY → authors(id) |
| publisher_id | BIGINT UNSIGNED | FOREIGN KEY → publishers(id) |
| category_id | BIGINT UNSIGNED | FOREIGN KEY → book_categories(id) |
| edition | VARCHAR(50) | NULL |
| publish_year | YEAR | NULL |
| pages | INT | NULL |
| price | DECIMAL(10,2) | NULL |
| rack_no | VARCHAR(50) | NULL |
| quantity | INT | DEFAULT 0 |
| available | INT | DEFAULT 0 |
| description | TEXT | NULL |
| cover_image | VARCHAR(255) | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

**Indexes:** `idx_books_isbn`, `idx_books_title`

---

### TABLE 49: book_issues

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| book_id | BIGINT UNSIGNED | FOREIGN KEY → books(id) |
| student_id | BIGINT UNSIGNED | FOREIGN KEY → students(id) |
| issue_date | DATE | NOT NULL |
| due_date | DATE | NOT NULL |
| return_date | DATE | NULL |
| status | ENUM('issued','returned','overdue','lost') | DEFAULT 'issued' |
| fine_amount | DECIMAL(10,2) | DEFAULT 0 |
| remarks | TEXT | NULL |
| issued_by | CHAR(36) | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

## HOSTEL TABLES

### TABLE 50: hostels

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| campus_id | BIGINT UNSIGNED | FOREIGN KEY → campuses(id) |
| name | VARCHAR(255) | NOT NULL |
| type | ENUM('male','female','mixed') | NOT NULL |
| address | TEXT | NULL |
| warden_name | VARCHAR(255) | NULL |
| warden_contact | VARCHAR(20) | NULL |
| total_rooms | INT | DEFAULT 0 |
| status | ENUM('active','inactive') | DEFAULT 'active' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 51: rooms

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| hostel_id | BIGINT UNSIGNED | FOREIGN KEY → hostels(id) |
| room_no | VARCHAR(50) | NOT NULL |
| floor | INT | DEFAULT 1 |
| total_beds | INT | DEFAULT 4 |
| rent_amount | DECIMAL(10,2) | NOT NULL |
| status | ENUM('available','full','maintenance') | DEFAULT 'available' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 52: hostel_allocations

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| student_id | BIGINT UNSIGNED | FOREIGN KEY → students(id) |
| room_id | BIGINT UNSIGNED | FOREIGN KEY → rooms(id) |
| session_id | BIGINT UNSIGNED | FOREIGN KEY → academic_sessions(id) |
| bed_no | VARCHAR(10) | NULL |
| start_date | DATE | NOT NULL |
| end_date | DATE | NULL |
| status | ENUM('active','left','expelled') | DEFAULT 'active' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

## TRANSPORT TABLES

### TABLE 53: vehicles

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| campus_id | BIGINT UNSIGNED | FOREIGN KEY → campuses(id) |
| vehicle_no | VARCHAR(50) | UNIQUE, NOT NULL |
| model | VARCHAR(100) | NULL |
| type | VARCHAR(100) | NULL |
| capacity | INT | NOT NULL |
| driver_name | VARCHAR(255) | NULL |
| driver_mobile | VARCHAR(20) | NULL |
| status | ENUM('active','maintenance','retired') | DEFAULT 'active' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 54: routes

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| campus_id | BIGINT UNSIGNED | FOREIGN KEY → campuses(id) |
| name | VARCHAR(255) | NOT NULL |
| vehicle_id | BIGINT UNSIGNED | FOREIGN KEY → vehicles(id) |
| fare_amount | DECIMAL(10,2) | NOT NULL |
| status | ENUM('active','inactive') | DEFAULT 'active' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 55: route_stoppages

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| route_id | BIGINT UNSIGNED | FOREIGN KEY → routes(id) |
| name | VARCHAR(255) | NOT NULL |
| address | TEXT | NULL |
| distance_km | DECIMAL(5,2) | NULL |
| pickup_time | TIME | NULL |
| drop_time | TIME | NULL |
| sort_order | INT | DEFAULT 0 |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

---

### TABLE 56: transport_allocations

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| student_id | BIGINT UNSIGNED | FOREIGN KEY → students(id) |
| route_id | BIGINT UNSIGNED | FOREIGN KEY → routes(id) |
| stoppage_id | BIGINT UNSIGNED | FOREIGN KEY → route_stoppages(id) |
| session_id | BIGINT UNSIGNED | FOREIGN KEY → academic_sessions(id) |
| status | ENUM('active','inactive') | DEFAULT 'active' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

## CERTIFICATE TABLES

### TABLE 57: certificate_templates

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| name | VARCHAR(255) | NOT NULL |
| type | ENUM('certificate','id_card','tc','bonafide','character') | NOT NULL |
| template_path | VARCHAR(255) | NOT NULL |
| variables | JSON | NULL |
| status | ENUM('active','inactive') | DEFAULT 'active' |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

### TABLE 58: certificates

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| template_id | BIGINT UNSIGNED | FOREIGN KEY → certificate_templates(id) |
| student_id | BIGINT UNSIGNED | FOREIGN KEY → students(id) |
| certificate_no | VARCHAR(100) | UNIQUE, NOT NULL |
| issue_date | DATE | NOT NULL |
| data | JSON | NULL |
| file_path | VARCHAR(255) | NULL |
| status | ENUM('draft','issued','cancelled') | DEFAULT 'draft' |
| issued_by | CHAR(36) | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

---

## SYSTEM TABLES

### TABLE 59: activity_logs

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| user_id | BIGINT UNSIGNED | NULL |
| action | VARCHAR(100) | NOT NULL |
| model_type | VARCHAR(255) | NULL |
| model_id | BIGINT UNSIGNED | NULL |
| old_data | JSON | NULL |
| new_data | JSON | NULL |
| ip_address | VARCHAR(45) | NULL |
| user_agent | TEXT | NULL |
| device | VARCHAR(100) | NULL |
| browser | VARCHAR(100) | NULL |
| os | VARCHAR(100) | NULL |
| created_at | TIMESTAMP | NULL |

**Indexes:** `idx_activity_logs_user_id`, `idx_activity_logs_model`, `idx_activity_logs_action`, `idx_activity_logs_created_at`

---

### TABLE 60: notifications

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| uuid | CHAR(36) | UNIQUE, NOT NULL |
| user_id | BIGINT UNSIGNED | FOREIGN KEY → users(id) |
| type | VARCHAR(100) | NOT NULL |
| title | VARCHAR(255) | NOT NULL |
| body | TEXT | NULL |
| data | JSON | NULL |
| is_read | BOOLEAN | DEFAULT FALSE |
| read_at | TIMESTAMP | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |
| deleted_at | TIMESTAMP | NULL |

**Indexes:** `idx_notifications_user_id`, `idx_notifications_is_read`

---

### TABLE 61: personal_access_tokens

Laravel Sanctum tokens.

| Column | Type | Constraints |
|--------|------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| tokenable_type | VARCHAR(255) | NOT NULL |
| tokenable_id | BIGINT UNSIGNED | NOT NULL |
| name | VARCHAR(255) | NOT NULL |
| token | VARCHAR(64) | UNIQUE, NOT NULL |
| abilities | TEXT | NULL |
| last_used_at | TIMESTAMP | NULL |
| expires_at | TIMESTAMP | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

**Indexes:** `idx_tokens_tokenable`

---

## TABLE INDEX

| # | Table Name | Purpose |
|---|-----------|---------|
| 1 | campuses | Multi-campus support |
| 2 | academic_sessions | Academic year management |
| 3 | users | Authentication users |
| 4 | roles | User roles (Spatie) |
| 5 | permissions | Permissions (Spatie) |
| 6 | role_permission | Role-Permission pivot |
| 7 | model_has_roles | Model-Role pivot |
| 8 | model_has_permissions | Model-Permission pivot |
| 9 | students | Student records |
| 10 | student_profiles | Student family info |
| 11 | student_guardians | Guardian records |
| 12 | student_addresses | Student addresses |
| 13 | student_documents | Student documents |
| 14 | student_sessions | Session enrollment |
| 15 | teachers | Teacher records |
| 16 | teacher_documents | Teacher documents |
| 17 | staffs | Staff records |
| 18 | academic_levels | Class levels |
| 19 | departments | Departments |
| 20 | classes | Class definitions |
| 21 | sections | Section definitions |
| 22 | subjects | Subject definitions |
| 23 | class_subjects | Class-Subject mapping |
| 24 | attendance_sessions | Attendance sessions |
| 25 | student_attendance | Daily attendance |
| 26 | exam_types | Exam type definitions |
| 27 | exams | Exam schedules |
| 28 | marks | Student marks |
| 29 | grade_rules | Grading scales |
| 30 | routine_slots | Time slots |
| 31 | class_routines | Class schedules |
| 32 | fee_heads | Fee categories |
| 33 | student_fees | Assigned fees |
| 34 | invoices | Payment invoices |
| 35 | invoice_items | Invoice line items |
| 36 | payments | Payment records |
| 37 | payment_methods | Payment methods |
| 38 | pages | CMS pages |
| 39 | menus | Navigation menus |
| 40 | menu_items | Menu items |
| 41 | notices | Notice board |
| 42 | gallery_albums | Photo albums |
| 43 | gallery_images | Gallery photos |
| 44 | settings | Application settings |
| 45 | book_categories | Library categories |
| 46 | authors | Book authors |
| 47 | publishers | Book publishers |
| 48 | books | Book inventory |
| 49 | book_issues | Book lending |
| 50 | hostels | Hostel definitions |
| 51 | rooms | Room allocations |
| 52 | hostel_allocations | Student hostel |
| 53 | vehicles | Transport vehicles |
| 54 | routes | Transport routes |
| 55 | route_stoppages | Route stoppages |
| 56 | transport_allocations | Student transport |
| 57 | certificate_templates | Certificate templates |
| 58 | certificates | Issued certificates |
| 59 | activity_logs | Activity tracking |
| 60 | notifications | User notifications |
| 61 | personal_access_tokens | API tokens |

---

## Migration Execution Order

1. System tables (users, roles, permissions)
2. Core tables (campuses, sessions)
3. Academic tables
4. Student/Teacher/Staff tables
5. Attendance tables
6. Result tables
7. Payment tables
8. CMS tables
9. Library tables
10. Hostel tables
11. Transport tables
12. Certificate tables
13. System logs

---

## Foreign Key Cascade Rules

```php
// Update: Cascade on update
$table->foreignId('user_id')->constrained()->cascadeOnUpdate();

// Delete: Restrict by default (no cascade)
$table->foreignId('student_id')->constrained()->restrictOnDelete();
```

---

## Soft Delete Implementation

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
}
```

---

**Document Version:** 1.0
**Last Updated:** Phase 006
**Status:** Finalized
