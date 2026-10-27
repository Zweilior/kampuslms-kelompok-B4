# EduSpace

1. Laudya Aprilia Khoirum                  | 10241038
2. Marchelino Senduk Kaunang               | 10241040
3. Moh. Irsyad Fiqi Ferdiansyah Difa Nanda | 10241042
4. Muhammad Arif Saputra                   | 10241044

### Cara Instalasi
1. Jika menggunakan Laragon, bisa melakukan instalasi Laragon melalui:https://github.com/leokhoa/laragon/releases   
2. Download PHP melalui: https://www.php.net/
3. Download Nodejs melalui: https://nodejs.org/en/download
4. Setup ketiganya satu per satu.
5. Selanjutnya buka folder "laragon" > "bin" > "php", lalu copy file PHP yang sudah didownload tadi kefolder "php" tersebut.
6. Buka aplikasi "Laragon" > klik kanan > pilih menu PHP kemudian select versi file PHP yang barudidownload.
7. Lakukan juga untuk file Nodejs yang tadi didownload, tapi lakukan pada folder "nodejs" seperti padagambar.
8. Selanjutnya bisa mendownload Composer melalui: https://getcomposer.org/download/ pilihComposer-Setup.exe
9. Kemudian kembali ke aplikasi Laragon > klik kanan > PHP > Extensions.
10. Lalu aktifkan:
    - pdo_sqlite
    - sqlite3
    - zip (untuk mengekstrak di laravel versi 12 nanti)
11. Jalankan laragon dengan klik "Start All" lalu klik kanan > Quick App > Laravel dan berikan nama projectLaravelnya.
12. Setelah itu Laravel akan langsung dibuat dan memakan waktu cukup lama tergantung device penggunamasing masing.
13. Kembali ke menu Laragon dan klik "Root" kemudian akan diarahkan ke folder "www" yang berisi fileLaravel yang sudah dibuat sebelumnya.
14. Terakhir bisa membuka VSCode dan project Laravel yang sudah dibuat tadi bisa digunakan.

### Konfigurasi Unggah Berkas
Materi dan pengumpulan tugas disimpan di disk privat (`storage/app/private`), bukan di `storage/app/public`, jadi tidak perlu `php artisan storage:link` untuk keduanya. Berkas hanya bisa diunduh lewat controller yang memeriksa hak akses.

Batas di `php.ini` mengalahkan batas validasi Laravel. Pastikan nilainya cukup:

| Pengaturan | Nilai minimal |
|------------|---------------|
| `upload_max_filesize` | 20M |
| `post_max_size` | 25M |

Di Laragon: klik kanan Laragon > PHP > php.ini, ubah nilai di atas, lalu restart Apache (Stop All lalu Start All). Jika berkas di bawah 20 MB tetap gagal diunggah dengan pesan "Berkas gagal diunggah", periksa dua nilai ini.

Pastikan `.env` tidak mengubah disk default: `FILESYSTEM_DISK=local` (atau tidak diisi).

### Menjalankan Tes
`php artisan test`

### Menjalankan Queue Notifikasi

KampusLMS menggunakan queue database untuk memproses notifikasi tugas secara asinkron. Pastikan `.env` berisi:

```env
QUEUE_CONNECTION=database
```

Tabel `jobs`, `job_batches`, dan `failed_jobs` sudah disediakan oleh migrasi bawaan Laravel. Tabel `notifications` juga sudah memiliki migrasi proyek. Jalankan migrasi setelah menyiapkan koneksi database:

```bash
php artisan migrate
```

Jalankan worker di terminal terpisah selama aplikasi digunakan:

```bash
php artisan queue:work
```

Jika worker tidak berjalan, tugas tetap tersimpan dan aplikasi tetap merespons, tetapi job notifikasi hanya menumpuk di tabel `jobs` dan belum diproses. Karena itu, pastikan terminal worker tetap terbuka. Periksa job yang gagal dengan:

```bash
php artisan queue:failed
```

Untuk mencoba ulang semua job yang gagal:

```bash
php artisan queue:retry all
```


### Jobdesk
|Anggota | Tugasnya |
|--------|--------- |
|Arif | Full Stack |
|Marchelino | Backend |
|Irsyad | Frontend 1 |
|Laudya | Frontend 2 |
