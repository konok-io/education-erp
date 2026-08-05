# Education ERP Architecture

## System Overview

```
                    React Monorepo
                    ┌───────────┼───────────┐
                    │           │           │
                   Web      Electron    Android
                    │           │           │
                    └───────────┼───────────┘
                                 │
                      REST API (Laravel 12)
                                 │
                         Service Layer
                                 │
                       Repository Layer
                                 │
                      MySQL Database
                                 │
              Optional SQLite Offline Sync
```

## Technology Stack

### Backend
- PHP 8.4+
- Laravel 12
- MySQL 8.0
- Sanctum (API Authentication)
- Redis (Cache/Sessions)

### Frontend
- React 19
- TypeScript (Strict Mode)
- Vite
- Tailwind CSS v4
- TanStack Query
- Zustand

### Desktop
- Electron

### Mobile
- Capacitor (Android)

## Directory Structure

```
education-erp/
├── backend/              # Laravel REST API
├── frontend/            # React Web Application
├── desktop/            # Electron Desktop App
├── android/            # Capacitor Android App
├── shared/             # Shared Types & Constants
├── docs/               # Documentation
├── scripts/            # Build & Utility Scripts
└── storage/            # Storage Directories
```

---

## Backend Architecture

```
backend/
├── app/
│   ├── Console/         # Artisan Commands
│   ├── Exceptions/      # Custom Exceptions
│   ├── Helpers/         # Helper Classes
│   ├── Http/
│   │   ├── Controllers/ # API Controllers
│   │   ├── Middleware/  # HTTP Middleware
│   │   ├── Requests/    # Form Request Validation
│   │   └── Resources/  # API Resources
│   ├── Models/          # Eloquent Models
│   ├── Policies/        # Authorization Policies
│   ├── Providers/       # Service Providers
│   ├── Repositories/
│   │   └── Contracts/  # Repository Interfaces
│   ├── Services/        # Business Logic Layer
│   ├── Traits/         # Reusable Traits
│   ├── Enums/          # Enum Classes
│   ├── DTO/             # Data Transfer Objects
│   └── ...
├── config/             # Laravel Configuration
├── database/           # Migrations, Seeders, Factories
├── routes/api/v1/      # API Routes (Versioned)
└── storage/            # Storage Files
```

### Dependency Flow

```
Controller → Service → Repository → Database
     ↓
  Request (Validation)
     ↓
  Resource (Response)
```

---

## Frontend Architecture

```
frontend/src/
├── api/                # API Client Configuration
├── assets/             # Static Assets
├── components/         # Reusable Components
│   ├── buttons/
│   ├── cards/
│   ├── forms/
│   ├── tables/
│   └── ...
├── features/           # Feature Modules
│   ├── authentication/
│   ├── dashboard/
│   ├── student/
│   └── ...
├── hooks/              # Custom React Hooks
├── layouts/            # Page Layouts
├── pages/              # Page Components
├── router/             # Route Definitions
├── services/           # Business Logic
├── store/              # Zustand State
├── types/              # TypeScript Types
└── utils/              # Utility Functions
```

### Feature Module Structure

```
features/student/
├── api/                # Student API calls
├── components/         # Student-specific components
├── hooks/              # Student-specific hooks
├── types/              # Student types
└── utils/              # Student utilities
```

---

## API Version Strategy

```
/api/v1/*  - Current stable version
/api/v2/*  - Future versions
```

All APIs must be versioned to ensure backward compatibility.

### API Response Format

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {},
  "meta": {
    "pagination": {}
  }
}
```

---

## Shared Package

The `shared/` package contains types and utilities shared across all platforms.

```
shared/
├── types/          # Shared TypeScript types
├── constants/      # Shared constants
├── api/            # API client
├── utils/          # Utility functions
└── validation/     # Validation schemas
```

---

## Database Design Principles

1. Use UUID for primary keys
2. Soft deletes for all tables
3. Audit columns (created_by, updated_by)
4. Multi-tenancy support ready
5. Indexed foreign keys
6. Timestamps on all tables

---

## Security Considerations

1. CORS configuration
2. Rate limiting
3. API authentication (Sanctum)
4. Role-based access control
5. Input validation
6. SQL injection prevention
7. XSS protection

---

## Performance Guidelines

1. Database indexing
2. Query optimization
3. Caching strategies
4. Lazy loading
5. Code splitting
6. CDN for static assets
