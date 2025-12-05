# Stage 1: Build stage
FROM php:8.2-fpm-alpine AS builder

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    postgresql-dev \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Copy package files
COPY package.json package-lock.json* ./

# Install Node dependencies (including dev dependencies for build)
RUN npm ci || npm install

# Copy application files
COPY . .

# Build assets
RUN npm run build

# Stage 2: Production stage
FROM php:8.2-cli-alpine

# Install system dependencies
RUN apk add --no-cache \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    mysql-client

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Copy PHP configuration
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Set working directory
WORKDIR /var/www/html

# Copy application from builder
COPY --from=builder /var/www/html /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Expose port (Railway will set PORT env variable)
EXPOSE 8080

# Start application
CMD sh -c "php artisan migrate --force && php artisan config:clear && php artisan config:cache && php artisan route:clear && php artisan route:cache && php artisan view:clear && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"

