#  3.3 Read → Break → Fix → Build
#### Nama : Marchelino Senduk Kaunang
#### NIM : 10241040

## 3.3 Read → Break → Fix → Build

### READ — Baca skema sebelum menulisnya (30 menit)

Sebelum menyentuh kode, kerjakan bersama kelompok:

1. Gambar ulang ERD dari spesifikasi di papan/kertas, tanpa melihat dokumen.
2. Untuk setiap foreign key, tentukan perilaku `onDelete`-nya dan **tuliskan alasannya**.
Jawab :

| Foreign Key | `onDelete` | Alasan |
|---|---|---|
| `mata_kuliah.dosen_id` → `dosen.id` | `RESTRICT` | Dosen yang masih memiliki mata kuliah tidak boleh langsung dihapus agar data mata kuliah tidak kehilangan dosen pengampunya. |
| `materi.mata_kuliah_id` → `mata_kuliah.id` | `CASCADE` | Materi merupakan bagian dari mata kuliah. Jika mata kuliahnya dihapus, materi yang terkait juga tidak diperlukan. |
| `tugas.mata_kuliah_id` → `mata_kuliah.id` | `CASCADE` | Tugas merupakan bagian dari mata kuliah, sehingga jika mata kuliah dihapus, tugas yang terkait ikut dihapus. |
| `submission.tugas_id` → `tugas.id` | `CASCADE` | Submission bergantung pada tugas. Jika tugas dihapus, submission yang terkait juga ikut dihapus. |
| `grades.submission_id` → `submission.id` | `CASCADE` | Nilai bergantung pada submission. Jika submission dihapus, nilai tersebut juga tidak diperlukan. |
| `submission.mahasiswa_id` → `mahasiswa.id` | `RESTRICT` | Mahasiswa yang masih memiliki riwayat pengumpulan sebaiknya tidak dapat dihapus sembarangan karena berhubungan dengan data akademik. |

Secara sederhana, `CASCADE` digunakan untuk data yang memang bergantung pada data induknya. Sedangkan `RESTRICT` digunakan untuk mencegah penghapusan data penting yang masih digunakan oleh data lain.

---

3. Jawab: kalau seorang dosen dihapus, apa yang terjadi pada mata kuliahnya? Kenapa dirancang begitu?
Jawab : 

Kalau seorang dosen dihapus, **mata kuliah yang masih menggunakan dosen tersebut tidak ikut dihapus**. Penghapusan dosen sebaiknya ditolak terlebih dahulu.

Alasannya karena mata kuliah memiliki data yang penting, seperti materi, tugas, submission, dan nilai mahasiswa. Kalau mata kuliah ikut terhapus hanya karena dosennya dihapus, data akademik yang berhubungan juga bisa ikut hilang.

Karena itu, `mata_kuliah.dosen_id` menggunakan **`RESTRICT`**. Dengan begitu, dosen tidak bisa dihapus selama masih menjadi dosen pengampu suatu mata kuliah. Data dosen perlu dipindahkan atau hubungannya dengan mata kuliah diselesaikan terlebih dahulu.

---

4. Jawab: kenapa `grades.submission_id` bersifat unique, bukan sekadar index biasa?
Jawab :

`grades.submission_id` dibuat **`UNIQUE`** karena satu submission hanya boleh memiliki **satu nilai**.

Contohnya:

```text
submission_id = 15 → nilai = 85
```

### BREAK — Lima kerusakan (45 menit)

| # | Yang dicoba | Yang harus Anda amati |
|---|-------------|------------------------|
| 1 | Hapus `unique(['course_id','user_id'])` dari `course_user`, lalu daftarkan mahasiswa yang sama dua kali | Data ganda lolos tanpa keluhan |
| 2 | Tambahkan `role` ke `$fillable` model `User`, lalu kirim request pembuatan user dengan `role=admin` lewat form yang **tidak punya field role** | **Mass assignment nyata** — Anda baru saja jadi admin |
| 3 | Ganti seluruh `$fillable` dengan `protected $guarded = [];` lalu ulangi nomor 2 | Kenapa `$guarded` kosong dilarang |
| 4 | Kosongkan isi `down()` di satu migrasi, lalu jalankan `php artisan migrate:refresh` | Migrasi tidak reversible = CI merah |
| 5 | Ubah `restrictOnDelete` pada `lecturer_id` menjadi `cascadeOnDelete`, lalu hapus satu dosen | Kehilangan data berantai |

Nomor 2 wajib benar-benar dilakukan, bukan sekadar dibayangkan. Anda perlu melihat sendiri bahwa **formulir di frontend bukan pembatas apa pun**.

### FIX — Repo cacat (30 menit)

Branch `w03` pada repo `kampuslms-broken` berisi migrasi dan model dengan **7 masalah**: urutan migrasi salah, dua unique composite hilang, satu `onDelete` keliru, satu `$guarded = []`, satu `down()` kosong, dan satu controller yang memakai `$request->all()`.

Perbaiki, kirim PR, dan **jelaskan dampak nyata tiap masalah** di deskripsi PR — bukan sekadar menyebut apa yang diubah.

Jawab : 
## 1. Urutan Migration Salah

Migration diperbaiki agar tabel parent dibuat terlebih dahulu sebelum tabel yang memiliki foreign key ke tabel tersebut.

**Dampak nyata:**

Jika urutan migration salah, `php artisan migrate` bisa gagal karena foreign key mengarah ke tabel yang belum dibuat. Akibatnya database tidak dapat dibuat dari kondisi kosong.

**Perbaikan:**

Migration diurutkan berdasarkan hubungan dan ketergantungan antar tabel.

---

## 2. Dua Composite Unique Hilang

Dua `unique composite` yang seharusnya ada ditambahkan kembali sesuai spesifikasi database.

**Dampak nyata:**

Tanpa `unique composite`, database dapat menerima data dengan kombinasi nilai yang sama lebih dari sekali. Akibatnya bisa muncul data duplikat yang seharusnya tidak diperbolehkan.

**Perbaikan:**

Menambahkan kembali constraint `unique` gabungan pada kolom yang sudah ditentukan oleh spesifikasi.

---

## 3. `onDelete` Keliru

Perilaku `onDelete` pada salah satu foreign key diperbaiki sesuai hubungan antar tabel.

**Dampak nyata:**

Jika menggunakan `CASCADE` padahal data seharusnya dipertahankan, menghapus data induk dapat menyebabkan data anak ikut terhapus. Sebaliknya, jika seharusnya `CASCADE` tetapi menggunakan `RESTRICT`, data induk tidak dapat dihapus karena masih memiliki data yang bergantung padanya.

**Perbaikan:**

Menggunakan perilaku `onDelete` yang sesuai dengan aturan dan kebutuhan data.

---

## 4. Penggunaan `$guarded = []`

Pada model ditemukan penggunaan:

```php
protected $guarded = [];
```