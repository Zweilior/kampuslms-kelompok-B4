
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

untuk route `/tentang` ini masih belum menggunakan controller karena Request langsung ditangani pada 