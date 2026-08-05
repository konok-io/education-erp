# Phase 010 - Enterprise User Management System

## Overview

This phase implements the complete Enterprise User Management System for the Education ERP. It provides CRUD operations, role assignment, permission management, search, filter, import/export, and activity tracking.

---

## Completed Tasks

### 1. User Model
- Complete user model with all relationships
- Scope for excluding super admin
- Role and campus relationships
- Activity logging support
- Login history relationship

### 2. User Controller
- `UserController.php` - All CRUD operations
- Avatar management
- Password management
- Status management
- Role assignment
- Import/Export
- Activity and login history

### 3. User Service
- `UserService.php` - Business logic
- Search functionality
- Bulk operations
- Export/Import handling

### 4. User Repository
- `UserRepository.php` - Data access layer
- UUID based queries
- Email/Mobile existence checks

### 5. Validation Requests
- `StoreUserRequest.php` - Create validation
- `UpdateUserRequest.php` - Update validation
- `ImportUserRequest.php` - Import validation

### 6. React Structure
- `userApi.ts` - API client
- `useUsers.ts` - Custom hook
- Page and component structure ready

---

## User Types

| Type | Description | Permissions |
|------|-------------|-------------|
| Admin | System administrator | Full access |
| Teacher | Academic staff | Limited access |
| Student | Enrolled students | Portal access |
| Staff | Non-teaching staff | Department access |
| Accountant | Finance staff | Financial access |

---

## User Status

| Status | Can Login | Description |
|--------|----------|-------------|
| active | ✅ | Active user |
| inactive | ❌ | Temporarily inactive |
| blocked | ❌ | Blocked by admin |
| suspended | ❌ | Suspended account |
| pending | ❌ | Awaiting activation |

---

## API Endpoints

### List Users
```
GET /api/v1/users
```

### Search Users
```
GET /api/v1/users/search?q={query}
```

### Create User
```
POST /api/v1/users
```

### View User
```
GET /api/v1/users/{uuid}
```

### Update User
```
PUT /api/v1/users/{uuid}
```

### Delete User
```
DELETE /api/v1/users/{uuid}
```

### Update Avatar
```
POST /api/v1/users/{uuid}/avatar
```

### Change Password (Admin)
```
POST /api/v1/users/{uuid}/password
```

### Update Status
```
POST /api/v1/users/{uuid}/status
```

### Assign Role
```
POST /api/v1/users/{uuid}/role
```

### Export Users
```
GET /api/v1/users/export?format=excel
```

### Import Users
```
POST /api/v1/users/import
```

### Bulk Update Status
```
PUT /api/v1/users/bulk-status
```

### Get Activities
```
GET /api/v1/users/{uuid}/activities
```

### Get Login History
```
GET /api/v1/users/{uuid}/login-history
```

---

## Permissions

| Permission | Description |
|------------|-------------|
| users.view | View user list and details |
| users.create | Create new users |
| users.edit | Edit existing users |
| users.delete | Delete users |
| users.export | Export user data |
| users.import | Import user data |
| users.assign.role | Assign roles to users |

---

## Validation Rules

### Create User
```php
[
    'name' => 'required|string|max:100',
    'email' => 'required|email|unique:users,email',
    'mobile' => 'nullable|string|unique:users,mobile',
    'password' => 'required|min:12|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/',
    'role_id' => 'required|exists:roles,id',
    'status' => 'in:active,inactive,blocked,suspended,pending',
]
```

### Update User
```php
[
    'name' => 'sometimes|string|max:100',
    'email' => 'sometimes|email|unique:users,email,uuid',
    'mobile' => 'nullable|string|unique:users,mobile,uuid',
    'password' => 'nullable|min:12|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/',
    'role_id' => 'sometimes|exists:roles,id',
    'status' => 'in:active,inactive,blocked,suspended,pending',
]
```

---

## Avatar Upload

- **Supported formats:** jpg, jpeg, png, webp
- **Maximum size:** 2MB
- **Storage path:** storage/app/public/users/
- **Dimensions:** 300x300 (resized)

---

## Super Admin Protection

The super admin (`admin@konok.io`) is:
- ❌ Never shown in user lists
- ❌ Never editable
- ❌ Never deletable
- ❌ Never changeable status
- ❌ Never exportable
- ❌ Never importable
- ❌ Never appears in search results

---

## React Structure

```
frontend/src/features/user-management/
├── services/
│   └── userApi.ts           # API client
├── hooks/
│   └── useUsers.ts          # Custom hook
├── pages/
│   └── (to be created)      # Page components
├── components/
│   └── (to be created)      # UI components
└── validation/
    └── (to be created)      # Form validation
```

---

## Search Capabilities

| Field | Type |
|-------|------|
| name | Like |
| email | Like |
| mobile | Like |
| uuid | Like |
| role | Exact |
| status | Exact |

---

## Filter Options

| Filter | Type |
|--------|------|
| role | String |
| status | String |
| campus_id | Integer |
| created_at | Date Range |

---

## Sorting Options

| Field | Direction |
|-------|-----------|
| created_at | asc, desc |
| name | asc, desc |
| email | asc, desc |
| status | asc, desc |

---

## Pagination

- Default: 20 items per page
- Options: 20, 50, 100, 200

---

## Next Phase

**Phase 011 - Enterprise Academic Master Data System**

- Academic Levels
- Faculties & Departments
- Programs
- Academic Sessions
- Groups & Sections
- Subjects
- Grading Rules
- GPA/CGPA Configuration

---

## Status

✅ Phase 010 Complete
