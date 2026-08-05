# PHASE-021.md

# Education ERP + CMS Enterprise Development Bible

## Phase 021 — Enterprise HR, Payroll & Leave Management System

**Version:** 1.0 LTS

---

# Objective

এই Phase-এর উদ্দেশ্য হলো একটি সম্পূর্ণ Enterprise Grade Human Resource (HR), Payroll & Leave Management System তৈরি করা।

এই Module-এর মাধ্যমে

- Employee Management
- Payroll
- Salary Structure
- Salary Processing
- Leave
- Holiday
- Overtime
- Attendance Integration
- Provident Fund
- Income Tax
- Loan
- Advance Salary

সম্পূর্ণভাবে পরিচালিত হবে।

এই Module

Employee Module

Attendance Module

Accounting Module

Notification Module

Reports Module

এর সাথে সম্পূর্ণ Integrated থাকবে।

---

# Previous Completed Phases

✅ Phase-001 ~ Phase-020 Completed Successfully

---

# Phase Scope

Included

✔ Employee Profile

✔ Department

✔ Designation

✔ Employment Type

✔ Salary Grade

✔ Salary Structure

✔ Payroll Processing

✔ Payroll Approval

✔ Payslip

✔ Bonus

✔ Festival Bonus

✔ Increment

✔ Promotion

✔ Demotion

✔ Overtime

✔ Leave Management

✔ Holiday Calendar

✔ Attendance Integration

✔ Loan Management

✔ Advance Salary

✔ Provident Fund

✔ Income Tax

✔ Final Settlement

✔ Employee Resignation

✔ Retirement

✔ Payroll Reports

✔ REST API

✔ React Module

✔ Electron Ready

✔ Android Ready

---

# HR Architecture

```
Employee

↓

Department

↓

Designation

↓

Salary Grade

↓

Salary Structure

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

# Implementation Summary

## Backend

### Database Migrations (27 files)

1. `create_employment_types_table.php` - Employment types
2. `create_designations_table.php` - Designations
3. `create_salary_grades_table.php` - Salary grades
4. `create_leave_types_table.php` - Leave types
5. `create_leaves_table.php` - Leave applications
6. `create_holidays_table.php` - Holiday calendar
7. `create_loans_table.php` - Loan management
8. `create_loan_repayments_table.php` - Loan repayments
9. `create_overtime_records_table.php` - Overtime records
10. `create_payrolls_table.php` - Payroll records
11. `create_payroll_details_table.php` - Payroll breakdown
12. `create_advance_salaries_table.php` - Advance salary
13. `create_bonuses_table.php` - Bonus management
14. `create_increments_table.php` - Increment records
15. `create_promotions_table.php` - Promotion records
16. `create_employee_exits_table.php` - Employee exit/final settlement
17. `create_provident_funds_table.php` - PF accounts
18. `create_pf_contributions_table.php` - PF contributions
19. `create_pf_withdrawals_table.php` - PF withdrawals
20. `create_tax_slabs_table.php` - Tax slabs
21. `create_employee_tax_records_table.php` - Employee tax records
22. `create_employee_attendances_table.php` - Employee attendance
23. `create_employee_documents_table.php` - Employee documents
24. `create_employee_emergency_contacts_table.php` - Emergency contacts
25. `create_employee_leave_balances_table.php` - Leave balances
26. `create_activity_logs_table.php` - Activity logging

### Models (17 files)

Located in `backend/app/Models/HR/`:

- `EmploymentType.php`
- `Designation.php`
- `SalaryGrade.php`
- `LeaveType.php`
- `Leave.php`
- `Holiday.php`
- `Loan.php`
- `LoanRepayment.php`
- `OvertimeRecord.php`
- `Payroll.php`
- `PayrollDetail.php`
- `AdvanceSalary.php`
- `Bonus.php`
- `Increment.php`
- `Promotion.php`
- `EmployeeExit.php`
- `ProvidentFund.php`
- `PFContribution.php`
- `PFWithdrawal.php`
- `TaxSlab.php`
- `EmployeeTaxRecord.php`
- `EmployeeAttendance.php`
- `EmployeeLeaveBalance.php`

### Services (1 file)

- `backend/app/Services/HR/HRService.php` - Comprehensive HR service

### API Resources (8 files)

Located in `backend/app/Http/Resources/HR/`:

- `SalaryGradeResource.php`
- `LeaveTypeResource.php`
- `HolidayResource.php`
- `AdvanceSalaryResource.php`
- `BonusResource.php`
- `IncrementResource.php`
- `PromotionResource.php`
- `EmployeeExitResource.php`
- `ProvidentFundResource.php`
- `TaxSlabResource.php`

### Database Seeders (5 files)

Located in `backend/database/seeders/`:

- `EmploymentTypeSeeder.php` - Seeds 7 employment types
- `DesignationSeeder.php` - Seeds 16 designations
- `SalaryGradeSeeder.php` - Seeds 6 salary grades
- `LeaveTypeSeeder.php` - Seeds 8 leave types
- `HolidaySeeder.php` - Seeds annual holidays
- `DatabaseSeeder.php` - Updated to include all HR seeders

---

## Frontend

### Pages (7 files)

Located in `frontend/src/features/hr/pages/`:

- `HRDashboard.tsx` - Dashboard with stats and quick actions
- `Payroll.tsx` - Payroll processing and management
- `LeaveManagement.tsx` - Leave request management
- `LoanManagement.tsx` - Loan application management
- `HolidayCalendar.tsx` - Holiday calendar view
- `PayslipViewer.tsx` - Payslip viewer with export
- `HRReports.tsx` - Payroll and leave reports

### Store (1 file)

Located in `frontend/src/features/hr/store/`:

- `hrStore.ts` - Zustand store for HR state management

### Types (1 file)

Located in `frontend/src/features/hr/types/`:

- `index.ts` - Complete TypeScript types for HR module

### API Service (1 file)

Located in `frontend/src/features/hr/services/`:

- `hrApi.ts` - API service for HR endpoints

---

# REST API Endpoints

## Employees

```
GET    /api/v1/employees              - List employees
POST   /api/v1/employees              - Create employee
GET    /api/v1/employees/{uuid}        - Get employee
PUT    /api/v1/employees/{uuid}       - Update employee
DELETE /api/v1/employees/{uuid}       - Delete employee
```

## Salary Grades

```
GET    /api/v1/hr/salary-grades       - List salary grades
POST   /api/v1/hr/salary-grades       - Create salary grade
GET    /api/v1/hr/salary-grades/{uuid} - Get salary grade
PUT    /api/v1/hr/salary-grades/{uuid} - Update salary grade
DELETE /api/v1/hr/salary-grades/{uuid} - Delete salary grade
```

## Leave Types

```
GET    /api/v1/hr/leave-types         - List leave types
POST   /api/v1/hr/leave-types         - Create leave type
GET    /api/v1/hr/leave-types/{uuid}  - Get leave type
PUT    /api/v1/hr/leave-types/{uuid}  - Update leave type
DELETE /api/v1/hr/leave-types/{uuid}  - Delete leave type
```

## Leaves

```
GET    /api/v1/hr/leaves              - List leaves
POST   /api/v1/hr/leaves              - Apply for leave
GET    /api/v1/hr/leaves/{uuid}       - Get leave
PUT    /api/v1/hr/leaves/{uuid}       - Update leave
POST   /api/v1/hr/leaves/{uuid}/approve - Approve leave
POST   /api/v1/hr/leaves/{uuid}/reject  - Reject leave
```

## Holidays

```
GET    /api/v1/hr/holidays            - List holidays
POST   /api/v1/hr/holidays            - Create holiday
GET    /api/v1/hr/holidays/{uuid}     - Get holiday
PUT    /api/v1/hr/holidays/{uuid}     - Update holiday
DELETE /api/v1/hr/holidays/{uuid}    - Delete holiday
```

## Loans

```
GET    /api/v1/hr/loans               - List loans
POST   /api/v1/hr/loans               - Apply for loan
GET    /api/v1/hr/loans/{uuid}        - Get loan
POST   /api/v1/hr/loans/{uuid}/approve - Approve loan
GET    /api/v1/hr/loans/balance/{uuid} - Get employee loan balance
```

## Overtime

```
GET    /api/v1/hr/overtimes           - List overtime records
POST   /api/v1/hr/overtimes           - Create overtime record
GET    /api/v1/hr/overtimes/{uuid}    - Get overtime record
POST   /api/v1/hr/overtimes/{uuid}/approve - Approve overtime
```

## Payroll

```
GET    /api/v1/hr/payroll             - List payrolls
POST   /api/v1/hr/payroll/process     - Process payroll
GET    /api/v1/hr/payroll/{uuid}      - Get payroll
POST   /api/v1/hr/payroll/{uuid}/approve - Approve payroll
POST   /api/v1/hr/payroll/{uuid}/pay  - Mark as paid
GET    /api/v1/hr/payroll/payslip/{uuid} - Get payslip
GET    /api/v1/hr/payroll/export      - Export payroll
```

## Advance Salary

```
GET    /api/v1/hr/advance-salaries    - List advance salaries
POST   /api/v1/hr/advance-salaries    - Request advance
GET    /api/v1/hr/advance-salaries/{uuid} - Get advance
POST   /api/v1/hr/advance-salaries/{uuid}/approve - Approve
```

## Bonus

```
GET    /api/v1/hr/bonuses             - List bonuses
POST   /api/v1/hr/bonuses             - Create bonus
GET    /api/v1/hr/bonuses/{uuid}      - Get bonus
POST   /api/v1/hr/bonuses/{uuid}/approve - Approve bonus
POST   /api/v1/hr/bonuses/{uuid}/pay  - Pay bonus
```

## Increment

```
GET    /api/v1/hr/increments          - List increments
POST   /api/v1/hr/increments          - Create increment
GET    /api/v1/hr/increments/{uuid}   - Get increment
POST   /api/v1/hr/increments/{uuid}/approve - Approve
POST   /api/v1/hr/increments/{uuid}/activate - Activate
```

## Promotion

```
GET    /api/v1/hr/promotions          - List promotions
POST   /api/v1/hr/promotions          - Create promotion
GET    /api/v1/hr/promotions/{uuid}   - Get promotion
POST   /api/v1/hr/promotions/{uuid}/approve - Approve
POST   /api/v1/hr/promotions/{uuid}/activate - Activate
```

## Employee Exit

```
GET    /api/v1/hr/employee-exits      - List exits
POST   /api/v1/hr/employee-exits      - Create exit/final settlement
GET    /api/v1/hr/employee-exits/{uuid} - Get exit
POST   /api/v1/hr/employee-exits/{uuid}/approve - Approve
POST   /api/v1/hr/employee-exits/{uuid}/process - Process
POST   /api/v1/hr/employee-exits/{uuid}/pay  - Pay settlement
```

## Provident Fund

```
GET    /api/v1/hr/provident-funds     - List PF accounts
POST   /api/v1/hr/provident-funds     - Create PF account
GET    /api/v1/hr/provident-funds/{uuid} - Get PF
POST   /api/v1/hr/provident-funds/{uuid}/contribution - Add contribution
```

## Tax

```
GET    /api/v1/hr/tax-slabs           - List tax slabs
POST   /api/v1/hr/tax-slabs           - Create tax slab
GET    /api/v1/hr/tax-slabs/{uuid}    - Get tax slab
PUT    /api/v1/hr/tax-slabs/{uuid}    - Update tax slab
```

## Reports

```
GET    /api/v1/hr/reports/payroll      - Payroll report
GET    /api/v1/hr/reports/leave       - Leave report
GET    /api/v1/hr/reports/loan        - Loan report
GET    /api/v1/hr/reports/tax         - Tax report
GET    /api/v1/hr/reports/pf          - PF report
```

## Dashboard

```
GET    /api/v1/hr/dashboard           - HR dashboard data
```

---

# Database Schema

## Key Tables

### employees
- id, uuid, employee_no
- first_name, last_name, email, phone
- department_id, designation_id
- salary_grade_id, employment_type_id
- joining_date, status

### payrolls
- id, uuid, payroll_no
- employee_id, payroll_month, payroll_year
- basic_salary, gross_salary, net_salary
- total_allowance, total_deduction
- tax_amount, pf_amount, loan_deduction
- overtime_amount, bonus_amount
- status, processed_at, approved_at, paid_at

### leaves
- id, uuid, leave_no
- employee_id, leave_type_id
- start_date, end_date, total_days
- reason, status
- approved_by, approved_at

### loans
- id, uuid, loan_no
- employee_id, loan_type
- principal_amount, interest_rate
- monthly_installment, remaining_amount
- status

### provident_funds
- id, uuid, pf_no
- employee_id
- employee_contribution, employer_contribution
- total_balance, status

---

# Employment Types

| Code | Name |
|------|------|
| PERMANENT | Permanent |
| CONTRACT | Contract |
| PART_TIME | Part-Time |
| TEMPORARY | Temporary |
| GUEST_FACULTY | Guest Faculty |
| VISITING | Visiting Faculty |
| INTERN | Intern |

---

# Designations

| Code | Name | Level |
|------|------|-------|
| PRINCIPAL | Principal | Executive |
| VP | Vice Principal | Executive |
| HOD | Head Of Department | Management |
| PROF | Professor | Faculty |
| ASSOC_PROF | Associate Professor | Faculty |
| ASST_PROF | Assistant Professor | Faculty |
| LECTURER | Lecturer | Faculty |
| TEACHER | Teacher | Faculty |
| ACCOUNTANT | Accountant | Staff |
| SR_OFFICER | Senior Officer | Staff |
| OFFICER | Officer | Staff |
| OFFICE_ASST | Office Assistant | Staff |
| LIBRARIAN | Librarian | Staff |
| DRIVER | Driver | Support |
| CLEANER | Cleaner | Support |
| SECURITY | Security Guard | Support |

---

# Leave Types

| Code | Name | Days | Paid | Encashable | Carry Forward |
|------|------|------|------|------------|---------------|
| CL | Casual Leave | 10 | Yes | No | No |
| SL | Sick Leave | 10 | Yes | No | No |
| AL | Annual Leave | 20 | Yes | Yes | Yes |
| ML | Maternity Leave | 90 | Yes | No | No |
| PL | Paternity Leave | 10 | Yes | No | No |
| STL | Study Leave | 15 | Yes | No | No |
| LWP | Leave Without Pay | 30 | No | No | No |
| SPL | Special Leave | 5 | Yes | No | No |

---

# Holiday Types

| Type | Description |
|------|-------------|
| weekly | Weekly Holiday |
| national | National Holiday |
| religious | Religious Holiday |
| institution | Institution Holiday |
| emergency | Emergency Holiday |

---

# Loan Types

| Type | Description |
|------|-------------|
| personal | Personal Loan |
| house | House Building Loan |
| vehicle | Vehicle Loan |
| emergency | Emergency Loan |
| festival | Festival Loan |

---

# Bonus Types

| Type | Description |
|------|-------------|
| festival | Festival Bonus |
| performance | Performance Bonus |
| yearly | Yearly Bonus |
| special | Special Bonus |

---

# Increment Types

| Type | Description |
|------|-------------|
| annual | Annual Increment |
| performance | Performance Increment |
| promotion | Promotion Increment |
| manual | Manual Increment |

---

# Exit Types

| Type | Description |
|------|-------------|
| resignation | Resignation |
| termination | Termination |
| retirement | Retirement |
| death | Death |

---

# Payroll Workflow

```
1. Attendance Recorded
   ↓
2. Leave Adjustment Calculated
   ↓
3. Overtime Calculated
   ↓
4. Salary Components Calculated
   ↓
5. Deductions Applied (PF, Tax, Loan, Advance)
   ↓
6. Payroll Processed (draft)
   ↓
7. Verification
   ↓
8. Approval
   ↓
9. Payment
   ↓
10. Accounting Entry Generated
```

---

# Payslip Components

### Earnings
- Basic Salary
- House Rent
- Medical Allowance
- Transport Allowance
- Mobile Allowance
- Special Allowance
- Overtime Amount
- Bonus Amount

### Deductions
- Provident Fund
- Income Tax
- Loan Repayment
- Advance Salary Deduction
- Other Deductions

---

# Security

- Repository Pattern with Service Layer
- Policy-based Authorization
- Permission Middleware
- Audit Trail (Activity Logs)
- Soft Delete on all models
- UUID on all models
- Encrypted Salary Data
- Input Validation

---

# Permissions

| Permission | Description |
|------------|-------------|
| employee.view | View employee records |
| employee.create | Create employee |
| employee.update | Update employee |
| employee.delete | Delete employee |
| payroll.process | Process payroll |
| payroll.approve | Approve payroll |
| payroll.export | Export payroll reports |
| leave.approve | Approve leave |
| loan.approve | Approve loan |
| hr.report | View HR reports |

---

# Validation Checklist

- [x] Employee Module Working
- [x] Payroll Processing Working
- [x] Leave Management Working
- [x] Holiday Calendar Working
- [x] Loan Management Working
- [x] Overtime Working
- [x] Advance Salary Working
- [x] Bonus Management Working
- [x] Increment Management Working
- [x] Promotion Management Working
- [x] Employee Exit Working
- [x] PF Management Working
- [x] Tax Management Working
- [x] Payslip Generation Working
- [x] Reports Working
- [x] REST API Working
- [x] React Module Working
- [x] Activity Log Working

---

# Git Workflow

```bash
git status
git add .
git commit -m "Phase 021: Enterprise HR, payroll & leave management completed"
git push origin main
```

---

# Acceptance Criteria

✅ Enterprise HR, Payroll & Leave Management System Completed

✅ Complete Human Resource Management Workflow Operational

✅ All HR modules integrated with Employee, Attendance, Accounting modules

✅ REST API endpoints for all HR operations

✅ React frontend with dashboard and management pages

✅ Database seeders for initial data

✅ Activity logging for audit trail

---

# Next Phase

## PHASE-022.md

Enterprise Library Management System

- Library Dashboard
- Book Categories
- Authors
- Publishers
- Book Accession Register
- ISBN & Barcode Management
- QR Code Support
- Digital Library
- E-Book Management
- Book Issue & Return
- Fine Calculation
- Reservation System
- Lost Book Management
- Library Membership
- OPAC Search
- Library Reports
- REST API
- React Module
- Electron Support
- Android Support

---

# AI Final Instruction

Stop Here.

Do NOT Generate Library Module.

Do NOT Modify Previous Phases.

Wait For Phase-022.
