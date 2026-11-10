#!/bin/sh
# Dijalankan setiap container menyala. Urutannya penting:
#   cek konfigurasi -> siapkan storage -> cache -> migrasi -> Supervisor.
# `set -e` membuat container berhenti pada kesalahan pertama. Railway lalu
# memulai ulang dan log menunjukkan sebabnya, daripada app jalan setengah rusak.
set -e
cd /var/www/html

# 1. Tanpa APP_KEY, enkripsi session/cookie gagal dengan pesan yang membingungkan.
if [ -z "${APP_KEY:-}" ]; then
    echo "[start] APP_KEY kosong. Isi di Variables Railway (php artisan key:generate --show)." >&2
    exit 1
fi

# 2. Volume Railway dipasang kosong di awal. Pastikan folder yang dibutuhkan ada.
#    (Kepemilikan file diatur di langkah terakhir, lihat langkah 6.)
mkdir -p storage/app/private storage/framework/cache storage/framework/sessions storage/framework/views storage/logs

# 3. Cache dibuat saat start (bukan saat build) karena variabel environment
#    baru tersedia di runtime. Setelah config:cache, env() di luar config/ bernilai null.
php artisan config:cache
# route:cache hanya optimasi. routes/web.php berisi route closure; kalau ada yang
# tidak bisa diserialisasi, lewati saja daripada membuat seluruh aplikasi gagal start.
php artisan route:cache || echo "[start] route:cache dilewati (lihat pesan di atas)." >&2
php artisan view:cache

# 4. Migrasi. Database Railway kadang belum siap saat container menyala,
#    jadi dicoba ulang. Kalau tetap gagal, berhenti (jangan `|| true`).
tries=0
until php artisan migrate --force; do
    tries=$((tries + 1))
    if [ "$tries" -ge 15 ]; then
        echo "[start] Migrasi gagal setelah $tries percobaan." >&2
        exit 1
    fi
    echo "[start] Database belum siap, coba lagi ($tries/15)..." >&2
    sleep 3
done

# 5. Seed hanya jika diminta (deploy pertama). Setelah berhasil, ubah RUN_SEED
#    menjadi false atau hapus variabelnya.
if [ "${RUN_SEED:-false}" = "true" ]; then
    echo "[start] RUN_SEED=true, menjalankan seeder."
    php artisan db:seed --force
fi

# 6. Serahkan kepemilikan ke www-data SETELAH semua perintah artisan di atas.
#    Skrip ini berjalan sebagai root: kalau artisan sempat menulis log atau cache,
#    filenya milik root dan Apache/worker (www-data) tidak bisa menulis lagi.
#    Ini juga yang membuat Volume Railway (dipasang sebagai root) bisa ditulis upload.
chown -R www-data:www-data storage bootstrap/cache

# 7. Serahkan ke Supervisor (menjadi proses utama container).
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
