# =============================================================================
# Tahap 1: Build aset front-end (Vite + Tailwind)
# =============================================================================
FROM node:22-slim AS assets

WORKDIR /app

# Install dependency Node terlebih dahulu agar cache Docker lebih optimal.
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

# Salin seluruh source code, kemudian build aset Vite.
COPY . .
RUN npm run build


# =============================================================================
# Tahap 2: Aplikasi Laravel (PHP 8.4 + Apache + Supervisor)
# =============================================================================
FROM php:8.4-apache AS app

# Install package dan ekstensi PHP yang dibutuhkan Laravel.
RUN apt-get update && apt-get install -y --no-install-recommends 
        libpng-dev 
        libonig-dev 
        libxml2-dev 
        libzip-dev 
        zip 
        unzip 
        git 
        curl 
        supervisor 
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

# Aktifkan mod_rewrite untuk routing Laravel.
# MPM bawaan dari image php:8.4-apache dibiarkan apa adanya untuk menghindari
# konflik "More than one MPM loaded".
RUN a2enmod rewrite

# Arahkan document root Apache ke folder public Laravel.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 
        -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' 
        /etc/apache2/sites-available/*.conf 
    && sed -ri 
        -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' 
        /etc/apache2/apache2.conf 
        /etc/apache2/conf-available/*.conf 
    && sed -ri 
        -e 's/AllowOverride None/AllowOverride All/g' 
        /etc/apache2/apache2.conf


# =============================================================================
# Konfigurasi PHP
# =============================================================================

COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini


# =============================================================================
# Composer
# =============================================================================

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install dependency PHP terlebih dahulu agar cache Docker lebih efisien.
COPY composer.json composer.lock ./

RUN composer install \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction


# =============================================================================
# Source code aplikasi
# =============================================================================

COPY . .

# Salin hasil build Vite dari stage assets.
COPY --from=assets /app/public/build ./public/build

# Generate autoloader Laravel yang sudah dioptimasi.
RUN composer dump-autoload \
        --optimize \
        --no-interaction


# =============================================================================
# Supervisor dan startup
# =============================================================================

COPY docker/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/start.sh /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh 
    && mkdir -p 
        storage/app/private 
        storage/framework/cache 
        storage/framework/sessions 
        storage/framework/views 
        storage/logs 
    && chown -R www-data:www-data storage bootstrap/cache 
    && chmod -R 775 storage bootstrap/cache


# Railway akan menyediakan PORT melalui environment variable.
EXPOSE 8080


# =============================================================================
# Startup container
# =============================================================================

CMD ["/usr/local/bin/start.sh"]