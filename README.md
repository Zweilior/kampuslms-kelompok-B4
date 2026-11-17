# EduSpace (KampusLMS – Kelompok B4)

**EduSpace** adalah nama aplikasi yang dikembangkan oleh Kelompok B4 untuk proyek **KampusLMS**, sebuah sistem manajemen pembelajaran (*Learning Management System*) berbasis web[cite: 3, 4]. Aplikasi ini memfasilitasi pengelolaan mata kuliah, distribusi materi ajar, pengumpulan dan penilaian tugas mahasiswa, serta pemrosesan notifikasi secara asinkron[cite: 13].

### Teknologi yang Digunakan
* **Backend**: PHP 8.2+ (kompatibel hingga PHP 8.4), Laravel 12[cite: 8, 13]
* **Frontend**: Blade, Tailwind CSS, Vite, Node.js & npm[cite: 4, 8]
* **Database**: SQLite (default lokal/testing) atau MySQL/MariaDB[cite: 8, 13]
* **Autentikasi & Otorisasi**: Laravel Session & Laravel Sanctum (API Token), Policy-based RBAC (Admin, Dosen, Mahasiswa)[cite: 10, 12]
* **Antrean (Queue) & Deployment**: Laravel Database Queue, Docker & Supervisor (Railway)[cite: 3, 8, 13]

---

### Prasyarat Lingkungan Lokal
Sebelum menjalankan proyek di komputer lokal, pastikan perangkat lunak berikut sudah terpasang[cite: 3, 8]:
1. **PHP** versi 8.2 atau lebih baru (disarankan PHP 8.2 – 8.4) dengan ekstensi aktif: `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, dan `zip`[cite: 8, 13].
2. **Composer** (manajer dependensi PHP)[cite: 3, 8].
3. **Node.js** (disarankan versi 20 LTS+) dan **npm**[cite: 3, 8].
4. **Database**: SQLite (bawaan PHP jika ekstensi aktif) atau MySQL/MariaDB (misalnya melalui Laragon/XAMPP)[cite: 8, 13].

---

### Panduan Instalasi & Menjalankan Proyek (Dari Clone)

Ikuti langkah-langkah berikut untuk menjalankan *repository* yang sudah ada di lingkungan lokal[cite: 3, 4, 8]:

1. **Clone Repository**
   ```bash
   git clone [https://github.com/username-atau-org/kampuslms-kelompok-B4.git](https://github.com/username-atau-org/kampuslms-kelompok-B4.git)
   cd kampuslms-kelompok-B4
   ```

2. **Instal Dependensi Backend dan Frontend**
   ```bash
   composer install
   npm ci
   ```

3. **Salin File Konfigurasi Environment**
   ```bash
   cp .env.example .env
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Sesuaikan Konfigurasi Lokal di `.env`**
   Buka file `.env` dan pastikan pengaturan berikut sesuai untuk pengembangan lokal[cite: 4, 8, 9]:
   ```env
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=[http://127.0.0.1:8000](http://127.0.0.1:8000)

   # Gunakan sqlite (pastikan file database/database.sqlite tersedia) atau sesuaikan ke mysql
   DB_CONNECTION=sqlite

   # Kosongkan untuk memakai fallback lokal ("password"), atau isi kata sandi khusus
   DEMO_PASSWORD=

   QUEUE_CONNECTION=database
   FILESYSTEM_DISK=local
   SESSION_SECURE_COOKIE=false
   ```
   *(Jika menggunakan SQLite dan file belum ada, buat file kosong dengan perintah `touch database/database.sqlite` di Git Bash/Linux/macOS atau buat file baru di dalam folder `database/`).*

6. **Jalankan Migrasi dan Seeder Database**
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Kompilasi Aset Frontend**
   Untuk mode pengembangan (*hot-reload*):
   ```bash
   npm run dev
   ```
   Atau untuk *build* aset statis lokal:
   ```bash
   npm run build
   ```

8. **Jalankan Server Aplikasi dan Queue Worker**
   Buka dua terminal terpisah untuk menjalankan server web lokal dan *queue worker*[cite: 4, 8, 13]:
   ```bash
   # Terminal 1: Menjalankan server aplikasi
   php artisan serve

   # Terminal 2: Menjalankan pemroses antrean notifikasi
   php artisan queue:work
   ```
   Aplikasi dapat diakses melalui `http://127.0.0.1:8000`. *(Jika menggunakan Virtual Host Laragon, pastikan document root mengarah ke folder `public/`)*.

---

### Konfigurasi Akun Demo (`DEMO_PASSWORD`)

Pengaturan kata sandi akun bawaan dari `DatabaseSeeder` dibedakan berdasarkan nilai `APP_ENV` pada `config/auth.php`[cite: 4, 9]:

* **Lingkungan Lokal & Testing (`APP_ENV=local` atau `APP_ENV=testing`)**:
  Jika `DEMO_PASSWORD` dikosongkan di `.env`, sistem otomatis menggunakan *fallback* kata sandi default **`password`**[cite: 9]. Jika diisi nilai lain (misalnya `DEMO_PASSWORD=Rahas1aDemo123`), seeder akan menggunakan nilai tersebut.
* **Lingkungan Production (`APP_ENV=production`)**:
  *Fallback* kata sandi **dinonaktifkan** (`null`) demi keamanan[cite: 9]. Anda **wajib** mendefinisikan `DEMO_PASSWORD` secara eksplisit melalui *environment variable* di platform *deployment* (seperti Railway) sebelum menjalankan `php artisan db:seed`[cite: 9]. Jika kosong, proses *seeding* akan dihentikan oleh sistem[cite: 9]. Jangan pernah menyimpan kata sandi aktual *production* di dalam *repository* maupun `.env.example`[cite: 4, 9].

**Daftar Akun Demo Seeder:**
| Peran | Email | NIM / NIP | Kata Sandi (Lokal Default) |
| :--- | :--- | :--- | :--- |
| Admin | `admin@kampuslms.test` | `ADM001` | `password` *(atau nilai `DEMO_PASSWORD`)*[cite: 2, 9] |
| Dosen | `dosen@kampuslms.test` | `NIP001` | `password` *(atau nilai `DEMO_PASSWORD`)*[cite: 2, 9] |
| Mahasiswa | `mahasiswa@kampuslms.test` | `NIM001` | `password` *(atau nilai `DEMO_PASSWORD`)*[cite: 2, 9] |

---

### Konfigurasi Unggah Berkas (Lokal)

Materi dan pengumpulan tugas disimpan di disk privat (`storage/app/private`), bukan di `storage/app/public`, sehingga tidak memerlukan `php artisan storage:link`[cite: 13]. Berkas hanya dapat diunduh melalui *controller* yang memvalidasi hak akses pengguna[cite: 13].

Untuk pengujian unggah berkas di lingkungan lokal (seperti Laragon/XAMPP), batas konfigurasi di `php.ini` dapat memblokir unggahan sebelum validasi Laravel berjalan[cite: 8, 13]. Pastikan nilai berikut terpenuhi pada `php.ini` lokal Anda[cite: 13]:

| Pengaturan `php.ini` | Nilai Minimal |
| :--- | :--- |
| `upload_max_filesize` | `20M`[cite: 13] |
| `post_max_size` | `25M`[cite: 13] |

*Catatan: Pengaturan di atas berlaku untuk environment PHP lokal (misalnya melalui menu Laragon > PHP > `php.ini`, lalu restart layanan web)[cite: 8, 13]. Untuk arsitektur container dan runtime server produksi (Docker, Supervisor, dan Railway), silakan merujuk pada [docs/deployment.md](docs/deployment.md)[cite: 3, 8].*

Pastikan `.env` tetap menggunakan disk lokal bawaan[cite: 13]:
```env
FILESYSTEM_DISK=local
```

---

### Menjalankan Queue Notifikasi

KampusLMS menggunakan *database queue* untuk memproses notifikasi secara asinkron[cite: 13]. Pastikan `.env` memiliki konfigurasi `QUEUE_CONNECTION=database` dan tabel antrean (`jobs`, `job_batches`, `failed_jobs`, `notifications`) sudah dimigrasi[cite: 13].

* **Menjalankan worker**[cite: 13]:
  ```bash
  php artisan queue:work
  ```
  *(Jika worker tidak aktif, data tugas tetap tersimpan tetapi job notifikasi akan menumpuk di tabel `jobs` dan belum terkirim)[cite: 13].*
* **Memeriksa job yang gagal**[cite: 13]:
  ```bash
  php artisan queue:failed
  ```
* **Mengulang semua job yang gagal**[cite: 13]:
  ```bash
  php artisan queue:retry all
  ```

---

### Menjalankan Pengujian (Automated Tests)

Pastikan dependensi sudah terpasang dan konfigurasi testing siap, lalu jalankan seluruh *test suite* Laravel[cite: 10, 12]:
```bash
php artisan test
```
Untuk skrip pengujian API dan otorisasi berbasis *curl*, lihat `scripts/test-api.sh` dan `scripts/test-authz.sh` (pastikan menyesuaikan `BASE_URL` dan token sesuai environment yang diuji)[cite: 5, 10].

---

### Tim & Kontribusi Aktual (Kelompok B4)

| Nama Anggota | NIM | Fokus Kontribusi & Fitur Utama | Jumlah Commit |
| :--- | :--- | :--- | :---: |
| Laudya Aprilia Khoirum | 10241038[cite: 13] | Fitur Frontend, Pengujian Aplikasi (`php artisan test`), Verifikasi Keamanan & Dokumentasi Performa[cite: 5, 9] | - |
| Marchelino Senduk Kaunang | 10241040[cite: 13] | Arsitektur Backend, Autentikasi/Otorisasi API, Seeder & Konfigurasi Demo, Dokumentasi Setup & API[cite: 4, 9] | - |
| Moh. Irsyad Fiqi Ferdiansyah Difa Nanda | 10241042[cite: 13] | Fitur Frontend, Konfigurasi CI/CD GitHub Actions, Pull Request Template & Alur Review[cite: 5, 9] | - |
| Muhammad Arif Saputra | 10241044[cite: 13] | Integrasi Full Stack, Konfigurasi Docker, Supervisor, & Panduan Deployment Railway[cite: 3, 9] | - |