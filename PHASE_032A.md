# PHASE-032A.md

# Education ERP + CMS Enterprise Development Bible

## Phase 032A — Enterprise Finance, Accounting & General Ledger Management System (Core Accounting Foundation)

**Version:** 1.0 LTS

---

# Objective

এই Phase-এর উদ্দেশ্য হলো পুরো ERP-এর জন্য একটি Enterprise Grade Double Entry Accounting Engine তৈরি করা।

এটি ভবিষ্যতের

- Student Fees
- Payroll
- Inventory
- Procurement
- Library Fine
- Hostel
- Transport
- Donation
- Research Grant

সহ সকল Financial Module-এর মূল ভিত্তি হবে।

**কোনো Financial Transaction সরাসরি Database Update করবে না।**

সবকিছু Journal Entry → General Ledger → Financial Reports Workflow অনুসরণ করবে।

---

# Previous Completed Phases

✅ Phase-001 ~ Phase-031 Completed Successfully

---

# Phase Scope

Included

✔ Finance Dashboard

✔ Fiscal Year Management

✔ Financial Period

✔ Chart of Accounts (COA)

✔ Account Groups

✔ Account Hierarchy

✔ Journal Entry

✔ Journal Approval

✔ General Ledger

✔ Ledger Posting

✔ Trial Balance

✔ Cost Centers

✔ Accounting Policies

✔ Opening Balance

✔ Closing Balance

✔ REST API

✔ React Module

✔ Electron Ready

✔ Android Ready

---

# Implementation Summary

## Backend

### Database Migrations (11 files from Phase 032A)

1. `fiscal_years` - Fiscal year management
2. `account_groups` - Account groups
3. `accounts` - Chart of accounts
4. `cost_centers` - Cost center management
5. `journal_entries` - Journal entries
6. `journal_entry_items` - Journal entry line items
7. `ledger_entries` - General ledger entries
8. `trial_balances` - Trial balance reports
9. `accounting_policies` - Accounting policies
10. `opening_balances` - Opening balance management
11. `finance_activities` - Activity logging

### Models (from Phase 009)

Located in `backend/app/Models/Finance/`:

- `FiscalYear.php` - Fiscal year management
- `Account.php` - Chart of accounts with hierarchy
- `AccountGroup.php` - Account groups by type
- `CostCenter.php` - Cost center management
- `JournalEntry.php` - Journal entries
- `JournalEntryDetail.php` - Journal entry details

### Services

- `backend/app/Services/Finance/AccountingService.php` - Double entry accounting engine

---

## Frontend

### Pages (5 files)

Located in `frontend/src/features/finance/pages/`:

- `FinanceDashboard.tsx` - Financial overview dashboard
- `ChartOfAccounts.tsx` - Account hierarchy tree
- `JournalEntry.tsx` - Journal entry management
- `Ledger.tsx` - General ledger view
- `TrialBalance.tsx` - Trial balance report

### Store (1 file)

Located in `frontend/src/features/finance/store/`:

- `financeStore.ts` - Zustand store for finance state

### Types (1 file)

Located in `frontend/src/features/finance/types/`:

- `index.ts` - Complete TypeScript types

### API Service (1 file)

Located in `frontend/src/features/finance/services/`:

- `financeApi.ts` - API service for finance endpoints

---

# Account Types

| Type | Description | Nature |
|------|-------------|--------|
| asset | Assets | Debit |
| liability | Liabilities | Credit |
| equity | Equity | Credit |
| income | Income | Credit |
| expense | Expenses | Debit |

---

# Account Groups

## Assets
- Current Assets
  - Cash
  - Bank
  - Accounts Receivable
- Fixed Assets
  - Land & Building
  - Furniture & Fixtures
  - Equipment

## Liabilities
- Current Liability
  - Accounts Payable
  - Tax Payable
- Long Term Liability
  - Bank Loans

## Equity
- Capital Fund
- Retained Earnings

## Income
- Tuition Fees
- Admission Fees
- Exam Fees
- Miscellaneous Income

## Expenses
- Salary & Allowances
- Utilities
- Maintenance
- Educational Resources

---

# Voucher Types

| Type | Code | Description |
|------|------|-------------|
| Payment Voucher | Payment | Cash/Bank payment |
| Receipt Voucher | Receipt | Cash/Bank receipt |
| Contra Voucher | Contra | Transfer between cash/bank |
| Journal Voucher | Journal | General journal entry |
| Opening Voucher | Opening | Opening balance entry |
| Adjustment Voucher | Adjustment | Adjustments |

---

# Journal Status

| Status | Description |
|--------|-------------|
| draft | Draft entry |
| pending | Pending approval |
| approved | Approved by reviewer |
| posted | Posted to ledger |
| rejected | Rejected |
| cancelled | Cancelled |

---

# Journal Approval Workflow

```
Draft → Submitted → Accountant Review → Finance Manager → Approved → Posted
```

---

# Double Entry Rule

Every journal entry must satisfy:

```
Total Debit = Total Credit
```

System will reject unbalanced entries.

---

# REST API Endpoints

## Chart of Accounts

```
GET    /api/v1/accounts              - List accounts
POST   /api/v1/accounts              - Create account
GET    /api/v1/accounts/{uuid}       - Get account
PUT    /api/v1/accounts/{uuid}       - Update account
DELETE /api/v1/accounts/{uuid}       - Delete account
GET    /api/v1/accounts/tree         - Get account tree
```

## Journal Entries

```
GET    /api/v1/journals              - List journals
POST   /api/v1/journals              - Create journal
GET    /api/v1/journals/{uuid}       - Get journal
PUT    /api/v1/journals/{uuid}       - Update journal
POST   /api/v1/journals/{uuid}/approve - Approve journal
POST   /api/v1/journals/{uuid}/post  - Post journal
POST   /api/v1/journals/{uuid}/reject - Reject journal
```

## Ledger

```
GET    /api/v1/ledger                - Get ledger entries
GET    /api/v1/ledger/{accountId}    - Get account ledger
```

## Trial Balance

```
GET    /api/v1/trial-balance         - Generate trial balance
GET    /api/v1/trial-balance/{id}    - Get trial balance
```

## Fiscal Years

```
GET    /api/v1/fiscal-years          - List fiscal years
POST   /api/v1/fiscal-years          - Create fiscal year
PUT    /api/v1/fiscal-years/{uuid}   - Update fiscal year
POST   /api/v1/fiscal-years/{uuid}/close - Close fiscal year
```

## Cost Centers

```
GET    /api/v1/cost-centers          - List cost centers
POST   /api/v1/cost-centers          - Create cost center
PUT    /api/v1/cost-centers/{uuid}   - Update cost center
```

---

# Permissions

| Permission | Description |
|------------|-------------|
| finance.view | View financial data |
| finance.create | Create accounts/entries |
| finance.update | Update financial data |
| finance.delete | Delete financial data |
| finance.journal | Manage journals |
| finance.approve | Approve journals |
| finance.post | Post to ledger |
| finance.report | View reports |
| finance.export | Export data |

---

# Validation Checklist

- [x] COA Working
- [x] Journal Working
- [x] Ledger Working
- [x] Trial Balance Working
- [x] Double Entry Validation Working
- [x] Fiscal Year Working
- [x] API Working
- [x] Audit Log Working

---

# Git Workflow

```bash
git status
git add .
git commit -m "Phase 032A: Finance core accounting foundation completed"
git push origin main
```

---

# Acceptance Criteria

✅ Enterprise Double Entry Accounting Engine Successfully Completed

✅ Core Accounting Foundation Ready

✅ Chart of Accounts with hierarchical structure

✅ Journal Entry with double entry validation

✅ Journal Approval Workflow

✅ General Ledger with running balance

✅ Trial Balance Generation

✅ Fiscal Year Management

✅ Cost Centers

✅ REST API endpoints for all operations

✅ React frontend with dashboard and management pages

✅ Activity logging for audit trail

---

# Next Phase

## PHASE-032B.md

- Balance Sheet
- Income Statement
- Cash Flow
- Budget Management
- Accounts Payable
- Accounts Receivable
- Bank Management
- Bank Reconciliation
- Fixed Asset Accounting
- Financial Reports
- REST API
- React Module

---

# AI Final Instruction

Stop Here.

Do NOT Generate Balance Sheet.

Do NOT Generate Accounts Payable.

Do NOT Generate Budget Module.

Wait For **PHASE-032B.md**
