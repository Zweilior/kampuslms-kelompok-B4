
# Deployment KampusLMS

## 1. Deskripsi

KampusLMS merupakan aplikasi **Learning Management System (LMS)** berbasis Laravel. Aplikasi dideploy ke platform Railway menggunakan Docker sebagai lingkungan container dan layanan MySQL Railway sebagai database production.

Proses deployment mencakup:

- Build aset frontend menggunakan Node.js dan Vite.
- Instalasi dependency PHP menggunakan Composer.
- Konfigurasi environment Laravel di Railway.
- Koneksi aplikasi dengan database MySQL Railway.
- Menjalankan migration database.
- Menjalankan aplikasi menggunakan PHP built-in server.
- Verifikasi deployment melalui log dan pengujian API production.
- Pengujian authentication dan authorization menggunakan script Bash.

Aplikasi menggunakan timezone **WITA (Asia/Makassar / UTC+8)**.

---

## 2. Teknologi yang Digunakan

Teknologi yang digunakan dalam proses deployment adalah:

- Laravel
- PHP 8.4
- MySQL
- Docker
- Railway
- Node.js 22
- Vite
- Composer
- Bash
- cURL

---

## 3. Persiapan Project

Sebelum melakukan deployment, beberapa file dan konfigurasi berikut harus tersedia:

- `composer.json`
- `composer.lock`
- `package.json`
- `package-lock.json`
- `Dockerfile`
- Konfigurasi environment Laravel
- Migration database
- Seeder database untuk inisialisasi awal
- Konfigurasi Vite

Aset frontend perlu dibangun selama proses Docker build karena folder hasil build Vite, yaitu `public/build`, tidak digunakan langsung dari repository.

---

## 4. Dockerfile

Deployment menggunakan konsep **multi-stage build**.

### 4.1 Tahap Pertama: Build Frontend

Tahap pertama menggunakan Node.js untuk menginstal dependency frontend dan membangun aset menggunakan Vite.

```dockerfile
FROM node:22-slim AS assets

WORKDIR /app

COPY package.json package-lock.json ./

RUN npm ci --no-audit --no-fund

COPY . .

RUN npm run build
```

Hasil build dari Vite kemudian digunakan oleh aplikasi Laravel melalui folder:

```text
public/build
```

Dengan proses ini, aset frontend dapat tersedia di production meskipun hasil build tidak disimpan langsung di repository.

---

### 4.2 Tahap Kedua: Aplikasi Laravel

Aplikasi Laravel menggunakan PHP 8.4 CLI.

Beberapa dependency dan ekstensi PHP yang digunakan antara lain:

- `pdo_mysql`
- `mbstring`
- `exif`
- `pcntl`
- `bcmath`
- `gd`
- `zip`

Composer digunakan untuk menginstal dependency PHP aplikasi.

Aplikasi tidak menggunakan Apache pada konfigurasi deployment akhir. Hal ini dilakukan untuk menghindari konflik konfigurasi Apache yang sebelumnya terjadi selama deployment.

---

## 5. Konfigurasi Environment Railway

Variabel environment utama yang digunakan pada Railway adalah sebagai berikut:

```env
APP_NAME=KampusLMS
APP_ENV=production
APP_KEY=base64:APP_KEY_LARAVEL
APP_DEBUG=false
APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}

APP_TIMEZONE=Asia/Makassar

DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true

QUEUE_CONNECTION=database
CACHE_STORE=database

FILESYSTEM_DISK=local

LOG_CHANNEL=stack
LOG_STACK=single,stderr
LOG_LEVEL=error

DEMO_PASSWORD=PASSWORD_AKUN_DEMO
```

Nilai sensitif seperti berikut tidak disimpan di repository:

- `APP_KEY`
- Password database
- Password akun demo
- Token authentication

Nilai tersebut dikonfigurasi melalui environment variables pada Railway.

---

## 6. Konfigurasi Database

Aplikasi menggunakan layanan MySQL yang disediakan oleh Railway.

Pada proses deployment normal, aplikasi menjalankan migration:

```bash
php artisan migrate --force
```

Perintah tersebut menjalankan migration yang belum diterapkan tanpa menghapus data yang telah tersedia pada database.

### 6.1 Seeder Database

Seeder digunakan untuk melakukan inisialisasi data pada database pertama kali.

Namun, seeder **tidak dijalankan pada setiap deployment** karena dapat menyebabkan data demo ditambahkan kembali secara berulang.

Pada konfigurasi startup normal, perintah berikut tidak digunakan:

```bash
php artisan db:seed --force
```

Seeder hanya dijalankan ketika database perlu diinisialisasi.

Dengan konfigurasi ini:

- Migration baru tetap dapat diterapkan.
- Data yang sudah ada tetap dipertahankan.
- Data demo tidak terus bertambah setiap deployment.

---

## 7. Menjalankan Aplikasi

Aplikasi dijalankan menggunakan PHP built-in server:

```bash
php -S 0.0.0.0:${PORT:-8080} -t public
```

Railway menyediakan environment variable `PORT` yang digunakan aplikasi untuk menerima request.

Log deployment menunjukkan aplikasi berhasil dijalankan pada port yang digunakan Railway, misalnya:

```text
PHP 8.4.27 Development Server (http://0.0.0.0:8080) started
```

Penggunaan PHP built-in server dipilih sebagai solusi deployment setelah konfigurasi Apache sebelumnya mengalami konflik.

---

## 8. Konfigurasi Routing

Route utama aplikasi diarahkan ke halaman login.

Konfigurasi route:

```php
Route::get('/', fn () => redirect()->route('login'));
```

Dengan konfigurasi tersebut, pengguna yang membuka halaman utama aplikasi akan diarahkan ke halaman login.

Alur request menjadi:

```text
GET /
   ↓
302 Redirect
   ↓
GET /login
   ↓
200 OK
```

---

## 9. Konfigurasi Timezone

Aplikasi menggunakan timezone WITA dengan konfigurasi:

```env
APP_TIMEZONE=Asia/Makassar
```

Timezone `Asia/Makassar` digunakan karena sesuai dengan zona waktu WITA atau:

```text
UTC+8
```

Konfigurasi ini diterapkan agar waktu yang digunakan dan ditampilkan aplikasi sesuai dengan waktu lokal WITA.

---

## 10. Proses Deployment di Railway

Langkah deployment dilakukan sebagai berikut:

1. Repository project dihubungkan ke Railway.
2. Service MySQL dibuat dan dikonfigurasikan.
3. Environment variables Laravel ditambahkan ke Railway.
4. Railway melakukan build berdasarkan `Dockerfile`.
5. Dependency frontend diinstal menggunakan Node.js.
6. Aset frontend dibangun menggunakan Vite.
7. Dependency PHP diinstal menggunakan Composer.
8. Container aplikasi dijalankan.
9. Laravel menjalankan migration database.
10. Aplikasi dijalankan menggunakan PHP built-in server.
11. Railway menyediakan public domain untuk mengakses aplikasi.
12. Deployment diverifikasi melalui log Railway dan pengujian API production.

---

# 11. Permasalahan dan Solusi

## 11.1 Dockerfile Parse Error

Pada proses awal deployment, terjadi error:

```text
unknown instruction: libpng-dev
```

Masalah terjadi karena penulisan multi-line command pada Dockerfile tidak terbaca dengan benar.

### Solusi

Penulisan perintah `RUN` diperbaiki dan penggunaan backslash (`\`) pada setiap baris disesuaikan dengan sintaks Dockerfile.

---

## 11.2 Apache More Than One MPM Loaded

Pada deployment menggunakan Apache, muncul error:

```text
AH00534: apache2: Configuration error: More than one MPM loaded.
```

Error tersebut menyebabkan Apache tidak dapat dijalankan dengan benar.

### Solusi

Konfigurasi deployment diubah dari PHP Apache menjadi PHP CLI.

Aplikasi kemudian dijalankan menggunakan:

```bash
php -S 0.0.0.0:${PORT:-8080} -t public
```

Dengan perubahan tersebut, aplikasi tidak lagi menggunakan Apache sehingga konflik Multiple Processing Module (MPM) dapat dihindari.

---

## 11.3 Data Database Bertambah Setiap Deployment

Pada konfigurasi awal, proses startup menjalankan:

```bash
php artisan migrate --force
php artisan db:seed --force
```

Karena container dapat dijalankan kembali ketika deployment dilakukan, perintah seeder menyebabkan data demo ditambahkan kembali.

Akibatnya, jumlah data seperti akun pengguna terus bertambah pada setiap deployment.

### Solusi

Perintah:

```bash
php artisan db:seed --force
```

dihapus dari proses startup normal.

Deployment selanjutnya hanya menjalankan:

```bash
php artisan migrate --force
```

Dengan demikian, migration baru tetap diterapkan tanpa membuat data demo berulang.

---

## 11.4 Halaman Utama Menampilkan Welcome Laravel

Route utama sebelumnya masih menggunakan halaman bawaan Laravel:

```php
Route::get('/', fn () => view('welcome'));
```

Akibatnya, pengguna melihat halaman `welcome` ketika membuka domain utama aplikasi.

### Solusi

Route utama diubah menjadi:

```php
Route::get('/', fn () => redirect()->route('login'));
```

Sekarang pengguna yang membuka halaman utama akan langsung diarahkan ke halaman login.

---

## 11.5 Perbedaan Timezone

Waktu aplikasi sebelumnya masih menggunakan UTC sehingga waktu yang ditampilkan tidak sesuai dengan waktu lokal WITA.

### Solusi

Timezone aplikasi diatur menjadi:

```text
Asia/Makassar
```

yang sesuai dengan:

```text
WITA (UTC+8)
```

---

# 12. Verifikasi Deployment

Deployment berhasil diverifikasi melalui log Railway.

Salah satu indikator adalah:

```text
Nothing to migrate.
```

Pesan tersebut menunjukkan bahwa seluruh migration yang tersedia telah diterapkan dan tidak terdapat migration baru yang perlu dijalankan.

Aplikasi juga berhasil menerima request:

```text
[302]: GET /
[200]: GET /login
```

Status tersebut menunjukkan:

- `302` — halaman utama berhasil melakukan redirect.
- `200` — halaman login berhasil diakses.

Aset frontend juga berhasil dimuat dari hasil build Vite, misalnya:

```text
GET /build/assets/app-*.css
GET /build/assets/login-*.css
```

Aplikasi juga berhasil menerima request untuk asset tambahan seperti:

```text
GET /js/tailwind-config.js
GET /favicon.ico
```

---

# 13. Pengujian API Production

Pengujian dilakukan terhadap API production KampusLMS yang telah dideploy di Railway.

Endpoint API berada pada format:

```text
https://DOMAIN_APLIKASI/api/v1
```

Script pengujian yang digunakan adalah:

```text
scripts/test-api.sh
scripts/test-authz.sh
```

Pengujian dilakukan menggunakan Git Bash dan `curl`.

---

## 13.1 Menentukan BASE_URL

Sebelum menjalankan pengujian, variabel `BASE_URL` harus diisi dengan URL API production.

Contoh:

```bash
export BASE_URL="https://DOMAIN_APLIKASI/api/v1"
```

Verifikasi nilai:

```bash
echo "$BASE_URL"
```

Contoh output:

```text
https://DOMAIN_APLIKASI/api/v1
```

---

## 13.2 Mendapatkan Token Authentication

Token diperoleh melalui endpoint login:

```text
POST /auth/login
```

### Admin

```bash
curl -s -X POST "$BASE_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@kampuslms.test","password":"PASSWORD"}'
```

Response:

```json
{
  "data": {
    "token": "TOKEN_ADMIN",
    "token_type": "Bearer"
  }
}
```

Simpan token sebagai environment variable:

```bash
export TOKEN_ADMIN="TOKEN_ADMIN"
```

---

### Dosen A

```bash
curl -s -X POST "$BASE_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"dosen@kampuslms.test","password":"PASSWORD"}'
```

Simpan token:

```bash
export TOKEN_DOSEN_A="TOKEN_DOSEN_A"
```

---

### Dosen B

```bash
curl -s -X POST "$BASE_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"EMAIL_DOSEN_B","password":"PASSWORD"}'
```

Simpan token:

```bash
export TOKEN_DOSEN_B="TOKEN_DOSEN_B"
```

---

### Mahasiswa

```bash
curl -s -X POST "$BASE_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"mahasiswa@kampuslms.test","password":"PASSWORD"}'
```

Simpan token:

```bash
export TOKEN_MAHASISWA="TOKEN_MAHASISWA"
```

> Token asli tidak dimasukkan ke dalam repository atau dokumentasi.

---

## 13.3 Verifikasi Identitas Pengguna

Endpoint berikut digunakan untuk memastikan token berhasil dan sesuai dengan pengguna:

```text
GET /me
```

### Dosen A

```bash
curl -s "$BASE_URL/me" \
  -H "Authorization: Bearer $TOKEN_DOSEN_A"
```

### Dosen B

```bash
curl -s "$BASE_URL/me" \
  -H "Authorization: Bearer $TOKEN_DOSEN_B"
```

### Mahasiswa

```bash
curl -s "$BASE_URL/me" \
  -H "Authorization: Bearer $TOKEN_MAHASISWA"
```

Dari hasil pengujian, endpoint `/me` digunakan untuk memastikan:

- Token valid.
- Role pengguna sesuai.
- ID pengguna dapat diketahui.
- Email pengguna sesuai dengan akun yang digunakan.

---

# 14. Menentukan ID Data untuk Pengujian

Script pengujian membutuhkan beberapa ID data.

Variabel yang digunakan:

```bash
ID_TUGAS_DOSEN_A
ID_TUGAS_DOSEN_B
ID_SUBMISSION
```

---

## 14.1 Menentukan Course Dosen

Daftar course dapat diperoleh menggunakan:

```bash
curl -s "$BASE_URL/courses" \
  -H "Authorization: Bearer $TOKEN_DOSEN_A"
```

Untuk Dosen B:

```bash
curl -s "$BASE_URL/courses" \
  -H "Authorization: Bearer $TOKEN_DOSEN_B"
```

Dari response tersebut dapat diketahui course yang dimiliki oleh masing-masing dosen.

---

## 14.2 Menentukan Assignment Milik Dosen

Assignment dari suatu course dapat diperoleh dengan:

```bash
curl -s "$BASE_URL/courses/ID_COURSE/assignments" \
  -H "Authorization: Bearer $TOKEN_DOSEN_A"
```

Contoh:

```bash
curl -s "$BASE_URL/courses/1/assignments" \
  -H "Authorization: Bearer $TOKEN_DOSEN_A"
```

Dari hasil pengujian production, salah satu assignment milik Dosen A menggunakan:

```text
ID Assignment Dosen A = 1
```

Untuk Dosen B, assignment diperoleh melalui course milik Dosen B:

```bash
curl -s "$BASE_URL/courses/ID_COURSE_DOSEN_B/assignments" \
  -H "Authorization: Bearer $TOKEN_DOSEN_B"
```

Dari hasil pengujian, salah satu assignment milik Dosen B menggunakan:

```text
ID Assignment Dosen B = 11
```

ID tersebut digunakan untuk menguji pencegahan akses antar dosen.

---

## 14.3 Menentukan Submission

Daftar submission dari sebuah assignment dapat diperoleh menggunakan:

```bash
curl -s "$BASE_URL/assignments/ID_ASSIGNMENT/submissions" \
  -H "Authorization: Bearer $TOKEN_DOSEN_A"
```

Contoh:

```bash
curl -s "$BASE_URL/assignments/1/submissions" \
  -H "Authorization: Bearer $TOKEN_DOSEN_A"
```

Dari hasil tersebut dapat dipilih submission yang benar-benar berkaitan dengan assignment milik Dosen A.

Salah satu ID submission yang digunakan dalam pengujian adalah:

```text
ID Submission = 3
```

---

## 14.4 Mengatur Variabel ID

Setelah seluruh data ditemukan, variabel environment dapat diatur:

```bash
export ID_TUGAS_DOSEN_A=1
export ID_TUGAS_DOSEN_B=11
export ID_SUBMISSION=3
```

Verifikasi:

```bash
echo "$BASE_URL"
echo "$ID_TUGAS_DOSEN_A"
echo "$ID_TUGAS_DOSEN_B"
echo "$ID_SUBMISSION"
```

Pada pengujian production, nilai tersebut berhasil digunakan oleh kedua script pengujian.

---

# 15. Pengujian API

Script berikut digunakan:

```bash
bash scripts/test-api.sh
```

Script melakukan pengujian terhadap beberapa endpoint penting:

### POST `/assignments`

Pengujian meliputi:

- Request tanpa token.
- Mahasiswa mencoba membuat tugas.
- Dosen membuat tugas dengan data kosong.

### PUT `/assignments/{assignment}`

Pengujian meliputi:

- Request tanpa token.
- Mahasiswa mencoba mengubah tugas.
- Dosen A mencoba mengubah tugas milik Dosen B.
- Dosen A mencoba mengubah tugas miliknya dengan request kosong.

### PUT `/submissions/{submission}/grade`

Pengujian meliputi:

- Request tanpa token.
- Mahasiswa mencoba memberikan nilai.
- Dosen B mencoba menilai submission milik Dosen A.
- Dosen A menilai submission miliknya dengan request kosong.

---

## 15.1 Hasil Pengujian API

Hasil pengujian production menunjukkan:

| Pengujian | HTTP yang Diharapkan | Hasil |
|---|---:|---|
| POST assignment tanpa token | 401 | Lulus |
| Mahasiswa membuat assignment | 403 | Lulus |
| Dosen membuat assignment dengan form kosong | 422 | Lulus |
| PUT assignment tanpa token | 401 | Lulus |
| Mahasiswa mengubah assignment | 403 | Lulus |
| Dosen A mengubah assignment Dosen B | 403 | Lulus |
| Dosen A mengubah assignment miliknya dengan form kosong | 422 | Lulus |
| PUT grade tanpa token | 401 | Lulus |
| Mahasiswa memberikan nilai | 403 | Lulus |
| Dosen B menilai submission milik Dosen A | 403 | Lulus |
| Dosen A menilai submission dengan form kosong | 422 | Lulus |

Output akhir:

```text
Semua pengujian API berhasil.
```

---

# 16. Pengujian Authentication dan Authorization

Script berikut digunakan:

```bash
bash scripts/test-authz.sh
```

Pengujian dilakukan untuk memastikan mekanisme authentication dan authorization berjalan sesuai dengan role pengguna.

---

## 16.1 Akses Tanpa Token

Endpoint yang membutuhkan autentikasi harus menolak request tanpa token.

Pengujian meliputi:

```text
GET /courses
POST /assignments
```

Expected result:

```text
401 Unauthorized
```

Hasil:

```text
Lulus
```

---

## 16.2 Pembatasan Berdasarkan Role

Mahasiswa tidak diperbolehkan mengakses fitur yang hanya dapat digunakan oleh dosen.

Pengujian meliputi:

```text
POST /assignments
POST /grade-components
```

Expected result:

```text
403 Forbidden
```

Hasil:

```text
Lulus
```

---

## 16.3 Pencegahan IDOR Antar Dosen

Pengujian juga dilakukan untuk memastikan seorang dosen tidak dapat mengakses atau memodifikasi resource milik dosen lain.

Pengujian meliputi:

```text
Dosen A → Assignment milik Dosen B
Dosen B → Submission milik Dosen A
```

Expected result:

```text
403 Forbidden
```

Hasil:

```text
Lulus
```

Pengujian ini digunakan untuk memverifikasi pencegahan **Insecure Direct Object Reference (IDOR)** antar pengguna dengan role dosen.

---

## 16.4 Akses Valid Sesuai Hak

Pengujian dilakukan terhadap akses yang valid.

Hasil yang diperoleh:

```text
GET /courses → 200 OK
PUT assignment milik sendiri dengan form kosong → 422
PUT grade submission milik sendiri dengan form kosong → 422
```

Status `422` pada request kosong menunjukkan bahwa request telah berhasil melewati proses authentication dan authorization, kemudian ditolak oleh validasi data.

---

## 16.5 Akses Admin

Administrator diuji menggunakan endpoint:

```text
GET /courses
```

Expected result:

```text
200 OK
```

Hasil:

```text
Lulus
```

---

# 17. Hasil Akhir Pengujian Authorization

| Pengujian | HTTP yang Diharapkan | Status |
|---|---:|---|
| Request tanpa token | 401 | Lulus |
| Mahasiswa mengakses fitur dosen | 403 | Lulus |
| Mahasiswa membuat komponen nilai | 403 | Lulus |
| Dosen A mengubah data Dosen B | 403 | Lulus |
| Dosen B menilai submission milik Dosen A | 403 | Lulus |
| Dosen melihat course | 200 | Lulus |
| Dosen mengubah resource sendiri dengan request kosong | 422 | Lulus |
| Dosen menilai submission sendiri dengan request kosong | 422 | Lulus |
| Admin mengakses endpoint | 200 | Lulus |

Output akhir:

```text
Semua pengujian otorisasi berhasil.
```

---

# 18. Temuan Selama Pengujian

Pada pengujian awal ditemukan bahwa request kosong ke endpoint:

```text
PUT /assignments/{assignment}
```

sebelumnya mengembalikan:

```text
200 OK
```

Padahal script pengujian mengharapkan:

```text
422 Unprocessable Entity
```

Penyebabnya adalah validasi pada method `update()` menggunakan aturan:

```php
'sometimes'
```

yang memungkinkan request kosong tidak memiliki field yang perlu divalidasi.

Validasi kemudian diperbaiki agar request kosong tidak dianggap sebagai update yang valid.

Setelah perbaikan diterapkan dan deployment diperbarui, pengujian menghasilkan:

```text
Dosen A mengubah tugas miliknya dengan form kosong
Hasil : BERHASIL (HTTP 422)
```

Setelah itu, seluruh pengujian API dan authorization berhasil.

---

# 19. Status Deployment

Deployment KampusLMS berhasil dilakukan di Railway.

Konfigurasi deployment memastikan:

- Aplikasi Laravel dapat berjalan di Railway.
- Database MySQL terhubung dengan aplikasi.
- Migration dapat dijalankan pada deployment.
- Data database tidak dibuat ulang pada setiap deployment.
- Seeder tidak berjalan berulang kali saat container dimulai.
- Halaman utama diarahkan ke halaman login.
- Aset frontend berhasil dibangun menggunakan Vite.
- Aplikasi menggunakan timezone WITA.
- API production dapat diakses.
- Authentication menggunakan token berhasil diverifikasi.
- Authorization berdasarkan role berjalan.
- Pencegahan akses antar resource dosen berjalan.
- Pengujian API berhasil.
- Pengujian authentication dan authorization berhasil.

## Hasil Akhir

```text
Deployment          : BERHASIL
API Testing         : SEMUA BERHASIL
Authorization Test  : SEMUA BERHASIL
Database Migration  : BERHASIL
Routing             : BERHASIL
Timezone WITA       : BERHASIL
```

Dengan demikian, aplikasi KampusLMS telah berhasil dideploy dan diverifikasi pada lingkungan production Railway.