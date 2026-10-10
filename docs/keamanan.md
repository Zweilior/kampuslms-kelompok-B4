# Keamanan KampusLMS: Daftar Titik Rawan IDOR

Kelompok B4 · EduSpace (KampusLMS)

Dokumen ini adalah kelanjutan tabel "Daftar Titik Rawan IDOR" dari Minggu 5. Pada Minggu 5 kolom perlindungannya masih "belum ada". Setelah Minggu 7 (Policy), setiap titik dipetakan ke **Policy atau query** yang menutupnya.

Sumber pemetaan: `routes/web.php`, `routes/api.php`, seluruh controller (web dan `Api/V1`), `app/Policies/*`, dan `tests/Feature/PolicyAccessTest.php`.

---

## 1. Cara membaca tabel

**IDOR** terjadi ketika pengguna mengganti ID di URL, body, atau parameter lain, lalu memperoleh data atau aksi milik orang lain.

**Lapisan pertahanan yang dipakai di repo ini:**

| Lapisan | Fungsi | Lokasi |
|---|---|---|
| Middleware `auth` | Menolak tamu (401) | grup route web, `auth:sanctum` di API |
| Middleware `role:<peran>` | Menolak peran yang salah (403) | `EnsureUserHasRole`, grup `admin`, `dosen`, `mahasiswa` |
| `scopeBindings()` | Memastikan child cocok dengan parent di URL (`{assignment}` milik `{course}`). **Bukan** pengecekan hak akses | grup `dosen` dan `mahasiswa` |
| **Policy** | Menentukan siapa boleh melakukan apa pada objek tertentu (admin, pemilik, terdaftar) | `app/Policies/*`, dipanggil lewat `Gate::authorize()` |
| **Query terfilter** | Hanya mengambil baris milik pengguna (`where('user_id', auth()->id())`, `where('lecturer_id', ...)`, `whereHas('students', ...)`) | controller |

**Kolom Status:**

| Simbol | Arti |
|---|---|
| ✅ Policy | Ditutup oleh Policy yang dipanggil dari controller |
| ✅ Query | Ditutup oleh query terfilter atau pengecekan inline di controller (setara Policy, tetapi belum memakai Policy) |
| ⚠️ Sebagian | Data sensitif sudah terlindungi, tetapi akses ke objek induknya belum dicek |
| ❌ Terbuka | Belum ada pemeriksaan hak akses. Rencana penutupan ada di bagian 4 |

> Catatan: Policy sengaja **tidak** memakai `Gate::before` untuk admin. Matriks akses menolak admin pada "mengumpulkan tugas" dan "memberi nilai", sehingga admin dicek per method (lihat `ChecksCourseRelation`).

---

## 2. Tabel Titik Rawan IDOR

### 2.1 Web: area Admin (`/admin/...`, middleware `auth` + `role:admin`)

| No | Method & URI | Parameter | Seharusnya boleh | Ditutup oleh (Policy / query) | Status |
|---:|---|---|---|---|---|
| 1 | `GET admin/users/{user}`, `GET admin/users/{user}/edit` | `user` | Admin | Middleware `role:admin`. Tidak ada `UserPolicy`; seluruh area ini khusus admin | ✅ Query (middleware) |
| 2 | `PUT/PATCH`, `DELETE admin/users/{user}` | `user` | Admin | Middleware `role:admin`. Kolom `role` tidak ada di `$fillable`, diisi eksplisit setelah validasi `in:admin,dosen,mahasiswa` | ✅ Query (middleware) |
| 3 | `GET admin/courses/{course}`, `GET admin/courses/{course}/edit` | `course` | Admin | `CoursePolicy@view` dan `@update` (`Gate::authorize` di `CourseController::show/edit`) | ✅ Policy |
| 4 | `PUT/PATCH admin/courses/{course}` | `course` | Admin | `CoursePolicy@update` (hanya admin) | ✅ Policy |
| 5 | `DELETE admin/courses/{course}` | `course` | Admin | `CoursePolicy@delete` (hanya admin) | ✅ Policy |
| 6 | `GET admin/materials/{material}/edit` | `material` | Admin | `MaterialPolicy@update` (`adminEdit`) | ✅ Policy |
| 7 | `PUT admin/materials/{material}` | `material` | Admin | `MaterialPolicy@update` (`adminUpdate`) | ✅ Policy |
| 8 | `DELETE admin/materials/{material}` | `material` | Admin | `MaterialPolicy@delete` (`adminDestroy`) | ✅ Policy |
| 9 | `POST admin/materials` | `course_id` (body) | Admin | `Course::findOrFail(course_id)` lalu `MaterialPolicy@create` dengan Course tujuan | ✅ Policy |
| 10 | `GET admin/assignments/{assignment}/edit` | `assignment` | Admin | `AssignmentPolicy@update` (`adminEdit`) | ✅ Policy |
| 11 | `PUT admin/assignments/{assignment}` | `assignment` | Admin | `AssignmentPolicy@update` (`adminUpdate`) | ✅ Policy |
| 12 | `DELETE admin/assignments/{assignment}` | `assignment` | Admin | `AssignmentPolicy@delete`. Penghapusan tugas yang sudah punya pengumpulan ditolak di controller (keputusan Q6), bukan soal hak akses | ✅ Policy |
| 13 | `POST admin/assignments` | `course_id` (body) | Admin | Validasi `exists:courses,id` (422) lalu `AssignmentPolicy@create` dengan Course tujuan | ✅ Policy |
| 14 | `GET admin/grades` | tanpa ID | Admin | `GradePolicy@viewAny` ditambah middleware `role:admin`. Admin memang boleh melihat semua nilai | ✅ Policy |

### 2.2 Web: area Dosen (`/dosen/courses/{course}/...`, middleware `auth` + `role:dosen` + `scopeBindings`)

| No | Method & URI | Parameter | Seharusnya boleh | Ditutup oleh (Policy / query) | Status |
|---:|---|---|---|---|---|
| 15 | `GET dosen/courses/{course}` | `course` | Dosen pemilik MK | `CoursePolicy@view` (`dosenShow`). Dosen lain mendapat 403 | ✅ Policy |
| 16 | `GET dosen/courses` dan `GET dosen/grades` | tanpa ID | Dosen | Query `Course::where('lecturer_id', user.id)`. Hanya MK yang diajar yang keluar | ✅ Query |
| 17 | `GET dosen/courses/{course}/materials` | `course` | Dosen pemilik MK | `MaterialPolicy@viewAny` | ✅ Policy |
| 18 | `GET .../materials/create`, `POST .../materials` | `course` | Dosen pemilik MK | `MaterialPolicy@create` dengan Course dari URL | ✅ Policy |
| 19 | `GET .../materials/{material}/edit`, `PUT .../materials/{material}` | `course`, `material` | Dosen pemilik MK | `MaterialPolicy@update` (lewat `material->course->lecturer_id`). `scopeBindings` memastikan `{material}` memang milik `{course}` di URL | ✅ Policy |
| 20 | `DELETE .../materials/{material}` | `course`, `material` | Dosen pemilik MK | `MaterialPolicy@delete` + `scopeBindings` | ✅ Policy |
| 21 | `GET .../assignments` | `course` | Dosen pemilik MK | `AssignmentPolicy@viewAny` | ✅ Policy |
| 22 | `GET .../assignments/create`, `POST .../assignments` | `course` | Dosen pemilik MK | `AssignmentPolicy@create` dengan Course dari URL. `created_by` diisi dari `auth()->id()`, bukan dari body | ✅ Policy |
| 23 | `GET .../assignments/{assignment}/edit`, `PUT .../assignments/{assignment}` | `course`, `assignment` | Dosen pemilik MK | `AssignmentPolicy@update` + `scopeBindings` | ✅ Policy |
| 24 | `DELETE .../assignments/{assignment}` | `course`, `assignment` | Dosen pemilik MK | `AssignmentPolicy@delete` + `scopeBindings` | ✅ Policy |
| 25 | `GET .../assignments/{assignment}/submissions` | `course`, `assignment` | Dosen pemilik MK | Pengecekan inline `abort_unless($course->lecturer_id === Auth::id(), 403)`. Query `$assignment->submissions()` sudah dibatasi `scopeBindings`. Setara `SubmissionPolicy@viewAny` | ✅ Query |
| 26 | `PUT .../submissions/{submission}/grade` | `course`, `assignment`, `submission` | Dosen pemilik MK, setelah tenggat | Inline: pemilik MK (403), `submission->assignment_id === assignment->id` (404), `due_at` sudah lewat (403). Setara `GradePolicy@create/update` | ✅ Query |
| 27 | `GET/POST .../courses/{course}/grade-components` | `course` | Dosen pemilik MK | `GradeComponentPolicy@viewAny` dan `@create` | ✅ Policy |
| 28 | `PUT/DELETE .../grade-components/{gradeComponent}` | `course`, `gradeComponent` | Dosen pemilik MK | `GradeComponentPolicy@update` dan `@delete` (lewat `component->course`) + `scopeBindings` | ✅ Policy |

### 2.3 Web: area Mahasiswa (`/mahasiswa/courses/{course}/...`, middleware `auth` + `role:mahasiswa` + `scopeBindings`)

| No | Method & URI | Parameter | Seharusnya boleh | Ditutup oleh (Policy / query) | Status |
|---:|---|---|---|---|---|
| 29 | `GET mahasiswa/courses`, `GET mahasiswa/dashboard`, `GET mahasiswa/monitoring-nilai` | tanpa ID | Mahasiswa | Query `auth()->user()->courses()`. Hanya MK yang diikuti. `finalGrades` difilter `where('user_id', auth id)` | ✅ Query |
| 30 | `GET mahasiswa/courses/{course}` | `course` | Mahasiswa terdaftar | `CoursePolicy@view` (`Gate::authorize` di `showCourse`). Non-terdaftar mendapat 403 | ✅ Policy |
| 31 | `GET .../courses/{course}/grade-components` | `course` | Mahasiswa terdaftar | Query `auth()->user()->courses()->whereKey($course->id)->exists()`, jika tidak maka 403. Isi submission juga difilter `where('user_id', auth id)` | ✅ Query |
| 32 | `GET mahasiswa/monitoring-nilai/{course}/grades` (dan redirect legacy `GET .../courses/{course}/grades`) | `course` | Mahasiswa terdaftar | `CoursePolicy@view` (`Gate::authorize` di `grades`). Nilai juga difilter query `submissions ... where('user_id', auth id)`. Redirect legacy meneruskan ke route ini sehingga ikut terlindungi | ✅ Policy |
| 33 | `GET .../courses/{course}/assignments` | `course` | Mahasiswa terdaftar | `AssignmentPolicy@viewAny` (keanggotaan MK). Tugas draft disembunyikan oleh query `where('status','published')` | ✅ Policy |
| 34 | `GET .../courses/{course}/materials` | `course` | Mahasiswa terdaftar | `MaterialPolicy@viewAny` (`Gate::authorize` di `materials`) | ✅ Policy |
| 35 | `GET .../assignments/{assignment}/submissions` | `course`, `assignment` | Mahasiswa terdaftar, hanya submission miliknya | `SubmissionPolicy@viewAny` (terdaftar dan tugas published; draft 403) ditambah query `$assignment->submissions()->where('user_id', auth()->id())` | ✅ Policy |
| 36 | `GET .../assignments/{assignment}/submissions/create` | `course`, `assignment` | Mahasiswa terdaftar, tugas published | `SubmissionPolicy@create` (`Gate::authorize` di `createSubmission`, sama dengan `store`). Non-terdaftar 403, draft 404 | ✅ Policy |
| 37 | `POST .../assignments/{assignment}/submissions` | `course`, `assignment` | Mahasiswa terdaftar, tugas published | `SubmissionPolicy@create` (`Gate::authorize`): tidak terdaftar 403, draft 404. `user_id` diisi dari `request->user()`, bukan dari body. Diuji di `MahasiswaSubmissionRouteTest` | ✅ Policy |

### 2.4 API `/api/v1/...` (middleware `auth:sanctum` + `throttle:60,1`)

Semua controller API memakai **pengecekan inline** (belum memanggil Policy). Logikanya sama dengan Policy yang bersangkutan, tetapi duplikat.

| No | Method & URI | Parameter | Seharusnya boleh | Ditutup oleh (Policy / query) | Status |
|---:|---|---|---|---|---|
| 38 | `GET /courses` | tanpa ID | Admin semua, dosen yang diajar, mahasiswa yang diikuti | Query: `where('lecturer_id', user.id)` untuk dosen, `whereHas('students', users.id = user.id)` untuk mahasiswa | ✅ Query |
| 39 | `GET /courses/{course}` | `course` | Admin, dosen pemilik, mahasiswa terdaftar | `CourseController::canAccessCourse()` (setara `CoursePolicy@view`) → 403. `assignments_count` untuk mahasiswa hanya menghitung `published` | ✅ Query |
| 40 | `GET /courses/{course}/materials` | `course` | Admin, dosen pemilik, mahasiswa terdaftar | `canAccessCourse()` (setara `MaterialPolicy@viewAny`) → 403 | ✅ Query |
| 41 | `GET /courses/{course}/assignments` | `course` | Admin, dosen pemilik, mahasiswa terdaftar (hanya published) | `canAccessCourse()` (setara `AssignmentPolicy@viewAny`) + query `where('status','published')` untuk mahasiswa. Filter `?status=draft` tidak bisa menembusnya karena kondisi published ditambahkan lebih dulu | ✅ Query |
| 42 | `GET /courses/{course}/grade-components` | `course` | Admin, dosen pemilik, mahasiswa terdaftar | Inline: mahasiswa harus ada di `course->students`, dosen harus `lecturer_id` sama (403) | ✅ Query |
| 43 | `POST /assignments` | `course_id` (body) | Dosen pemilik MK | Peran harus dosen (403), lalu `Course::findOrFail(course_id)` dan `lecturer_id === user.id` (403). Setara `AssignmentPolicy@create`. `created_by` dari `user->id` | ✅ Query |
| 44 | `PUT/PATCH /assignments/{assignment}` | `assignment` | Dosen pemilik MK | Inline `assignment->course->lecturer_id !== auth()->id()` → 403. Setara `AssignmentPolicy@update`. Otorisasi dijalankan sebelum validasi | ✅ Query |
| 45 | `DELETE /assignments/{assignment}` | `assignment` | Dosen pemilik MK | Inline peran dosen dan `course->lecturer_id === user.id` → 403. Setara `AssignmentPolicy@delete` | ✅ Query |
| 46 | `GET /assignments/{assignment}/submissions` | `assignment` | Dosen pemilik MK | `isOwningLecturer()` → 403, lalu query `$assignment->submissions()`. Setara `SubmissionPolicy@viewAny` | ✅ Query |
| 47 | `POST /assignments/{assignment}/submissions` | `assignment` | Mahasiswa terdaftar | Inline: harus mahasiswa dan `course->students()->where('users.id', user.id)->exists()` → 403; tugas draft → 404. `user_id` dari token, bukan body. Setara `SubmissionPolicy@create` | ✅ Query |
| 48 | `PUT /submissions/{submission}/grade` | `submission` | Dosen pemilik MK dari submission itu | `isOwningLecturer()` lewat `submission->assignment->course->lecturer_id` → 403. Otorisasi sebelum validasi (403 menang atas 422). Setara `GradePolicy@create/update` | ✅ Query |
| 49 | `GET /notifications` | tanpa ID | Pemilik notifikasi | Query `$request->user()->notifications()` | ✅ Query |
| 50 | `POST /notifications/{id}/read` | `id` (UUID) | Pemilik notifikasi | `Notification::findOrFail($id)` (404), lalu cek `notifiable_type` dan `notifiable_id` sama dengan pengguna → 403 | ✅ Query |
| 51 | `POST /grade-components` | `course_id` (body) | Dosen pemilik MK | Peran dosen (403), `Course::findOrFail` lalu `lecturer_id === user.id` (403). Setara `GradeComponentPolicy@create` | ✅ Query |
| 52 | `GET /grade-components/{gradeComponent}` | `gradeComponent` | Admin, dosen pemilik, mahasiswa terdaftar | Inline lewat `gradeComponent->course` (mahasiswa di `students`, dosen `lecturer_id`). Setara `GradeComponentPolicy@viewAny` | ✅ Query |
| 53 | `PUT/PATCH`, `DELETE /grade-components/{gradeComponent}` | `gradeComponent` | Dosen pemilik MK | Inline peran dosen dan `course->lecturer_id === user.id` → 403. Setara `GradeComponentPolicy@update/delete` | ✅ Query |

### 2.5 File yang diunggah

| No | Objek | Seharusnya boleh | Ditutup oleh (Policy / query) | Status |
|---:|---|---|---|---|
| 54 | File pengumpulan via API (`submissions/{assignment_id}/<nama-acak>`) | Pemilik, dosen pemilik MK, admin | Disimpan di disk `local` (privat), nama diacak Laravel, dan **tidak ada route unduh**, sehingga tidak bisa diakses langsung. Saat endpoint unduh dibuat, wajib lewat `SubmissionPolicy@download` | ✅ Query (disk privat) |
| 55 | File materi (`materials/<hash>` di disk `local`, privat) | Admin, dosen pemilik, mahasiswa terdaftar | Disimpan di disk privat (`store('materials')` tanpa `'public'`), sehingga tidak ada URL langsung. Satu-satunya jalan adalah route `courses.materials.download` → `MaterialController::download` → `MaterialPolicy@download` (403 bila tidak berhak, 404 bila bukan file atau file hilang) + `scopeBindings` agar `{material}` harus milik `{course}`. Diuji di `MaterialDownloadTest` | ✅ Policy |

### 2.6 Ringkasan

| Status | Jumlah baris | Nomor |
|---|---:|---|
| ✅ Policy | 31 | 3–15, 17–24, 27, 28, 30, 32–37, 55 |
| ✅ Query | 24 | 1, 2, 16, 25, 26, 29, 31, 38–54 |
| ⚠️ Sebagian | 0 | - |
| ❌ Terbuka | 0 | - |

---

## 3. Policy yang tersedia dan di mana dipakai

| Policy | Ability | Aturan singkat | Dipanggil dari |
|---|---|---|---|
| `CoursePolicy` | `viewAny` | admin, dosen, mahasiswa (isi daftar difilter query) | `CourseController::index/dosenIndex/dosenGradesIndex` |
| | `view` | admin, dosen pemilik, mahasiswa terdaftar | `CourseController::show/dosenShow`, `MahasiswaController::showCourse/grades` |
| | `create/update/delete` | hanya admin | `CourseController` |
| | `manageEnrollment` | admin, dosen pemilik | belum ada route |
| `MaterialPolicy` | `viewAny/view` | admin, dosen pemilik, mahasiswa terdaftar | `MaterialController` (dosen dan admin), `MahasiswaController::materials` |
| | `download` | sama dengan `view` | `MaterialController::download` (route `courses.materials.download`) |
| | `create/update/delete` | admin, dosen pemilik MK tujuan | `MaterialController` |
| `AssignmentPolicy` | `viewAny` | admin, dosen pemilik, mahasiswa terdaftar | `AssignmentController::dosenIndex`, `MahasiswaController::assignments` |
| | `view` | admin/dosen pemilik semua. Mahasiswa terdaftar hanya `published`, draft dijawab 404 | tidak ada route |
| | `create/update/delete` | admin, dosen pemilik | `AssignmentController` |
| `SubmissionPolicy` | `viewAny` | admin, dosen pemilik, mahasiswa terdaftar (published) | `MahasiswaController::submissions` |
| | `view/download` | mahasiswa hanya miliknya, dosen pemilik MK, admin | **belum dipanggil** |
| | `create` | hanya mahasiswa terdaftar, draft 404 | `SubmissionController::store` (web), `MahasiswaController::createSubmission` |
| | `update` | hanya pemilik, belum dinilai, published, masih terdaftar | **belum dipanggil** |
| | `delete` | ditolak semua | **belum dipanggil** |
| `GradePolicy` | `viewAny` | admin, dosen, mahasiswa | `AdminGradeController::index` |
| | `view` | admin semua, dosen pemilik MK, mahasiswa hanya nilainya | **belum dipanggil** |
| | `create/update` | hanya dosen pemilik MK, setelah `due_at` lewat | **belum dipanggil** (web dan API memakai inline) |
| | `delete` | ditolak semua | **belum dipanggil** |
| `GradeComponentPolicy` | `viewAny/create/update/delete` | `viewAny` admin, dosen pemilik, mahasiswa terdaftar. Lainnya admin dan dosen pemilik | `GradeComponentController` (web) |

Policy ditemukan otomatis lewat konvensi nama `App\Policies\<Model>Policy`; ini dikunci oleh `PolicyAccessTest::test_policies_are_auto_discovered`.

---

## 4. Penutupan yang sudah dikerjakan

**Area mahasiswa (No 30, 32–36):** `MahasiswaController` memanggil `Gate::authorize(...)` di `showCourse`, `grades`, `assignments`, `materials`, `submissions`, dan `createSubmission`, memakai Policy yang sudah ada. Diuji di `tests/Feature/MahasiswaAccessTest.php`: mahasiswa non-terdaftar mendapat 403 di semua route tersebut, mahasiswa terdaftar mendapat 200, dan tugas draft diblokir (403 pada daftar submission, 404 pada form pengumpulan).

**File materi (No 55):**

1. `MaterialController` menyimpan file dengan `store('materials')` (disk `local`, privat) dan menghapusnya dengan `Storage::delete(...)`, bukan lagi disk `public`.
2. Method `download` memanggil `Gate::authorize('download', $material)` dan mengirim file lewat `Storage::download(...)`.
3. Route `GET /courses/{course}/materials/{material}/download` (nama `courses.materials.download`) dengan middleware `auth` dan `scopeBindings()`.
4. Tautan unduh ditambahkan di tampilan dosen, mahasiswa, dan admin. Diuji di `tests/Feature/MaterialDownloadTest.php`.

**Langkah sekali jalan untuk data lama:** file yang diunggah sebelum perubahan ini masih berada di `storage/app/public/materials`, sehingga masih bisa dibuka lewat URL. Path di database (`materials/<nama>`) sama di kedua disk, jadi cukup memindahkan folder tanpa mengubah database:

```bash
# Linux / macOS / Git Bash
mkdir -p storage/app/private/materials
mv storage/app/public/materials/* storage/app/private/materials/
```

```powershell
# Windows PowerShell (Laragon)
New-Item -ItemType Directory -Force storage\app\private\materials
Move-Item storage\app\public\materials\* storage\app\private\materials\
```

**Opsional (konsistensi):** pindahkan pengecekan inline di `SubmissionController` web (No 25, 26) dan seluruh controller API (No 38–53) ke `Gate::authorize(...)`, agar satu aturan tidak ditulis dua kali dan tidak bisa menyimpang antara web dan API.

---

## 5. Catatan di luar IDOR (ditemukan saat pemetaan)

1. **`Web\SubmissionController::store` menerima `file_path` dari klien** (string bebas hingga 255 karakter, tanpa upload file). Ini bukan IDOR, tetapi nilai itu tersimpan apa adanya. API sudah benar: file diunggah, divalidasi `mimes:pdf,doc,docx,zip` maksimal 5 MB, dan disimpan di disk privat.
2. **Aturan tenggat nilai tidak sama di web dan API.** Web (`dosenGrade`) menolak penilaian sebelum `due_at`, sedangkan `Api\V1\SubmissionController::grade` tidak. `GradePolicy@create/update` sudah memuat aturan ini; memakainya di kedua tempat akan menyamakan perilaku.
3. **`Api\V1\AssignmentController::destroy` tidak memblokir tugas yang sudah punya pengumpulan** (409), padahal komentar `AssignmentPolicy@delete` dan versi web menyatakan demikian.
4. **Pesan 403 `Api\V1\AssignmentController::update`** ("Forbidden. Anda bukan pemilik tugas ini.") berbeda dari kontrak ("Anda tidak memiliki akses ke sumber daya ini.").
5. **Perbandingan ketat `===`** pada `$course->lecturer_id === Auth::id()` (web `SubmissionController`) bergantung pada driver DB yang mengembalikan integer. Jika tidak, pemilik sah justru ditolak (gagal aman, bukan gagal buka). Sebaiknya diseragamkan dengan `(int)` seperti di Policy.
6. **Tautan file pengumpulan di `dosen/assignments/show.blade.php`** memakai `Storage::url($submission->file_path)`. File yang dikirim lewat API tersimpan di disk privat dan belum punya route unduh, sehingga tautan itu tidak akan berfungsi untuknya. Saat route unduh submission dibuat, gunakan `SubmissionPolicy@download` (pola yang sama dengan `MaterialController::download`).
7. **Method "Standard/legacy"** di `MaterialController` dan `AssignmentController` (`index`, `show`, `create`, `store`, `edit`, `update`, `destroy`) sudah memanggil Gate, tetapi route-nya tidak lagi terdaftar di `routes/web.php`, sehingga tidak dapat dijangkau.

---

## 6. Cara memverifikasi

```bash
# Daftar route berparameter model
php artisan route:list --except-vendor

# Uji Policy terhadap matriks akses dan route mahasiswa (SQLite in-memory agar DB dev aman)
DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan test --filter=PolicyAccessTest
DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan test --filter=MahasiswaAccessTest
DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan test --filter=MaterialDownloadTest

# Uji otorisasi API end-to-end (isi token di dalam skrip lebih dulu)
bash scripts/test-api.sh
```

Uji manual IDOR: login sebagai mahasiswa yang **tidak** terdaftar di suatu MK, lalu buka URL MK tersebut langsung (`/mahasiswa/courses/{id}`, `/materials`, `/assignments`). Hasil yang benar adalah 403.
