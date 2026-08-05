# Phase 018 - Enterprise Online Admission Management System

## Overview

This phase implements the complete Enterprise Online Admission Management System for the Education ERP. This module handles the entire admission process from campaign creation to student registration.

---

## Admission Architecture

```
Admission Campaign
    ↓
Online Application
    ↓
Document Upload
    ↓
Eligibility Check
    ↓
Payment (bKash, Nagad, Rocket, SSLCommerz)
    ↓
Admission Test / Interview
    ↓
Merit List
    ↓
Waiting List
    ↓
Approval
    ↓
Student Auto-Registration
    ↓
User Account Creation
```

---

## Completed Tasks

### Models (5 models)

| Model | Description |
|-------|-------------|
| AdmissionCampaign | Admission campaigns |
| AdmissionApplication | Application records |
| AdmissionDocument | Uploaded documents |
| AdmissionPayment | Payment records |
| QuotaConfiguration | Quota settings |

### Controller
- `AdmissionController.php` - Complete CRUD and operations

### Service
- `AdmissionService.php` - All business logic

### API Routes
- Complete REST API (25+ endpoints)

### React Structure
- `types/` - TypeScript interfaces
- `services/` - API client

---

## Campaign Status

| Status | Description |
|--------|-------------|
| draft | Draft |
| open | Open for Application |
| closed | Closed |
| processing | Processing |
| published | Results Published |
| completed | Completed |
| archived | Archived |

---

## Application Status

| Status | Description |
|--------|-------------|
| draft | Draft |
| submitted | Submitted |
| pending_payment | Pending Payment |
| pending_document | Pending Document |
| document_verified | Document Verified |
| test_scheduled | Test Scheduled |
| test_completed | Test Completed |
| interview_scheduled | Interview Scheduled |
| merit | In Merit List |
| waiting | Waiting List |
| rejected | Rejected |
| approved | Approved |
| admitted | Admitted |
| cancelled | Cancelled |

---

## Quota System

| Quota | Description |
|-------|-------------|
| general | General |
| freedom_fighter | Freedom Fighter |
| tribal | Tribal |
| disabled | Disabled |
| women | Women |
| employee | Employee Children |

---

## Document Types

| Type | Description |
|------|-------------|
| photo | Applicant Photo |
| signature | Signature |
| ssc_certificate | SSC Certificate |
| ssc_marksheet | SSC Marksheet |
| hsc_certificate | HSC Certificate |
| hsc_marksheet | HSC Marksheet |
| birth_certificate | Birth Certificate |
| nid | National ID |
| quota_certificate | Quota Certificate |

---

## Payment Methods

| Method | Description |
|--------|-------------|
| bkash | bKash |
| nagad | Nagad |
| rocket | Rocket |
| sslcommerz | SSLCommerz |
| cash | Cash |
| bank | Bank Transfer |

---

## API Endpoints

### Campaigns
- `GET /api/v1/admissions/campaigns` - List campaigns
- `POST /api/v1/admissions/campaigns` - Create campaign
- `PUT /api/v1/admissions/campaigns/{id}` - Update campaign
- `POST /api/v1/admissions/campaigns/{id}/toggle` - Toggle active

### Applications
- `GET /api/v1/admissions` - List applications
- `POST /api/v1/admissions` - Create application
- `GET /api/v1/admissions/{id}` - View application
- `PUT /api/v1/admissions/{id}` - Update application
- `POST /api/v1/admissions/{id}/submit` - Submit application

### Documents
- `POST /api/v1/admissions/documents` - Upload document
- `PUT /api/v1/admissions/documents/{id}/verify` - Verify document

### Payments
- `POST /api/v1/admissions/payment` - Initiate payment
- `PUT /api/v1/admissions/payment/{id}/verify` - Verify payment

### Merit & Approval
- `POST /api/v1/admissions/merit` - Generate merit list
- `PUT /api/v1/admissions/{id}/merit` - Update merit position
- `POST /api/v1/admissions/{id}/approve` - Approve application
- `POST /api/v1/admissions/{id}/reject` - Reject application

### Interview
- `POST /api/v1/admissions/{id}/interview` - Schedule interview

### Dashboard
- `GET /api/v1/admissions/dashboard/stats` - Dashboard stats
- `GET /api/v1/admissions/dashboard/applicant/{no}` - Applicant dashboard

### Reports & Export
- `GET /api/v1/admissions/report` - Get report
- `GET /api/v1/admissions/export` - Export applications

---

## Key Features

✅ Admission Campaign Management
✅ Admission Circular
✅ Online Application Form
✅ Document Upload (Photo, Certificate, NID)
✅ Eligibility Rules Engine
✅ Quota System
✅ Payment Gateway Integration (Ready)
✅ Merit List Generation
✅ Waiting List Management
✅ Interview Scheduling
✅ Student Auto-Registration
✅ Auto User Account Creation
✅ SMS Notification Ready
✅ Email Notification Ready
✅ Applicant Dashboard
✅ Payment Verification
✅ Soft delete
✅ UUID-based public API

---

## Merit List Generation

```json
{
  "position": 1,
  "application_no": "APP-2026-000001",
  "name": "John Doe",
  "gpa": 5.00,
  "status": "merit"
}
```

---

## Applicant Dashboard

```json
{
  "application": {
    "no": "APP-2026-000001",
    "status": "merit",
    "payment_status": "paid",
    "merit_position": 15
  },
  "campaign": {
    "title": "HSC Admission 2026",
    "application_fee": 500
  },
  "documents": [...],
  "payments": [...]
}
```

---

## Student Auto-Creation

After approval, the system automatically:
1. Creates student record with student number
2. Creates student profile
3. Creates user account
4. Assigns to academic session and class

---

## Permissions

| Permission | Description |
|------------|-------------|
| admission.view | View applications |
| admission.create | Create campaigns |
| admission.update | Update applications |
| admission.delete | Delete applications |
| admission.verify | Verify documents |
| admission.approve | Approve applications |
| admission.publish | Publish results |
| admission.payment | Process payments |
| admission.import | Import data |
| admission.export | Export data |

---

## React Structure

```
frontend/src/features/admission/
├── types/
│   └── index.ts              # TypeScript types
├── services/
│   └── admissionApi.ts       # API client
├── hooks/                    # (Ready for hooks)
├── pages/                    # (Ready for pages)
└── components/             # (Ready for components)
```

---

## Next Phase

**Phase 019 - Enterprise Fee, Billing & Payment Management System**

- Dynamic Fee Structure
- Invoice Generation
- Student Billing
- Payment Integration
- Scholarship & Waiver
- Financial Reports

---

## Status

✅ Phase 018 Complete
