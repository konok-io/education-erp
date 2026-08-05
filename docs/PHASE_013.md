# Phase 013 - Enterprise Teacher Management System

## Overview

This phase implements the complete Enterprise Teacher Management System for the Education ERP. Teachers are critical users who can take attendance, enter results, and manage classes.

---

## Teacher Architecture

```
Teacher
    ↓
Profile (first_name, last_name, gender, DOB, etc.)
    ↓
Qualifications (SSC, HSC, Bachelor, Masters, MPhil, PhD)
    ↓
Experience (organization, designation, duration)
    ↓
Academic Assignment (department, program, class, section)
    ↓
Subject Assignment (subjects, programs, semesters)
    ↓
Salary (basic, allowances, deductions)
    ↓
Leave (casual, medical, earned, maternity)
    ↓
Documents (certificates, NID, CV)
```

---

## Completed Tasks

### Models (8 models)

| Model | Description |
|-------|-------------|
| Teacher | Main teacher record |
| TeacherProfile | Personal information |
| TeacherQualification | Education qualifications |
| TeacherExperience | Work experience |
| TeacherDocument | Uploaded documents |
| TeacherSubjectAssignment | Subject assignments |
| TeacherClassAssignment | Class assignments |
| TeacherSalary | Salary profile |
| TeacherLeave | Leave records |

### Controller
- `TeacherController.php` - Complete CRUD and operations

### Service
- `TeacherService.php` - All business logic

### API Routes
- Complete REST API (30+ endpoints)

### React Structure
- `types/` - TypeScript interfaces
- `services/` - API client

---

## API Endpoints

### CRUD Operations
- `GET /api/v1/teachers` - List teachers
- `POST /api/v1/teachers` - Register teacher
- `GET /api/v1/teachers/{uuid}` - View teacher
- `PUT /api/v1/teachers/{uuid}` - Update teacher
- `DELETE /api/v1/teachers/{uuid}` - Delete teacher
- `GET /api/v1/teachers/search` - Search teachers
- `GET /api/v1/teachers/by-number/{no}` - Find by teacher number

### Profile
- `POST /api/v1/teachers/{uuid}/profile` - Update profile
- `POST /api/v1/teachers/{uuid}/photo` - Upload photo

### Qualifications
- `POST /api/v1/teachers/{uuid}/qualifications` - Add qualification
- `PUT /api/v1/teachers/{uuid}/qualifications/{id}` - Update qualification
- `DELETE /api/v1/teachers/{uuid}/qualifications/{id}` - Delete qualification

### Experience
- `POST /api/v1/teachers/{uuid}/experiences` - Add experience
- `PUT /api/v1/teachers/{uuid}/experiences/{id}` - Update experience
- `DELETE /api/v1/teachers/{uuid}/experiences/{id}` - Delete experience

### Subject Assignment
- `GET /api/v1/teachers/{uuid}/subjects` - Get assigned subjects
- `POST /api/v1/teachers/{uuid}/subjects` - Assign subjects
- `DELETE /api/v1/teachers/{uuid}/subjects/{id}` - Remove subject

### Class Assignment
- `GET /api/v1/teachers/{uuid}/classes` - Get assigned classes
- `POST /api/v1/teachers/{uuid}/classes` - Assign classes
- `DELETE /api/v1/teachers/{uuid}/classes/{id}` - Remove class

### Salary
- `GET /api/v1/teachers/{uuid}/salary` - Get salary
- `POST /api/v1/teachers/{uuid}/salary` - Update salary

### Leave
- `GET /api/v1/teachers/{uuid}/leaves` - Get leave history
- `POST /api/v1/teachers/{uuid}/leaves` - Apply for leave

### QR Code
- `GET /api/v1/teachers/{uuid}/qr-code` - Generate QR code

### Import/Export
- `POST /api/v1/teachers/import` - Import teachers
- `GET /api/v1/teachers/export` - Export teachers

### Statistics
- `GET /api/v1/teachers/statistics` - Get statistics
- `GET /api/v1/teachers/active-count` - Active teacher count

---

## Teacher Status

| Status | Can Login | Can Take Attendance | Can Enter Result |
|--------|-----------|---------------------|------------------|
| pending | ❌ | ❌ | ❌ |
| active | ✅ | ✅ | ✅ |
| inactive | ❌ | ❌ | ❌ |
| on_leave | ❌ | ❌ | ❌ |
| suspended | ❌ | ❌ | ❌ |
| retired | ❌ | ❌ | ❌ |
| resigned | ❌ | ❌ | ❌ |
| terminated | ❌ | ❌ | ❌ |

---

## Employment Types

| Type | Description |
|------|-------------|
| permanent | Full-time permanent |
| contractual | Contract-based |
| part_time | Part-time |
| guest | Guest faculty |
| visiting | Visiting faculty |

---

## Leave Types

| Type | Description |
|------|-------------|
| casual | Casual leave |
| medical | Medical leave |
| earned | Earned leave |
| maternity | Maternity leave |
| special | Special leave |
| without_pay | Leave without pay |

---

## Teacher Number Format

```
TR-2026-000001
│  │      │
│  │      └── Sequential number
│  └─────── Year
└────────── Prefix
```

---

## Key Features

✅ Auto-generated teacher number (TR-2026-000001)
✅ Profile management
✅ Qualifications (SSC to PhD)
✅ Work experience tracking
✅ Subject assignment per session
✅ Class assignment per session
✅ Salary profile with auto-calculation
✅ Leave management
✅ Document upload
✅ QR code generation
✅ Search & filters
✅ Soft delete
✅ UUID-based public API

---

## Permissions

| Permission | Description |
|------------|-------------|
| teacher.view | View teacher list/details |
| teacher.create | Register new teacher |
| teacher.update | Edit teacher information |
| teacher.delete | Delete teacher |
| teacher.import | Import teachers |
| teacher.export | Export teachers |
| teacher.assign.subject | Assign subjects |
| teacher.assign.class | Assign classes |
| teacher.assign.department | Assign departments |
| teacher.salary.view | View salary |
| teacher.leave.manage | Manage leave |
| teacher.print.id | Print ID card |

---

## React Structure

```
frontend/src/features/teachers/
├── types/
│   └── index.ts              # TypeScript types
├── services/
│   └── teacherApi.ts         # API client
├── hooks/                    # (Ready for hooks)
├── pages/                    # (Ready for pages)
└── components/               # (Ready for components)
```

---

## Next Phase

**Phase 014 - Enterprise Employee & Staff Management System**

- Staff Registration
- Employee Profile
- Departments & Designations
- Salary Grade
- Shift Management
- Attendance Integration
- Leave Management
- Payroll Preparation

---

## Status

✅ Phase 013 Complete
