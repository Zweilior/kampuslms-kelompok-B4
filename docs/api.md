# Dokumentasi REST API KampusLMS (v1)

Dokumen ini menjelaskan seluruh endpoint REST API KampusLMS sesuai kontrak
Bagian 5 spesifikasi proyek. Semua contoh dapat dijalankan langsung terhadap
data seeder.[cite: 14]

- [1. Ikhtisar](#1-ikhtisar)
- [2. Konvensi](#2-konvensi)
- [3. Ringkasan endpoint](#3-ringkasan-endpoint)
- [4. Detail endpoint](#4-detail-endpoint)
- [5. Matriks hak akses](#5-matriks-hak-akses)
- [6. Catatan keamanan](#6-catatan-keamanan)
- [7. Batasan yang diketahui](#7-batasan-yang-diketahui)[cite: 14]

---

## 1. Ikhtisar

| Item | Nilai |
|------|-------|
| Base URL (lokal) | `http://localhost:8000/api/v1` |
| Autentikasi | Laravel Sanctum, Bearer token |
| Format | JSON (`Accept: application/json`) |
| Zona waktu tanggal | ISO 8601, UTC |
| Rate limit | `POST /auth/login`: 5 permintaan/menit. Endpoint lain: 60 permintaan/menit |[cite: 14]

### Menyiapkan data demo

```bash
php artisan migrate:fresh --seed
php artisan serve
```

### Akun demo (dari seeder)

Kata sandi akun demo diatur melalui konfigurasi `auth.demo_password` (`config/auth.php`) yang membaca variabel lingkungan `DEMO_PASSWORD`:
- **Lingkungan `local` / `testing`**: Jika `DEMO_PASSWORD` dikosongkan di `.env`, seeder otomatis memakai *fallback* kata sandi **`password`**[cite: 9]. Jika `DEMO_PASSWORD` diisi nilai khusus, gunakan nilai tersebut[cite: 9].
- **Lingkungan `production`**: *Fallback* dinonaktifkan (`null`)[cite: 9]. Seeder hanya berjalan jika `DEMO_PASSWORD` diisi secara eksplisit melalui *environment variable* server (misalnya Railway)[cite: 9]. Kredensial *production* tidak dicantumkan di dalam dokumentasi maupun *repository*[cite: 4, 9].

| Peran | Email | Password (Fallback Lokal/Testing) |
|-------|-------|-----------------------------------|
| Admin | `admin@kampuslms.test` | `password` *(atau sesuai `DEMO_PASSWORD`)*[cite: 9, 14] |
| Dosen | `dosen@kampuslms.test` (Budi Santoso, mengajar Pemrograman Web) | `password` *(atau sesuai `DEMO_PASSWORD`)*[cite: 9, 14] |
| Mahasiswa | `mahasiswa@kampuslms.test` (Andi Pratama, mengikuti Basis Data, Rekayasa Perangkat Lunak, dan Analisis dan Perancangan Sistem) | `password` *(atau sesuai `DEMO_PASSWORD`)*[cite: 9, 14] |

> ID pada contoh response (course, tugas, submission) dapat berbeda di database
> lokal Anda, tergantung data tambahan yang pernah dibuat. Untuk hasil yang sama
> dengan dokumen ini, gunakan `migrate:fresh --seed`.[cite: 14]

### Cara mengambil token

Contoh di bawah menggunakan kata sandi *fallback* lokal (`password`). Sesuaikan nilai `password` dengan `DEMO_PASSWORD` yang aktif di lingkungan pengujian Anda[cite: 9, 14]:

```bash
curl -s -X POST http://localhost:8000/api/v1/auth/login \
  -H "Accept: application/json" \
  -d "email=dosen@kampuslms.test" -d "password=password"
```

Salin nilai `token` (lengkap dengan bagian `angka|`), lalu pakai di semua contoh:[cite: 14]

```bash
export TOKEN="isi-token-di-sini"
```

> **Windows CMD:** perintah `curl` pada dokumen ini ditulis multi-baris dengan
> `\`. Di CMD tulis dalam satu baris, dan ganti `$TOKEN` dengan token Anda.
> Setiap login menghapus token lama milik akun yang sama.[cite: 14]

---

## 2. Konvensi

### Header wajib

```
Accept: application/json
Authorization: Bearer <token>        (kecuali POST /auth/login)
```

Seluruh error di bawah `/api/*` selalu dikembalikan sebagai JSON, walaupun
header `Accept` terlupa.[cite: 14]

### Format response sukses

Satu objek:[cite: 14]

```json
{ "data": { "id": 1 } }
```

Koleksi (dengan paginasi 15 item per halaman, gunakan `?page=2` dst.):[cite: 14]

```json
{
  "data": [ { "id": 1 } ],
  "meta": { "current_page": 1, "last_page": 5, "total": 47 }
}
```

`POST` yang membuat data baru mengembalikan **201**. `DELETE` dan `logout`
mengembalikan **204** tanpa isi.[cite: 14]

### Format response error

| Status | Kapan | Isi |
|--------|-------|-----|
| 401 | Token tidak ada, salah, atau sudah dicabut | `{"message":"Unauthenticated."}` |
| 403 | Login valid, tetapi tidak berhak | `{"message":"Anda tidak memiliki akses ke sumber daya ini."}` |
| 404 | Sumber daya tidak ada (atau sengaja disembunyikan) | `{"message":"Sumber daya tidak ditemukan."}` |
| 409 | Konflik keadaan data | `{"message":"<penjelasan>"}` |
| 422 | Validasi gagal | `{"message":"Data yang diberikan tidak valid.","errors":{"<field>":["..."]}}` |
| 429 | Melebihi rate limit | `{"message":"Too Many Attempts."}` + header `Retry-After` |[cite: 14]

Perbedaan penting: **401** berarti belum terautentikasi, **403** berarti sudah
login tetapi perannya atau kepemilikannya tidak sesuai.[cite: 14]

> Dengan `APP_DEBUG=true` (lingkungan lokal), Laravel menambahkan field debug
> (`exception`, `file`, `trace`) pada beberapa error seperti 409 dan 429. Di
> produksi `APP_DEBUG=false`, jadi hanya `message` yang tampil.[cite: 14]

---

## 3. Ringkasan endpoint

| # | Method | Endpoint | Akses |
|---|--------|----------|-------|
| 1 | POST | `/auth/login` | Publik |
| 2 | POST | `/auth/logout` | Semua peran |
| 3 | GET | `/me` | Semua peran |
| 4 | GET | `/courses` | Semua peran (disaring per peran) |
| 5 | GET | `/courses/{id}` | Admin, dosen pemilik, mahasiswa terdaftar |
| 6 | GET | `/courses/{id}/materials` | Admin, dosen pemilik, mahasiswa terdaftar |
| 7 | GET | `/courses/{id}/assignments` | Admin, dosen pemilik, mahasiswa terdaftar |
| 8 | POST | `/assignments` | Dosen pemilik course |
| 9 | PUT / PATCH | `/assignments/{id}` | Dosen pemilik |
| 10 | DELETE | `/assignments/{id}` | Dosen pemilik |
| 11 | GET | `/assignments/{id}/submissions` | Dosen pemilik |
| 12 | POST | `/assignments/{id}/submissions` | Mahasiswa terdaftar |
| 13 | PUT | `/submissions/{id}/grade` | Dosen pemilik |
| 14 | GET | `/notifications` | Semua peran (milik sendiri) |
| 15 | POST | `/notifications/{id}/read` | Semua peran (milik sendiri) |[cite: 14]

---

## 4. Detail endpoint

### 4.1 Auth

#### 1. `POST /auth/login`

- **Akses:** publik. Dibatasi 5 permintaan per menit.[cite: 14]
- **Parameter (body):**[cite: 14]

| Field | Aturan |
|-------|--------|
| `email` | wajib, format email |
| `password` | wajib |[cite: 14]

**Request** *(menggunakan fallback password lokal)*[cite: 9, 14]

```bash
curl -s -X POST http://localhost:8000/api/v1/auth/login \
  -H "Accept: application/json" \
  -d "email=dosen@kampuslms.test" -d "password=password"
```

**Sukses: 200**[cite: 14]

```json
{
  "data": {
    "token": "13|g8kOo63Yicu05vXkQvbuTZVBpgPDl2vvrykcXFVsf9175f3c",
    "token_type": "Bearer"
  }
}
```

**Gagal**[cite: 14]

- 401, email atau password salah (pesan sama untuk keduanya, agar email yang
  terdaftar tidak bisa ditebak):[cite: 14]
  ```json
  { "message": "Email atau password salah." }
  ```
- 422, field kosong:[cite: 14]
  ```json
  {
    "message": "Data yang diberikan tidak valid.",
    "errors": { "email": ["The email field is required."] }
  }
  ```
- 429, lebih dari 5 percobaan dalam semenit:[cite: 14]
  ```json
  { "message": "Too Many Attempts." }
  ```

#### 2. `POST /auth/logout`

- **Akses:** semua peran yang login. Hanya mencabut token yang sedang dipakai.[cite: 14]

```bash
curl -i -X POST http://localhost:8000/api/v1/auth/logout \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 204 No Content** (tanpa isi).[cite: 14]
**Gagal: 401** `{"message":"Unauthenticated."}`[cite: 14]

#### 3. `GET /me`

- **Akses:** semua peran yang login.[cite: 14]
- Pemilik akun melihat `email` dan `nim_nip` miliknya sendiri.[cite: 14]

```bash
curl -s http://localhost:8000/api/v1/me \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 200**[cite: 14]

```json
{
  "data": {
    "id": 3,
    "name": "Andi Pratama",
    "role": "mahasiswa",
    "nim_nip": "NIM001",
    "email": "mahasiswa@kampuslms.test"
  }
}
```

**Gagal: 401** `{"message":"Unauthenticated."}`[cite: 14]

---

### 4.2 Courses

#### 4. `GET /courses`

- **Akses:** semua peran, hasil disaring di level query.[cite: 14]
  - Dosen: mata kuliah yang diajar.[cite: 14]
  - Mahasiswa: mata kuliah yang diikuti.[cite: 14]
  - Admin: semua mata kuliah.[cite: 14]
- **Parameter:** `?page=` (opsional).[cite: 14]
- Untuk mahasiswa, `assignments_count` hanya menghitung tugas `published`.[cite: 14]

```bash
curl -s "http://localhost:8000/api/v1/courses?page=1" \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 200** (response mahasiswa, dipersingkat dari 3 item)[cite: 14]

```json
{
  "data": [
    {
      "id": 2,
      "code": "SI251402",
      "name": "Basis Data",
      "description": "Mata kuliah Basis Data pada Kampus LMS.",
      "sks": 3,
      "status": "active",
      "lecturer": { "id": 4, "name": "Roberto Ullrich", "role": "dosen" },
      "materials_count": 0,
      "assignments_count": 2
    }
  ],
  "meta": { "current_page": 1, "last_page": 1, "total": 3 }
}
```

Objek `lecturer` memuat `nim_nip` dan `email` hanya untuk admin. Mahasiswa tidak
mendapatkannya.[cite: 14]

**Gagal: 401** tanpa token.[cite: 14]

#### 5. `GET /courses/{id}`

- **Akses:** admin, dosen pemilik course, atau mahasiswa yang terdaftar.[cite: 14]

```bash
curl -s http://localhost:8000/api/v1/courses/2 \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 200**[cite: 14]

```json
{
  "data": {
    "id": 2,
    "code": "SI251402",
    "name": "Basis Data",
    "description": "Mata kuliah Basis Data pada Kampus LMS.",
    "sks": 3,
    "status": "active",
    "lecturer": { "id": 4, "name": "Roberto Ullrich", "role": "dosen" },
    "materials_count": 0,
    "assignments_count": 2
  }
}
```

**Gagal**[cite: 14]

- 403, bukan pemilik atau tidak terdaftar:
  `{"message":"Anda tidak memiliki akses ke sumber daya ini."}`[cite: 14]
- 404, course tidak ada: `{"message":"Sumber daya tidak ditemukan."}`[cite: 14]

#### 6. `GET /courses/{id}/materials`

- **Akses:** sama dengan `GET /courses/{id}`.[cite: 14]
- **Parameter:** `?page=` (opsional).[cite: 14]

```bash
curl -s http://localhost:8000/api/v1/courses/1/materials \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 200** (nilai contoh)[cite: 14]

```json
{
  "data": [
    {
      "id": 1,
      "course_id": 1,
      "title": "Pengenalan Laravel",
      "description": "Slide pertemuan 1.",
      "type": "file",
      "original_name": "pertemuan-1.pdf",
      "file_size": 1048576,
      "mime_type": "application/pdf",
      "external_url": null
    }
  ],
  "meta": { "current_page": 1, "last_page": 1, "total": 2 }
}
```

Path penyimpanan internal file tidak pernah ditampilkan. Pengunduhan file
dibuat sebagai endpoint terpisah pada minggu berikutnya.[cite: 14]

**Gagal:** 403 (tidak berhak), 404 (course tidak ada).[cite: 14]

#### 7. `GET /courses/{id}/assignments`

- **Akses:** sama dengan `GET /courses/{id}`.[cite: 14]
- **Parameter:**[cite: 14]

| Parameter | Keterangan |
|-----------|------------|
| `?status=` | opsional: `draft` atau `published` |
| `?page=` | opsional |[cite: 14]

- Mahasiswa hanya pernah melihat tugas `published`, apa pun nilai `?status=`.[cite: 14]

```bash
curl -s "http://localhost:8000/api/v1/courses/2/assignments?status=published" \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 200**[cite: 14]

```json
{
  "data": [
    {
      "id": 4,
      "course_id": 2,
      "title": "Tugas 1 - Basis Data",
      "instructions": "Kerjakan tugas sesuai materi yang telah diberikan.",
      "due_at": "2026-09-07T16:34:25.000000Z",
      "max_score": 100,
      "allow_late": true,
      "status": "published"
    }
  ],
  "meta": { "current_page": 1, "last_page": 1, "total": 2 }
}
```

**Gagal:** 403 (tidak berhak), 404 (course tidak ada).[cite: 14]

---

### 4.3 Assignments

#### 8. `POST /assignments`

- **Akses:** dosen, hanya pada course yang diajarnya.[cite: 14]
- **Parameter (body):**[cite: 14]

| Field | Aturan |
|-------|--------|
| `course_id` | wajib, integer |
| `title` | wajib, maks 255 karakter |
| `instructions` | wajib |
| `due_at` | wajib, tanggal (mis. `2026-12-31 23:59:00`) |
| `max_score` | wajib, integer, minimal 0 |
| `allow_late` | wajib, boolean (`1`/`0`/`true`/`false`) |
| `status` | wajib: `draft` atau `published` |[cite: 14]

```bash
curl -s -X POST http://localhost:8000/api/v1/assignments \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN" \
  -d "course_id=1" -d "title=Tugas 5 - Pemrograman Web" \
  -d "instructions=Buat REST API sederhana." \
  -d "due_at=2026-12-31 23:59:00" -d "max_score=100" \
  -d "allow_late=1" -d "status=draft"
```

**Sukses: 201**[cite: 14]

```json
{
  "data": {
    "id": 19,
    "course_id": 1,
    "title": "Tugas 5 - Pemrograman Web",
    "instructions": "Buat REST API sederhana.",
    "due_at": "2026-12-31T23:59:00.000000Z",
    "max_score": 100,
    "allow_late": true,
    "status": "draft"
  }
}
```

**Gagal**[cite: 14]

- 403, bukan dosen, atau course bukan milik dosen ini. Course yang tidak ada
  juga dijawab 403, supaya ID course tidak bisa ditebak:
  `{"message":"Anda tidak memiliki akses ke sumber daya ini."}`[cite: 14]
- 422, validasi gagal:[cite: 14]
  ```json
  {
    "message": "Data yang diberikan tidak valid.",
    "errors": {
      "title": ["The title field is required."],
      "status": ["The status field is required."]
    }
  }
  ```

#### 9. `PUT` / `PATCH /assignments/{id}`

- **Akses:** dosen pemilik course dari tugas ini.[cite: 14]
- **Parameter:** sama dengan `POST /assignments` (tanpa `course_id`), semuanya
  **opsional**. Hanya field yang dikirim yang diubah.[cite: 14]

```bash
curl -s -X PATCH http://localhost:8000/api/v1/assignments/19 \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN" \
  -d "status=published" -d "max_score=90"
```

**Sukses: 200** (objek tugas yang sudah diperbarui)[cite: 14]

```json
{
  "data": {
    "id": 19,
    "course_id": 1,
    "title": "Tugas 5 - Pemrograman Web",
    "instructions": "Buat REST API sederhana.",
    "due_at": "2026-12-31T23:59:00.000000Z",
    "max_score": 90,
    "allow_late": true,
    "status": "published"
  }
}
```

**Gagal:** 403 (bukan dosen pemilik), 404 (tugas tidak ada), 422 (nilai tidak valid).[cite: 14]

#### 10. `DELETE /assignments/{id}`

- **Akses:** dosen pemilik course dari tugas ini.[cite: 14]
- Tugas yang **sudah memiliki pengumpulan mahasiswa tidak dapat dihapus**.[cite: 14]

```bash
curl -i -X DELETE http://localhost:8000/api/v1/assignments/19 \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 204 No Content**[cite: 14]

**Gagal**[cite: 14]

- 403, bukan dosen pemilik.[cite: 14]
- 404, tugas tidak ada.[cite: 14]
- 409, sudah ada pengumpulan:[cite: 14]
  ```json
  { "message": "Tugas tidak dapat dihapus karena sudah memiliki pengumpulan mahasiswa." }
  ```

---

### 4.4 Submissions dan penilaian

#### 11. `GET /assignments/{id}/submissions`

- **Akses:** dosen pemilik course dari tugas ini. Admin dan mahasiswa mendapat 403.[cite: 14]
- **Parameter:** `?page=` (opsional). Terbaru lebih dulu.[cite: 14]
- Dosen melihat `nim_nip` mahasiswa tetapi tidak melihat email mereka.[cite: 14]

```bash
curl -s http://localhost:8000/api/v1/assignments/1/submissions \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 200** (dipersingkat dari 10 item)[cite: 14]

```json
{
  "data": [
    {
      "id": 1,
      "assignment_id": 1,
      "student": {
        "id": 7,
        "name": "Catalina Beier Sr.",
        "role": "mahasiswa",
        "nim_nip": "4626081930"
      },
      "original_name": "tugas-1.pdf",
      "file_size": 3528221,
      "note": "Pengumpulan tugas oleh mahasiswa.",
      "submitted_at": "2026-09-08T16:34:25+00:00",
      "is_late": true,
      "grade": {
        "id": 1,
        "submission_id": 1,
        "score": 99.32,
        "feedback": "Hasil pekerjaan sangat baik.",
        "graded_at": "2026-09-14T16:34:26+00:00"
      }
    }
  ],
  "meta": { "current_page": 1, "last_page": 1, "total": 10 }
}
```

`grade` bernilai `null` untuk submission yang belum dinilai.[cite: 14]

**Gagal:** 403 (bukan dosen pemilik, termasuk dosen lain), 404 (tugas tidak ada).[cite: 14]

#### 12. `POST /assignments/{id}/submissions`

- **Akses:** mahasiswa yang terdaftar di course tugas ini.[cite: 14]
- **Format:** `multipart/form-data`.[cite: 14]
- **Parameter:**[cite: 14]

| Field | Aturan |
|-------|--------|
| `file` | wajib, tipe `pdf`, `doc`, `docx`, atau `zip`, maks 5 MB |
| `note` | opsional, teks, maks 1000 karakter |[cite: 14]

**Aturan bisnis**[cite: 14]

- Tugas harus berstatus `published`. Tugas draft dijawab **404**.[cite: 14]
- Lewat tenggat: ditandai `is_late: true`. Jika `allow_late` bernilai `false`,
  pengumpulan ditolak dengan 422.[cite: 14]
- **Kirim ulang** menimpa pengumpulan sebelumnya (file lama dihapus) selama belum
  dinilai: **200**. Jika sudah dinilai: **409**.[cite: 14]
- File disimpan di penyimpanan privat dengan nama acak. Path internal tidak
  pernah ditampilkan.[cite: 14]
- Dosen pemilik course menerima notifikasi `PengumpulanBaru`.[cite: 14]

```bash
curl -s -X POST http://localhost:8000/api/v1/assignments/4/submissions \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN" \
  -F "file=@tugas.pdf" -F "note=Pengumpulan pertama"
```

**Sukses: 201** (pertama kali) atau **200** (kirim ulang)[cite: 14]

```json
{
  "data": {
    "id": 61,
    "assignment_id": 4,
    "original_name": "tugas.pdf",
    "file_size": 245760,
    "note": "Pengumpulan pertama",
    "submitted_at": "2026-10-06T11:30:00+00:00",
    "is_late": true
  }
}
```

**Gagal**[cite: 14]

- 403, bukan mahasiswa, atau tidak terdaftar di course ini.[cite: 14]
- 404, tugas tidak ada atau masih draft.[cite: 14]
- 409, sudah dinilai:[cite: 14]
  ```json
  { "message": "Tugas ini sudah dinilai dan tidak dapat dikumpulkan ulang." }
  ```
- 422, file salah atau tenggat lewat:[cite: 14]
  ```json
  {
    "message": "Data yang diberikan tidak valid.",
    "errors": { "file": ["The file field must be a file of type: pdf, doc, docx, zip."] }
  }
  ```
  ```json
  {
    "message": "Data yang diberikan tidak valid.",
    "errors": { "assignment": ["Batas waktu pengumpulan sudah lewat."] }
  }
  ```

#### 13. `PUT /submissions/{id}/grade`

- **Akses:** dosen pemilik course dari tugas tempat submission ini berada.[cite: 14]
- **Sifat:** *upsert*, aman dipanggil berulang. **201** saat nilai dibuat
  pertama kali, **200** saat diperbarui. Penilaian ulang tanpa `feedback`
  mengosongkan feedback sebelumnya.[cite: 14]
- **Parameter (body):**[cite: 14]

| Field | Aturan |
|-------|--------|
| `score` | wajib, angka, antara 0 dan `max_score` tugas |
| `feedback` | opsional, teks, maks 2000 karakter |[cite: 14]

- Mahasiswa pemilik submission menerima notifikasi `NilaiDiberikan`.[cite: 14]

```bash
curl -s -X PUT http://localhost:8000/api/v1/submissions/1/grade \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN" \
  -d "score=85" -d "feedback=Bagus"
```

**Sukses: 201** (pertama kali) atau **200** (diperbarui)[cite: 14]

```json
{
  "data": {
    "id": 61,
    "submission_id": 1,
    "score": 85,
    "feedback": "Bagus",
    "graded_at": "2026-10-06T11:12:12+00:00",
    "grader": {
      "id": 2,
      "name": "Budi Santoso",
      "role": "dosen",
      "nim_nip": "NIP001",
      "email": "dosen@kampuslms.test"
    }
  }
}
```

**Gagal**[cite: 14]

- 403, mahasiswa, atau dosen yang bukan pemilik course.[cite: 14]
- 404, submission tidak ada.[cite: 14]
- 422, nilai di luar rentang:[cite: 14]
  ```json
  {
    "message": "Data yang diberikan tidak valid.",
    "errors": { "score": ["The score field must not be greater than 100."] }
  }
  ```

#### 14. Submission
- Mahasiswa TIDAK dapat mengubah atau menghapus submission sendiri. 
  Untuk submit ulang, hubungi dosen atau admin.[cite: 14]

---

### 4.5 Notifications

#### 14. `GET /notifications`

- **Akses:** semua peran. Hanya notifikasi milik user yang login, terbaru lebih
  dulu.[cite: 14]
- **Parameter:** `?page=` (opsional).[cite: 14]

```bash
curl -s http://localhost:8000/api/v1/notifications \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 200**[cite: 14]

```json
{
  "data": [
    {
      "id": "1a312cd2-7351-4efb-aa5d-1f5cb0145ce0",
      "type": "TugasBaru",
      "data": { "message": "Tugas baru telah diterbitkan." },
      "is_read": false,
      "read_at": null,
      "created_at": "2026-10-06T09:28:28+00:00"
    }
  ],
  "meta": { "current_page": 1, "last_page": 1, "total": 3 }
}
```

Nilai `type` yang ada: `TugasBaru`, `NilaiDiberikan`, `PengingatTenggat`,
`PengumpulanBaru`.[cite: 14]

**Gagal: 401** tanpa token.[cite: 14]

#### 15. `POST /notifications/{id}/read`

- **Akses:** semua peran, hanya untuk notifikasi miliknya. `id` berupa UUID.[cite: 14]
- **Idempoten:** dipanggil berulang tetap 200 dan `read_at` tidak berubah.[cite: 14]

```bash
curl -s -X POST \
  http://localhost:8000/api/v1/notifications/1a312cd2-7351-4efb-aa5d-1f5cb0145ce0/read \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 200**[cite: 14]

```json
{
  "data": {
    "id": "1a312cd2-7351-4efb-aa5d-1f5cb0145ce0",
    "type": "TugasBaru",
    "data": { "message": "Tugas baru telah diterbitkan." },
    "is_read": true,
    "read_at": "2026-10-06T09:39:21+00:00",
    "created_at": "2026-10-06T09:28:28+00:00"
  }
}
```

(`data` yang di dalam adalah isi notifikasi; `data` di luar adalah pembungkus
response.)[cite: 14]

**Gagal**[cite: 14]

- 403, notifikasi milik pengguna lain.[cite: 14]
- 404, notifikasi tidak ada: `{"message":"Sumber daya tidak ditemukan."}`[cite: 14]

---

## 5. Matriks hak akses

| Endpoint | Admin | Dosen | Mahasiswa |
|----------|:-----:|:-----:|:---------:|
| `POST /auth/login` | ✅ | ✅ | ✅ |
| `POST /auth/logout`, `GET /me` | ✅ | ✅ | ✅ |
| `GET /courses` | semua | yang diajar | yang diikuti |
| `GET /courses/{id}` dan `/materials` | ✅ | pemilik | terdaftar |
| `GET /courses/{id}/assignments` | semua | pemilik | terdaftar, hanya `published` |
| `POST /assignments` | ❌ 403 | pemilik course | ❌ 403 |
| `PUT/PATCH/DELETE /assignments/{id}` | ❌ 403 | pemilik | ❌ 403 |
| `GET /assignments/{id}/submissions` | ❌ 403 | pemilik | ❌ 403 |
| `POST /assignments/{id}/submissions` | ❌ 403 | ❌ 403 | terdaftar |
| `PUT /submissions/{id}/grade` | ❌ 403 | pemilik | ❌ 403 |
| `GET /notifications`, `POST /notifications/{id}/read` | milik sendiri | milik sendiri | milik sendiri |[cite: 14]

---

## 6. Catatan keamanan

- **Otorisasi dicek di level query atau sebelum aksi**, bukan sekadar
  menyembunyikan tombol di tampilan. Mengganti ID di URL tidak memberi akses ke
  data pengguna lain.[cite: 14]
- **Otorisasi dicek sebelum validasi.** Pengguna yang tidak berhak selalu
  mendapat 403, bukan 422.[cite: 14]
- **Daftar putih field.** API Resource hanya mengeluarkan field yang
  diizinkan. `password`, token, dan path internal file tidak pernah dikeluarkan.
  `email` hanya terlihat oleh pemilik akun dan admin, `nim_nip` oleh pemilik,
  admin, dan dosen.[cite: 14]
- **Pesan login seragam** untuk email salah maupun password salah.[cite: 14]
- **Rate limiting** pada login (5/menit) dan seluruh endpoint lain (60/menit).[cite: 14]
- **File unggahan** disimpan di penyimpanan privat (bukan folder publik)
  dengan nama acak. Nama dari klien tidak dipakai sebagai path.[cite: 14]
- **Tugas draft** tidak terlihat oleh mahasiswa, termasuk jumlahnya pada
  `assignments_count` dan saat mencoba mengumpulkan.[cite: 14]

---

## 7. Batasan yang diketahui

- Pesan di dalam `errors` (detail per field) masih berbahasa Inggris bawaan
  Laravel. Field `message` utamanya sudah berbahasa Indonesia sesuai kontrak.[cite: 14]
- Belum ada endpoint pengunduhan file materi dan submission (direncanakan pada
  minggu berikutnya).[cite: 14]
- Pengecekan kepemilikan saat ini ada di controller. Pada Minggu 7 logika ini
  dipindahkan ke Policy.[cite: 14]
- Satu akun hanya memiliki satu token aktif. Login baru mencabut token lama
  (termasuk di perangkat lain).[cite: 14]