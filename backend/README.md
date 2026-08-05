# Education ERP Backend

Laravel 12 REST API for Education ERP & CMS Platform.

## Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js LTS (for frontend assets)

## Installation

```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Create database (run in MySQL first)
mysql -u root -p -e "CREATE DATABASE education_erp"

# Start development server
php artisan serve
```

## API Endpoints

- `GET /api/health` - Health check endpoint
- `GET /` - Welcome message

## Documentation

See the main project documentation for more details.
