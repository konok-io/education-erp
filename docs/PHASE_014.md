# Phase 014 - Enterprise Employee & Staff Management System

## Overview

This phase implements the complete Enterprise Employee & Staff Management System for the Education ERP. This module manages all non-academic staff including administration, accounts, IT, library, security, and support staff.

---

## Employee Architecture

```
Employee
    ↓
Profile (first_name, last_name, gender, DOB, etc.)
    ↓
Department (Administration, Accounts, IT, Library)
    ↓
Designation (Principal, Accountant, Librarian)
    ↓
Employment Type (Permanent, Contract, Probation)
    ↓
Salary Grade
    ↓
Shift (Morning, Day, Evening, Night)
    ↓
Salary (basic, allowances, deductions)
    ↓
Leave (casual, medical, annual, maternity)
    ↓
Documents (NID, certificates, resume)
```

---

## Completed Tasks

### Models (11 models)

| Model | Description |
|-------|-------------|
| Employee | Main employee record |
| EmployeeProfile | Personal information |
| Designation | Job titles (Principal, Accountant, etc.) |
| EmploymentType | Employment types |
| SalaryGrade | Salary grades with allowances |
| Shift | Work shifts with timing |
| EmployeeDocument | Uploaded documents |
| EmployeeEmergencyContact | Emergency contacts |
| EmployeeLeave | Leave records |
| EmployeeSalary | Salary profile |

### Controller
- `EmployeeController.php` - Complete CRUD and operations

### Service
- `EmployeeService.php` - All business logic

### API Routes
- Complete REST API (25+ endpoints)

### React Structure
- `types/` - TypeScript interfaces
- `services/` - API client

---

## API Endpoints

### CRUD Operations
- `GET /api/v1/employees` - List employees
- `POST /api/v1/employees` - Register employee
- `GET /api/v1/employees/{uuid}` - View employee
- `PUT /api/v1/employees/{uuid}` - Update employee
- `DELETE /api/v1/employees/{uuid}` - Delete employee
- `GET /api/v1/employees/search` - Search employees

### Profile
- `POST /api/v1/employees/{uuid}/profile` - Update profile
- `POST /api/v1/employees/{uuid}/photo` - Upload photo

### Status
- `POST /api/v1/employees/{uuid}/status` - Update status

### Salary
- `POST /api/v1/employees/{uuid}/salary` - Update salary

### Leave
- `GET /api/v1/employees/{uuid}/leaves` - Get leave history
- `POST /api/v1/employees/{uuid}/leaves` - Apply for leave

### QR Code
- `GET /api/v1/employees/{uuid}/qr-code` - Generate QR code

### Import/Export
- `POST /api/v1/employees/import` - Import employees
- `GET /api/v1/employees/export` - Export employees

### Statistics
- `GET /api/v1/employees/statistics` - Get statistics
- `GET /api/v1/employees/active-count` - Active employee count

### Lookups
- `GET /api/v1/employees/lookups/departments` - Get departments
- `GET /api/v1/employees/lookups/designations` - Get designations
- `GET /api/v1/employees/lookups/shifts` - Get shifts
- `GET /api/v1/employees/lookups/salary-grades` - Get salary grades

---

## Employee Status

| Status | Can Login | Description |
|--------|-----------|-------------|
| pending | ❌ | Awaiting approval |
| active | ✅ | Active employee |
| inactive | ❌ | Temporarily inactive |
| on_leave | ❌ | On leave |
| suspended | ❌ | Suspended |
| retired | ❌ | Retired |
| resigned | ❌ | Resigned |
| terminated | ❌ | Terminated |

---

## Employment Types

| Type | Description |
|------|-------------|
| permanent | Full-time permanent |
| contract | Contract-based |
| probation | Probation period |
| temporary | Temporary |
| daily_basis | Daily wage |
| part_time | Part-time |

---

## Leave Types

| Type | Description |
|------|-------------|
| casual | Casual leave |
| medical | Medical leave |
| annual | Annual leave |
| maternity | Maternity leave |
| paternity | Paternity leave |
| special | Special leave |
| without_pay | Leave without pay |

---

## Employee Number Format

```
EMP-2026-000001
│  │      │
│  │      └── Sequential number
│  └─────── Year
└────────── Prefix
```

---

## Key Features

✅ Auto-generated employee number (EMP-2026-000001)
✅ Profile management
✅ Department assignment
✅ Designation management
✅ Employment type tracking
✅ Salary grade system
✅ Shift management
✅ Salary profile with auto-calculation
✅ Leave management
✅ QR code generation
✅ Import/Export
✅ Search & filters
✅ Soft delete
✅ UUID-based public API

---

## Salary Grade Structure

```php
Grade Name: A1
Basic Salary: 25,000
House Rent: 10,000
Medical: 2,000
Transport: 3,000
Special: 5,000
─────────────────
Gross: 45,000
```

---

## Shift Configuration

```php
Shift: Morning
Start: 08:00
End: 16:00
Late After: 30 minutes
Working Hours: 8
Break Time: 1 hour
```

---

## Permissions

| Permission | Description |
|------------|-------------|
| employee.view | View employee list/details |
| employee.create | Register new employee |
| employee.update | Edit employee information |
| employee.delete | Delete employee |
| employee.import | Import employees |
| employee.export | Export employees |
| employee.shift.manage | Manage shifts |
| employee.leave.manage | Manage leave |
| employee.salary.manage | Manage salary |
| employee.print.id | Print ID card |

---

## React Structure

```
frontend/src/features/employees/
├── types/
│   └── index.ts              # TypeScript types
├── services/
│   └── employeeApi.ts       # API client
├── hooks/                    # (Ready for hooks)
├── pages/                    # (Ready for pages)
└── components/               # (Ready for components)
```

---

## Next Phase

**Phase 015 - Enterprise Attendance Management System**

- Student Attendance
- Teacher Attendance
- Employee Attendance
- QR Attendance
- Barcode Attendance
- Biometric Integration Ready
- Manual Attendance
- Attendance Correction
- Attendance Reports

---

## Status

✅ Phase 014 Complete
