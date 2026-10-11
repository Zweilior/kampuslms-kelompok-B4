# Break Week 4 – Pemrograman Web
**Nama:** Laudya Aprilia Khoirum  
**NIM:** 10241038  
**Program Studi:** Sistem Informasi  

---

## 1. Hapus `@csrf` dari form, lalu kirim

Jawaban: 
Normalnya, form di Laravel dilengkapi dengan `@csrf` untuk memverifikasi bahwa request yang masuk benar-benar berasal dari aplikasi kita sendiri.

Nah, saat `@csrf` ini kita hapus dari form, lalu kita coba tekan tombol submit (seperti pada form hapus course), Laravel langsung menolak request tersebut dan menampilkan error `419 PAGE EXPIRED`. Hal ini terjadi karena request yang mengubah data tidak membawa token CSRF yang valid. Pengujian ini membuktikan bahwa CSRF token sangat penting untuk mencegah serangan Cross-Site Request Forgery yang memanfaatkan session pengguna tanpa izin.

---

## 2. Ganti `$request->validated()` menjadi `$request->all()`, lalu kirim field liar lewat `curl`

Jawaban:
Sebelum dirusak, penggunaan `$request->validated()` memastikan bahwa hanya field yang sudah dinyatakan lolos aturan validasi saja yang boleh masuk ke dalam variabel $data.

Namun, setelah kita ganti menjadi `$request->all()`, pintu mass assignment kembali terbuka lebar. Ketika dikirimkan request berisi field liar (misalnya melalui `curl`), input yang tidak ada di daftar aturan validasi tetap ikut masuk ke dalam `$data`. Hal ini sangat berbahaya karena penyerang bisa menyisipkan data sensitif yang tidak seharusnya diproses.

---

## 3. Hapus validasi `exists:users,id` pada `lecturer_id`, kirim `lecturer_id=99999`

Jawaban:
Awalnya, validasi `exists:users,id` menjaga agar `lecturer_id` yang dimasukkan harus merupakan ID dosen yang benar-benar terdaftar di tabel `users`.

Setelah aturan `exists` ini kita hapus dan kita coba masukkan `lecturer_id = 99999` (ID fiktif), request lolos dari validasi tingkat aplikasi. Namun begitu menyentuh database, sistem langsung mengalami crash dan melempar error `500 Internal Server Error` akibat pelanggaran foreign key constraint (`SQLSTATE Integrity constraint violation`). Tanpa validasi ini, aplikasi gagal memberikan pesan error yang ramah dan berisiko menciptakan data yatim (orphan data).   

---

## 4. Hapus validasi `in:...` pada `status`, kirim `status=superadmin`

Jawaban:
Sebelumnya, validasi `in:draft,active,archived` membatasi agar nilai status course hanya bisa diisi oleh pilihan yang sudah ditentukan.

Begitu aturan `in` dihapus dan kita mencoba mengirim status liar seperti `superadmin`, validasi aplikasi melewatinya begitu saja. Namun, saat data hendak disimpan, database menolak nilai tersebut karena tidak sesuai dengan batasan tipe data ENUM/VARCHAR, yang akhirnya memicu error `500 Internal Server Error`. Pengujian ini menunjukkan bahwa validasi `in` berfungsi penting sebagai benteng pertama untuk menjaga integritas data sebelum sampai ke database.   

---

## 5. Hapus `->withQueryString()`, lakukan pencarian lalu klik halaman 2

Jawaban:
Biasanya, saat kita melakukan pencarian atau menyaring data, parameter filter di URL akan terus dipertahankan ketika kita berpindah ke halaman berikutnya.

Tetapi begitu `->withQueryString()` dihapus dari pemanggilan `paginate()`, bug klasik langsung terjadi. Saat kita melakukan pencarian lalu mengklik navigasi ke Halaman 2, parameter pencarian di URL mendadak hilang dan ter-reset. Akibatnya, halaman kedua malah menampilkan seluruh data mentah tanpa filter pencarian sebelumnya.

---

## 6. Ganti `return redirect()` menjadi `return view()` pada `store`, lalu tekan F5 setelah simpan

Jawaban:
Dalam pola Post-Redirect-Get (PRG), setelah proses simpan data (`POST`) berhasil, controller idealnya mengembalikan `redirect()` agar browser beralih ke method `GET`.

Saat kita ubah hasilnya menjadi `return view()`, browser tetap berada dalam status respons `POST`. Dampaknya, ketika pengguna tidak sengaja menekan F5 (refresh) di halaman tersebut, browser akan menampilkan peringatan Confirm Form Resubmission. Jika pengguna memilih melanjutkan, form akan dikirim ulang dan menciptakan data ganda di database.

---

## 7. Hapus `old(...)` dari semua input, lalu kirim form dengan satu kesalahan

Jawaban:
Normalnya, helper `old('field_name')` berfungsi untuk menyimpan data yang sudah diketik pengguna di dalam session sementara jika validasi form mengalami kegagalan.   

Setelah fungsi `old()` dihapus dari atribut `value` di input form, timbul masalah besar pada pengalaman pengguna. Saat pengguna salah mengisi satu kolom dan submit gagal, seluruh kolom inputan lainnya mendadak kembali kosong melompong. Pengguna pun dipaksa mengulang dan mengetik kembali seluruh isian form dari awal. 