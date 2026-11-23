# Dokumentasi CI dan Integrasi Test (GitHub Actions)

**Proyek:** KampusLMS / EduSpace (Kelompok B4)  
**Komponen:** Continuous Integration (CI) & Automated Testing  
**Tanggal Verifikasi:** 23 November 2026  
**Status Pipeline:** ![CI](https://github.com/Zweilior/kampuslms-kelompok-B4/actions/workflows/ci.yml/badge.svg) **PASSING**

---

## 1. Ringkasan & Konfigurasi Workflow CI

Pipeline CI dikonfigurasi melalui berkas `.github/workflows/ci.yml` dan berjalan otomatis di runner **Ubuntu Latest** setiap kali terjadi event **`push`** maupun **`pull_request`** ke branch `main`.

### Berkas Konfigurasi: `.github/workflows/ci.yml`

```yaml
name: CI

on:
  push:
    branches: [main]
  pull_request:
    branches: [main]

jobs:
  tests:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout kode
        uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: mbstring, pdo_sqlite, sqlite3, fileinfo, intl
          coverage: none

      - name: Setup Node
        uses: actions/setup-node@v4
        with:
          node-version: '20'
          cache: npm

      - name: Install dependency PHP
        run: composer install --no-interaction --prefer-dist --no-progress

      - name: Siapkan environment & database
        run: |
          cp .env.example .env
          php artisan key:generate --force
          touch database/database.sqlite

      - name: Install dan build frontend (Vite)
        run: |
          npm ci
          npm run build

      - name: Jalankan migrasi database
        run: php artisan migrate --force
        env:
          DB_CONNECTION: sqlite
          DB_DATABASE: database/database.sqlite

      - name: Jalankan tes
        run: php artisan test
```

### Tahapan Kunci pada Pipeline:
1. **Setup Runtime:** Memasang PHP 8.4 dengan ekstensi wajib (`pdo_sqlite`, `sqlite3`, `mbstring`, `fileinfo`, `intl`) serta Node.js 20.
2. **Dependency Resolution:** Memasang pustaka backend via `composer install` dan frontend via `npm ci`.
3. **Environment Setup:** Menyiapkan file `.env` dari `.env.example`, menghasilkan `APP_KEY`, dan membuat database file SQLite fisik.
4. **Asset Compilation:** Menjalankan `npm run build` agar Vite manifest tersedia (mencegah error *Vite manifest not found* saat rendering view Blade).
5. **Migration Verification:** Memverifikasi integritas seluruh skema migrasi database menggunakan `php artisan migrate --force` pada koneksi SQLite.
6. **Test Suite Execution:** Menjalankan seluruh pengujian unit dan feature via `php artisan test`.

---

## 2. Otomatisasi Database Testing & Independensi Data

### A. Otomatisasi Database Testing
Pengujian otomatis tidak membutuhkan setup database MySQL server eksternal di runner CI. Hal ini dikonfigurasi langsung pada berkas `phpunit.xml`:

```xml
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
    <env name="BCRYPT_ROUNDS" value="4"/>
    <env name="CACHE_STORE" value="array"/>
    <env name="MAIL_MAILER" value="array"/>
    <env name="QUEUE_CONNECTION" value="sync"/>
    <env name="SESSION_DRIVER" value="array"/>
</php>
```

Setiap test case menggunakan trait `Illuminate\Foundation\Testing\RefreshDatabase`. Saat pengujian dijalankan, Laravel secara otomatis:
- Membuat database SQLite sementara di dalam memori (`:memory:`).
- Menjalankan migrasi skema tabel secara otomatis dari awal.
- Mengisolasi transaksi data untuk setiap unit pengujian (reset otomatis setelah pengujian selesai).

### B. Independensi dari Seeder Lokal
Konfigurasi CI **sama sekali tidak bergantung pada data seeder lokal (`db:seed`)**:
- Seluruh kebutuhan data uji dibuat secara mandiri dan deterministik menggunakan **Model Factories** (`User::factory()`, `Course::factory()`, `Assignment::factory()`, `Submission::factory()`, dll).
- Tidak ada test yang mengasumsikan keberadaan data statis di database, sehingga pipeline CI bersifat *hermetic* (terisolasi dan reproducible di lingkungan mana pun).

---

## 3. Cakupan Pengujian Seluruh Anggota (Pasca-Merge PR)

Setelah semua Pull Request dari seluruh anggota kelompok digabungkan (*merged*) ke branch utama, seluruh test suite baru ikut terdeteksi dan dieksekusi secara otomatis oleh CI.

### Hasil Pengujian Aktual:
```text
  Tests:    141 passed (819 assertions)
  Duration: 117.87s
```

### Rincian Kontribusi Test per Anggota:
| Anggota | Modul / Fitur | Test Suite Terkait | Skenario Pengujian Baru |
| :--- | :--- | :--- | :--- |
| **Laudya Aprilia** | Manajemen Pendaftaran Mahasiswa | `DosenCourseEnrollmentTest` | • Paginasi daftar mahasiswa terdaftar & belum terdaftar.<br>• Proteksi pencegahan penghapusan pendaftaran mahasiswa yang sudah memiliki riwayat tugas. |
| **Marchelino S. K.** | Keamanan & Transaksi Pengumpulan Tugas | `MahasiswaCourseSubmissionsTest`, `MaterialDownloadTest` | • Validasi scoped binding antara URL mata kuliah dan tugas.<br>• Pembungkusan transaksi database (`DB::transaction`) saat upload berkas.<br>• Akses privat berkas materi & submission. |
| **M. Arif Saputra** | Batas Beban & Pengamanan Enrollment | `AdminCourseEnrollmentTest`, `DosenCourseEnrollmentTest` | • Validasi penolakan request pendaftaran lebih dari 50 mahasiswa sekaligus (`max:50`).<br>• Keamanan otorisasi role Admin & Dosen pengampu. |
| **M. Irsyad Fiqi F.** | Optimasi Notifikasi & Alur CI | `NotificationWebTest`, `GradeNotificationFlowTest` | • Eliminasi query database fallback pada URL resolver notifikasi.<br>• Polling hitungan notifikasi belum dibaca bebas dari query N+1.<br>• Alur pengiriman event notifikasi nilai & tugas baru. |

---

## 4. Penanganan Masalah Khusus CI (*Troubleshooting CI-Only Failures*)

Dalam lingkungan CI (Linux/Ubuntu Runner), terdapat beberapa perbedaan kritis dibandingkan lingkungan lokal (Windows/Laragon) yang telah diantisipasi:

1. **Sensitivitas Huruf Besar/Kecil (*Case Sensitivity*):**
   - *Masalah:* Windows bersifat *case-insensitive*, sedangkan Ubuntu *case-sensitive*. Nama file view atau class yang salah kapitalisasi dapat lulus di lokal namun gagal di CI (*Class Not Found* / *View Not Found*).
   - *Mitigasi:* Seluruh namespace PSR-4 dan penamaan file Blade telah diselaraskan dengan path direktori secara presisi.
2. **Ketiadaan Berkas Manifest Vite:**
   - *Masalah:* Pengujian halaman web yang merender layout induk Blade akan gagal dengan pesan `Vite manifest not found at ...` jika aset frontend belum di-build.
   - *Mitigasi:* Workflow CI menyertakan langkah `npm ci` dan `npm run build` sebelum langkah `php artisan test`.
3. **Migrasi Fisik vs In-Memory:**
   - *Masalah:* Perintah `php artisan migrate --force` di luar PHPUnit memerlukan file database nyata.
   - *Mitigasi:* Workflow CI menyertakan perintah `touch database/database.sqlite` sebelum migrasi dijalankan.

---

## 5. Panduan Cara Menjalankan Test

### A. Menjalankan di Komputer Lokal

```bash
# Menjalankan seluruh test suite (141 tests)
php artisan test

# Menjalankan pengujian grup tertentu (Unit / Feature)
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Menjalankan pengujian modul spesifik (contoh: Notifikasi atau Enrollment)
php artisan test --filter=NotificationWebTest
php artisan test --filter=DosenCourseEnrollmentTest

# Menjalankan pengujian dengan rincian waktu eksekusi
php artisan test --profile
```

### B. Menjalankan & Memeriksa di CI (GitHub Actions)

1. **Otomatis Melalui Pull Request:**
   - Setiap kali membuat Pull Request baru ke branch `main`, GitHub Actions akan otomatis memicu workflow `CI`.
   - Scroll ke bagian bawah halaman PR di GitHub pada kotak **Checks**:
     - Ikon **kuning berputar**: Pipeline CI sedang berjalan.
     - Ikon **centang hijau**: Seluruh 141 pengujian berhasil lulus (*All checks have passed*).
     - Ikon **silang merah**: Ada pengujian yang gagal (klik **Details** untuk melihat error log).
2. **Memeriksa Riwayat Pipeline di Tab Actions:**
   - Buka repositori di browser: `https://github.com/Zweilior/kampuslms-kelompok-B4/actions`
   - Klik workflow run terbaru untuk melihat durasi, log tiap tahapan, dan hasil akhir eksekusi test.
