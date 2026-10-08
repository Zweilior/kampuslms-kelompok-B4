# Dokumentasi REST API KampusLMS (v1)

Dokumen ini menjelaskan seluruh endpoint REST API KampusLMS sesuai kontrak
Bagian 5 spesifikasi proyek. Semua contoh dapat dijalankan langsung terhadap
data seeder.

- [1. Ikhtisar](#1-ikhtisar)
- [2. Konvensi](#2-konvensi)
- [3. Ringkasan endpoint](#3-ringkasan-endpoint)
- [4. Detail endpoint](#4-detail-endpoint)
- [5. Matriks hak akses](#5-matriks-hak-akses)
- [6. Catatan keamanan](#6-catatan-keamanan)
- [7. Batasan yang diketahui](#7-batasan-yang-diketahui)

---

## 1. Ikhtisar

| Item | Nilai |
|------|-------|
| Base URL (lokal) | `http://localhost:8000/api/v1` |
| Autentikasi | Laravel Sanctum, Bearer token |
| Format | JSON (`Accept: application/json`) |
| Zona waktu tanggal | ISO 8601, UTC |
| Rate limit | `POST /auth/login`: 5 permintaan/menit. Endpoint lain: 60 permintaan/menit |

### Menyiapkan data demo

```bash
php artisan migrate:fresh --seed
php artisan serve
```

### Akun demo (dari seeder)

| Peran | Email | Password |
|-------|-------|----------|
| Admin | `admin@kampuslms.test` | `password` |
| Dosen | `dosen@kampuslms.test` (Budi Santoso, mengajar Pemrograman Web) | `password` |
| Mahasiswa | `mahasiswa@kampuslms.test` (Andi Pratama, mengikuti Basis Data, Rekayasa Perangkat Lunak, dan Analisis dan Perancangan Sistem) | `password` |

> ID pada contoh response (course, tugas, submission) dapat berbeda di database
> lokal Anda, tergantung data tambahan yang pernah dibuat. Untuk hasil yang sama
> dengan dokumen ini, gunakan `migrate:fresh --seed`.

### Cara mengambil token

```bash
curl -s -X POST http://localhost:8000/api/v1/auth/login \
  -H "Accept: application/json" \
  -d "email=dosen@kampuslms.test" -d "password=password"
```

Salin nilai `token` (lengkap dengan bagian `angka|`), lalu pakai di semua contoh:

```bash
export TOKEN="isi-token-di-sini"
```

> **Windows CMD:** perintah `curl` pada dokumen ini ditulis multi-baris dengan
> `\`. Di CMD tulis dalam satu baris, dan ganti `$TOKEN` dengan token Anda.
> Setiap login menghapus token lama milik akun yang sama.

---

## 2. Konvensi

### Header wajib

```
Accept: application/json
Authorization: Bearer <token>        (kecuali POST /auth/login)
```

Seluruh error di bawah `/api/*` selalu dikembalikan sebagai JSON, walaupun
header `Accept` terlupa.

### Format response sukses

Satu objek:

```json
{ "data": { "id": 1 } }
```

Koleksi (dengan paginasi 15 item per halaman, gunakan `?page=2` dst.):

```json
{
  "data": [ { "id": 1 } ],
  "meta": { "current_page": 1, "last_page": 5, "total": 47 }
}
```

`POST` yang membuat data baru mengembalikan **201**. `DELETE` dan `logout`
mengembalikan **204** tanpa isi.

### Format response error

| Status | Kapan | Isi |
|--------|-------|-----|
| 401 | Token tidak ada, salah, atau sudah dicabut | `{"message":"Unauthenticated."}` |
| 403 | Login valid, tetapi tidak berhak | `{"message":"Anda tidak memiliki akses ke sumber daya ini."}` |
| 404 | Sumber daya tidak ada (atau sengaja disembunyikan) | `{"message":"Sumber daya tidak ditemukan."}` |
| 409 | Konflik keadaan data | `{"message":"<penjelasan>"}` |
| 422 | Validasi gagal | `{"message":"Data yang diberikan tidak valid.","errors":{"<field>":["..."]}}` |
| 429 | Melebihi rate limit | `{"message":"Too Many Attempts."}` + header `Retry-After` |

Perbedaan penting: **401** berarti belum terautentikasi, **403** berarti sudah
login tetapi perannya atau kepemilikannya tidak sesuai.

> Dengan `APP_DEBUG=true` (lingkungan lokal), Laravel menambahkan field debug
> (`exception`, `file`, `trace`) pada beberapa error seperti 409 dan 429. Di
> produksi `APP_DEBUG=false`, jadi hanya `message` yang tampil.

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
| 15 | POST | `/notifications/{id}/read` | Semua peran (milik sendiri) |

---

## 4. Detail endpoint

### 4.1 Auth

#### 1. `POST /auth/login`

- **Akses:** publik. Dibatasi 5 permintaan per menit.
- **Parameter (body):**

| Field | Aturan |
|-------|--------|
| `email` | wajib, format email |
| `password` | wajib |

**Request**

```bash
curl -s -X POST http://localhost:8000/api/v1/auth/login \
  -H "Accept: application/json" \
  -d "email=dosen@kampuslms.test" -d "password=password"
```

**Sukses: 200**

```json
{
  "data": {
    "token": "13|g8kOo63Yicu05vXkQvbuTZVBpgPDl2vvrykcXFVsf9175f3c",
    "token_type": "Bearer"
  }
}
```

**Gagal**

- 401, email atau password salah (pesan sama untuk keduanya, agar email yang
  terdaftar tidak bisa ditebak):
  ```json
  { "message": "Email atau password salah." }
  ```
- 422, field kosong:
  ```json
  {
    "message": "Data yang diberikan tidak valid.",
    "errors": { "email": ["The email field is required."] }
  }
  ```
- 429, lebih dari 5 percobaan dalam semenit:
  ```json
  { "message": "Too Many Attempts." }
  ```

#### 2. `POST /auth/logout`

- **Akses:** semua peran yang login. Hanya mencabut token yang sedang dipakai.

```bash
curl -i -X POST http://localhost:8000/api/v1/auth/logout \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 204 No Content** (tanpa isi).
**Gagal: 401** `{"message":"Unauthenticated."}`

#### 3. `GET /me`

- **Akses:** semua peran yang login.
- Pemilik akun melihat `email` dan `nim_nip` miliknya sendiri.

```bash
curl -s http://localhost:8000/api/v1/me \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 200**

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

**Gagal: 401** `{"message":"Unauthenticated."}`

---

### 4.2 Courses

#### 4. `GET /courses`

- **Akses:** semua peran, hasil disaring di level query.
  - Dosen: mata kuliah yang diajar.
  - Mahasiswa: mata kuliah yang diikuti.
  - Admin: semua mata kuliah.
- **Parameter:** `?page=` (opsional).
- Untuk mahasiswa, `assignments_count` hanya menghitung tugas `published`.

```bash
curl -s "http://localhost:8000/api/v1/courses?page=1" \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 200** (response mahasiswa, dipersingkat dari 3 item)

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
mendapatkannya.

**Gagal: 401** tanpa token.

#### 5. `GET /courses/{id}`

- **Akses:** admin, dosen pemilik course, atau mahasiswa yang terdaftar.

```bash
curl -s http://localhost:8000/api/v1/courses/2 \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 200**

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

**Gagal**

- 403, bukan pemilik atau tidak terdaftar:
  `{"message":"Anda tidak memiliki akses ke sumber daya ini."}`
- 404, course tidak ada: `{"message":"Sumber daya tidak ditemukan."}`

#### 6. `GET /courses/{id}/materials`

- **Akses:** sama dengan `GET /courses/{id}`.
- **Parameter:** `?page=` (opsional).

```bash
curl -s http://localhost:8000/api/v1/courses/1/materials \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 200** (nilai contoh)

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
dibuat sebagai endpoint terpisah pada minggu berikutnya.

**Gagal:** 403 (tidak berhak), 404 (course tidak ada).

#### 7. `GET /courses/{id}/assignments`

- **Akses:** sama dengan `GET /courses/{id}`.
- **Parameter:**

| Parameter | Keterangan |
|-----------|------------|
| `?status=` | opsional: `draft` atau `published` |
| `?page=` | opsional |

- Mahasiswa hanya pernah melihat tugas `published`, apa pun nilai `?status=`.

```bash
curl -s "http://localhost:8000/api/v1/courses/2/assignments?status=published" \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 200**

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

**Gagal:** 403 (tidak berhak), 404 (course tidak ada).

---

### 4.3 Assignments

#### 8. `POST /assignments`

- **Akses:** dosen, hanya pada course yang diajarnya.
- **Parameter (body):**

| Field | Aturan |
|-------|--------|
| `course_id` | wajib, integer |
| `title` | wajib, maks 255 karakter |
| `instructions` | wajib |
| `due_at` | wajib, tanggal (mis. `2026-12-31 23:59:00`) |
| `max_score` | wajib, integer, minimal 0 |
| `allow_late` | wajib, boolean (`1`/`0`/`true`/`false`) |
| `status` | wajib: `draft` atau `published` |

```bash
curl -s -X POST http://localhost:8000/api/v1/assignments \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN" \
  -d "course_id=1" -d "title=Tugas 5 - Pemrograman Web" \
  -d "instructions=Buat REST API sederhana." \
  -d "due_at=2026-12-31 23:59:00" -d "max_score=100" \
  -d "allow_late=1" -d "status=draft"
```

**Sukses: 201**

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

**Gagal**

- 403, bukan dosen, atau course bukan milik dosen ini. Course yang tidak ada
  juga dijawab 403, supaya ID course tidak bisa ditebak:
  `{"message":"Anda tidak memiliki akses ke sumber daya ini."}`
- 422, validasi gagal:
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

- **Akses:** dosen pemilik course dari tugas ini.
- **Parameter:** sama dengan `POST /assignments` (tanpa `course_id`), semuanya
  **opsional**. Hanya field yang dikirim yang diubah.

```bash
curl -s -X PATCH http://localhost:8000/api/v1/assignments/19 \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN" \
  -d "status=published" -d "max_score=90"
```

**Sukses: 200** (objek tugas yang sudah diperbarui)

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

**Gagal:** 403 (bukan dosen pemilik), 404 (tugas tidak ada), 422 (nilai tidak valid).

#### 10. `DELETE /assignments/{id}`

- **Akses:** dosen pemilik course dari tugas ini.
- Tugas yang **sudah memiliki pengumpulan mahasiswa tidak dapat dihapus**.

```bash
curl -i -X DELETE http://localhost:8000/api/v1/assignments/19 \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 204 No Content**

**Gagal**

- 403, bukan dosen pemilik.
- 404, tugas tidak ada.
- 409, sudah ada pengumpulan:
  ```json
  { "message": "Tugas tidak dapat dihapus karena sudah memiliki pengumpulan mahasiswa." }
  ```

---

### 4.4 Submissions dan penilaian

#### 11. `GET /assignments/{id}/submissions`

- **Akses:** dosen pemilik course dari tugas ini. Admin dan mahasiswa mendapat 403.
- **Parameter:** `?page=` (opsional). Terbaru lebih dulu.
- Dosen melihat `nim_nip` mahasiswa tetapi tidak melihat email mereka.

```bash
curl -s http://localhost:8000/api/v1/assignments/1/submissions \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 200** (dipersingkat dari 10 item)

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

`grade` bernilai `null` untuk submission yang belum dinilai.

**Gagal:** 403 (bukan dosen pemilik, termasuk dosen lain), 404 (tugas tidak ada).

#### 12. `POST /assignments/{id}/submissions`

- **Akses:** mahasiswa yang terdaftar di course tugas ini.
- **Format:** `multipart/form-data`.
- **Parameter:**

| Field | Aturan |
|-------|--------|
| `file` | wajib, tipe `pdf`, `doc`, `docx`, atau `zip`, maks 5 MB |
| `note` | opsional, teks, maks 1000 karakter |

**Aturan bisnis**

- Tugas harus berstatus `published`. Tugas draft dijawab **404**.
- Lewat tenggat: ditandai `is_late: true`. Jika `allow_late` bernilai `false`,
  pengumpulan ditolak dengan 422.
- **Kirim ulang** menimpa pengumpulan sebelumnya (file lama dihapus) selama belum
  dinilai: **200**. Jika sudah dinilai: **409**.
- File disimpan di penyimpanan privat dengan nama acak. Path internal tidak
  pernah ditampilkan.
- Dosen pemilik course menerima notifikasi `PengumpulanBaru`.

```bash
curl -s -X POST http://localhost:8000/api/v1/assignments/4/submissions \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN" \
  -F "file=@tugas.pdf" -F "note=Pengumpulan pertama"
```

**Sukses: 201** (pertama kali) atau **200** (kirim ulang)

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

**Gagal**

- 403, bukan mahasiswa, atau tidak terdaftar di course ini.
- 404, tugas tidak ada atau masih draft.
- 409, sudah dinilai:
  ```json
  { "message": "Tugas ini sudah dinilai dan tidak dapat dikumpulkan ulang." }
  ```
- 422, file salah atau tenggat lewat:
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

- **Akses:** dosen pemilik course dari tugas tempat submission ini berada.
- **Sifat:** *upsert*, aman dipanggil berulang. **201** saat nilai dibuat
  pertama kali, **200** saat diperbarui. Penilaian ulang tanpa `feedback`
  mengosongkan feedback sebelumnya.
- **Parameter (body):**

| Field | Aturan |
|-------|--------|
| `score` | wajib, angka, antara 0 dan `max_score` tugas |
| `feedback` | opsional, teks, maks 2000 karakter |

- Mahasiswa pemilik submission menerima notifikasi `NilaiDiberikan`.

```bash
curl -s -X PUT http://localhost:8000/api/v1/submissions/1/grade \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN" \
  -d "score=85" -d "feedback=Bagus"
```

**Sukses: 201** (pertama kali) atau **200** (diperbarui)

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

**Gagal**

- 403, mahasiswa, atau dosen yang bukan pemilik course.
- 404, submission tidak ada.
- 422, nilai di luar rentang:
  ```json
  {
    "message": "Data yang diberikan tidak valid.",
    "errors": { "score": ["The score field must not be greater than 100."] }
  }
  ```

---

### 4.5 Notifications

#### 14. `GET /notifications`

- **Akses:** semua peran. Hanya notifikasi milik user yang login, terbaru lebih
  dulu.
- **Parameter:** `?page=` (opsional).

```bash
curl -s http://localhost:8000/api/v1/notifications \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 200**

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
`PengumpulanBaru`.

**Gagal: 401** tanpa token.

#### 15. `POST /notifications/{id}/read`

- **Akses:** semua peran, hanya untuk notifikasi miliknya. `id` berupa UUID.
- **Idempoten:** dipanggil berulang tetap 200 dan `read_at` tidak berubah.

```bash
curl -s -X POST \
  http://localhost:8000/api/v1/notifications/1a312cd2-7351-4efb-aa5d-1f5cb0145ce0/read \
  -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```

**Sukses: 200**

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
response.)

**Gagal**

- 403, notifikasi milik pengguna lain.
- 404, notifikasi tidak ada: `{"message":"Sumber daya tidak ditemukan."}`

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
| `GET /notifications`, `POST /notifications/{id}/read` | milik sendiri | milik sendiri | milik sendiri |

---

## 6. Catatan keamanan

- **Otorisasi dicek di level query atau sebelum aksi**, bukan sekadar
  menyembunyikan tombol di tampilan. Mengganti ID di URL tidak memberi akses ke
  data pengguna lain.
- **Otorisasi dicek sebelum validasi.** Pengguna yang tidak berhak selalu
  mendapat 403, bukan 422.
- **Daftar putih field.** API Resource hanya mengeluarkan field yang
  diizinkan. `password`, token, dan path internal file tidak pernah dikeluarkan.
  `email` hanya terlihat oleh pemilik akun dan admin, `nim_nip` oleh pemilik,
  admin, dan dosen.
- **Pesan login seragam** untuk email salah maupun password salah.
- **Rate limiting** pada login (5/menit) dan seluruh endpoint lain (60/menit).
- **File unggahan** disimpan di penyimpanan privat (bukan folder publik)
  dengan nama acak. Nama dari klien tidak dipakai sebagai path.
- **Tugas draft** tidak terlihat oleh mahasiswa, termasuk jumlahnya pada
  `assignments_count` dan saat mencoba mengumpulkan.

---

## 7. Batasan yang diketahui

- Pesan di dalam `errors` (detail per field) masih berbahasa Inggris bawaan
  Laravel. Field `message` utamanya sudah berbahasa Indonesia sesuai kontrak.
- Belum ada endpoint pengunduhan file materi dan submission (direncanakan pada
  minggu berikutnya).
- Pengecekan kepemilikan saat ini ada di controller. Pada Minggu 7 logika ini
  dipindahkan ke Policy.
- Satu akun hanya memiliki satu token aktif. Login baru mencabut token lama
  (termasuk di perangkat lain).
