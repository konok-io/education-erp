# Phase 011 - Enterprise Academic Master Data Management System

## Overview

This phase establishes the complete academic foundation for the Education ERP system. All subsequent modules (Student, Teacher, Result, Attendance) depend on this data structure.

---

## Academic Hierarchy

```
Institution
    ↓
Campus
    ↓
Academic Level (HSC, Honours, Degree)
    ↓
Faculty (Science, Arts, Commerce)
    ↓
Department (CS, Physics, Accounting)
    ↓
Program (HSC Science, BSc Honours)
    ↓
Session (2025-2026)
    ↓
Semester (1st Year, Semester 1)
    ↓
Class (XI Science, Honours 1st Year)
    ↓
Section (A, B, Morning)
    ↓
Group (Science, Commerce)
    ↓
Subject Category (Compulsory, Optional)
    ↓
Subject (Mathematics, Physics)
```

---

## Completed Tasks

### Models (13 models)

| Model | Description |
|-------|-------------|
| AcademicLevel | Education levels (HSC, Honours, Degree) |
| Faculty | Academic faculties |
| Department | Academic departments |
| Program | Academic programs |
| AcademicSession | Academic sessions/years |
| Semester | Program semesters/years |
| AcademicClass | Class sections |
| Section | Class sections (A, B) |
| Group | Student groups (Science, Commerce) |
| SubjectCategory | Subject categories |
| Subject | Academic subjects |
| ProgramSubject | Subject-program assignments |
| GradeRule | Grading rules |
| GpaRule | GPA calculation rules |
| AcademicCalendar | Academic events/holidays |

### Controller
- `AcademicController.php` - All academic CRUD operations

### Service
- `AcademicService.php` - Academic business logic

### API Routes
- Complete REST API for all academic entities

### React Structure
- `types/` - TypeScript types
- `services/` - API client
- `hooks/` - Custom hooks

---

## API Endpoints

### Academic Levels
- `GET /api/v1/academic/levels` - List
- `POST /api/v1/academic/levels` - Create
- `GET /api/v1/academic/levels/{uuid}` - Show
- `PUT /api/v1/academic/levels/{uuid}` - Update
- `DELETE /api/v1/academic/levels/{uuid}` - Delete

### Faculties
- `GET /api/v1/academic/faculties` - List
- `POST /api/v1/academic/faculties` - Create
- `GET /api/v1/academic/faculties/{uuid}` - Show
- `PUT /api/v1/academic/faculties/{uuid}` - Update
- `DELETE /api/v1/academic/faculties/{uuid}` - Delete

### Departments
- `GET /api/v1/academic/departments` - List
- `POST /api/v1/academic/departments` - Create
- `GET /api/v1/academic/departments/{uuid}` - Show
- `PUT /api/v1/academic/departments/{uuid}` - Update
- `DELETE /api/v1/academic/departments/{uuid}` - Delete

### Programs
- `GET /api/v1/academic/programs` - List
- `POST /api/v1/academic/programs` - Create
- `GET /api/v1/academic/programs/{uuid}` - Show
- `PUT /api/v1/academic/programs/{uuid}` - Update
- `DELETE /api/v1/academic/programs/{uuid}` - Delete

### Sessions
- `GET /api/v1/academic/sessions` - List
- `POST /api/v1/academic/sessions` - Create
- `GET /api/v1/academic/sessions/{uuid}` - Show
- `PUT /api/v1/academic/sessions/{uuid}` - Update
- `DELETE /api/v1/academic/sessions/{uuid}` - Delete
- `POST /api/v1/academic/sessions/{uuid}/set-current` - Set current

### Semesters
- `GET /api/v1/academic/semesters` - List
- `POST /api/v1/academic/semesters` - Create
- `GET /api/v1/academic/semesters/{uuid}` - Show
- `PUT /api/v1/academic/semesters/{uuid}` - Update
- `DELETE /api/v1/academic/semesters/{uuid}` - Delete

### Classes
- `GET /api/v1/academic/classes` - List
- `POST /api/v1/academic/classes` - Create
- `GET /api/v1/academic/classes/{uuid}` - Show
- `PUT /api/v1/academic/classes/{uuid}` - Update
- `DELETE /api/v1/academic/classes/{uuid}` - Delete

### Sections
- `GET /api/v1/academic/sections` - List
- `POST /api/v1/academic/sections` - Create
- `GET /api/v1/academic/sections/{uuid}` - Show
- `PUT /api/v1/academic/sections/{uuid}` - Update
- `DELETE /api/v1/academic/sections/{uuid}` - Delete

### Groups
- `GET /api/v1/academic/groups` - List
- `POST /api/v1/academic/groups` - Create
- `GET /api/v1/academic/groups/{uuid}` - Show
- `PUT /api/v1/academic/groups/{uuid}` - Update
- `DELETE /api/v1/academic/groups/{uuid}` - Delete

### Subjects
- `GET /api/v1/academic/subjects` - List
- `POST /api/v1/academic/subjects` - Create
- `GET /api/v1/academic/subjects/{uuid}` - Show
- `PUT /api/v1/academic/subjects/{uuid}` - Update
- `DELETE /api/v1/academic/subjects/{uuid}` - Delete

### Grade Rules
- `GET /api/v1/academic/grade-rules` - List
- `POST /api/v1/academic/grade-rules` - Create
- `GET /api/v1/academic/grade-rules/{uuid}` - Show
- `PUT /api/v1/academic/grade-rules/{uuid}` - Update
- `DELETE /api/v1/academic/grade-rules/{uuid}` - Delete

### Academic Calendar
- `GET /api/v1/academic/calendar` - List
- `POST /api/v1/academic/calendar` - Create
- `GET /api/v1/academic/calendar/{uuid}` - Show
- `PUT /api/v1/academic/calendar/{uuid}` - Update
- `DELETE /api/v1/academic/calendar/{uuid}` - Delete

### Lookups
- `GET /api/v1/academic/hierarchy` - Get full hierarchy
- `GET /api/v1/academic/programs/{uuid}/subjects` - Get program subjects
- `GET /api/v1/academic/sessions/{uuid}/classes` - Get session classes

---

## React Structure

```
frontend/src/features/academic/
├── types/
│   └── index.ts              # TypeScript types
├── services/
│   └── academicApi.ts        # API client
├── hooks/
│   └── useAcademic.ts        # Custom hooks
├── pages/                    # (Ready for pages)
└── components/               # (Ready for components)
```

---

## Permissions

| Permission | Description |
|------------|-------------|
| academic.view | View academic data |
| academic.create | Create academic data |
| academic.update | Update academic data |
| academic.delete | Delete academic data |
| academic.import | Import academic data |
| academic.export | Export academic data |
| academic.settings | Configure academic settings |

---

## Validation Rules

### Academic Level
- name: required, unique
- code: required, unique
- sort_order: numeric

### Session
- title: required, unique
- start_date: date, before end_date
- end_date: date, after start_date
- Only one session can be current

### Subject
- subject_code: required, unique
- subject_name: required
- credit: numeric, positive
- full_marks: numeric, positive

---

## Grade Rules Example

| Grade | Point | Min % | Max % |
|-------|-------|-------|-------|
| A+ | 5.00 | 80 | 100 |
| A | 4.00 | 70 | 79 |
| A- | 3.50 | 60 | 69 |
| B | 3.00 | 50 | 59 |
| C | 2.00 | 40 | 49 |
| D | 1.00 | 33 | 39 |
| F | 0.00 | 0 | 32 |

---

## Next Phase

**Phase 012 - Enterprise Student Management System**

- Student Registration
- Student Profile
- Guardian Information
- Document Management
- Medical Information
- Academic Assignment
- Student Promotion
- Student Transfer
- Student Status Management

---

## Status

✅ Phase 011 Complete
