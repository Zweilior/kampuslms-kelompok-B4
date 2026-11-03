# Performa KampusLMS (Minggu 11)

Dokumen ini mencatat pengukuran performa **sebelum** dan **sesudah** optimisasi.
Seluruh bagian telah diisi.

## 1. Cara mengukur

- Data uji: `php artisan migrate:fresh --seed`. Mata kuliah uji **Pemrograman Web** (15 mahasiswa, 4 tugas), dosen **Budi Santoso**, mahasiswa **Andi Pratama**. Tabel `grades` berisi 497 baris. Data yang sama dipakai untuk pengukuran sebelum dan sesudah (dump `kampus_db_sebelum.sql`).
- Alat: perintah `php artisan perf:measure --md --detail` (`app/Console/Commands/MeasurePerformance.php`). Tiap halaman dijalankan sebagai request internal; satu kali pemanasan, lalu 3 kali ukur, waktu diambil mediannya.
- Verifikasi manual dengan Debugbar (3 November 2026) pada tiga halaman: Rekap nilai (dosen), Daftar tugas (dosen), dan Rekap nilai (admin). Jumlah query sesuai skrip dengan selisih satu, karena Debugbar menghitung `select * from users where id = ?` (user dari sesi) yang dilewati skrip.
- Catatan membaca angka:
  - Jumlah query adalah angka yang paling stabil.
  - Waktu dan memori dari CLI hanya untuk membandingkan sebelum dan sesudah, bukan angka absolut Debugbar. Selisih waktu antar pengukuran sekitar 10 sampai 30%, jadi perubahan di bawah itu dianggap noise.
  - Kolom "Duplikat" membandingkan teks SQL tanpa nilai binding, sehingga bisa menghasilkan false positive (lihat temuan 3).

## 2. Hasil sebelum dan sesudah optimisasi

Diurutkan dari jumlah query terbanyak pada pengukuran sebelum optimisasi. Pengukuran "sesudah" dilakukan setelah seluruh perbaikan tergabung, dengan data yang sama.

| Halaman | Query (sebelum) | Waktu ms (sebelum) | Memori MB (sebelum) | Query (sesudah) | Waktu ms (sesudah) | Memori MB (sesudah) |
|---------|---:|---:|---:|---:|---:|---:|
| Daftar pengumpulan (dosen) | 13 | 15.8 | 0.46 | 16 | 14.7 | 0.49 |
| Rekap nilai (admin) | 11 | 15.5 | 0.42 | 10 | 9.1 | 0.42 |
| Rekap nilai per matkul (mahasiswa) | 10 | 12.0 | 0.28 | 10 | 7.4 | 0.29 |
| Daftar tugas (dosen) | 9 | 13.6 | 0.27 | 5 | 5.9 | 0.26 |
| Rekap nilai (dosen) | 8 | 42.2 | 1.24 | 8 | 25.0 | 1.11 |
| Dashboard mahasiswa | 8 | 12.4 | 0.34 | 8 | 8.2 | 0.35 |
| Rekap nilai (mahasiswa) | 7 | 16.9 | 0.80 | 7 | 13.5 | 0.80 |
| Dashboard dosen | 7 | 15.3 | 0.27 | 5 | 6.1 | 0.25 |
| Daftar tugas (mahasiswa) | 6 | 8.5 | 0.25 | 6 | 6.2 | 0.26 |
| Dashboard admin | 6 | 5.7 | 0.22 | 3 | 3.6 | 0.22 |
| Detail mata kuliah (mahasiswa) | 5 | 5.8 | 0.22 | 5 | 4.6 | 0.23 |
| Daftar mata kuliah (mahasiswa) | 4 | 8.4 | 0.35 | 4 | 5.5 | 0.36 |
| Daftar mata kuliah (dosen) | 4 | 5.6 | 0.25 | 4 | 4.3 | 0.25 |
| Detail mata kuliah (dosen) | 3 | 5.8 | 0.22 | 3 | 4.5 | 0.23 |

Semua halaman berstatus 200 pada pengukuran sebelum dan sesudah. Duplikat query: 0 di semua halaman pada pengukuran sesudah.

Catatan membaca tabel:

- **Kolom waktu antar sesi tidak sebanding.** Pengukuran "sebelum" dan "sesudah" dilakukan pada sesi berbeda. Penurunan waktu pada halaman yang jumlah query dan memorinya tidak berubah (misalnya Dashboard mahasiswa 12.4 menjadi 8.2 ms) dianggap noise, bukan perbaikan. Satu-satunya perbandingan waktu yang valid adalah Rekap nilai (dosen), yang diukur berurutan dalam satu sesi (lihat bagian 5).
- **Dashboard diukur dalam keadaan cache hangat.** Skrip menjalankan satu request pemanasan sebelum mengukur, sehingga cache sudah terisi. Pada cache dingin jumlah query lebih tinggi.
- **Daftar pengumpulan (dosen) naik dari 13 menjadi 16 query.** Penyebabnya pagination: dua daftar (submission dan mahasiswa yang belum mengumpulkan) masing-masing menambah satu query `count(*)`. Jumlah baris yang dimuat kini dibatasi 15 per daftar. Manfaatnya baru terasa pada data yang lebih besar dan belum diukur.

## 3. Halaman terburuk dan alasannya

- **Berdasarkan jumlah query (sebelum):** Daftar pengumpulan (dosen), 13 query. Semua query berbeda (tidak ada duplikat) dan index terpakai (`type=ref`), jadi bukan N+1.
- **Berdasarkan waktu dan memori (sebelum):** **Rekap nilai (dosen)**. Verifikasi Debugbar (3 November 2026): total waktu request 149 ms, total waktu query 19,35 ms (sekitar 13%), 9 query, 425 model dimuat. Sebagai pembanding, Rekap nilai (admin) memuat 67 model dan Daftar tugas (dosen) 10 model. Waktu dihabiskan di PHP (pembuatan model, penghitungan nilai di `CourseController::dosenGradesIndex`, dan render), bukan di database.
- Target modul "rekap nilai di bawah 15 query" **sudah terpenuhi sebelum optimisasi** (7 sampai 11 query) dan tetap terpenuhi sesudahnya (7 sampai 10 query). Peningkatan yang dikejar ada pada waktu, memori, dan N+1 kecil.

## 4. Temuan

| # | Halaman | Temuan | Bukti | Status |
|---|---------|--------|-------|--------|
| 1 | Daftar tugas (dosen) | N+1: `select * from courses where id = ?` berjalan 4 kali, sebanyak jumlah tugas. Penyebab: `@can('update'/'delete', $assignment)` di view memicu `AssignmentPolicy` membaca `$assignment->course` per tugas. | Duplikat 3 dari 9 query; 4x query `courses` | **Diperbaiki.** Relasi `course` diisi dari `$course` yang sudah ada (`setRelation`), tanpa query tambahan. Query 9 menjadi 5, duplikat 3 menjadi 0. |
| 2 | Rekap nilai (admin) | `Grade::count()` menduplikasi hitungan `paginate()`; dua `count(*)` yang sama persis. | 2x `select count(*) from grades` | **Diperbaiki.** Memakai `$grades->total()`. Query 11 menjadi 10, duplikat 1 menjadi 0. |
| 3 | Dashboard admin | **Bukan N+1** (false positive). Dua query `count(*) ... where role = ?` memakai nilai role berbeda. | Duplikat 1 | **Diperbaiki sebagai optimisasi.** Tiga hitungan user digabung menjadi satu query `groupBy` dan statistik di-cache 10 menit. Query 6 menjadi 3 (cache hangat). |
| 4 | Rekap nilai (admin) | `order by graded_at` memindai seluruh tabel; kolom `graded_at` tanpa index. | Tabel di bawah | Index dibuat (`grades_graded_at_index`). Pada data uji MySQL tetap memilih scan penuh; bukti pada data besar di bawah. |
| 5 | Rekap nilai (dosen) | Beban ada di memori dan waktu, bukan jumlah query. | 42.2 ms, 1.24 MB, 8 query; 425 model | **Diperbaiki sebagian.** Waktu turun sekitar 42%, memori sekitar 10%, query tetap 8 (lihat bagian 5). |
| 6 | Halaman mahasiswa | Tidak ditemukan N+1. | Duplikat 0 di semua halaman mahasiswa | Tidak perlu perbaikan |

### Bukti EXPLAIN

Sebelum index (data seeder, 497 baris):

```sql
EXPLAIN SELECT * FROM grades ORDER BY graded_at DESC LIMIT 15;
```

| table | type | key | rows |
|-------|------|-----|-----:|
| grades | ALL | (kosong) | 497 |

Sesudah index (data seeder, 497 baris): index `grades_graded_at_index` terdaftar (dikonfirmasi `SHOW INDEX FROM grades`), tetapi hasil `EXPLAIN` yang sama tetap `type=ALL`, `key=NULL`, `rows=497`, `Extra=Using filesort`. MySQL menilai memindai seluruh tabel lebih murah pada tabel sekecil ini, sehingga **tidak ada perubahan terukur di halaman**.

Pembuktian pada tabel uji sementara 100.000 baris (`grades_bench`, dihapus setelah uji, tabel `grades` tidak disentuh):

| Kondisi | type | key | rows | Extra |
|---------|------|-----|-----:|-------|
| Sebelum index | ALL | (kosong) | 99999 | Using filesort |
| Sesudah index (setelah `ANALYZE TABLE`) | index | grades_bench_graded_at_index | 15 | Backward index scan |

Pada percobaan pertama, `EXPLAIN` sesudah `ADD INDEX` masih `ALL` (rows 99999) karena statistik tabel belum diperbarui setelah data dimuat. Setelah `ANALYZE TABLE` (MySQL 8.4.3), optimizer memakai index tanpa dipaksa. Pelajaran: setelah memuat data dalam jumlah besar atau menambah index, jalankan `ANALYZE TABLE` sebelum membaca hasil `EXPLAIN`.

Kesimpulan: pada 100.000 baris, index `graded_at` mengubah query dari memindai 99.999 baris dengan filesort menjadi membaca 15 baris lewat index. Pada data seeder (497 baris) manfaat itu tidak muncul karena optimizer menilai scan penuh lebih murah.

Catatan: `EXPLAIN` pada `grades.submission_id` juga menunjukkan `ALL`, tetapi **itu bukan index yang hilang**. Kolom itu sudah `unique`; pada tabel 497 baris dengan daftar `IN` yang panjang, MySQL memilih memindai seluruh tabel. Tidak ada index yang ditambahkan di sana.

## 5. Perbaikan yang dilakukan

| # | Perbaikan | File | Dampak terukur |
|---|-----------|------|----------------|
| 1 | Menghilangkan N+1 `courses` di daftar tugas dosen dengan `setRelation('course', $course)` | `AssignmentController::dosenIndex` | Query 9 menjadi 5, duplikat 3 menjadi 0 |
| 2 | Memakai `$grades->total()` alih-alih `Grade::count()` | `AdminGradeController` | Query 11 menjadi 10, duplikat 1 menjadi 0 |
| 3 | Rekap nilai dosen (A dan B, dirinci di bawah) | `CourseController::dosenGradesIndex` | Waktu 43,2 menjadi 25,0 ms, memori 1,24 menjadi 1,11 MB |
| 4 | Tiga hitungan user digabung menjadi satu `groupBy`; statistik dashboard di-cache (admin 10 menit, dosen 1 menit) dengan pembersihan otomatis lewat event model | `DashboardController`, model `User`, `Course`, `Assignment`, `Submission`, `Grade` | Dashboard admin 6 menjadi 3 query, dashboard dosen 7 menjadi 5 (cache hangat) |
| 5 | Pagination 15 baris pada daftar mahasiswa terdaftar, mahasiswa yang tersedia, daftar pengumpulan, dan mahasiswa yang belum mengumpulkan; view terkait diperbarui | `CourseEnrollmentController`, `SubmissionController`, view `dosen/courses/enrollments` dan `dosen/assignments/show` | Jumlah baris per daftar dibatasi; query daftar pengumpulan naik 13 menjadi 16 (tambahan `count`) |
| 6 | Ekspor nilai memakai `lazy(500)` alih-alih `get()` agar tidak memuat seluruh data sekaligus | `SubmissionController::dosenExport`, `dosenExportCourseGrades` | Tidak diukur (tidak ada halaman GET) |
| 7 | Eager loading `grade.grader` pada query submission dosen | `SubmissionController` | Mencegah N+1 saat view menampilkan penilai |
| 8 | `Model::preventLazyLoading` aktif di luar produksi; perbaikan N+1 pada API resource disertai tes regresi | `AppServiceProvider`, `Api/V1/*`, `GradeComponentResource`, `ApiLazyLoadingTest` | 14 halaman terukur semuanya status 200, tanpa pelanggaran lazy loading |
| 9 | Index baru: `grades.graded_at`, `submissions.is_late`, `submissions.submitted_at`, `notifications.read_at` | migrasi `add_graded_at_index_to_grades_table`, `add_performance_indexes_to_tables` | Hanya `graded_at` yang punya bukti `EXPLAIN` sebelum (`ALL`, 497 baris). Tiga index lainnya **belum dibuktikan** dengan `EXPLAIN` dan pada data uji kecil kemungkinan tidak dipakai optimizer. |

Pemeriksaan tambahan: tidak ada pemanggilan `env()` di luar folder `config/` (app, routes, bootstrap, database, resources).

### Rincian perbaikan 3: Rekap nilai (dosen)

Dua perubahan, dilakukan dan diukur satu per satu (data sama, sesi pengukuran sama, berurutan):

- **A.** Submission dikelompokkan per mahasiswa satu kali (`groupBy('user_id')`), bukan disaring ulang dengan `where()` untuk setiap pasangan mahasiswa x tugas.
- **B.** Eager loading hanya memuat kolom yang dipakai `CourseGradeCalculator` (`grade_component_id`, `max_score`, `weight`, `score`, dan kunci relasi).

| Kondisi | Query | Waktu (ms) | Memori (MB) |
|---|---:|---:|---:|
| Kode lama | 8 | 43,2 | 1,24 |
| Sesudah A | 8 | 26,8 | 1,24 |
| Sesudah A + B | 8 | 25,0 | 1,11 |

Hasil perhitungan tidak berubah: snapshot rata-rata dan jumlah mahasiswa dinilai untuk 17 mata kuliah dari 5 dosen identik sebelum dan sesudah (snapshot "sebelum" direkam dengan kode lama), dan 136 tes lulus (801 assertion). Waktu diukur dari CLI dan bersifat indikatif. Jumlah query tidak berubah; yang turun adalah beban PHP. Dampak pada data yang lebih besar belum diukur.

Verifikasi Debugbar sesudah perbaikan (`/dosen/grades`, muatan kedua): 425 model dimuat (tidak berubah dari sebelum perbaikan), memori 4 MB (absolut, tidak berubah pada resolusi Debugbar), total waktu request 144 ms (sebelumnya 149 ms; selisih berada dalam noise). Query nilai kini hanya memilih kolom yang dipakai kalkulator. Perbaikan ini mengurangi beban PHP yang terukur dari CLI, tetapi tidak menghasilkan perubahan yang terlihat pada waktu end-to-end di browser maupun jumlah model.

### Pengujian manual di browser (3 November 2026)

Dilakukan dengan `preventLazyLoading` aktif, setelah semua perbaikan tergabung:

| Uji | Hasil |
|-----|-------|
| Pagination (mahasiswa tersedia, daftar pengumpulan dengan filter status) | Lulus; navigasi halaman bekerja dan filter tidak hilang saat pindah halaman |
| Tombol "tandai semua dibaca" | Lulus; badge hilang, tetap 0 setelah muat ulang, tanpa error |
| Ekspor nilai (`lazy(500)`) | Lulus; isi file lengkap dan sama dengan tampilan halaman |
| Invalidasi cache dashboard dosen (menilai satu pengumpulan) | Lulus; angka "belum dinilai" berkurang langsung tanpa menunggu 1 menit |
| Invalidasi cache dashboard admin (menambah satu pengguna) | Lulus; total pengguna naik langsung tanpa menunggu 10 menit |
| Halaman utama tiap role (admin, dosen, mahasiswa) termasuk notifikasi | Lulus; tidak ada halaman error |

## 6. Cache

Bagian yang di-cache: statistik dashboard admin dan dosen dengan `Cache::remember()`.

| Data | Masa berlaku | Dibersihkan saat | Akibat jika basi |
|------|-------------:|------------------|------------------|
| Statistik jumlah user dan mata kuliah admin | 10 menit | Model `User` atau `Course` tersimpan/dihapus | Angka dashboard dapat tertinggal maksimal 10 menit |
| Jumlah mata kuliah, tugas terbuka, dan submission belum dinilai milik dosen | 1 menit | Model `Course`, `Assignment`, `Submission`, atau `Grade` tersimpan/dihapus | Angka sementara dapat tertinggal maksimal 1 menit jika invalidasi tidak terjadi |

Bagian yang sengaja **tidak** di-cache: daftar tugas yang perlu dinilai, nilai mahasiswa, rekap nilai, status pengumpulan, dan jumlah notifikasi belum dibaca. Data tersebut harus mencerminkan keadaan terbaru karena data basi dapat menyebabkan pengguna mengambil keputusan yang salah atau melewatkan tindakan penting.

Catatan: angka dashboard di bagian 2 diukur dalam keadaan cache hangat.

## 7. Ringkasan sesudah optimisasi

- **Rekap nilai (dosen)** setelah optimisasi: 8 query, 25,0 ms, 1,11 MB (sebelumnya 8 query, 43,2 ms, 1,24 MB). Hasil perhitungan identik (17 mata kuliah dari 5 dosen).
- **N+1 yang terbukti dihilangkan:** daftar tugas dosen (4 query `courses` berulang) dan hitungan ganda `count(*)` di rekap nilai admin. Duplikat query pada 14 halaman: 0.
- **Query berkurang:** Daftar tugas (dosen) 9 menjadi 5, Rekap nilai (admin) 11 menjadi 10, Dashboard dosen 7 menjadi 5 dan Dashboard admin 6 menjadi 3 (dua terakhir dengan cache hangat).
- **Query bertambah:** Daftar pengumpulan (dosen) 13 menjadi 16, akibat pagination. Imbalannya jumlah baris yang dimuat dibatasi.
- **Target modul** "rekap nilai di bawah 15 query" terpenuhi sebelum dan sesudah optimisasi (7 sampai 10 query).
- **Keterbatasan:** data uji kecil (15 mahasiswa per mata kuliah) sehingga dampak pada skala besar belum diukur; waktu dari CLI bersifat indikatif; jumlah model yang dimuat pada rekap nilai dosen belum diverifikasi ulang selain di bagian 5; index `graded_at` tidak terpakai pada data uji dan baru dibuktikan pada tabel 100.000 baris; tiga index lain belum dibuktikan.
- **Catatan untuk minggu 12:** pada Debugbar, query `update sessions` konsisten menjadi query paling lambat per request (sekitar 5 sampai 9 ms) karena sesi disimpan di database, dan endpoint `notifications/unread-count` menjalankan lima query tiap polling. Keduanya layak ditinjau saat deployment.