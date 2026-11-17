# Hasil Verifikasi Pra-Review

Tanggal pengujian: 17 November 2026

## Lingkungan

- Windows, PHP 8.5.12 CLI dari Laragon.
- Composer dependencies tersedia (`vendor/`); Node dependencies juga tersedia.
- Konfigurasi PHPUnit menggunakan SQLite in-memory (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`).
- PHP CLI memuat `pdo_mysql`, tetapi tidak memuat `pdo_sqlite` atau `sqlite3`.
- Working tree bersih saat pengujian. Pengujian dilakukan pada branch `laudya-38`.

## Perintah dan hasil aktual

### PHPUnit

Perintah:

```text
php artisan test
```

Hasil:

- **122 gagal, 15 lulus, 77 assertion**, durasi 19,79 detik, exit code 2.
- Test yang lulus mencakup `Tests\Unit\ExampleTest`, `Tests\Unit\FileSizeTest`,
  `Tests\Feature\AboutPageTest`, dan `Tests\Feature\AuthorizationErrorPagesTest`.
- Kegagalan feature test memiliki penyebab yang sama: `QueryException: could not
  find driver` saat Laravel membuka koneksi SQLite `:memory:`.
- Hasil ini belum dapat dipakai untuk menyimpulkan fitur aplikasi gagal. Driver
  SQLite perlu diaktifkan pada PHP CLI, kemudian seluruh suite perlu dijalankan
  ulang.

### Skrip API dan otorisasi

Perintah yang dicoba melalui `C:\laragon\bin\git\bin\bash.exe`:

```text
./scripts/test-api.sh
./scripts/test-authz.sh
```

Kedua skrip berhenti pada validasi environment dengan exit code 2:
`Variabel environment BASE_URL wajib diisi.` Skrip membutuhkan URL deployment,
token untuk admin/mahasiswa/dosen, dan ID data uji. Nilai tersebut tidak
tersedia pada sesi ini, sehingga tidak ada request HTTP atau hasil status code
yang dapat dicatat. Token dan rahasia sengaja tidak ditulis di dokumen ini.

### Hasil pengujian production yang sudah terdokumentasi

Setelah pengujian lokal di atas, `docs/deployment.md` mencatat hasil eksekusi
kedua skrip terhadap API production setelah deployment diperbarui:

- `scripts/test-api.sh`: seluruh 11 skenario lulus, termasuk 401 tanpa token,
  403 untuk akses mahasiswa atau resource dosen lain, dan 422 untuk request
  dosen yang lolos otorisasi tetapi gagal validasi form kosong.
- `scripts/test-authz.sh`: seluruh skenario lulus, termasuk akses mahasiswa,
  dosen, dan admin, serta pencegahan IDOR antar dosen.
- Output akhir yang dicatat: `Semua pengujian API berhasil.` dan
  `Semua pengujian otorisasi berhasil.`

Hasil production tersebut berasal dari catatan deployment yang sudah ada,
bukan eksekusi ulang dalam sesi ini. ID data uji dicatat di
`docs/deployment.md`, sedangkan token dan password tidak dicatat.

## Verifikasi akses berbasis hasil yang tersedia

Verifikasi API production yang terdokumentasi telah mencakup role mahasiswa,
dosen, dan admin, termasuk pembatasan IDOR antar dosen. Namun, pengujian
resource privat secara khusus belum tercatat pada kedua skrip tersebut.
Feature test lokal terkait policy dan unduhan material privat tetap belum
tervalidasi karena terhenti sebelum database SQLite dapat dibuat.

## Tinjauan performa dan keamanan

`docs/performa.md` sudah mencatat trade-off pagination: daftar pengumpulan
dosen meningkat dari 13 menjadi 16 query karena dua query `count(*)`, sementara
jumlah baris yang dimuat dibatasi 15 per daftar. Catatan tersebut tidak diubah;
kenaikan query tidak disebut sebagai regresi karena manfaat pada data besar
belum diukur.

`docs/keamanan.md` mencatat pemetaan policy/query untuk role dan resource privat,
serta temuan lanjutan yang perlu ditindaklanjuti, termasuk perbedaan aturan
tenggat nilai antara web dan API.

## Tindak lanjut

1. Aktifkan ekstensi `pdo_sqlite` dan `sqlite3` pada PHP CLI yang dipakai
   Composer/PHPUnit.
2. Jalankan ulang `php artisan test` dan simpan daftar test serta pesan gagal
   aktual setelah hambatan environment hilang.
3. Jika diperlukan bukti terbaru, ulangi kedua skrip dengan secret melalui
   environment lokal/CI, bukan dokumentasi atau commit.
4. Tambahkan atau jalankan skenario unduhan material privat untuk admin, dosen
   pemilik, mahasiswa terdaftar, pengguna lain, dan tamu; catat status HTTP
   tanpa mencatat token.
5. Koordinasikan kegagalan fungsional yang tersisa dengan pemilik fitur sebelum
   mengajukan pull request.
