### 1. Hapus unique(['course_id','user_id']) dari course_user, lalu daftarkan mahasiswa yang sama dua kali

Setelah baris berikut dinonaktifkan:
```
$table->unique(['course_id', 'user_id']);
```
sistem akan tetap menerima pendaftaran mahasiswa yang sama ke mata kuliah yang sama sebanyak dua kali, tanpa muncul peringatan atau penolakan dari database.

Kondisi ini bertentangan dengan logika bisnis enrollment, di mana satu pasangan `course_id` dan `user_id` semestinya hanya boleh tercatat satu kali. Mengandalkan pengecekan di controller saja tidak cukup untuk menutup celah ini, sebab bila dua permintaan datang dalam waktu yang hampir bersamaan, keduanya bisa saja sama-sama dianggap "belum terdaftar" sebelum salah satunya benar-benar tersimpan lebih dulu.

Dapat dilihat bahwa constraint `unique(['course_id', 'user_id'])` bukan sekadar mempercepat pencarian data seperti fungsi index pada umumnya, melainkan benar-benar berperan sebagai penjaga terakhir agar data ganda tidak bisa masuk, apa pun kondisi di sisi aplikasi.

### 2. Tambahkan role ke $fillable model User, lalu kirim request pembuatan user dengan role=admin lewat form yang tidak punya field role

Ketika `'role'` dimasukkan ke dalam `$fillable`, kolom tersebut ternyata bisa diisi lewat mass assignment sekalipun form pendaftaran yang sesungguhnya tidak pernah menyediakan input untuk `role`. Percobaan mengirim nilai `role => 'admin'` secara langsung — baik melalui Tinker maupun request HTTP — berhasil tersimpan di database dan user tersebut otomatis berstatus admin.

Hal ini memperlihatkan bahwa tampilan formulir di sisi frontend sama sekali bukan jaminan keamanan.

Oleh sebab itu, `role` seharusnya tidak dicantumkan di `$fillable`, melainkan diisi secara eksplisit lewat logika di controller, bukan berasal dari input pengguna.

Setelah percobaan ini selesai, `'role'` dikeluarkan kembali dari `$fillable`:
```php
protected $fillable = [
    'name',
    'email',
    'password',
];
```

### 3. Ganti seluruh $fillable dengan protected $guarded = []; lalu ulangi nomor 2

Menerapkan `$guarded = []` ternyata membuka **seluruh** kolom pada tabel agar bisa diisi lewat mass assignment, tidak terbatas pada `role` saja. Saat percobaan nomor 2 diulang dalam kondisi ini, bukan hanya `role=admin` yang berhasil disisipkan, tapi kolom lain seperti `email_verified_at` pun ikut bisa diisi begitu saja tanpa melalui proses verifikasi apa pun.

Kondisi ini lebih berisiko dibanding sekadar lupa mengeluarkan satu kolom tertentu dari `$fillable`, karena `$guarded = []` juga otomatis membuka akses ke kolom-kolom baru yang akan ditambahkan di migrasi selanjutnya.

Dari sini bisa disimpulkan bahwa `$guarded = []` tidak layak dipakai. Pendekatan `$fillable` yang eksplisit jauh lebih aman karena sifatnya whitelist — hanya kolom yang benar-benar disebutkan yang boleh diisi lewat mass assignment, sementara kolom baru secara bawaan tetap tertutup sampai sengaja ditambahkan.

### 4. Kosongkan isi down() di satu migrasi, lalu jalankan php artisan migrate:refresh

Sebagai percobaan, isi method `down()` pada migrasi `courses` dikosongkan, sehingga proses rollback tidak benar-benar menghapus tabel `courses`. Setelah itu dijalankan:
```
php artisan migrate:refresh
```

Proses rollback memang tampak berjalan tanpa keluhan (karena tidak ada perintah yang dijalankan sama sekali), namun ketika tahap `up()` dieksekusi kembali, muncul pesan error:
```
SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'courses' already exists
```

Error ini muncul karena tabel `courses` sebenarnya masih ada saat Laravel mencoba membuatnya ulang — bukti bahwa migrasi tersebut tidak reversible. Kondisi semacam ini juga akan membuat proses CI gagal, sebab `migrate:refresh` menjadi salah satu tahapan yang wajib berhasil di pipeline.

Untuk itu, `down()` harus benar-benar menjalankan kebalikan dari `up()`:
```php
public function down(): void
{
    Schema::dropIfExists('courses');
}
```

Setelah pengujian selesai, isi `down()` dikembalikan seperti semula, kemudian database disegarkan ulang:
```
php artisan migrate:fresh --seed
```

### 5. Ubah restrictOnDelete pada lecturer_id menjadi cascadeOnDelete, lalu hapus satu dosen

Pada percobaan ini, constraint `lecturer_id` di tabel `courses` yang semula:
```
->restrictOnDelete();
```
diganti sementara menjadi:
```
->cascadeOnDelete();
```

Setelah migrasi dijalankan ulang, diuji satu dosen yang mengampu suatu mata kuliah, kemudian dosen tersebut dihapus. Hasilnya, mata kuliah yang diampunya ikut terhapus secara otomatis dari database, bukan sekadar ditolak atau dibiarkan tanpa pengampu.

Ini menunjukkan bahwa `cascadeOnDelete()` bisa memicu hilangnya data `courses` — dan berpotensi data turunannya seperti materi maupun tugas — hanya karena satu akun dosen dihapus, padahal mata kuliah semestinya tetap dipertahankan meski pengampunya berubah atau keluar.

Karena itu, pilihan yang tepat untuk `lecturer_id` adalah:
```
->restrictOnDelete();
```
sehingga sistem akan menolak penghapusan dosen selama ia masih terdaftar sebagai pengampu suatu mata kuliah, memaksa admin memindahkan pengampu terlebih dahulu sebelum akun dosen tersebut benar-benar bisa dihapus.

Setelah percobaan selesai, `cascadeOnDelete()` dikembalikan menjadi:
```
->restrictOnDelete();
```
lalu database direfresh:
```
php artisan migrate:fresh --seed
```