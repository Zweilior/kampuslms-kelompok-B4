# Dokumentasi Debugging Week 14

Dokumen ini mencatat temuan hasil audit dan debugging pada fitur enrollment,
Assignment API, serta Grade Component API. Setiap temuan dilengkapi dengan
dampak, perbaikan, dan pengujian regresi.

## 1. Penghapusan enrollment tidak memeriksa riwayat akademik mahasiswa

**Lokasi:** `app/Http/Controllers/CourseEnrollmentController.php`

### Masalah

Pada implementasi sebelumnya, admin atau dosen dapat menghapus enrollment
mahasiswa hanya dengan memeriksa role mahasiswa dan keberadaan enrollment
pada mata kuliah. Pemeriksaan belum mempertimbangkan apakah mahasiswa sudah
memiliki riwayat pengumpulan tugas atau nilai akhir pada mata kuliah tersebut.

### Dampak

Mahasiswa dapat dikeluarkan dari mata kuliah meskipun sudah memiliki riwayat
akademik. Data submission dan nilai tidak otomatis terhapus, tetapi mahasiswa
dapat kehilangan akses ke halaman atau fitur yang bergantung pada relasi
enrollment, seperti daftar mata kuliah dan komponen nilai.

### Perbaikan

Sebelum menghapus enrollment, sistem memeriksa keberadaan submission dan
nilai akhir mahasiswa pada mata kuliah terkait. Jika salah satunya ditemukan,
penghapusan ditolak dan sistem menampilkan pesan bahwa mahasiswa masih
memiliki riwayat akademik.

### Pengujian regresi

- `test_owner_cannot_remove_student_with_submission_history`
- `test_owner_cannot_remove_student_with_final_grade`

Pengujian memastikan enrollment tetap tercatat ketika mahasiswa memiliki
submission atau nilai akhir.

## 2. API assignment menerima request update kosong

**Lokasi:** `app/Http/Controllers/Api/V1/AssignmentController.php`

### Masalah

Pada implementasi sebelumnya, endpoint update assignment menggunakan
validasi `sometimes|required` untuk setiap field. Ketika request tidak
mengirimkan field apa pun, seluruh field opsional tidak divalidasi dan data
hasil validasi menjadi kosong. Sistem tetap menjalankan proses update dan
mengembalikan respons sukses.

### Dampak

Client API dapat menerima respons HTTP 200 meskipun tidak ada data yang
diperbarui. Hal ini membuat respons API tidak mencerminkan tujuan operasi
update dan dapat membingungkan client.

### Perbaikan

Sistem memeriksa apakah request memiliki setidaknya satu field yang dapat
diperbarui. Jika tidak ada, API mengembalikan HTTP 422 dengan pesan:

`Minimal satu data harus diisi untuk memperbarui tugas.`

### Pengujian regresi

- `test_api_rejects_empty_assignment_update`

Pengujian memastikan request kosong menghasilkan HTTP 422 dan judul
assignment tidak berubah.

### Bukti BREAK/FIX

Pemeriksaan `hasAny()` pada controller sempat dinonaktifkan sementara untuk
simulasi BREAK. Pengujian regresi kemudian gagal karena endpoint mengembalikan
HTTP 200, bukan HTTP 422. Setelah pemeriksaan dikembalikan, pengujian kembali
berhasil.

## 3. API Grade Component mengakses relasi yang belum dimuat

**Lokasi:**
- `app/Http/Controllers/Api/V1/AssignmentController.php`
- `app/Http/Controllers/Api/V1/GradeComponentController.php`
- `app/Http/Resources/GradeComponentResource.php`

### Masalah

Project mengaktifkan `Model::preventLazyLoading()` pada environment
non-production melalui `AppServiceProvider`.

Pada implementasi sebelumnya, beberapa endpoint mengakses relasi `course`
dan `students` secara langsung tanpa memastikan relasi tersebut sudah
dimuat. Kondisi ini berpotensi memicu lazy loading exception ketika endpoint
dijalankan pada environment testing.

Selain itu, `GradeComponentResource` sebelumnya menghitung jumlah assignment
dengan memanggil `$this->assignments()->count()` secara langsung. Pemanggilan
tersebut menjalankan query count tersendiri setiap kali resource dibuat.

### Dampak

Akses relasi yang belum dimuat dapat menyebabkan endpoint gagal ketika
pencegahan lazy loading aktif. Query count yang dijalankan langsung dari
resource juga menambah query database yang seharusnya dapat disiapkan oleh
controller.

### Perbaikan

- Menggunakan `loadMissing('course')` sebelum mengakses relasi course.
- Menggunakan query relasi eksplisit untuk memeriksa enrollment mahasiswa.
- Menggunakan `withCount('assignments')` atau `loadCount('assignments')`
  pada controller.
- Mengubah resource agar membaca `assignments_count` yang sudah disiapkan,
  bukan menjalankan query count sendiri.

### Pengujian regresi

File: `tests/Feature/ApiLazyLoadingTest.php`

- `test_assignment_update_and_delete_do_not_lazy_load_course`
- `test_grade_component_endpoints_do_not_lazy_load_course_or_assignments`

Hasil pengujian setelah perbaikan:

`php artisan test --filter=ApiLazyLoadingTest`

- 2 tests passed
- 9 assertions
- 0 failures

### Catatan

Perubahan `assignments_count` merupakan optimasi query yang berkaitan dengan
perbaikan API, tetapi berbeda dari masalah lazy loading relasi. Tidak ada
klaim peningkatan performa dalam persentase karena belum dilakukan pengukuran
kuantitatif khusus untuk perubahan ini.

---

## Ringkasan

| No. | Temuan | Perbaikan / Bukti Pengujian |
|---|---|---|
| 1 | Enrollment dapat dihapus meskipun mahasiswa memiliki riwayat akademik | Pemeriksaan submission dan nilai akhir; test regresi |
| 2 | Request update assignment kosong menghasilkan respons sukses | Validasi minimal satu field; test regresi dan simulasi BREAK/FIX |
| 3 | API mengakses relasi yang belum dimuat ketika lazy loading dicegah | `loadMissing`, query relasi eksplisit, dan test `ApiLazyLoadingTest` |
