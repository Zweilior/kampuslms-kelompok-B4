# =============================================================================
# Tahap 1: Build aset front-end (Vite + Tailwind)
# =============================================================================
FROM node:22-slim AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY . .
RUN npm run build


# =============================================================================
# Tahap 2: Laravel + PHP 8.4 + Apache
# =============================================================================
FROM php:8.4-apache

# Install dependency sistem dan ekstensi PHP yang dibutuhkan Laravel.
RUN apt-get update && apt-get install -y --no-install-recommends 
    libpng-dev 
    libonig-dev 
    libxml2-dev 
    libzip-dev 
    zip 
    unzip 
    git 
    curl 
    && docker-php-ext-install 
    pdo_mysql 
    mbstring 
    exif 
    pcntl 
    bcmath 
    gd 
    zip 
    && apt-get clean 
    && rm -rf /var/lib/apt/lists/*


# =============================================================================
# Konfigurasi Apache
# =============================================================================

# Aktifkan rewrite untuk routing Laravel.
# Pastikan hanya MPM bawaan yang digunakan.
RUN a2enmod rewrite 
    && rm -f /etc/apache2/mods-enabled/mpm_event.* 
    /etc/apache2/mods-enabled/mpm_worker.* 
    && a2enmod mpm_prefork 
    && sed -ri -e 's/AllowOverride None/AllowOverride All/g' 
    /etc/apache2/apache2.conf


# Document root Laravel harus mengarah ke /public.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 
    -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' 
    /etc/apache2/sites-available/*.conf 
    && sed -ri 
    -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' 
    /etc/apache2/apache2.conf 
    /etc/apache2/conf-available/*.conf


# =============================================================================
# Composer
# =============================================================================

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install 
    --no-dev 
    --no-scripts 
    --no-autoloader 
    --prefer-dist 
    --no-interaction 
    --ignore-platform-reqs


# =============================================================================
# Source aplikasi
# =============================================================================

COPY . .

# Salin hasil build Vite.
COPY --from=assets /app/public/build ./public/build

# Optimasi autoloader.
RUN composer dump-autoload 
    --no-dev 
    --optimize 
    --no-interaction


# =============================================================================
# Permission
# =============================================================================

RUN mkdir -p 
    storage/app/private 
    storage/framework/cache 
    storage/framework/sessions 
    storage/framework/views 
    storage/logs 
    && chown -R www-data:www-data storage bootstrap/cache 
    && chmod -R 775 storage bootstrap/cache


EXPOSE 8080


# =============================================================================
# Startup Railway
# =============================================================================

CMD ["sh", "-c", "rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* && export PORT=${PORT:-8080} && sed -ri \"s/^Listen .*/Listen ${PORT}/\" /etc/apache2/ports.conf && sed -ri \"s/<VirtualHost \\*:.*>/<VirtualHost *:${PORT}>/\" /etc/apache2/sites-available/000-default.conf && php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan migrate --force && php artisan db:seed --force && exec apache2-foreground"]