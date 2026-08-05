# Phase 019 - Enterprise Fee, Billing & Payment Management System

## Overview

This phase implements the complete Enterprise Fee, Billing & Payment Management System for the Education ERP. This module handles all financial operations including fee collection, invoicing, payments, and financial reporting.

---

## Payment Architecture

```
Academic Session
    ↓
Fee Structure
    ↓
Invoice Generation
    ↓
Payment Collection
    ↓
Receipt Generation
    ↓
Ledger
    ↓
Financial Reports
```

---

## Completed Tasks

### Models (8 models)

| Model | Description |
|-------|-------------|
| FeeCategory | Fee categories |
| FeeStructure | Fee structures |
| Invoice | Student invoices |
| Payment | Payment records |
| Waiver | Fee waivers |
| Installment | Installment plans |
| Refund | Refund requests |
| Fine | Fine management |

### Controller
- `PaymentController.php` - Complete CRUD and operations

### Service
- `PaymentService.php` - All business logic

### API Routes
- Complete REST API (30+ endpoints)

### React Structure
- `types/` - TypeScript interfaces
- `services/` - API client

---

## Fee Categories

| Type | Description |
|------|-------------|
| admission | Admission Fee |
| registration | Registration Fee |
| tuition | Tuition Fee |
| exam | Exam Fee |
| library | Library Fee |
| laboratory | Laboratory Fee |
| sports | Sports Fee |
| transport | Transport Fee |
| hostel | Hostel Fee |
| certificate | Certificate Fee |
| development | Development Fee |
| fine | Fine |
| miscellaneous | Miscellaneous |

---

## Invoice Status

| Status | Description |
|--------|-------------|
| draft | Draft |
| pending | Pending |
| partial | Partial |
| paid | Paid |
| overdue | Overdue |
| cancelled | Cancelled |

---

## Payment Methods

| Method | Description |
|--------|-------------|
| cash | Cash |
| bank | Bank Transfer |
| cheque | Cheque |
| bkash | bKash |
| nagad | Nagad |
| rocket | Rocket |
| sslcommerz | SSLCommerz |
| stripe | Stripe |
| paypal | PayPal |

---

## Fee Frequency

| Frequency | Description |
|-----------|-------------|
| one_time | One Time |
| monthly | Monthly |
| quarterly | Quarterly |
| half_yearly | Half Yearly |
| yearly | Yearly |
| custom | Custom |

---

## API Endpoints

### Fee Categories
- `GET /api/v1/payments/categories` - List categories
- `POST /api/v1/payments/categories` - Create category

### Fee Structure
- `GET /api/v1/payments/structures` - List structures
- `POST /api/v1/payments/structures` - Create structure
- `PUT /api/v1/payments/structures/{id}` - Update structure

### Invoices
- `GET /api/v1/payments/invoices` - List invoices
- `POST /api/v1/payments/invoices` - Create invoice
- `GET /api/v1/payments/invoices/{id}` - View invoice
- `PUT /api/v1/payments/invoices/{id}` - Update invoice
- `DELETE /api/v1/payments/invoices/{id}` - Delete invoice
- `POST /api/v1/payments/invoices/generate` - Generate bulk invoices

### Payments
- `GET /api/v1/payments` - List payments
- `POST /api/v1/payments` - Collect payment
- `PUT /api/v1/payments/{id}/verify` - Verify payment
- `GET /api/v1/payments/receipt/{id}` - Get receipt

### Waivers
- `POST /api/v1/payments/waivers` - Apply waiver

### Installments
- `POST /api/v1/payments/installments` - Create plan

### Refunds
- `POST /api/v1/payments/refunds` - Request refund
- `PUT /api/v1/payments/refunds/{id}` - Process refund

### Fines
- `POST /api/v1/payments/fines` - Create fine

### Ledger
- `GET /api/v1/payments/ledger` - Get student ledger

### Reports
- `GET /api/v1/payments/reports/collection` - Collection report
- `GET /api/v1/payments/reports/due` - Due report
- `GET /api/v1/payments/reports/dashboard` - Dashboard

### Export
- `GET /api/v1/payments/export` - Export payments

---

## Key Features

✅ Dynamic Fee Categories
✅ Fee Structure Management
✅ Invoice Generation
✅ Student Billing
✅ Payment Collection (Online & Offline)
✅ Partial Payment Support
✅ Installment Plans
✅ Scholarship & Waiver
✅ Fine Management
✅ Refund Processing
✅ Receipt Generation
✅ Student Ledger
✅ Financial Reports
✅ Collection Report
✅ Due Report
✅ Dashboard Statistics
✅ Soft delete
✅ UUID-based public API

---

## Dashboard Statistics

```json
{
  "today_collection": 50000,
  "month_collection": 500000,
  "total_due": 1200000,
  "pending_invoices": 150,
  "overdue_invoices": 45
}
```

---

## Receipt Format

```json
{
  "receipt_no": "RCP-20260805-0001",
  "payment_no": "PAY-20260805-0001",
  "date": "2026-08-05 14:30",
  "student": {
    "name": "John Doe",
    "student_no": "STU-2026-000001"
  },
  "invoice": { "no": "INV-202608-00001" },
  "amount": 5000,
  "amount_in_words": "Five Taka Only",
  "payment_method": "bkash",
  "transaction_id": "TRX123456",
  "collected_by": "Admin"
}
```

---

## Ledger Entry

```json
{
  "date": "2026-08-01",
  "type": "invoice",
  "description": "Invoice #INV-202608-00001",
  "amount": 5000,
  "balance": 5000
}
```

---

## Permissions

| Permission | Description |
|------------|-------------|
| payment.view | View payments |
| payment.create | Create invoices |
| payment.update | Update invoices |
| payment.delete | Delete invoices |
| payment.collect | Collect payments |
| payment.refund | Process refunds |
| payment.waiver | Apply waivers |
| payment.scholarship | Manage scholarships |
| payment.report | View reports |
| payment.export | Export data |

---

## React Structure

```
frontend/src/features/payments/
├── types/
│   └── index.ts              # TypeScript types
├── services/
│   └── paymentApi.ts         # API client
├── hooks/                    # (Ready for hooks)
├── pages/                    # (Ready for pages)
└── components/            # (Ready for components)
```

---

## Next Phase

**Phase 020 - Enterprise Accounting & Finance Management System**

- Chart of Accounts
- General Ledger
- Journal Entries
- Financial Statements

---

## Status

✅ Phase 019 Complete
