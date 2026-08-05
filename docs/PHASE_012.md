# Phase 012 - Enterprise Student Management System

## Overview

This phase implements the complete Enterprise Student Management System for the Education ERP. This is the most critical module as all other modules (Attendance, Result, Payment, etc.) depend on it.

---

## Student Architecture

```
Student
    ↓
Profile (first_name, last_name, gender, DOB, etc.)
    ↓
Guardian (father, mother, guardian)
    ↓
Academic (session, program, class, section, group)
    ↓
Medical (height, weight, allergies, diseases)
    ↓
Documents (birth certificate, NID, certificates)
    ↓
Promotion (history, records)
    ↓
Transfer (campus, department, class transfers)
```

---

## Completed Tasks

### Models (6 models)

| Model | Description |
|-------|-------------|
| Student | Main student record with academic assignment |
| StudentProfile | Personal information, address |
| Guardian | Guardian/father/mother information |
| StudentMedical | Health information |
| StudentDocument | Uploaded documents |
| StudentPromotion | Promotion history |
| StudentTransfer | Transfer history |

### Controller
- `StudentController.php` - Complete CRUD and operations

### Service
- `StudentService.php` - All business logic

### API Routes
- Complete REST API (25+ endpoints)

### React Structure
- `types/` - TypeScript interfaces
- `services/` - API client

---

## API Endpoints

### CRUD Operations
- `GET /api/v1/students` - List students
- `POST /api/v1/students` - Register student
- `GET /api/v1/students/{uuid}` - View student
- `PUT /api/v1/students/{uuid}` - Update student
- `DELETE /api/v1/students/{uuid}` - Delete student
- `GET /api/v1/students/search` - Search students
- `GET /api/v1/students/by-number/{no}` - Find by student number

### Profile
- `POST /api/v1/students/{uuid}/profile` - Update profile
- `POST /api/v1/students/{uuid}/photo` - Upload photo

### Guardian
- `POST /api/v1/students/{uuid}/guardian` - Update guardian

### Medical
- `POST /api/v1/students/{uuid}/medical` - Update medical info

### Documents
- `GET /api/v1/students/{uuid}/documents` - List documents
- `POST /api/v1/students/{uuid}/documents` - Upload document
- `DELETE /api/v1/students/{uuid}/documents/{docUuid}` - Delete document

### Status
- `POST /api/v1/students/{uuid}/status` - Update status

### Promotion
- `POST /api/v1/students/{uuid}/promote` - Promote student
- `GET /api/v1/students/{uuid}/promotions` - Promotion history

### Transfer
- `POST /api/v1/students/{uuid}/transfer` - Transfer student
- `GET /api/v1/students/{uuid}/transfers` - Transfer history

### QR Code
- `GET /api/v1/students/{uuid}/qr-code` - Generate QR code

### Import/Export
- `POST /api/v1/students/import` - Import students
- `GET /api/v1/students/export` - Export students

### Statistics
- `GET /api/v1/students/statistics` - Get statistics
- `GET /api/v1/students/active-count` - Active student count

---

## Student Status

| Status | Description | Can Attend Classes |
|--------|-------------|-------------------|
| pending | Awaiting approval | ❌ |
| active | Active student | ✅ |
| inactive | Temporarily inactive | ❌ |
| transferred | Transferred out | ❌ |
| graduated | Graduated | ❌ |
| suspended | Suspended | ❌ |
| expelled | Expelled | ❌ |
| dropped | Dropped out | ❌ |
| alumni | Alumni | ❌ |

---

## Student Number Format

```
ST-2026-000001
│  │      │
│  │      └── Sequential number
│  └─────── Year
└────────── Prefix
```

---

## React Structure

```
frontend/src/features/students/
├── types/
│   └── index.ts              # TypeScript types
├── services/
│   └── studentApi.ts         # API client
├── hooks/                    # (Ready for hooks)
├── pages/                    # (Ready for pages)
└── components/              # (Ready for components)
```

---

## Key Features

### Auto-Generated Student Number
```php
Student::generateStudentNo($sessionCode)
// Output: ST-2026-000001
```

### QR Code Generation
Contains: UUID, Student Number, Name

### Photo Upload
- Formats: jpg, jpeg, png, webp
- Auto resize: 300x300
- Max size: 2MB

### Document Types
- Birth Certificate
- NID
- Passport
- SSC Certificate
- HSC Certificate
- Transfer Certificate
- Character Certificate
- Photo
- Signature
- Other

---

## Permissions

| Permission | Description |
|------------|-------------|
| student.view | View student list/details |
| student.create | Register new student |
| student.update | Edit student information |
| student.delete | Delete student |
| student.import | Import students |
| student.export | Export students |
| student.promote | Promote students |
| student.transfer | Transfer students |
| student.print | Print ID cards |
| student.qrcode | Generate QR codes |

---

## Document Storage

```
storage/app/public/students/
├── photos/
│   └── {uuid}.jpg
├── documents/
│   └── {student_uuid}/
│       ├── birth_certificate.pdf
│       ├── nid.pdf
│       └── ...
└── signatures/
    └── {uuid}.png
```

---

## Next Phase

**Phase 013 - Enterprise Teacher Management System**

- Teacher Registration
- Teacher Profile
- Academic Assignment
- Subject Assignment
- Class Assignment
- Department Assignment
- Workload Management
- Teacher Leave Management
- Teacher Documents
- Teacher Salary Profile
- Teacher QR Code
- Teacher ID Card

---

## Status

✅ Phase 012 Complete
