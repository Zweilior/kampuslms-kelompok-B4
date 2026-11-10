# =============================================================================
# Tahap 1: build aset front-end (Vite + Tailwind).
# public/build ada di .gitignore, jadi harus dibangun di sini. Tanpa tahap ini
# @vite() di layout melempar error "Vite manifest not found" dan semua halaman 500.
# =============================================================================
FROM node:22-slim AS assets
WORKDIR /app

# package*.json disalin lebih dulu supaya layer `npm ci` tidak diulang
# selama dependensi tidak berubah.
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

# Seluruh proyek disalin (bukan hanya resources/) karena Tailwind v4 memindai
# proyek untuk mencari nama kelas, termasuk yang ada di luar resources/views.
COPY . .
RUN npm run build


# =============================================================================
# Tahap 2: image akhir (PHP 8.4 + Apache + Supervisor).
# PHP 8.4 dipilih karena paket Symfony 8 di composer.lock mensyaratkan >= 8.4.1.
# =============================================================================
FROM php:8.4-apache AS app

# Ekstensi PHP. pcntl dibutuhkan queue worker untuk menangani sinyal berhenti.
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev libonig-dev libxml2-dev libzip-dev \
        zip unzip git curl supervisor \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Apache: mod_rewrite untuk route Laravel, hanya satu MPM (prefork), dan
# document root ke public/ (bukan akar proyek, supaya .env dan composer.json
# tidak bisa diunduh). Port dibaca dari variabel PORT milik Railway.
# Apache: mod_rewrite, hanya satu MPM (prefork),
# dan document root diarahkan ke folder public Laravel.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN a2enmod rewrite \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Batas unggah PHP (selaras dengan validasi aplikasi).
COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# Composer 2 (versi dipatok, bukan :latest).
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Dependensi dipasang sebelum kode disalin supaya layer ini ter-cache.
# --no-scripts karena artisan belum ada di tahap ini.
# Sengaja TANPA --ignore-platform-reqs: PHP 8.4 sudah memenuhi semua syarat,
# dan flag itu hanya menyembunyikan ketidakcocokan yang seharusnya gagal saat build.
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY . .
# Aset hasil build dari tahap 1.
COPY --from=assets /app/public/build ./public/build

# Autoloader teroptimasi + package:discover (yang tadi ditunda oleh --no-scripts).
RUN composer dump-autoload --no-dev --optimize --no-interaction

# Konfigurasi Supervisor dan skrip start.
COPY docker/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh \
    && mkdir -p storage/app/private storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

# Satu proses induk (supervisord) yang menjaga Apache dan queue worker tetap hidup.
CMD ["/usr/local/bin/start.sh"]
