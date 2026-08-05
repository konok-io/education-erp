# Environment Variables Guide

## Overview

All sensitive configuration must be stored in `.env` files. Never hardcode values.

## Backend (.env)

### Application
| Variable | Default | Description |
|----------|---------|-------------|
| `APP_NAME` | Education ERP | Application name |
| `APP_ENV` | local | Environment (local/production) |
| `APP_KEY` | - | Application encryption key |
| `APP_DEBUG` | true | Debug mode |
| `APP_URL` | localhost | Application URL |

### Database
| Variable | Default | Description |
|----------|---------|-------------|
| `DB_CONNECTION` | mysql | Database driver |
| `DB_HOST` | 127.0.0.1 | Database host |
| `DB_PORT` | 3306 | Database port |
| `DB_DATABASE` | education_erp | Database name |
| `DB_USERNAME` | root | Database username |
| `DB_PASSWORD` | - | Database password |

### Session
| Variable | Default | Description |
|----------|---------|-------------|
| `SESSION_DRIVER` | database | Session storage |
| `SESSION_LIFETIME` | 120 | Session timeout (minutes) |

### Cache
| Variable | Default | Description |
|----------|---------|-------------|
| `CACHE_STORE` | database | Cache driver |
| `CACHE_PREFIX` | - | Cache key prefix |

### Mail
| Variable | Default | Description |
|----------|---------|-------------|
| `MAIL_MAILER` | log | Mail driver |
| `MAIL_HOST` | 127.0.0.1 | SMTP host |
| `MAIL_PORT` | 2525 | SMTP port |

### Redis
| Variable | Default | Description |
|----------|---------|-------------|
| `REDIS_HOST` | 127.0.0.1 | Redis host |
| `REDIS_PASSWORD` | null | Redis password |
| `REDIS_PORT` | 6379 | Redis port |

---

## Frontend (.env)

| Variable | Default | Description |
|----------|---------|-------------|
| `VITE_API_URL` | http://localhost:8000 | Backend API URL |

---

## Environment Setup

### Development
```bash
# Backend
cd backend
cp .env.example .env
php artisan key:generate

# Frontend
cd frontend
cp .env.example .env
```

### Production
```bash
# Backend
cp .env.example .env
php artisan key:generate --show
# Set APP_ENV=production
# Set APP_DEBUG=false
# Configure production database
# Configure production mail service

# Frontend
cp .env.example .env
# Set VITE_API_URL to production API
```

---

## Security Rules

1. ✅ Use `.env` files for all secrets
2. ✅ Add `.env` to `.gitignore`
3. ✅ Use `.env.example` for documentation
4. ❌ Never commit `.env` to git
5. ❌ Never hardcode secrets in code
6. ❌ Never share `.env` files
