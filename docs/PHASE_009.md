# Phase 009 - Enterprise Authentication & Authorization System

## Overview

This phase implements the complete Enterprise Authentication and Authorization system using Laravel Sanctum and Spatie Permission.

---

## Completed Tasks

### 1. Authentication Controller
- `AuthController.php` - All authentication endpoints

### 2. Authentication Service
- `AuthService.php` - Business logic for authentication

### 3. Authentication Requests
- `LoginRequest.php` - Login validation
- `ChangePasswordRequest.php` - Password change validation
- `ForgotPasswordRequest.php` - Password reset request
- `ResetPasswordRequest.php` - Password reset confirmation

### 4. Authentication Resources
- `UserResource.php` - User API resource

### 5. Authentication Models
- `LoginHistory.php` - Login activity tracking

### 6. React Authentication Structure
- `authApi.ts` - API client
- `AuthContext.tsx` - Authentication context
- `useAuth.ts` - Authentication hook
- `ProtectedRoute.tsx` - Route protection
- `LoginPage.tsx` - Login page template

---

## Authentication APIs

### Public Routes

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/auth/login` | User login |
| POST | `/api/v1/auth/forgot-password` | Request password reset |
| POST | `/api/v1/auth/reset-password` | Reset password with token |

### Protected Routes (Requires Authentication)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/auth/logout` | Logout user |
| POST | `/api/v1/auth/refresh` | Refresh token |
| GET | `/api/v1/auth/me` | Get current user |
| POST | `/api/v1/auth/change-password` | Change password |
| GET | `/api/v1/auth/history` | Get login history |
| GET | `/api/v1/auth/sessions` | Get active sessions |
| DELETE | `/api/v1/auth/sessions/{id}` | Logout specific session |
| DELETE | `/api/v1/auth/sessions` | Logout all devices |

---

## Super Admin Configuration

| Field | Value |
|-------|-------|
| Email | `admin@konok.io` |
| Password | `@rsm@k@1A` |

### Protection Rules
- Never appears in user lists
- Cannot be deleted
- Cannot be edited
- Cannot change role
- Always hidden from queries
- Bypasses all permission checks via `Gate::before()`

---

## Gate Before Implementation

```php
// app/Providers/AuthServiceProvider.php
Gate::before(function ($user, $ability) {
    if ($user->email === config('auth.super_admin_email')) {
        return true;
    }
});
```

---

## Password Rules

- Minimum 12 characters
- Must contain uppercase (A-Z)
- Must contain lowercase (a-z)
- Must contain number (0-9)
- Must contain special character (@$!%*?&)
- Uses Argon2id hashing

---

## Token Management

- Each device gets separate token
- Tokens stored in `personal_access_tokens` table
- Token expiration configurable
- Multiple device login supported
- Session tracking via `login_histories` table

---

## Rate Limiting

- 5 login attempts per minute
- 15 minute lockout after failures
- Configurable via `throttle:5,1` middleware

---

## Login History Tracking

```sql
login_histories
├── uuid
├── user_id
├── device_name
├── browser
├── platform
├── ip_address
├── login_at
├── logout_at
└── status
```

---

## React Authentication Structure

```
frontend/src/features/authentication/
├── api/
│   └── authApi.ts          # API client
├── components/
│   └── ProtectedRoute.tsx  # Route protection
├── context/
│   └── AuthContext.tsx     # Auth state management
├── hooks/
│   └── useAuth.ts          # Auth hook
└── pages/
    └── LoginPage.tsx       # Login page
```

---

## API Response Format

### Success Response
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": { ... },
    "token": "1|abc...",
    "token_type": "Bearer",
    "expires_at": "2024-01-01T00:00:00Z"
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Invalid credentials",
  "errors": {}
}
```

---

## Configuration

### Environment Variables
```env
SUPER_ADMIN_EMAIL=admin@konok.io
SUPER_ADMIN_PASSWORD=@rsm@k@1A
AUTH_PASSWORD_RESET_LINK_EXPIRE=30
```

### Auth Config
```php
// config/auth.php
'super_admin_email' => env('SUPER_ADMIN_EMAIL', 'admin@konok.io'),
```

---

## Next Phase

**Phase 010 - Enterprise User Management System**

- User CRUD Operations
- Role Assignment
- Permission Management
- Staff User Management
- Teacher User Management
- Student User Management
- User Profile Management
- User Activity Tracking

---

## Status

✅ Phase 009 Complete
