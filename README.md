# Education ERP & CMS Platform

Enterprise-grade Education Resource Planning and Content Management System.

## Architecture

```
React Monorepo
├── Web (React + TypeScript)
├── Desktop (Electron)
└── Android (Capacitor)

Laravel 12 REST API

MySQL 8
```

## Project Structure

```
education-erp/
├── backend/       # Laravel 12 REST API
├── frontend/     # React 19 + TypeScript
├── desktop/      # Electron Desktop App
├── android/      # Android (Capacitor)
├── shared/       # Shared types/configs
├── docs/         # Documentation
└── scripts/      # Build/utility scripts
```

## Technology Stack

### Backend
- **PHP 8.4** - Server-side language
- **Laravel 12** - PHP Framework
- **MySQL 8** - Primary database
- **Sanctum** - API Authentication

### Frontend
- **React 19** - UI Library
- **TypeScript** - Type Safety
- **Vite** - Build Tool
- **Tailwind CSS v4** - Styling
- **TanStack Query** - Data Fetching
- **Zustand** - State Management

## Getting Started

### Prerequisites
- PHP 8.4+
- Composer
- Node.js LTS
- MySQL 8.0+
- Git

### Installation

```bash
# Clone the repository
git clone <repository-url>
cd education-erp

# Backend Setup
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

# Frontend Setup
cd ../frontend
npm install
cp .env.example .env
npm run dev
```

## Development Standards

- **ESLint** - Code linting
- **Prettier** - Code formatting
- **Husky** - Git hooks
- **lint-staged** - Pre-commit linting

### Code Quality Commands

```bash
# Frontend
npm run lint          # Run ESLint
npm run lint:fix      # Fix ESLint issues
npm run format        # Format with Prettier
npm run typecheck     # TypeScript check
npm run build         # Production build
```

## Documentation

- [Coding Standards](docs/CODING_STANDARDS.md)
- [Git Workflow](docs/GIT_WORKFLOW.md)
- [Commit Convention](docs/COMMIT_CONVENTION.md)
- [Environment Variables](docs/ENVIRONMENT.md)

## Git Workflow

```
main ───────────── Production
  │
develop ────────── Development
  │
feature/* ──────── Features
  │
hotfix/* ───────── Production Fixes
```

## Version

1.0.0 (Development)

## License

MIT License
