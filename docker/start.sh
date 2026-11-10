#!/bin/sh

# Dijalankan setiap container menyala.
# Urutan:
# konfigurasi Apache -> cek environment -> storage -> cache
# -> migrasi -> seeder -> permission -> Supervisor.
set -e

cd /var/www/html


# =============================================================================
# Konfigurasi port Apache untuk Railway
# =============================================================================

PORT="${PORT:-8080}"

sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:.*>/<VirtualHost *:${PORT}>/" 
    /etc/apache2/sites-available/000-default.conf


# =============================================================================
# Validasi environment
# =============================================================================

# Laravel membutuhkan APP_KEY untuk enkripsi cookie, session, dan data aplikasi.
if [ -z "${APP_KEY:-}" ]; then
    echo "[start] APP_KEY kosong. Isi APP_KEY di Variables Railway." >&2
    exit 1
fi


# =============================================================================
# Siapkan folder storage
# =============================================================================

mkdir -p 
    storage/app/private 
    storage/framework/cache 
    storage/framework/sessions 
    storage/framework/views 
    storage/logs


# =============================================================================
# Cache Laravel
# =============================================================================

# Environment Railway baru tersedia saat runtime, sehingga cache dibuat di sini.
php artisan config:cache

# Route cache hanya optimasi.
# Jika ada route closure yang tidak dapat di-cache, aplikasi tetap bisa berjalan.
php artisan route:cache || 
    echo "[start] route:cache dilewati karena terdapat route yang tidak dapat di-cache." >&2

php artisan view:cache


# =============================================================================
# Migrasi database
# =============================================================================

# Database Railway mungkin membutuhkan waktu untuk siap saat container pertama kali
# dijalankan. Karena itu, migrasi dicoba ulang maksimal 15 kali.

tries=0

until php artisan migrate --force; do
    tries=$((tries + 1))

    if [ "$tries" -ge 15 ]; then
        echo "[start] Migrasi gagal setelah ${tries} percobaan." >&2
        exit 1
    fi

    echo "[start] Database belum siap, mencoba kembali (${tries}/15)..." >&2
    sleep 3
done


# =============================================================================
# Seeder database
# =============================================================================

# Seeder dijalankan jika RUN_SEED=true di Variables Railway.
#
# Pada deployment pertama:
# RUN_SEED=true
#
# Setelah data awal berhasil dibuat, ubah menjadi:
# RUN_SEED=false
#
# Dengan cara ini, seeder tidak otomatis dijalankan ulang setiap redeploy.

if [ "${RUN_SEED:-false}" = "true" ]; then
    echo "[start] RUN_SEED=true, menjalankan database seeder."

    php artisan db:seed --force

    echo "[start] Database seeder berhasil dijalankan."
else
    echo "[start] Seeder dilewati karena RUN_SEED bukan true."
fi


# =============================================================================
# Permission
# =============================================================================

# Supervisor, Apache, dan queue worker berjalan menggunakan user www-data.
# Pastikan Laravel dapat menulis cache, session, log, dan file upload.

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache


# =============================================================================
# Jalankan Supervisor
# =============================================================================

exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf