# Hasil Verifikasi Pra-Review

Tanggal pengujian: 17 November 2026

## Lingkungan

- Windows, PHP 8.5.12 CLI dari Laragon.
- Composer dependencies tersedia (`vendor/`); Node dependencies juga tersedia.
- Konfigurasi PHPUnit menggunakan SQLite in-memory (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`).
- PHP CLI memuat `pdo_mysql`, `pdo_sqlite`, dan `sqlite3`.
- Working tree bersih saat pengujian. Pengujian dilakukan pada branch `laudya-38`.

## Perintah dan hasil aktual

### PHPUnit

Perintah:

```text
php artisan test
```

Hasil:

- **137 lulus, 806 assertion**, durasi 9,16 detik, exit code 0.
- Tidak ada test gagal.
- Suite mencakup pengujian role dan policy, akses mahasiswa/dosen/admin,
  pencegahan IDOR, unduhan material privat, notifikasi, lazy loading, dan
  pembersihan file.

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
dosen, dan admin, termasuk pembatasan IDOR antar dosen. Feature test lokal juga
memvalidasi akses resource privat: admin, pemilik/enrolled user dapat
mengunduh material, sedangkan outsider dan tamu ditolak; akses material melalui
course yang tidak sesuai menghasilkan 404.

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

1. Jika diperlukan bukti terbaru, ulangi kedua skrip dengan secret melalui
   environment lokal/CI, bukan dokumentasi atau commit.
2. Koordinasikan kegagalan fungsional yang tersisa dengan pemilik fitur sebelum
   mengajukan pull request.
