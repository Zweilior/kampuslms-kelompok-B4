# =============================================================================
# Tahap 1: Build aset frontend
# =============================================================================
FROM node:22-slim AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY . .
RUN npm run build


# =============================================================================
# Tahap 2: Laravel + PHP 8.4 CLI
# =============================================================================
FROM php:8.4-cli

# Install dependency sistem dan ekstensi PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    supervisor \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


# =============================================================================
# Composer
# =============================================================================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html


# Install dependency PHP
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction


# =============================================================================
# Source aplikasi
# =============================================================================
COPY . .

# =============================================================================
# Supervisor
# =============================================================================
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy hasil build Vite
COPY --from=assets /app/public/build ./public/build

# Optimasi autoloader
RUN composer dump-autoload \
    --no-dev \
    --optimize \
    --no-interaction


# =============================================================================
# Storage permission
# =============================================================================
RUN mkdir -p \
    storage/app/private \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache


# Railway akan memberikan PORT sendiri
EXPOSE 8080


# =============================================================================
# Startup
# =============================================================================
CMD ["sh", "-c", "php artisan migrate --force && php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan event:cache && exec supervisord -c /etc/supervisor/conf.d/supervisord.conf"]