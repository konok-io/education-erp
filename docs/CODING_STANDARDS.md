# Coding Standards

## Overview

This document defines the coding standards for the Education ERP project.

## General Rules

### Code Quality
- Never duplicate code - use functions/components
- Never write business logic in controllers
- Never write SQL in controllers
- Always use Service Layer pattern
- Always validate Request
- Always use Resource for API responses
- Never use Global Variables
- No hardcoded values - use config/env

### File Organization
```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   ├── Requests/        # Form Request validation
│   │   └── Resources/      # API Resources
│   ├── Models/
│   ├── Services/           # Business logic
│   ├── Repositories/       # Data access layer
│   └── Traits/

frontend/
src/
├── api/
├── components/            # Reusable UI components
├── features/              # Feature-based modules
├── hooks/                 # Custom React hooks
├── layouts/
├── pages/                 # Page components
├── services/              # Business logic
├── store/                 # State management
├── types/                 # TypeScript types
└── utils/                 # Utility functions
```

---

## Frontend Standards (React 19 + TypeScript)

### Components
- Functional components only (no class components)
- Use React Hooks
- Component Driven Development
- Props interfaces required
- Extract reusable components

### Naming Conventions

| Type | Convention | Example |
|------|------------|---------|
| Component | PascalCase | `StudentCard.tsx` |
| Hook | camelCase + use | `useAuth.ts` |
| Utility | camelCase | `formatDate.ts` |
| Type/Interface | PascalCase | `UserType.ts` |
| Constant | UPPER_SNAKE | `MAX_RETRY_COUNT` |
| CSS Classes | kebab-case | `student-card` |

### File Naming
- Components: `PascalCase.tsx`
- Hooks: `camelCase.ts`
- Utils: `camelCase.ts`
- Types: `camelCase.ts` or `PascalCase.ts`

### Folder Structure
```
components/
├── StudentCard/
│   ├── StudentCard.tsx
│   └── index.ts
├── Button/
│   ├── Button.tsx
│   └── index.ts
```

### Imports
```typescript
// Use path aliases
import { Button } from '@components/Button'
import { useAuth } from '@hooks/useAuth'
import type { User } from '@types/user'
```

### React Best Practices
```typescript
// ✅ Good
interface ButtonProps {
  children: React.ReactNode
  onClick?: () => void
  variant?: 'primary' | 'secondary'
}

export const Button = ({ children, onClick, variant = 'primary' }: ButtonProps) => {
  return (
    <button className={`btn btn-${variant}`} onClick={onClick}>
      {children}
    </button>
  )
}

// ❌ Bad
export const Button = ({ children, onClick, variant = 'primary' }: any) => {
  return <button className={`btn btn-${variant}`} onClick={onClick}>{children}</button>
}
```

### State Management
```typescript
// ✅ Use Zustand for global state
import { create } from 'zustand'

interface AuthStore {
  user: User | null
  login: (user: User) => void
  logout: () => void
}

export const useAuthStore = create<AuthStore>((set) => ({
  user: null,
  login: (user) => set({ user }),
  logout: () => set({ user: null }),
}))
```

### API Calls
```typescript
// ✅ Use React Query for server state
import { useQuery, useMutation } from '@tanstack/react-query'
import { apiClient } from '@api/client'

export const useStudents = () => {
  return useQuery({
    queryKey: ['students'],
    queryFn: () => apiClient.get('/api/v1/students'),
  })
}
```

---

## Backend Standards (PHP/Laravel)

### PHP Version
- PHP 8.4+
- Strict types enabled

### PSR Standards
- PSR-12 for code style
- PSR-4 for autoloading
- PSR-7 for HTTP messages

### File Headers
```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
```

### Naming Conventions

| Type | Convention | Example |
|------|------------|---------|
| Controller | PascalCase | `StudentController.php` |
| Model | PascalCase | `Student.php` |
| Service | PascalCase | `StudentService.php` |
| Repository | PascalCase | `StudentRepository.php` |
| Request | PascalCase | `StoreStudentRequest.php` |
| Resource | PascalCase | `StudentResource.php` |
| Migration | snake_case | `create_students_table.php` |
| Method | camelCase | `getStudentById()` |

### Service Layer Pattern
```php
// ✅ Controller delegates to Service
class StudentController extends Controller
{
    public function __construct(
        private readonly StudentService $studentService
    ) {}

    public function index(): JsonResponse
    {
        $students = $this->studentService->getAllStudents();
        return StudentResource::collection($students);
    }
}

// ✅ Service contains business logic
class StudentService
{
    public function getAllStudents(): Collection
    {
        return $this->repository->all();
    }
}
```

### Form Request Validation
```php
// ✅ Use Form Request
class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:students'],
        ];
    }
}
```

### API Resources
```php
// ✅ Use API Resources
class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
```

---

## CSS/Tailwind Standards

### Utility Classes
- Use Tailwind utility classes
- No inline styles
- No custom CSS files (except index.css)

### Class Organization
```tsx
// ✅ Group related classes
<button
  className="
    px-4 py-2           // Padding
    bg-blue-500         // Background
    text-white          // Text
    font-medium         // Font
    rounded-md          // Border radius
    hover:bg-blue-600   // Hover state
    disabled:opacity-50 // Disabled state
  "
>
  Submit
</button>
```

---

## Documentation Rules

Every feature module must include:
1. README.md
2. Installation instructions
3. API documentation
4. Flow diagram
5. Database notes
6. Change log

---

## Testing Rules

### Backend (PHPUnit)
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=StudentTest
```

### Frontend (Vitest)
```bash
# Run all tests
npm run test

# Run with coverage
npm run test:coverage
```

---

## Environment Variables

### Rules
- Never hardcode sensitive values
- Use .env files
- Document all variables

### Required Variables
```
# Backend
APP_NAME
APP_ENV
APP_KEY
DB_CONNECTION
DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD

# Frontend
VITE_API_URL
```

### Document Variables
```markdown
| Variable | Type | Default | Description |
|----------|------|---------|-------------|
| VITE_API_URL | string | localhost:8000 | Backend API URL |
```
