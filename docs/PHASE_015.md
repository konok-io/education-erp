# Phase 015 - Enterprise Attendance Management System

## Overview

This phase implements the complete Enterprise Attendance Management System for the Education ERP. This module handles attendance for students, teachers, and employees with multiple entry methods and comprehensive reporting.

---

## Attendance Architecture

```
Attendance
    ↓
Student / Teacher / Employee
    ↓
Session / Class / Section / Subject
    ↓
Entry Methods (Manual, QR, Barcode, RFID, GPS)
    ↓
Approval Workflow
    ↓
Reports & Analytics
```

---

## Completed Tasks

### Models (2 models)

| Model | Description |
|-------|-------------|
| Attendance | Core attendance record |
| AttendanceCorrection | Correction requests |

### Controller
- `AttendanceController.php` - Complete CRUD and operations

### Service
- `AttendanceService.php` - All business logic

### API Routes
- Complete REST API (20+ endpoints)

### React Structure
- `types/` - TypeScript interfaces
- `services/` - API client

---

## Attendance Types

| Type | Description |
|------|-------------|
| student | Student attendance |
| teacher | Teacher attendance |
| employee | Employee attendance |

---

## Attendance Status

| Status | Description |
|--------|-------------|
| present | Present |
| absent | Absent |
| late | Late arrival |
| leave | On leave |
| half_day | Half day |
| holiday | Holiday |
| weekend | Weekend |
| exam_duty | Exam duty |
| official_tour | Official tour |
| remote | Remote work |

---

## Entry Methods

| Method | Description |
|--------|-------------|
| manual | Manual entry |
| qr | QR Code |
| barcode | Barcode |
| rfid | RFID Card |
| fingerprint | Fingerprint |
| face | Face Recognition |
| gps | GPS Location |
| api | API Integration |
| mobile | Mobile App |

---

## API Endpoints

### List
- `GET /api/v1/attendance` - List all attendances

### Student Attendance
- `POST /api/v1/attendance/student` - Mark student attendance
- `GET /api/v1/attendance/student/{uuid}` - Get student attendance

### Teacher Attendance
- `POST /api/v1/attendance/teacher` - Mark teacher attendance
- `GET /api/v1/attendance/teacher/{uuid}` - Get teacher attendance

### Employee Attendance
- `POST /api/v1/attendance/employee` - Mark employee attendance
- `GET /api/v1/attendance/employee/{uuid}` - Get employee attendance

### QR Attendance
- `POST /api/v1/attendance/qr/verify` - Verify QR code
- `POST /api/v1/attendance/qr/mark` - Mark by QR code

### Approval
- `POST /api/v1/attendance/{uuid}/approve` - Approve attendance
- `POST /api/v1/attendance/approve/bulk` - Bulk approve

### Correction
- `POST /api/v1/attendance/correction` - Request correction
- `PUT /api/v1/attendance/correction/{uuid}` - Review correction
- `GET /api/v1/attendance/corrections` - List corrections

### Reports
- `GET /api/v1/attendance/report` - Get report
- `GET /api/v1/attendance/report/class-summary` - Class summary

### Analytics
- `GET /api/v1/attendance/analytics` - Get analytics
- `GET /api/v1/attendance/dashboard` - Dashboard stats

### Import/Export
- `POST /api/v1/attendance/import` - Import attendance
- `GET /api/v1/attendance/export` - Export attendance

---

## Key Features

✅ Student Attendance (Daily, Subject-wise)
✅ Teacher Attendance
✅ Employee Attendance
✅ QR Code Attendance
✅ Barcode Attendance Ready
✅ RFID Ready
✅ GPS Ready
✅ Face Recognition Ready
✅ Attendance Approval
✅ Attendance Correction
✅ Attendance Reports
✅ Attendance Analytics
✅ Dashboard Statistics
✅ Import/Export
✅ Soft delete
✅ UUID-based public API

---

## QR Code Data Structure

```json
{
  "uuid": "teacher-uuid",
  "id_number": "TR-2026-000001",
  "name": "John Doe",
  "type": "teacher"
}
```

---

## Attendance Correction Workflow

```
1. Request Correction
   ↓
2. Reason & New Status
   ↓
3. Review by Admin
   ↓
4. Approve/Reject
   ↓
5. Status Updated
```

---

## Dashboard Statistics

```json
{
  "student": { "total": 100, "present": 95, "absent": 3, "late": 2 },
  "teacher": { "total": 20, "present": 19, "absent": 0, "late": 1 },
  "employee": { "total": 15, "present": 14, "absent": 1, "late": 0 },
  "pending_approvals": 5,
  "pending_corrections": 2
}
```

---

## Permissions

| Permission | Description |
|------------|-------------|
| attendance.view | View attendance |
| attendance.create | Mark attendance |
| attendance.update | Update attendance |
| attendance.delete | Delete attendance |
| attendance.approve | Approve attendance |
| attendance.correct | Request/Review corrections |
| attendance.import | Import attendance |
| attendance.export | Export attendance |
| attendance.analytics | View analytics |
| attendance.report | Generate reports |

---

## React Structure

```
frontend/src/features/attendance/
├── types/
│   └── index.ts              # TypeScript types
├── services/
│   └── attendanceApi.ts     # API client
├── hooks/                    # (Ready for hooks)
├── pages/                    # (Ready for pages)
└── components/               # (Ready for components)
```

---

## Next Phase

**Phase 016 - Enterprise Result Processing & Examination System**

- Exam Management
- Exam Schedule
- Subject-wise Mark Entry
- GPA / CGPA Calculation
- Tabulation Sheet
- Merit List
- Transcript
- Marksheet

---

## Status

✅ Phase 015 Complete
