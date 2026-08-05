# Phase 021 - Enterprise HR, Payroll & Leave Management System

## Overview

This phase implements the complete Enterprise HR, Payroll & Leave Management System for the Education ERP. This module handles all HR operations including employee management, payroll processing, leave management, and overtime tracking.

---

## HR Architecture

```
Employee
    ↓
Department
    ↓
Designation
    ↓
Salary Grade
    ↓
Attendance
    ↓
Leave
    ↓
Payroll
    ↓
Payslip
    ↓
Accounting
```

---

## Completed Tasks

### Models (10 models)

| Model | Description |
|-------|-------------|
| SalaryGrade | Salary grades |
| SalaryStructure | Salary components |
| Payroll | Payroll records |
| PayrollDetail | Payroll details |
| LeaveType | Leave types |
| Leave | Leave applications |
| Holiday | Holiday calendar |
| Loan | Employee loans |
| LoanRepayment | Loan repayments |
| OvertimeRecord | Overtime records |

### Controller
- `HRController.php` - Complete CRUD and operations

### Service
- `HRService.php` - All business logic

### API Routes
- Complete REST API (30+ endpoints)

### React Structure
- `types/` - TypeScript interfaces
- `services/` - API client

---

## Leave Types

| Type | Code | Days | Paid |
|------|------|------|------|
| Casual Leave | CL | 10 | Yes |
| Sick Leave | SL | 10 | Yes |
| Annual Leave | AL | 20 | Yes |
| Maternity Leave | ML | 90 | Yes |
| Paternity Leave | PL | 10 | Yes |
| Study Leave | STL | 15 | Yes |
| Leave Without Pay | LWP | 30 | No |

---

## Loan Types

| Type | Description |
|------|-------------|
| personal | Personal Loan |
| house | House Building Loan |
| vehicle | Vehicle Loan |
| emergency | Emergency Loan |
| festival | Festival Loan |

---

## Overtime Types

| Type | Rate |
|------|------|
| normal | 1.5x |
| weekend | 2.0x |
| holiday | 2.5x |
| night | 1.75x |

---

## API Endpoints

### Salary Grades
- `GET /api/v1/hr/salary-grades` - List grades
- `POST /api/v1/hr/salary-grades` - Create grade

### Payroll
- `GET /api/v1/hr/payrolls` - List payrolls
- `POST /api/v1/hr/payrolls` - Process payroll
- `POST /api/v1/hr/payrolls/bulk` - Bulk process
- `POST /api/v1/hr/payrolls/{id}/approve` - Approve
- `POST /api/v1/hr/payrolls/{id}/pay` - Mark paid
- `GET /api/v1/hr/payrolls/{id}/payslip` - Get payslip

### Leave Types
- `GET /api/v1/hr/leave-types` - List types
- `POST /api/v1/hr/leave-types` - Create type

### Leaves
- `GET /api/v1/hr/leaves` - List leaves
- `POST /api/v1/hr/leaves` - Apply leave
- `POST /api/v1/hr/leaves/{id}/approve` - Approve
- `POST /api/v1/hr/leaves/{id}/reject` - Reject
- `GET /api/v1/hr/leaves/balance/{id}` - Get balance

### Holidays
- `GET /api/v1/hr/holidays` - List holidays
- `POST /api/v1/hr/holidays` - Create holiday

### Loans
- `GET /api/v1/hr/loans` - List loans
- `POST /api/v1/hr/loans` - Create loan
- `POST /api/v1/hr/loans/{id}/approve` - Approve
- `GET /api/v1/hr/loans/balance/{id}` - Get balance

### Overtime
- `GET /api/v1/hr/overtimes` - List overtimes
- `POST /api/v1/hr/overtimes` - Create overtime
- `POST /api/v1/hr/overtimes/{id}/approve` - Approve

### Reports
- `GET /api/v1/hr/reports/payroll` - Payroll report
- `GET /api/v1/hr/reports/leave` - Leave report

### Dashboard
- `GET /api/v1/hr/dashboard` - HR Dashboard

### Export
- `GET /api/v1/hr/export/payslips` - Export payslips

---

## Key Features

✅ Employee Management
✅ Salary Grades
✅ Salary Structure
✅ Payroll Processing
✅ Attendance Integration
✅ Leave Management
✅ Holiday Calendar
✅ Overtime Tracking
✅ Loan Management
✅ Advance Salary
✅ Provident Fund Deduction
✅ Income Tax Deduction
✅ Payslip Generation
✅ Payroll Reports
✅ HR Dashboard
✅ Soft delete
✅ UUID-based public API

---

## Payroll Components

### Earnings
- Basic Salary
- House Rent
- Medical Allowance
- Transport Allowance
- Mobile Allowance
- Special Allowance
- Overtime
- Bonus

### Deductions
- Provident Fund
- Income Tax
- Loan Deduction
- Advance Deduction

---

## Payslip Format

```json
{
  "employee": {
    "name": "John Doe",
    "employee_no": "EMP-001",
    "department": "Accounts",
    "designation": "Accountant"
  },
  "payroll": {
    "no": "PR/2026/08/0001",
    "month": 8,
    "year": 2026
  },
  "earnings": [
    { "name": "Basic Salary", "amount": 30000 },
    { "name": "House Rent", "amount": 9000 }
  ],
  "deductions": [
    { "name": "PF", "amount": 3900 },
    { "name": "Tax", "amount": 1500 }
  ],
  "totals": {
    "gross": 45000,
    "net": 39600
  }
}
```

---

## Leave Balance

```json
[
  {
    "type": "Casual Leave",
    "code": "CL",
    "total": 10,
    "used": 3,
    "pending": 2,
    "remaining": 5
  }
]
```

---

## Permissions

| Permission | Description |
|------------|-------------|
| employee.view | View employees |
| employee.create | Create employee |
| employee.update | Update employee |
| payroll.process | Process payroll |
| payroll.approve | Approve payroll |
| leave.approve | Approve leave |
| loan.approve | Approve loan |
| hr.report | View reports |

---

## React Structure

```
frontend/src/features/hr/
├── types/
│   └── index.ts              # TypeScript types
├── services/
│   └── hrApi.ts              # API client
├── hooks/                    # (Ready for hooks)
├── pages/                    # (Ready for pages)
└── components/            # (Ready for components)
```

---

## Next Phase

**Phase 022 - Enterprise Library Management System**

- Library Dashboard
- Book Management
- Book Issue & Return
- Fine Calculation
- Library Reports

---

## Status

✅ Phase 021 Complete
