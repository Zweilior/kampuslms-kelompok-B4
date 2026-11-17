## 📌 Ringkasan Perubahan (Summary)
<!-- Jelaskan secara singkat perubahan apa saja yang dibuat dalam Pull Request ini -->

## 🎯 Alasan (Motivation & Context)
<!-- Mengapa perubahan ini diperlukan? Masalah apa yang diselesaikan atau fitur apa yang ditambahkan? Sertakan tautan issue jika ada (e.g. Closes #123) -->

## 🧪 Langkah Pengujian (Testing Steps)
<!-- Tuliskan panduan langkah demi langkah agar reviewer dapat memverifikasi atau menguji fungsionalitas ini secara lokal -->
1. 
2. 
3. 

Hasil pengujian otomatis:
- [ ] Pengujian otomatis (`php artisan test`) berhasil dijalankan dan semua lulus (PASS).

## 🔒 Pemeriksaan Keamanan & Otorisasi (Security & Authorization Checklist)
<!-- Pastikan aspek keamanan dan pembatasan hak akses sudah terpenuhi -->
- [ ] Otorisasi route dan controller sudah diterapkan menggunakan Middleware / Policy / Gate yang sesuai (misal: Admin, Dosen pemilik, Mahasiswa terdaftar).
- [ ] Input request divalidasi dengan benar untuk mencegah celah keamanan (XSS, SQL Injection, IDOR).
- [ ] Tidak ada data sensitif (password, token, kredensial, kunci API) yang bocor di response, log, atau commit.

## ⚡ Potensi Masalah Query & Performa (N+1 Query Check)
<!-- Periksa apakah ada eager loading yang terlewat pada relasi Eloquent -->
- [ ] Sudah diperiksa menggunakan Laravel Debugbar / query log bahwa query relasi telah dioptimalkan dengan Eager Loading (`with()`) jika memuat daftar/koleksi data.
- [ ] Bebas dari potensi query N+1 pada loop data tampilan (Blade) atau Resource/API collection.

## 🤖 Penggunaan AI (Bila Relevan)
<!-- Jelaskan keterlibatan alat bantu AI (misalnya Antigravity, GitHub Copilot, ChatGPT, dll.) jika digunakan dalam pengerjaan PR ini -->
- [ ] Tidak menggunakan AI
- [ ] Menggunakan AI untuk bantuan pengembangan:
  - **Tool yang digunakan**: <!-- sebutkan tool -->
  - **Bagian yang dibantu**: <!-- misal: pembuatan unit test, refactoring logika X -->
  - **Verifikasi**: Kode yang dihasilkan telah ditinjau dan divalidasi secara manual oleh author.
