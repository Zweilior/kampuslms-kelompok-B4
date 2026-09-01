# Tugas 1 – Pemrograman Web
**Nama:** Laudya Aprilia Khoirum  
**NIM:** 10241038  
**Program Studi:** Sistem Informasi  

---

## 1. Buka `public/index.php.` Baca dari atas ke bawah. Tulis dalam 3 kalimat apa yang dilakukan berkas ini.

Jawaban: 

- Berkas ini mencatat waktu mulai eksekusi aplikasi dan memeriksa apakah sistem sedang dalam mode pemeliharaan (maintenance mode).
  
    ![Soal 1.1](img/week.1.1.1.png)

- Selanjutnya, berkas memuat autoloader Composer untuk mengimpor seluruh dependensi serta menginisialisasi pustaka bootstrap aplikasi Laravel.
  
    ![Soal 1.2](img/week.1.1.2.png)

- Terakhir, berkas menangkap request HTTP yang masuk dari pengguna dan memprosesnya untuk mengembalikan respons web.
  
    ![Soal 1.3](img/week.1.1.3.png)
 
---

## 2. Buka `bootstrap/app.php`. Identifikasi bagian mana yang mengurus route, mana yang mengurus middleware, mana yang mengurus exception.

Jawaban:

- Routing: Ditangani oleh metode ->withRouting(...), di mana rute-rute aplikasi (seperti web.php dan console.php) didaftarkan.
  
    ![Soal 2.1](img/week.1.2.1.png)

- Middleware: Ditangani oleh metode ->withMiddleware(function (Middleware $middleware) { ... }), tempat kita dapat mengonfigurasi atau menambahkan middleware global/kelompok.
  
    ![Soal 2.2](img/week.1.2.2.png)

- Exception: Ditangani oleh metode ->withExceptions(function (Exceptions $exceptions) { ... }), tempat pengaturan penanganan error dan exception kustom dilakukan.
  
    ![Soal 2.3](img/week.1.2.3.png)

---

## 3. Buka `routes/web.php`. Temukan route yang menghasilkan halaman selamat datang. Ubah teksnya, muat ulang browser, pastikan berubah.

Jawaban:

- Jadi ketika `routes/web.php` baru dibuka isinya seperti ini, terlihat bahwa terdapat bagian `view('welcome')`

    ![Soal 3.1](img/week.1.3.1.png)

- Pada tulisan `view('welcome')` tersebut kita ubah menjadi tulisan "Halo semuanya, saya laudya dan saya suka boneka"

    ![Soal 3.2](img/week.1.3.2.png)

- Setelah itu dapat kita refresh halaman chrome kita dan tampilannya akan berubah jadi plain seperti ini

    ![Soal 3.3](img/week.1.3.3.png)

- Ada lagi jika kita buka laman asli dashboard laravel kita akan ditemukan oleh tampilan seperti dibawah

    ![Soal 3.4](img/week.1.3.4.png)

- Tulisan "Let's get started" dapat kita ubah jika kita buka `resources/view/welcome.blade.php`.

    ![Soal 3.5](img/week.1.3.5.png)

- Ini adalah halaman yang di panggil oleh `view('welcome')` di `routes/web.php`
    ![Soal 3.6](img/week.1.3.6.png)

- Sehingga jika kita cari kalimat "Let's get started" dan menggantinya dengan kalimat lain maka tulisannya juga akan berubah
    
    ![Soal 3.7](img/week.1.3.7.png)

    ![Soal 3.8](img/week.1.3.8.png)

---

## 4. Jalankan `php artisan route:list`. Cocokkan keluarannya dengan isi `routes/web.php`.

Jawaban:

![Soal 4.1](img/week.1.4.1.png)
- Di dalam terminal saya lihat lihat tabel rute.
- Pada baris dengan Method: GET | HEAD paling pertama terdapat definisi Route::get('/', ...) yang ada pada berkas routes/web.php.