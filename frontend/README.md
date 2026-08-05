# Education ERP Frontend

React 18 + TypeScript frontend for Education ERP & CMS Platform.

## Tech Stack

- **React 18** - UI Library
- **TypeScript** - Type Safety
- **Vite** - Build Tool
- **React Router v6** - Routing
- **TanStack Query** - Data Fetching
- **Zustand** - State Management
- **React Hook Form + Zod** - Form Handling & Validation
- **Tailwind CSS v4** - Styling

## Getting Started

```bash
# Install dependencies
npm install

# Copy environment file
cp .env.example .env

# Start development server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview
```

## Project Structure

```
src/
├── api/           # API client configuration
├── assets/        # Static assets
├── components/    # Reusable UI components
├── features/      # Feature-based modules
├── hooks/         # Custom React hooks
├── layouts/       # Page layouts
├── pages/         # Page components
├── router/        # Route definitions
├── services/      # Business logic services
├── store/         # Zustand state stores
├── styles/        # Global styles
├── types/         # TypeScript type definitions
└── utils/         # Utility functions
```

## Scripts

- `npm run dev` - Start development server
- `npm run build` - Build for production
- `npm run preview` - Preview production build
- `npm run lint` - Run ESLint
- `npm run typecheck` - Run TypeScript type checking

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `VITE_API_URL` | Backend API URL | `http://localhost:8000` |

## Documentation

See the main project documentation for more details.
