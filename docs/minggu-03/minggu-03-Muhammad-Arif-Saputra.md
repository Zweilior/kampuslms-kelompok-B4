# BREAK
## Nama : Muhammad Arif Saputra
## NIM : 10241044

### 1. Hapus unique(['course_id','user_id']) dari course_user, lalu daftarkan mahasiswa yang sama dua kali

Hasil percobaan menunjukkan bahwa jika constraint:
```
$table->unique(['course_id', 'user_id']);
```
dihapus, database mengizinkan mahasiswa yang sama terdaftar dua kali pada mata kuliah yang sama.

Hal ini tidak sesuai dengan aturan data enrollment, karena kombinasi `course_id` dan `user_id` seharusnya hanya boleh muncul satu kali.

Karena itu, `unique(['course_id', 'user_id'])` diperlukan untuk menjaga integritas data di level database. Constraint tersebut bukan hanya untuk mempercepat pencarian seperti index, tetapi benar-benar mencegah data duplikat.

Setelah pengujian selesai, constraint tersebut dikembalikan:
```php
$table->unique(['course_id', 'user_id']);
```
dan database di-reset kembali dengan:
```php
php artisan migrate:fresh --seed
```

### 2. Tambahkan role ke $fillable model User, lalu kirim request pembuatan user dengan role=admin lewat form yang tidak punya field role

Hasil percobaan menunjukkan bahwa ketika `'role'` ditambahkan ke `$fillable`, field `role` dapat diisi melalui mass assignment. Dengan percobaan membuat user menggunakan `role => 'admin'`, database berhasil menyimpan user tersebut dengan role admin.

Hal ini berbahaya karena `role` menentukan hak akses pengguna. Jika `role` diperbolehkan melalui mass assignment, pengguna dapat berpotensi memberikan role yang tidak seharusnya dimiliki.

Karena itu, `role` tidak boleh dimasukkan ke `$fillable` dan harus diberikan secara eksplisit melalui logic backend.

Setelah pengujian selesai, `'role'` dihapus kembali dari `$fillable` sehingga menjadi:
```php
protected $fillable = [
    'name',
    'email',
    'password',
    'nim_nip',
];
```

### 3. Ganti seluruh $fillable dengan protected $guarded = []; lalu ulangi nomor 2

Karena `$guarded = []` membuat semua atribut dapat diisi melalui mass assignment. Itu berbahaya untuk field sensitif seperti `role`, karena role dapat ikut diisi dari input mass assignment. Saya menggunakan `$fillable` secara eksplisit agar hanya field yang memang diperbolehkan yang dapat diisi melalui mass assignment.

### 4. Kosongkan isi down() di satu migrasi, lalu jalankan php artisan migrate:refresh

Untuk pengujian, method `down()` pada migration `users` dikosongkan sehingga proses rollback tidak menghapus tabel `users`.
Kemudian dijalankan:
```
php artisan migrate:refresh
```
Hasilnya, proses rollback dianggap berhasil, tetapi ketika migration dijalankan kembali terjadi error:
```
Table 'users' already exists
```
Hal ini menunjukkan bahwa migration tidak dapat melakukan rollback dengan benar karena tabel users masih ada.

Karena itu, method down() harus berisi perintah untuk membalikkan perubahan dari up(), yaitu:
```
public function down(): void
{
    Schema::dropIfExists('users');
}
```
Setelah pengujian selesai, down() dikembalikan seperti semula dan database di-reset kembali dengan:
```
php artisan migrate:fresh --seed
```

### 5. Ubah restrictOnDelete pada lecturer_id menjadi cascadeOnDelete, lalu hapus satu dosen

Untuk pengujian, constraint lecturer_id pada tabel courses yang awalnya menggunakan:
```
->restrictOnDelete();
```
sementara diubah menjadi:
```
->cascadeOnDelete();
```
Kemudian dibuat satu dosen dan satu course yang menggunakan dosen tersebut sebagai lecturer_id. Setelah dosen dihapus, course yang terkait juga ikut terhapus. Hasil pengecekan terhadap course tersebut menghasilkan null.

Hal ini menunjukkan bahwa cascadeOnDelete() dapat menyebabkan data course ikut terhapus ketika dosen dihapus.

Karena itu, lecturer_id menggunakan:
```
->restrictOnDelete();
```
agar data course tidak ikut terhapus secara otomatis ketika dosen yang terkait dihapus.

Setelah pengujian selesai, cascadeOnDelete() dikembalikan menjadi:
```
->restrictOnDelete();
```
dan database di-reset kembali dengan:
```
php artisan migrate:fresh --seed
```