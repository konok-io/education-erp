# Phase 008 - Core System Foundation

## Overview

This phase establishes the Laravel backend core foundation for the Education ERP system. It includes all base classes, traits, helpers, middleware, and configurations needed for the entire application.

---

## Completed Tasks

### 1. Core Traits

| Trait | Purpose |
|-------|---------|
| `HasUuid` | Auto-generate UUID on model creation |
| `ApiResponse` | Standardized API response methods |
| `ActivityLogger` | Spatie activity logging integration |
| `HasStatus` | Status filtering and checking methods |

### 2. Base Classes

| Class | Path | Purpose |
|-------|------|---------|
| `BaseController` | `app/Http/Controllers/` | Generic controller with API response |
| `BaseRepository` | `app/Repositories/` | Generic repository pattern |
| `BaseService` | `app/Services/` | Generic service layer |
| `BaseRequest` | `app/Http/Requests/` | Generic form request validation |
| `BaseResource` | `app/Http/Resources/` | Generic API resource |

### 3. Middleware

| Middleware | Purpose |
|------------|---------|
| `CheckCampus` | Verify user has campus assigned |
| `CheckAcademicSession` | Ensure active academic session exists |
| `CheckUserStatus` | Verify user account is active |
| `EnsureSuperAdmin` | Restrict access to super admin only |

### 4. Enums

| Enum | Values |
|------|--------|
| `RoleType` | super-admin, admin, teacher, student, accountant, librarian, etc. |
| `Status` | active, inactive, pending, suspended, deleted |
| `Gender` | male, female, other |
| `AttendanceStatus` | present, absent, late, excused |
| `PaymentStatus` | pending, partial, paid, waiver, cancelled, refunded |
| `AdmissionStatus` | pending, approved, rejected, active, inactive, alumni, transferred |
| `ResultStatus` | draft, published, archived |

### 5. Helper Classes

| Helper | Purpose |
|--------|---------|
| `DateHelper` | Date formatting, Bangla date conversion |
| `FileHelper` | File upload, delete, copy operations |
| `ImageHelper` | Image resize, thumbnail, watermark |
| `NumberHelper` | Currency, percentage, grade calculations |
| `helpers.php` | Global helper functions |

---

## Required Packages

The following packages should be installed via Composer:

```bash
composer require laravel/sanctum
composer require spatie/laravel-permission
composer require ramsey/uuid
composer require intervention/image
composer require barryvdh/laravel-debugbar --dev
composer require spatie/laravel-activitylog
composer require maatwebsite/excel
composer require laravel/telescope --dev
```

### Publishing Configurations

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider"
```

---

## Configuration Files

### auth.php
```php
'super_admin_email' => env('SUPER_ADMIN_EMAIL', 'admin@konok.io'),
```

### sanctum.php
- API token lifetime configuration
- Token expiration settings

### permission.php
- Role and permission cache settings

---

## Global Helper Functions

| Function | Description |
|----------|-------------|
| `generate_uuid()` | Generate UUID v4 |
| `current_campus_id()` | Get current user's campus ID |
| `current_session_id()` | Get active academic session ID |
| `is_super_admin()` | Check if current user is super admin |
| `is_admin()` | Check if current user is admin |
| `format_date()` | Format date to specific pattern |
| `format_currency()` | Format currency with symbol |
| `setting()` | Get app setting value |

---

## API Response Format

All API responses follow this structure:

### Success Response
```json
{
    "success": true,
    "message": "Operation successful",
    "data": { ... }
}
```

### Paginated Response
```json
{
    "success": true,
    "message": "Data retrieved",
    "data": [ ... ],
    "meta": {
        "current_page": 1,
        "last_page": 10,
        "per_page": 15,
        "total": 150
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

---

## Middleware Stack

The application uses the following middleware stack:

```php
// API middleware stack
'sanitize' => \App\Http\Middleware\SanitizeInput::class,
'throttle:api' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
'campus' => \App\Http\Middleware\CheckCampus::class,
'session' => \App\Http\Middleware\CheckAcademicSession::class,
'status' => \App\Http\Middleware\CheckUserStatus::class,
'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
```

---

## Folder Structure

```
backend/
├── app/
│   ├── Enums/
│   │   ├── RoleType.php
│   │   ├── Status.php
│   │   ├── Gender.php
│   │   ├── AttendanceStatus.php
│   │   ├── PaymentStatus.php
│   │   ├── AdmissionStatus.php
│   │   └── ResultStatus.php
│   ├── Helpers/
│   │   ├── DateHelper.php
│   │   ├── FileHelper.php
│   │   ├── ImageHelper.php
│   │   ├── NumberHelper.php
│   │   └── helpers.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── BaseController.php
│   │   ├── Middleware/
│   │   │   ├── CheckCampus.php
│   │   │   ├── CheckAcademicSession.php
│   │   │   ├── CheckUserStatus.php
│   │   │   └── EnsureSuperAdmin.php
│   │   ├── Requests/
│   │   │   └── BaseRequest.php
│   │   └── Resources/
│   │       └── BaseResource.php
│   ├── Repositories/
│   │   └── BaseRepository.php
│   ├── Services/
│   │   └── BaseService.php
│   └── Traits/
│       ├── HasUuid.php
│       ├── ApiResponse.php
│       ├── ActivityLogger.php
│       └── HasStatus.php
```

---

## Super Admin Configuration

**Email:** `admin@konok.io`
**Password:** `@rsm@k@1A`

### Security Measures
- Never appears in user lists
- Cannot be deleted
- Cannot be edited
- Bypasses all permission checks via `Gate::before()`

### AuthServiceProvider Gate
```php
Gate::before(function ($user, $ability) {
    if ($user->email === config('auth.super_admin_email')) {
        return true;
    }
});
```

---

## Next Phase

**Phase 009 - Enterprise Authentication System**

- Multi Guard Authentication
- React Authentication Flow
- Sanctum Token Authentication
- Role & Permission Integration
- API Authentication Endpoints

---

## Status

✅ Phase 008 Complete
