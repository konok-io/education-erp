# Education ERP Dockerfile
# Multi-stage build for optimized production image

# Stage 1: Composer dependencies
FROM composer:2.6 AS vendor

WORKDIR /app

COPY backend/composer.json backend/composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs

COPY backend/ .

RUN composer dump-autoload --optimize --no-dev

# Stage 2: Frontend build
FROM node:20-alpine AS frontend-builder

WORKDIR /app

COPY frontend/package.json frontend/package-lock.json ./

RUN npm ci

COPY frontend/ .

RUN npm run build

# Stage 3: Production image
FROM php:8.2-fpm-alpine AS production

# Install system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    nginx \
    supervisor \
    openssl \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        xml \
        gd \
        zip \
        bcmath \
        opcache \
    && pecl install \
        redis \
    && docker-php-ext-enable \
        redis \
    && rm -rf /var/cache/apk/*

# Configure PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Configure Nginx
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Configure Supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create app directory
RUN mkdir -p /app && chown -R www-data:www-data /app

WORKDIR /app

# Copy application files
COPY --from=vendor /app /app
COPY --from=frontend-builder /app/dist /app/public

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/public \
    && chmod -R 775 /app/storage /app/bootstrap/cache /app/public

# Create non-root user
RUN addgroup -g 1000 -S www-data \
    && adduser -u 1000 -S www-data -G www-data \
    && chown -R www-data:www-data /app

USER www-data

# Expose ports
EXPOSE 8080

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:8080/health || exit 1

# Start command
CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && php-fpm"]
