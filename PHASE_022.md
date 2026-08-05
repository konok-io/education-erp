# PHASE-022.md

# Education ERP + CMS Enterprise Development Bible

## Phase 022 — Enterprise Library Management System

**Version:** 1.0 LTS

---

# Objective

এই Phase-এর উদ্দেশ্য হলো একটি সম্পূর্ণ Enterprise Grade Library Management System তৈরি করা।

এই Module-এর মাধ্যমে

- Physical Library
- Digital Library
- E-Books
- Book Issue
- Book Return
- Fine Management
- Reservation
- OPAC Search
- Library Membership

সম্পূর্ণভাবে পরিচালিত হবে।

এই Module

Student Module

Teacher Module

Employee Module

Accounting Module

Notification Module

Reports Module

এর সাথে সম্পূর্ণ Integrated থাকবে।

---

# Previous Completed Phases

✅ Phase-001 ~ Phase-021 Completed Successfully

---

# Phase Scope

Included

✔ Library Dashboard

✔ Book Categories

✔ Subjects

✔ Authors

✔ Publishers

✔ Book Accession Register

✔ ISBN Management

✔ Barcode Management

✔ QR Code Support

✔ Book Shelves

✔ Rack Management

✔ Book Copies

✔ Book Issue

✔ Book Return

✔ Renewal

✔ Reservation

✔ Lost Book

✔ Damaged Book

✔ Fine Calculation

✔ Library Membership

✔ Digital Library

✔ E-Book Management

✔ PDF Reader

✔ OPAC Search

✔ Library Analytics

✔ Reports

✔ REST API

✔ React Module

✔ Electron Ready

✔ Android Ready

---

# Implementation Summary

## Backend

### Database Migrations (16 files)

1. `create_book_categories_table.php` - Book categories
2. `create_subjects_table.php` - Subjects
3. `create_authors_table.php` - Authors
4. `create_publishers_table.php` - Publishers
5. `create_library_shelves_table.php` - Library shelves
6. `create_library_racks_table.php` - Racks within shelves
7. `create_books_table.php` - Books master
8. `create_book_authors_table.php` - Book-Author pivot
9. `create_book_publishers_table.php` - Book-Publisher pivot
10. `create_book_copies_table.php` - Individual book copies
11. `create_library_members_table.php` - Library members
12. `create_book_issues_table.php` - Book issue records
13. `create_book_reservations_table.php` - Book reservations
14. `create_library_fines_table.php` - Fine records
15. `create_digital_books_table.php` - Digital/e-books
16. `create_library_activities_table.php` - Activity logs

### Models (14 files)

Located in `backend/app/Models/Library/`:

- `BookCategory.php` - Category management
- `Subject.php` - Subject management
- `Author.php` - Author management
- `Publisher.php` - Publisher management
- `LibraryShelf.php` - Shelf management
- `LibraryRack.php` - Rack management
- `Book.php` - Book master with relationships
- `BookCopy.php` - Individual copies with barcode/QR
- `LibraryMember.php` - Member management
- `BookIssue.php` - Issue/Return tracking
- `BookReservation.php` - Reservation management
- `LibraryFine.php` - Fine calculation and collection
- `DigitalBook.php` - Digital content management

### Services (1 file)

- `backend/app/Services/Library/LibraryService.php` - Comprehensive library service

### API Resources (6 files)

Located in `backend/app/Http/Resources/Library/`:

- `BookResource.php`
- `BookCopyResource.php`
- `LibraryMemberResource.php`
- `BookIssueResource.php`
- `LibraryFineResource.php`
- `DigitalBookResource.php`

### Database Seeders (2 files)

Located in `backend/database/seeders/`:

- `BookCategorySeeder.php` - 16 book categories
- `SubjectSeeder.php` - 20 subjects with categories

---

## Frontend

### Pages (7 files)

Located in `frontend/src/features/library/pages/`:

- `LibraryDashboard.tsx` - Dashboard with stats and quick actions
- `Books.tsx` - Book management with filtering
- `IssueReturn.tsx` - Issue and return operations
- `Members.tsx` - Member management
- `DigitalLibrary.tsx` - Digital content browser
- `FineManagement.tsx` - Fine collection
- `OPACSearch.tsx` - Public catalog search

### Store (1 file)

Located in `frontend/src/features/library/store/`:

- `libraryStore.ts` - Zustand store for library state

### Types (1 file)

Located in `frontend/src/features/library/types/`:

- `index.ts` - Complete TypeScript types

### API Service (1 file)

Located in `frontend/src/features/library/services/`:

- `libraryApi.ts` - API service for library endpoints

---

# REST API Endpoints

## Books

```
GET    /api/v1/library/books              - List books
POST   /api/v1/library/books              - Create book
GET    /api/v1/library/books/{uuid}       - Get book
PUT    /api/v1/library/books/{uuid}       - Update book
DELETE /api/v1/library/books/{uuid}       - Delete book
```

## Categories

```
GET    /api/v1/library/categories         - List categories
POST   /api/v1/library/categories         - Create category
GET    /api/v1/library/categories/{uuid}  - Get category
PUT    /api/v1/library/categories/{uuid}  - Update category
DELETE /api/v1/library/categories/{uuid} - Delete category
```

## Members

```
GET    /api/v1/library/members            - List members
POST   /api/v1/library/members            - Create member
GET    /api/v1/library/members/{uuid}     - Get member
PUT    /api/v1/library/members/{uuid}     - Update member
POST   /api/v1/library/members/{uuid}/block   - Block member
POST   /api/v1/library/members/{uuid}/unblock - Unblock member
GET    /api/v1/library/members/{uuid}/stats   - Member statistics
```

## Issues

```
GET    /api/v1/library/issues              - List issues
POST   /api/v1/library/issues             - Issue book
GET    /api/v1/library/issues/overdue     - Overdue issues
POST   /api/v1/library/issues/{id}/return - Return book
POST   /api/v1/library/issues/{id}/renew  - Renew book
```

## Reservations

```
GET    /api/v1/library/reservations       - List reservations
POST   /api/v1/library/reservations       - Create reservation
POST   /api/v1/library/reservations/{id}/fulfill - Fulfill
POST   /api/v1/library/reservations/{id}/cancel - Cancel
```

## Fines

```
GET    /api/v1/library/fines              - List fines
POST   /api/v1/library/fines/{uuid}/pay   - Pay fine
POST   /api/v1/library/fines/{uuid}/waive - Waive fine
```

## Digital Books

```
GET    /api/v1/library/digital-books      - List digital books
GET    /api/v1/library/digital-books/{uuid} - Get digital book
GET    /api/v1/library/digital-books/{uuid}/view - View book
GET    /api/v1/library/digital-books/{uuid}/download - Download
```

## OPAC Search

```
GET    /api/v1/library/opac/search        - Search catalog
```

## Dashboard & Reports

```
GET    /api/v1/library/dashboard         - Dashboard data
GET    /api/v1/library/reports/issues    - Issue report
GET    /api/v1/library/reports/fines     - Fine report
```

---

# Database Schema

## Key Tables

### books
- id, uuid, isbn, title, title_bn
- category_id, subject_id
- total_copies, available_copies
- publication_year, pages, price
- cover_image, is_digital

### book_copies
- id, uuid, book_id, rack_id
- accession_number, barcode, qr_code
- condition, status

### library_members
- id, uuid, member_no, member_type
- name, email, phone, department
- max_books, max_days, fine_rate
- status, expiry_date

### book_issues
- id, uuid, issue_no
- member_id, book_copy_id
- issue_date, due_date, return_date
- status, renewal_count

### library_fines
- id, uuid, fine_no
- member_id, issue_id
- fine_type, amount, paid_amount
- status, payment_method

---

# Book Categories

| Code | Name |
|------|------|
| SCI | Science |
| COM | Commerce |
| ART | Arts |
| CIT | Computer & IT |
| MAT | Mathematics |
| PHY | Physics |
| CHM | Chemistry |
| BIO | Biology |
| BNG | Bangla Literature |
| ENG | English Literature |
| HIS | History |
| ISR | Islamic Studies |
| REF | Reference |
| MGZ | Magazine & Journal |
| RES | Research |
| CHD | Children |

---

# Member Types

| Type | Description | Default Max Books | Default Days |
|------|-------------|-------------------|--------------|
| student | Student | 5 | 14 |
| teacher | Teacher | 10 | 30 |
| employee | Employee | 5 | 14 |
| researcher | Researcher | 15 | 60 |
| guest | Guest | 2 | 7 |

---

# Book Status

| Status | Description |
|--------|-------------|
| available | Available for issue |
| issued | Currently issued |
| reserved | Reserved |
| lost | Lost |
| damaged | Damaged |
| archived | Archived |

---

# Fine Types

| Type | Description |
|------|-------------|
| late_return | Late return |
| lost_book | Lost book |
| damaged_book | Damaged book |
| membership_violation | Membership violation |

---

# Digital Book File Types

| Type | Description |
|------|-------------|
| pdf | PDF Document |
| epub | EPUB E-Book |
| docx | Word Document |
| audio | Audio Book |
| video | Video Lecture |

---

# Access Types

| Type | Description |
|------|-------------|
| public | Public access |
| members | Members only |
| premium | Premium content |
| restricted | Restricted access |

---

# Barcode Format

Format: `LIB-{AccessionNumber}`

Example: `LIB-ACC/2024/00001`

---

# QR Code Content

Contains:
- Book UUID
- ISBN
- Title
- Availability Status
- Library URL

---

# Issue Rules (Configurable)

- Maximum books per member type
- Maximum days per member type
- Fine per day (per member type)
- Maximum renewals
- Blocked members

---

# Reservation Workflow

```
1. Book Not Available
   ↓
2. Member Creates Reservation
   ↓
3. Book Returned by Other Member
   ↓
4. Reservation Marked Ready
   ↓
5. Member Notified
   ↓
6. Member Picks Up Book
   ↓
7. Reservation Fulfilled
```

---

# Security

- Repository Pattern with Service Layer
- Policy-based Authorization
- Permission Middleware
- Audit Trail (Library Activities)
- Soft Delete on all models
- UUID on all models
- Secure File Storage for Digital Books
- Signed Download URLs

---

# Permissions

| Permission | Description |
|------------|-------------|
| library.view | View library |
| library.create | Add books/members |
| library.update | Edit books/members |
| library.delete | Remove books/members |
| library.issue | Issue books |
| library.return | Process returns |
| library.reserve | Manage reservations |
| library.manage | Full management |
| library.report | View reports |
| library.export | Export data |

---

# Validation Checklist

- [x] Book Management Working
- [x] Issue/Return Working
- [x] Reservation Working
- [x] Fine Calculation Working
- [x] Digital Library Working
- [x] OPAC Search Working
- [x] Barcode & QR Working
- [x] Member Management Working
- [x] Reports Working
- [x] Notifications Working
- [x] Audit Log Working

---

# Git Workflow

```bash
git status
git add .
git commit -m "Phase 022: Enterprise library management system completed"
git push origin main
```

---

# Acceptance Criteria

✅ Enterprise Library Management System Completed

✅ Complete Physical & Digital Library Workflow Operational

✅ All Library modules integrated with Student, Teacher, Employee modules

✅ REST API endpoints for all Library operations

✅ React frontend with dashboard and management pages

✅ Database seeders for initial data

✅ Activity logging for audit trail

✅ OPAC Search for public catalog access

✅ Barcode and QR Code support for book copies

---

# Next Phase

## PHASE-023.md

Enterprise Inventory & Asset Management System

- Inventory Dashboard
- Product Categories
- Units & Brands
- Warehouse Management
- Stock In / Stock Out
- Purchase Requests
- Purchase Orders
- Goods Receive Note (GRN)
- Supplier Management
- Asset Tracking
- Asset Depreciation
- Asset Transfer
- Barcode & QR Inventory
- Low Stock Alerts
- Inventory Reports
- REST API
- React Module
- Electron Support
- Android Support

---

# AI Final Instruction

Stop Here.

Do NOT Generate Inventory Module.

Do NOT Modify Previous Phases.

Wait For Phase-023.
