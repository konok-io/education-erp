# PHASE-033.md

# Education ERP + CMS Enterprise Development Bible

## Phase 033 — Enterprise Payroll, Salary, Benefits & Human Compensation Management System

**Version:** 1.0 LTS

---

# Objective

এই Phase-এর উদ্দেশ্য হলো একটি সম্পূর্ণ Enterprise Grade Payroll, Salary, Benefits, Compensation ও Employee Financial Management System তৈরি করা।

এই Module সম্পূর্ণভাবে HR Module, Attendance Module, Leave Module, Finance Module, Tax Engine ও Bank Management এর সাথে Integrated থাকবে।

কোনো Salary Manual Edit করা যাবে না। Salary সর্বদা Attendance, Leave, Overtime, Bonus, Tax, Loan ও Allowance থেকে Automatic Generate হবে।

---

# Previous Completed Phases

✅ Phase-001 ~ Phase-032 Completed Successfully

---

# Phase Scope

Included

✔ Payroll Dashboard

✔ Employee Salary Structure

✔ Pay Grade

✔ Salary Components

✔ Earnings

✔ Deductions

✔ Allowances

✔ Overtime

✔ Attendance Integration

✔ Leave Integration

✔ Bonus Management

✔ Festival Bonus

✔ Performance Bonus

✔ Loan Management

✔ Salary Advance

✔ Provident Fund (PF)

✔ Gratuity

✔ Income Tax

✔ Payroll Processing

✔ Salary Approval

✔ Salary Slip

✔ Bank Transfer File

✔ Payroll Reports

✔ REST API

✔ React Module

✔ Electron Ready

✔ Android Ready

---

# Implementation Summary

## Backend

### Database Migrations (7 files from Phase 033)

1. `pay_grades` - Pay grade configuration
2. `salary_structures` - Employee salary structures
3. `payroll_processing` - Monthly payroll processing
4. `payroll_items` - Individual payroll items
5. `bonuses` - Employee bonuses
6. `employee_loans` - Employee loans
7. `provident_funds` - Provident fund accounts

---

## Frontend

### Pages (7 files)

Located in `frontend/src/features/payroll/pages/`:

- `PayrollDashboard.tsx` - Payroll overview with KPIs
- `PayrollProcessing.tsx` - Monthly payroll processing
- `PayrollReports.tsx` - Generate and view reports
- `salary/SalaryStructure.tsx` - Pay grades & salary structures
- `bonus/BonusManagement.tsx` - Bonus management
- `loan/LoanPFManagement.tsx` - Loans & PF management
- `payslip/SalarySlip.tsx` - Salary slip viewer & PDF

---

# Pay Grades

## Supported Grades

| Code | Name | Min Salary | Max Salary |
|------|------|------------|------------|
| G-01 | Grade-01 (Staff) | ৳25,000 | ৳40,000 |
| G-02 | Grade-02 (Officer) | ৳40,000 | ৳60,000 |
| G-03 | Grade-03 (Senior Officer) | ৳60,000 | ৳85,000 |
| G-04 | Grade-04 (Manager) | ৳85,000 | ৳120,000 |
| LEC | Lecturer | ৳65,000 | ৳95,000 |
| ASTP | Assistant Professor | ৳95,000 | ৳140,000 |
| ASCP | Associate Professor | ৳140,000 | ৳180,000 |
| PROF | Professor | ৳180,000 | ৳250,000 |

---

# Salary Components

## Earnings

| Component | Description |
|-----------|-------------|
| Basic Salary | Base salary based on pay grade |
| House Rent | 25% of basic (adjustable) |
| Medical Allowance | 10% of basic |
| Transport Allowance | 10% of basic |
| Other Allowance | Special allowances |

## Deductions

| Deduction | Description |
|-----------|-------------|
| Income Tax | Based on tax slabs |
| Provident Fund | Employee contribution |
| Loan Recovery | Monthly installment |
| Advance Recovery | Advance salary recovery |
| Absent Deduction | Per day salary deduction |
| Late Deduction | Late arrival penalty |

---

# Bonus Types

| Type | Description |
|------|-------------|
| Festival Bonus | Eid, Puja, Christmas bonuses |
| Performance Bonus | Quarterly/Annual performance |
| Special Bonus | Special recognition |
| Research Bonus | Publication incentives |
| Project Bonus | Project completion |

---

# Loan Types

| Type | Interest Rate |
|------|-------------|
| Personal Loan | 10% |
| Car Loan | 8% |
| Home Loan | 6% |
| Salary Advance | 0% |

---

# Payroll Processing Workflow

```
Attendance Import
↓
Leave Adjustment
↓
Overtime Calculation
↓
Bonus Addition
↓
Tax Calculation
↓
PF Deduction
↓
Loan Recovery
↓
Net Salary Calculation
↓
Validation
↓
Approval
↓
Payment
↓
Bank Transfer
↓
Journal Entry
↓
Payslip Generation
```

---

# REST API Endpoints

## Payroll

```
GET    /api/v1/payroll
POST   /api/v1/payroll/process
GET    /api/v1/payroll/{uuid}
PUT    /api/v1/payroll/{uuid}
POST   /api/v1/payroll/{uuid}/approve
POST   /api/v1/payroll/{uuid}/pay
```

## Salary Structure

```
GET    /api/v1/salary-structures
POST   /api/v1/salary-structures
PUT    /api/v1/salary-structures/{uuid}
```

## Pay Grades

```
GET    /api/v1/pay-grades
POST   /api/v1/pay-grades
PUT    /api/v1/pay-grades/{uuid}
```

## Bonus

```
GET    /api/v1/bonuses
POST   /api/v1/bonuses
PUT    /api/v1/bonuses/{uuid}
POST   /api/v1/bonuses/{uuid}/approve
```

## Loan

```
GET    /api/v1/loans
POST   /api/v1/loans
PUT    /api/v1/loans/{uuid}
POST   /api/v1/loans/{uuid}/approve
```

## PF

```
GET    /api/v1/provident-funds
POST   /api/v1/provident-funds
GET    /api/v1/provident-funds/{uuid}/statement
```

## Payslip

```
GET    /api/v1/payslips
GET    /api/v1/payslips/{uuid}
GET    /api/v1/payslips/{uuid}/pdf
```

## Reports

```
GET    /api/v1/payroll/reports/monthly
GET    /api/v1/payroll/reports/department
GET    /api/v1/payroll/reports/bonus
GET    /api/v1/payroll/reports/pf
GET    /api/v1/payroll/reports/loan
GET    /api/v1/payroll/reports/bank-transfer
```

---

# Permissions

| Permission | Description |
|------------|-------------|
| payroll.view | View payroll |
| payroll.process | Process payroll |
| payroll.approve | Approve payroll |
| payroll.pay | Make payment |
| salary.manage | Manage salary structures |
| bonus.manage | Manage bonuses |
| loan.manage | Manage loans |
| pf.manage | Manage provident fund |
| payroll.report | Generate reports |
| payroll.export | Export data |

---

# Validation Checklist

- [x] Salary Structure Working
- [x] Payroll Processing Working
- [x] Attendance Integration Working
- [x] Leave Integration Working
- [x] Bonus Working
- [x] Loan Working
- [x] PF Working
- [x] Salary Slip Working
- [x] Reports Working
- [x] Finance Journal Integration Working

---

# Git Workflow

```bash
git status
git add .
git commit -m "Phase 033: Enterprise payroll, salary & compensation management completed"
git push origin main
```

---

# Acceptance Criteria

✅ Enterprise Payroll, Salary, Benefits & Compensation Management System Successfully Completed

✅ Complete Payroll Life Cycle Operational

✅ Automatic Financial Journal Posting Enabled

✅ Integration with HR, Attendance, Leave, Finance & Tax Modules

---

# AI Final Instruction

Stop Here.

Do NOT Generate HR Module.

Do NOT Modify Previous Phases.

Wait For **PHASE-034.md**

---

# Next Phase

## PHASE-034.md

**Enterprise Human Resource Management (HRM) System**

### Modules

- HR Dashboard
- Employee Management
- Recruitment
- Job Circular
- Applicant Tracking System (ATS)
- Interview Management
- Employee Onboarding
- Service Book
- Employment History
- Promotion & Transfer
- Performance Appraisal (KPI)
- Disciplinary Management
- Exit Management
- Organization Chart
- Document Management
- HR Reports
- REST API
- React Module
- Electron Support
- Android Support
