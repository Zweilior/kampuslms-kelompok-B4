#  2.3 Read → Break → Fix → Build
#### Nama : Marchelino Senduk Kaunang
#### NIM : 10241040

Ambil route `/tentang` yang Anda buat minggu lalu. 

1. Baris mana di `routes/web.php` yang menangkapnya?
Jawab:

Route `/tentang` ditangkap oleh:
```php
Route::get('/tentang', function () { 
    return view('tentang'); 
})->name('tentang');
```
Pada saat kita membuka halaman `/tentang`, Laravel akan mengeksekusi kode tersebut.

2. Kalau ditangani controller, berkas dan method mana?
Jawab:

Route ini tidak menggunakan controller. Langsung menggunakan function/Closure yang ada di `web.php`. Jadi tidak ada file controller khusus untuk halaman tentang.


3. View mana yang dikembalikan? Di path apa persisnya?
Jawab:

View yang dipanggil adalah:
```php
return view('tentang');
```

Laravel akan mencari file:
```php
resources/views/tentang.blade.php
```

Jadi file `tentang.blade.php` harus berada di folder resources/views.

4. Layout apa yang membungkusnya?
Jawab:

Dari kode `web.php` ini, belum terlihat adanya layout. Route hanya langsung memanggil `tentang.blade.php`. Kalau di dalam file tersebut ada <x-layout>, barulah halaman tersebut menggunakan layout tersebut.

5. Jalankan `php artisan route:list --path=tentang`. Cocok dengan analisis Anda?
Jawab:

Jalankan
```php
php artisan route:list --path=tentang
```

Hasilnya seharusnya menampilkan route GET/HEAD `/tentang` dengan nama route tentang. Hasil tersebut cocok dengan analisis karena memang route `/tentang` dibuat menggunakan `Route::get()` dan diberi nama `tentang`.