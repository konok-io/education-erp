# Phase 020 - Enterprise Accounting & Finance Management System

## Overview

This phase implements the complete Enterprise Accounting & Finance Management System for the Education ERP. This module serves as the financial core with double-entry bookkeeping, ledgers, vouchers, and comprehensive financial statements.

---

## Accounting Architecture

```
Fiscal Year
    ↓
Chart of Accounts
    ↓
Journal Entries (Double Entry)
    ↓
Ledger
    ↓
Trial Balance
    ↓
Profit & Loss
    ↓
Balance Sheet
```

---

## Completed Tasks

### Models (7 models)

| Model | Description |
|-------|-------------|
| Account | Chart of accounts |
| JournalEntry | Journal/voucher entries |
| JournalEntryDetail | Entry details (dr/cr) |
| FiscalYear | Fiscal years |
| CostCenter | Cost centers |
| Asset | Fixed assets |
| Budget | Budget management |

### Controller
- `FinanceController.php` - Complete CRUD and operations

### Service
- `FinanceService.php` - All business logic

### API Routes
- Complete REST API (30+ endpoints)

### React Structure
- `types/` - TypeScript interfaces
- `services/` - API client

---

## Account Types

| Type | Description |
|------|-------------|
| asset | Assets |
| liability | Liabilities |
| equity | Equity |
| income | Income |
| expense | Expense |

---

## Account Groups

| Group | Description |
|-------|-------------|
| cash | Cash |
| bank | Bank |
| receivable | Receivable |
| payable | Payable |
| capital | Capital |
| sales | Sales |
| purchase | Purchase |
| salary | Salary |
| utility | Utility |
| tax | Tax |

---

## Voucher Types

| Type | Description |
|------|-------------|
| journal | Journal Voucher |
| payment | Payment Voucher |
| receipt | Receipt Voucher |
| contra | Contra Voucher |
| adjustment | Adjustment Voucher |
| opening | Opening Voucher |
| closing | Closing Voucher |

---

## Journal Entry Status

| Status | Description |
|--------|-------------|
| draft | Draft |
| verified | Verified |
| approved | Approved |
| posted | Posted |
| locked | Locked |

---

## API Endpoints

### Accounts
- `GET /api/v1/finance/accounts` - List accounts
- `POST /api/v1/finance/accounts` - Create account
- `PUT /api/v1/finance/accounts/{id}` - Update account
- `DELETE /api/v1/finance/accounts/{id}` - Delete account

### Journal Entries
- `GET /api/v1/finance/journal` - List entries
- `POST /api/v1/finance/journal` - Create entry
- `PUT /api/v1/finance/journal/{id}` - Update entry
- `DELETE /api/v1/finance/journal/{id}` - Delete entry
- `POST /api/v1/finance/journal/{id}/post` - Post entry
- `POST /api/v1/finance/journal/{id}/approve` - Approve entry

### Ledger
- `GET /api/v1/finance/ledger` - Get ledger
- `GET /api/v1/finance/accounts/{id}/summary` - Account summary

### Reports
- `GET /api/v1/finance/reports/trial-balance` - Trial Balance
- `GET /api/v1/finance/reports/profit-loss` - Profit & Loss
- `GET /api/v1/finance/reports/balance-sheet` - Balance Sheet
- `GET /api/v1/finance/reports/cash-book` - Cash Book
- `GET /api/v1/finance/reports/bank-book` - Bank Book

### Fiscal Year
- `GET /api/v1/finance/fiscal-years` - List fiscal years
- `POST /api/v1/finance/fiscal-years` - Create fiscal year
- `POST /api/v1/finance/fiscal-years/{id}/close` - Close fiscal year

### Cost Centers
- `GET /api/v1/finance/cost-centers` - List cost centers
- `POST /api/v1/finance/cost-centers` - Create cost center

### Assets
- `GET /api/v1/finance/assets` - List assets
- `POST /api/v1/finance/assets` - Create asset
- `POST /api/v1/finance/assets/{id}/depreciation` - Calculate depreciation

### Budgets
- `GET /api/v1/finance/budgets` - List budgets
- `POST /api/v1/finance/budgets` - Create budget

### Dashboard
- `GET /api/v1/finance/dashboard` - Dashboard

### Export
- `GET /api/v1/finance/export` - Export reports

---

## Key Features

✅ Chart of Accounts
✅ Double Entry Accounting
✅ Journal Vouchers (Journal, Payment, Receipt, Contra)
✅ General Ledger
✅ Trial Balance
✅ Profit & Loss Statement
✅ Balance Sheet
✅ Cash Book
✅ Bank Book
✅ Fiscal Year Management
✅ Cost Centers
✅ Assets & Depreciation
✅ Budget Management
✅ Financial Dashboard
✅ Multi-currency Ready
✅ Multi-branch Ready
✅ Soft delete
✅ UUID-based public API

---

## Double Entry Rule

Every transaction must have:
- Debit entries
- Credit entries
- Total Debit = Total Credit

---

## Dashboard Statistics

```json
{
  "cash_balance": 500000,
  "bank_balance": 2000000,
  "total_balance": 2500000,
  "today": {
    "income": 50000,
    "expense": 30000
  },
  "month": {
    "income": 500000,
    "expense": 300000,
    "net": 200000
  },
  "pending_vouchers": 5
}
```

---

## Ledger Format

```json
{
  "account": { "name": "Cash", "code": "1001" },
  "opening_balance": 10000,
  "entries": [
    {
      "date": "2026-08-01",
      "voucher_no": "JU/2026/08/0001",
      "description": "Received from student",
      "dr": 5000,
      "cr": 0,
      "balance": 15000
    }
  ],
  "totals": { "dr": 5000, "cr": 0, "balance": 15000 }
}
```

---

## Permissions

| Permission | Description |
|------------|-------------|
| finance.view | View financial data |
| finance.create | Create vouchers |
| finance.update | Update vouchers |
| finance.delete | Delete vouchers |
| finance.post | Post vouchers |
| finance.approve | Approve vouchers |
| finance.report | View reports |
| finance.export | Export reports |
| finance.budget | Manage budgets |
| finance.reconciliation | Bank reconciliation |

---

## React Structure

```
frontend/src/features/finance/
├── types/
│   └── index.ts              # TypeScript types
├── services/
│   └── financeApi.ts         # API client
├── hooks/                    # (Ready for hooks)
├── pages/                    # (Ready for pages)
└── components/            # (Ready for components)
```

---

## Next Phase

**Phase 021 - Enterprise HR, Payroll & Leave Management System**

- Employee Payroll
- Salary Structure
- Bonus & Overtime
- Leave Management
- Provident Fund

---

## Status

✅ Phase 020 Complete
