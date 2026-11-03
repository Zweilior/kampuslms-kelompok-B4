# Performa KampusLMS (Minggu 11)

Dokumen ini mencatat pengukuran performa **sebelum** dan **sesudah** optimisasi.
Bagian bertanda `[ISI]` diisi setelah perbaikan selesai atau setelah verifikasi manual.

## 1. Cara mengukur

- Data uji: `php artisan migrate:fresh --seed`. Mata kuliah uji **Pemrograman Web** (15 mahasiswa, 4 tugas), dosen **Budi Santoso**, mahasiswa **Andi Pratama**. Tabel `grades` berisi 497 baris.
- Alat: perintah `php artisan perf:measure --md --detail` (`app/Console/Commands/MeasurePerformance.php`). Tiap halaman dijalankan sebagai request internal; satu kali pemanasan, lalu 3 kali ukur, waktu diambil mediannya.
- Verifikasi manual dengan Debugbar (3 November 2026) pada tiga halaman:
Rekap nilai (dosen), Daftar tugas (dosen), dan Rekap nilai (admin). Jumlah
query sesuai skrip dengan selisih satu karena Debugbar menghitung
select * from users where id = ? (user dari sesi) yang dilewati skrip.
- Catatan membaca angka:
  - Jumlah query adalah angka yang paling stabil.
  - Waktu dan memori dari CLI hanya untuk membandingkan sebelum dan sesudah, bukan angka absolut Debugbar. Selisih waktu antar pengukuran sekitar 20 sampai 30%, jadi perubahan di bawah itu dianggap noise.
  - Kolom "Duplikat" membandingkan teks SQL tanpa nilai binding, sehingga bisa menghasilkan false positive (lihat temuan 3).

## 2. Hasil sebelum optimisasi

Diurutkan dari jumlah query terbanyak. Kolom "sesudah" diisi setelah perbaikan.

| Halaman | Query (sebelum) | Waktu ms (sebelum) | Memori MB (sebelum) | Query (sesudah) | Waktu ms (sesudah) | Memori MB (sesudah) |
|---------|---:|---:|---:|---:|---:|---:|
| Daftar pengumpulan (dosen) | 13 | 15.8 | 0.46 | [ISI] | [ISI] | [ISI] |
| Rekap nilai (admin) | 11 | 15.5 | 0.42 | [ISI] | [ISI] | [ISI] |
| Rekap nilai per matkul (mahasiswa) | 10 | 12.0 | 0.28 | [ISI] | [ISI] | [ISI] |
| Daftar tugas (dosen) | 9 | 13.6 | 0.27 | [ISI] | [ISI] | [ISI] |
| Rekap nilai (dosen) | 8 | 42.2 | 1.24 | [ISI] | [ISI] | [ISI] |
| Dashboard mahasiswa | 8 | 12.4 | 0.34 | [ISI] | [ISI] | [ISI] |
| Rekap nilai (mahasiswa) | 7 | 16.9 | 0.80 | [ISI] | [ISI] | [ISI] |
| Dashboard dosen | 7 | 15.3 | 0.27 | [ISI] | [ISI] | [ISI] |
| Daftar tugas (mahasiswa) | 6 | 8.5 | 0.25 | [ISI] | [ISI] | [ISI] |
| Dashboard admin | 6 | 5.7 | 0.22 | [ISI] | [ISI] | [ISI] |
| Detail mata kuliah (mahasiswa) | 5 | 5.8 | 0.22 | [ISI] | [ISI] | [ISI] |
| Daftar mata kuliah (mahasiswa) | 4 | 8.4 | 0.35 | [ISI] | [ISI] | [ISI] |
| Daftar mata kuliah (dosen) | 4 | 5.6 | 0.25 | [ISI] | [ISI] | [ISI] |
| Detail mata kuliah (dosen) | 3 | 5.8 | 0.22 | [ISI] | [ISI] | [ISI] |

Semua halaman berstatus 200 pada pengukuran ini.

## 3. Halaman terburuk dan alasannya

- **Berdasarkan jumlah query:** Daftar pengumpulan (dosen), 13 query. Semua query berbeda (tidak ada duplikat) dan index terpakai (`type=ref`), jadi bukan N+1.
- **Berdasarkan waktu dan memori:** **Rekap nilai (dosen)**, Verifikasi Debugbar (3 November 2026): total waktu request 149 ms, total waktu
query 19,35 ms (sekitar 13%), 9 query, 425 model dimuat. Sebagai pembanding,
Rekap nilai (admin) memuat 67 model dan Daftar tugas (dosen) 10 model. Waktu
dihabiskan di PHP (pembuatan model, penghitungan nilai di CourseController::
dosenGradesIndex, dan render), bukan di database.
- Target modul "rekap nilai di bawah 15 query" **sudah terpenuhi sebelum optimisasi** (7 sampai 11 query). Peningkatan yang dikejar ada pada waktu, memori, dan N+1 kecil.

## 4. Temuan

| # | Halaman | Temuan | Bukti | Status |
|---|---------|--------|-------|--------|
| 1 | Daftar tugas (dosen) | N+1: `select * from courses where id = ?` berjalan 4 kali, sebanyak jumlah tugas. Penyebab: `@can('update'/'delete', $assignment)` di view memicu `AssignmentPolicy` membaca `$assignment->course` per tugas. | Duplikat 3 dari 9 query; 4x query `courses` | Diperbaiki dengan eager loading `course` |
| 2 | Rekap nilai (admin) | `Grade::count()` menduplikasi hitungan `paginate()`; dua `count(*)` yang sama persis. | 2x `select count(*) from grades` | `[ISI]` |
| 3 | Dashboard admin | **Bukan N+1** (false positive). Dua query `count(*) ... where role = ?` memakai nilai role berbeda. Bisa digabung menjadi satu query `groupBy`. | Duplikat 1 | `[ISI]` |
| 4 | Rekap nilai (admin) | `order by graded_at` memindai seluruh tabel; kolom `graded_at` tanpa index. | Tabel di bawah | Migration index dibuat; EXPLAIN sesudah `[ISI]` |
| 5 | Rekap nilai (dosen) | Beban ada di memori dan waktu, bukan jumlah query. | 42.2 ms, 1.24 MB, 8 query | `[ISI]` |
| 6 | Halaman mahasiswa | Tidak ditemukan N+1. | Duplikat 0 di semua halaman mahasiswa | Tidak perlu perbaikan |

### Bukti EXPLAIN

Sebelum index:

```sql
EXPLAIN SELECT * FROM grades ORDER BY graded_at DESC LIMIT 15;
```

| table | type | key | rows |
|-------|------|-----|-----:|
| grades | ALL | (kosong) | 497 |

Sesudah index: `[ISI]` (jalankan `EXPLAIN` yang sama, tempel hasilnya).

Catatan: `EXPLAIN` pada `grades.submission_id` juga menunjukkan `ALL`, tetapi **itu bukan index yang hilang**. Kolom itu sudah `unique`; pada tabel 497 baris dengan daftar `IN` yang panjang, MySQL memilih memindai seluruh tabel. Tidak ada index yang ditambahkan di sana.

## 5. Perbaikan yang dilakukan

Perbaikan yang dilakukan:

- `CourseEnrollmentController::dosenIndex` memaginasi mahasiswa terdaftar dan mahasiswa yang tersedia, masing-masing dengan page name terpisah agar tidak saling menggeser.
- `SubmissionController::dosenIndex` memaginasi pengumpulan dan mahasiswa yang belum mengumpulkan. Ekspor pengumpulan memakai lazy iteration agar tidak memuat seluruh data ke memori.
- Daftar tugas/materi per mata kuliah dan komponen nilai tetap memakai `get()` karena jumlah normalnya belasan baris dan tampilan tidak membutuhkan navigasi halaman. Daftar mata kuliah mahasiswa/dosen juga tetap tidak dipaginasi karena dikelompokkan aktif/arsip dan jumlahnya kecil per pengguna.
- `AssignmentController::dosenIndex` eager load `course` untuk mencegah N+1 dari policy di Blade.
- Migration baru menambahkan index `grades.graded_at`; hasil EXPLAIN sesudah pengukuran akhir ditempel di bagian 4.
- Kolom angka sesudah pada bagian 2 masih diisi setelah seluruh PR ter-merge dan dataset baseline diimpor ulang.

## 6. Cache

Bagian yang di-cache: statistik dashboard admin dan dosen dengan `Cache::remember()`.

| Data | Masa berlaku | Dibersihkan saat | Akibat jika basi |
|------|-------------:|------------------|------------------|
| Statistik jumlah user dan mata kuliah admin | 10 menit | Model `User` atau `Course` tersimpan/dihapus | Angka dashboard dapat tertinggal maksimal 10 menit |
| Jumlah mata kuliah, tugas terbuka, dan submission belum dinilai milik dosen | 1 menit | Model `Course`, `Assignment`, `Submission`, atau `Grade` tersimpan/dihapus | Angka sementara dapat tertinggal maksimal 1 menit jika invalidasi tidak terjadi |

Bagian yang sengaja **tidak** di-cache: daftar tugas yang perlu dinilai, nilai mahasiswa, rekap nilai, status pengumpulan, dan jumlah notifikasi belum dibaca. Data tersebut harus mencerminkan keadaan terbaru karena data basi dapat menyebabkan pengguna mengambil keputusan yang salah atau melewatkan tindakan penting.

## 7. Ringkasan sesudah optimisasi

`[ISI]` Halaman rekap nilai setelah optimisasi: `[ISI]` query, `[ISI]` ms, `[ISI]` MB.