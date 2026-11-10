#!/usr/bin/env bash
# =============================================================================
# deploy.sh: prosedur deploy ulang KampusLMS.
#
# Jalankan dari mana saja (skrip pindah ke foldernya sendiri):
#     bash deploy.sh
#
# Satu skrip untuk dua lingkungan:
#   - Server biasa (VPS): ada folder .git, jadi kode ditarik dan dibangun di sini.
#   - Container Railway: kode sudah dimasukkan ke image oleh Dockerfile (tidak ada
#     .git maupun npm), jadi langkah tarik kode dan build dilewati. Di Railway,
#     kode baru masuk lewat `git push` ke main; skrip ini mengurus langkah
#     sesudahnya (migrasi, cache, restart worker, cek kesehatan).
#
# Variabel opsional:
#   DEPLOY_BRANCH  cabang yang ditarik di server biasa   (default: main)
#   HEALTH_URL     alamat pemeriksaan kesehatan aplikasi (default: http://127.0.0.1:$PORT/up)
#
# Setiap langkah punya komentar "Kalau dihilangkan: ..." supaya jelas apa
# akibatnya kalau baris itu dihapus.
# =============================================================================

# Berhenti pada kesalahan pertama (-e), pada variabel yang belum diisi (-u), dan
# pada kegagalan di tengah pipa (pipefail).
# Kalau dihilangkan: skrip terus berjalan dengan keadaan setengah jadi, misalnya
# aplikasi dibuka kembali padahal migrasi gagal.
set -euo pipefail

# Pindah ke folder proyek, apa pun folder tempat skrip dipanggil.
# Kalau dihilangkan: perintah artisan gagal "Could not open input file: artisan".
cd "$(dirname "$0")"

BRANCH="${DEPLOY_BRANCH:-main}"
HEALTH_URL="${HEALTH_URL:-http://127.0.0.1:${PORT:-8080}/up}"
MAINTENANCE=0

log() { echo "[deploy] $*"; }

# Dijalankan setiap skrip berakhir. Kalau gagal, aplikasi SENGAJA tidak dibuka
# kembali: lebih baik pengunjung melihat halaman maintenance daripada aplikasi
# yang setengah ter-update.
# Kalau dihilangkan: kegagalan tidak memberi petunjuk apa yang harus dilakukan.
on_exit() {
    status=$?
    if [ "$status" -ne 0 ]; then
        echo "" >&2
        echo "[deploy] GAGAL (kode keluar $status)." >&2
        if [ "$MAINTENANCE" -eq 1 ]; then
            echo "[deploy] Aplikasi dibiarkan dalam mode maintenance." >&2
            echo "[deploy] Perbaiki masalahnya lalu jalankan ulang skrip ini." >&2
            echo "[deploy] Buka paksa hanya jika yakin aman: php artisan up" >&2
        fi
    fi
}
trap on_exit EXIT

if [ -d .git ]; then MODE="server"; else MODE="container"; fi
log "Mode: $MODE"

# 1. Mode maintenance. --retry menambahkan header Retry-After untuk klien.
# Kalau dihilangkan: pengunjung bisa membuka aplikasi saat migrasi berjalan di
# tengah jalan dan melihat error atau data setengah jadi.
log "1/9 Mode maintenance menyala"
php artisan down --retry=60
MAINTENANCE=1

if [ "$MODE" = "server" ]; then
    # 2. Tarik kode terbaru. --ff-only menolak merge otomatis.
    # Kalau dihilangkan: server menjalankan kode lama. Tanpa --ff-only, perubahan
    # manual di server bisa memicu commit merge yang tidak disengaja.
    log "2/9 Tarik kode (cabang $BRANCH)"
    git pull --ff-only origin "$BRANCH"

    # 3. Dependensi PHP produksi. --no-dev membuang paket pengembangan
    # (debugbar, dll.); --optimize-autoloader mempercepat pemuatan kelas.
    # Kalau dihilangkan: paket baru tidak terpasang dan aplikasi error
    # "Class not found", atau paket dev ikut aktif di produksi.
    log "3/9 composer install"
    composer install --no-dev --optimize-autoloader --no-interaction

    # 4. Bangun aset Vite. public/build ada di .gitignore, jadi tidak ikut git pull.
    # Kalau dihilangkan: tampilan memakai CSS/JS lama, atau error
    # "Vite manifest not found" bila belum pernah dibangun.
    log "4/9 Bangun aset front-end"
    command -v npm >/dev/null 2>&1 || { echo "[deploy] npm tidak ditemukan; pasang Node.js dulu." >&2; exit 1; }
    npm ci
    npm run build
else
    log "2-4/9 Dilewati: kode dan aset sudah ada di image (dibangun oleh Dockerfile)"
fi

# 5. Migrasi database. --force wajib di produksi (tanpa itu artisan bertanya
# konfirmasi dan skrip macet). Seeder TIDAK dijalankan di sini: seed ulang
# menimpa atau menggandakan data.
# Kalau dihilangkan: kode baru memakai kolom/tabel yang belum ada -> error 500.
log "5/9 Migrasi database"
php artisan migrate --force

# 6. Hapus semua cache lama, lalu buat ulang. optimize:clear menghapus cache
# config, route, view, event, dan cache aplikasi.
# Kalau dihilangkan: aplikasi masih memakai konfigurasi/route/view versi lama.
# Catatan: setelah config:cache, env() di luar config/ bernilai null.
log "6/9 Bersihkan dan buat ulang cache"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Skrip bisa dijalankan sebagai root (di container). File cache/log buatan root
# tidak bisa ditulis Apache dan worker yang berjalan sebagai www-data.
# Kalau dihilangkan: sesudah deploy, upload atau log bisa gagal "Permission denied".
if [ "$(id -u)" = "0" ] && id www-data >/dev/null 2>&1; then
    chown -R www-data:www-data storage bootstrap/cache
fi

# 7. Minta worker antrean berhenti setelah job yang sedang berjalan selesai.
# Supervisor langsung menyalakannya lagi dengan kode terbaru.
# Kalau dihilangkan: worker lama tetap memegang kode LAMA di memori. Job
# notifikasi diproses dengan logika yang sudah tidak ada di repo, dan bug-nya
# sulit dilacak karena web dan worker menjalankan versi yang berbeda.
log "7/9 Restart queue worker"
php artisan queue:restart

# 8. Pastikan aplikasi benar-benar menjawab SEBELUM keluar dari maintenance.
# /up tetap bisa diakses saat maintenance (Laravel mengecualikannya otomatis).
# Kalau dihilangkan: aplikasi bisa dibuka untuk umum padahal gagal start
# (misalnya cache rusak), dan kamu baru tahu dari keluhan pengguna.
log "8/9 Cek kesehatan: $HEALTH_URL"
tries=0
until curl -fsS -m 5 -o /dev/null "$HEALTH_URL"; do
    tries=$((tries + 1))
    if [ "$tries" -ge 15 ]; then
        echo "[deploy] Aplikasi tidak menjawab di $HEALTH_URL setelah $tries percobaan." >&2
        exit 1
    fi
    sleep 2
done
log "Aplikasi menjawab."

# 9. Keluar dari maintenance. Hanya tercapai kalau semua langkah di atas lolos.
# Kalau dihilangkan: aplikasi tetap menampilkan halaman maintenance selamanya.
log "9/9 Keluar dari mode maintenance"
php artisan up
MAINTENANCE=0
log "Selesai."
