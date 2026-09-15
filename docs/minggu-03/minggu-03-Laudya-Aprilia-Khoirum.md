# Break Week 3 – Pemrograman Web
**Nama:** Laudya Aprilia Khoirum  
**NIM:** 10241038  
**Program Studi:** Sistem Informasi  

---

## 1. Hapus `unique(['course_id','user_id'])` dari `course_user`, lalu daftarkan mahasiswa yang sama dua kali 

Jawaban: 
Saat kita mengeksekusi tanpa menghapus saat mencoba mendaftarkan mahasiswa yang sama ke mata kuliah yang sama dua kali, database menolak dan melempar error `Integrity constraint violation (Duplicate entry)` karena Mahasiswa hanya bisa terdaftar 1 kali.

Tetapi saat `unique(['course_id','user_id'])` dari `course_user` pada line 20, pendaftaran ganda berhasil lolos ke database tanpa error. Mahasiswa terdaftar 2 kali (atau lebih) pada mata kuliah yang sama, menyebabkan data duplikat yang merusak integritas sistem.

---

## 2. Tambahkan `role` ke `$fillable` model `User`, lalu kirim request pembuatan user dengan `role=admin` lewat form yang **tidak punya field role**

Jawaban:
Normalnya saat membuat pengguna baru menggunakan mass assignment (`User::create([...])`) dengan menyisipkan `'role' => 'admin'`, nilai role tersebut diabaikan oleh Eloquent dan Pengguna tetap mendapat role default (misal: `mahasiswa`).

Nah yang terjadi setelah kita rusak itu pengguna baru berhasil dibuat dan langsung mendapatkan status `role = 'admin'`. Hal ini membuka celah keamanan Mass Assignment Vulnerability, di mana penyerang bisa mengambil alih hak akses sistem melalui pengiriman payload form.

---

## 3. Ganti seluruh `$fillable` dengan `protected $guarded = [];` lalu ulangi nomor 2

Jawaban:
Sebelum kita ganti, hanya kolom yang secara eksplisit didefinisikan di dalam `$fillable` yang boleh diisi secara massal (whitelisting). Kolom sensitif seperti `role` aman dari manipulasi request luar.

Setelah kita ganti, fitur pertahanan mass assignment bawaan Laravel mati total dan seluruh kolom tabel `users` terbuka bebas untuk diubah atau diisi melalui request input dari luar tanpa ada penyaringan sama sekali.

---

## 4. Kosongkan isi `down()` di satu migrasi, lalu jalankan `php artisan migrate:refresh`

Jawaban:
Sebelum kita kosongkan perintah `php artisan migrate:refresh` atau `migrate:rollback` berjalan lancar karena method `down()` mengeksekusi perintah penghapusan tabel (misal: `Schema::dropIfExists('courses')`).

Setelah dikosongkan, perintah `php artisan migrate:refresh` gagal total dan melempar `QueryException` (tabel sudah ada). Migrasi menjadi irreversible (tidak bisa dibalikkan) dan menyebabkan pipeline CI/CD GitHub Actions berstatus merah (gagal).

---

## 5. Ubah `restrictOnDelete` pada `lecturer_id` menjadi `cascadeOnDelete`, lalu hapus satu dosen | Kehilangan data berantai

Jawaban:
Saat mencoba menghapus akun dosen yang masih mengampu kelas, database menolak penghapusan dengan error  `Foreign key constraint fails`. Data mata kuliah, materi, dan tugas tetap aman.

Tetapi setelah kita ubah, yang terjadi malah akun dosen berhasil terhapus, namun memicu penghapusan berantai (cascade deletion). Seluruh mata kuliah yang diampu oleh dosen tersebut—beserta materi, tugas, dan submission mahasiswa di dalamnya—ikut terhapus permanen dari database.