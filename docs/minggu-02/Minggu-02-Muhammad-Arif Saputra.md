
# READ
### Nama : Muhammad Arif Saputra
### NIM : 10241044

---

## 1. Baris mana di `routes/web.php` yang menangkapnya?

Jadi route `/tentang` dijalankan pada bagian 
```
Route::get('/tentang', function () {
    return view('tentang');
});
```
yang ada pada berkas `routes/web.php`

## 2. Kalau ditangani controller, berkas dan method mana?

untuk route `/tentang` ini masih belum menggunakan controller karena Request langsung ditangani pada closure yang berada di `routes/web.php`  
Jadi urutannya dari browser itu bakal masuk ke route terus di file `web.php` akan memanggil `view('tentang')` dan akan memanggil berkas `resources/views/tentang.blade.php` dan di browser akan muncul halaman `/tentang`.

## 3. View mana yang dikembalikan? Path nya di mana? 

Bagian view yang dikembalikan yaitu `return view('tentang');` dan path nya `resources/views/tentang.blade.php`

## 4. Layout apa yang membungkusnya?

View tentang.blade.php belum menggunakan layout untuk membungkusnya. Karena file tersebut merupakan halaman HTML mandiri yang langsung berisi struktur `<!DOCTYPE html>, <head>, dan <body>.

## 5. Jalankan `route::list --path=tentang`

```
D:\Pelajaran\VSC\laragon\www\ProWeb\kampuslms>php artisan route:list --path=tentang

  GET|HEAD       tentang ........................................................................................................................................................ routes/web.php:9

                                                                                                                                                                                Showing [1] routes
```

Hasil php artisan route:list --path=tentang menunjukkan bahwa route /tentang terdaftar dengan method GET|HEAD pada routes/web.php baris 9. Hal ini membuktikan bahwa Laravel telah mengenali dan mendaftarkan route /tentang dengan benar.

# BREAK

## 1. Mengubah `GET` menjadi `post` pada route mata kuliah

Prediksi: Jika route::get diganti menjadi route::post maka ketika /courses dibuka melalui browser akan terjadi ketidaksesuaian karena browser mengirim request GET.

Hasil: Setelah Route::get diubah menjadi post ketika membuka /courses muncul error 405 Method Not Allowed.

## 2. View tidak ditemukan

Prediksi: Jika nama view pada return view() diubah menjadi view yang tidak tersedia, Laravel akan menghasilkan error karena sistem tidak menemukan file view tersebut.

Hasil: Setelah nama view diubah menjadi `courses.alamak` dan halaman /courses dibuka, laravel menampilkan error `View [courses.alamak] not found.`

## 3. Hapus `name('courses.show')`

Prediksi: 