## Nama: Moh Irsyad Fiqi Ferdiansyah Difa Nanda
## NIM: 10241042

---

### Tujuan

Mengamati dampak ketika proteksi request, validasi, pola redirect, pengisian ulang form, dan pengelompokan query diubah atau dihilangkan pada fitur mata kuliah.

> **Catatan pengujian:** Analisis berikut mengacu pada implementasi aplikasi saat ini dan skema database aktif (MySQL). Perubahan BREAK bersifat sementara; jangan meninggalkan aplikasi dalam kondisi rusak. Jalankan pengujian pada lingkungan lokal dan pulihkan setiap perubahan setelah diamati. Nilai cookie/session pada contoh perintah harus berasal dari sesi lokal sendiri dan jangan dibagikan.
>
> Hasil pada laporan ini menjelaskan hasil yang diperkirakan dari kode dan skema yang diperiksa; tidak ada perubahan BREAK yang ditinggalkan pada source code.

## Ringkasan

| # | Yang dirusak | Hasil yang perlu diamati |
|---|---|---|
| 1 | Menghapus `@csrf` dari form | Request ditolak dengan HTTP 419 di luar environment testing |
| 2 | Mengganti `$request->validated()` dengan `$request->all()` | Pada implementasi ini Form Request tetap memvalidasi request; perubahan satu baris saja belum melewati validasi |
| 3 | Menghapus aturan `exists:users,id` | Foreign key MySQL mencegah ID dosen yang tidak ada tersimpan |
| 4 | Menghapus aturan `in` pada `status` | Enum MySQL menolak status di luar daftar yang diizinkan |
| 5 | Menghapus `withQueryString()` | Parameter filter/pencarian tidak ikut ke tautan halaman berikutnya |
| 6 | Mengganti redirect menjadi render view langsung | Refresh meminta pengiriman ulang POST; validasi unique saat ini mencegah duplikasi kode |
| 7 | Menghapus `old(...)` dari input | Nilai form hilang ketika validasi gagal |
| 8 | Menghapus pengelompokan `where(function...)` | Prioritas `AND`/`OR` dapat membuat filter status bocor |

## 1. Token CSRF Dihapus

**Prediksi**

Jika token CSRF tidak dikirim, middleware Laravel akan menolak request yang mengubah data. Form tidak akan berhasil menyimpan mata kuliah.

**Yang dirusak**

`@csrf` berada di partial yang digunakan form tambah mata kuliah:

```text
resources/views/admin/courses/_form.blade.php
```

Hapus sementara baris berikut dari partial tersebut:

```blade
@csrf
```

**Pengujian dan hasil**

Kirim form tambah mata kuliah melalui browser. Pada lingkungan web normal, request POST tidak menyertakan token yang diharapkan dan Laravel menampilkan **419 Page Expired**. Data tidak tersimpan. Middleware CSRF tidak dijalankan dengan cara yang sama pada environment testing, jadi pengujian ini perlu dilakukan melalui aplikasi lokal normal, bukan hanya mengandalkan feature test.

**Kesimpulan**

Token CSRF memastikan request perubahan data berasal dari form aplikasi yang memiliki token session yang sesuai. Tanpa token yang valid, request ditolak.

## 2. `validated()` Diganti dengan `all()`

**Prediksi**

`validated()` hanya mengembalikan field yang sudah lolos aturan validasi. `all()` mengambil seluruh input request sehingga berisiko memasukkan data liar ke proses penyimpanan.

**Yang dirusak**

Pada `CourseController::store()`, ubah sementara:

```php
$data = $request->validated();
```

menjadi:

```php
$data = $request->all();
```

**Pengujian dan hasil pada implementasi saat ini**

Controller menerima `StoreCourseRequest`, bukan `Request` biasa. Laravel menjalankan validasi Form Request sebelum method `store()` dipanggil. Karena itu, mengganti `validated()` menjadi `all()` saja **tidak otomatis melewati validasi**. Contohnya, `sks=99` tetap ditolak oleh aturan `between:1,6`.

Selain itu, model `Course` memakai `$fillable` untuk membatasi atribut yang dapat diisi secara mass assignment. Field liar yang tidak tercantum di sana tidak otomatis menjadi kolom yang tersimpan.

Dengan demikian, hasil aktual dari perubahan satu baris ini pada struktur sekarang adalah validasi tetap berlaku; percobaan tersebut belum membuktikan validasi berhasil dilewati. Untuk mendemonstrasikan bypass validasi secara nyata, Form Request harus lebih dahulu dihilangkan/dilewati dan penyimpanan menerima input mentah. Tindakan tersebut tidak disarankan dilakukan pada aplikasi atau database berisi data penting.

**Kesimpulan**

`validated()` tetap merupakan cara yang lebih aman dan eksplisit untuk menentukan data yang disimpan. Namun, efeknya perlu dipahami bersama siklus validasi Form Request dan `$fillable`; `all()` sendiri tidak selalu berarti semua field dapat ditulis.

## 3. Validasi `exists:users,id` Dihapus

**Prediksi**

ID dosen fiktif seperti `99999` akan lolos dari validasi Laravel. Perlu diperiksa apakah data yatim benar-benar tersimpan atau database menolaknya.

**Yang dirusak**

Pada `app/Http/Requests/StoreCourseRequest.php`, ubah sementara aturan:

```php
'lecturer_id' => ['required', 'integer', 'exists:users,id'],
```

menjadi:

```php
'lecturer_id' => ['required', 'integer'],
```

**Pengujian dan hasil berdasarkan skema aplikasi**

Kirim request POST ke route yang benar, yaitu `/admin/courses`, dengan `lecturer_id=99999`. Form request tidak lagi menemukan kesalahan validasi. Akan tetapi, kolom `courses.lecturer_id` memiliki foreign key ke `users.id`. MySQL menolak insert tersebut dengan kesalahan integritas referensial (umumnya SQLSTATE `23000`, MySQL error `1452`); baris mata kuliah tidak tersimpan sebagai data yatim.

Karena controller tidak menangani exception database ini sebagai kesalahan validasi, respons pada konfigurasi web normal dapat menjadi **500 Internal Server Error**, bukan pesan validasi yang ramah.

Contoh bentuk request lokal (gunakan cookie dan token CSRF milik sesi sendiri):

```bash
curl -X POST http://127.0.0.1:8000/admin/courses \
  -H "X-CSRF-TOKEN: <token-csrf-sesi-lokal>" \
  -b "laravel_session=<cookie-sesi-lokal>" \
  -d "code=BRK-LECTURER-01" \
  -d "name=Uji Dosen Tidak Ada" \
  -d "sks=3" \
  -d "lecturer_id=99999" \
  -d "status=draft"
```

**Kesimpulan**

Aturan `exists` memberi umpan balik yang tepat sebelum insert dilakukan. Foreign key menjadi perlindungan lapis kedua untuk integritas data, tetapi tidak menggantikan pesan validasi yang jelas.

## 4. Validasi Enum `status` Dihapus

**Prediksi**

Nilai `status=superadmin` akan lolos dari validasi aplikasi setelah aturan `in` dihapus. Nilai tersebut kemudian diuji terhadap tipe kolom database.

**Yang dirusak**

Pada `StoreCourseRequest`, ubah sementara:

```php
'status' => ['required', 'in:draft,active,archived'],
```

menjadi:

```php
'status' => ['required', 'string'],
```

**Pengujian dan hasil berdasarkan skema aplikasi**

Kirim POST ke `/admin/courses` dengan semua field wajib yang valid dan `status=superadmin`. Kolom `courses.status` didefinisikan sebagai enum MySQL yang hanya menerima `draft`, `active`, atau `archived`. Dengan konfigurasi MySQL aktif, nilai di luar enum ditolak saat insert; respons dapat berupa **500** jika exception database tidak ditangani.

**Kesimpulan**

Aturan `in` memberi pesan validasi yang dapat dipahami sebelum database dihubungi. Enum database menjaga integritas pada lapisan penyimpanan, tetapi tanpa validasi request pengguna dapat menerima kegagalan yang tidak ramah.

## 5. `withQueryString()` Dihapus

**Prediksi**

Pencarian dan filter akan hilang ketika pengguna berpindah ke halaman berikutnya.

**Yang dirusak**

Pada pagination di `CourseController::index()`, hapus sementara:

```php
->withQueryString()
```

sehingga bagian akhirnya menjadi:

```php
$courses = $query
    ->orderBy('code')
    ->orderBy('id')
    ->paginate(15);
```

**Pengujian dan hasil**

1. Pastikan terdapat lebih dari 15 mata kuliah yang cocok dengan pencarian atau filter status.
2. Buka daftar mata kuliah dengan query, misalnya `/admin/courses?search=IF&status=active`.
3. Klik halaman 2.

Tanpa `withQueryString()`, tautan pagination tidak meneruskan parameter request. URL halaman berikutnya hanya memuat nomor halaman, misalnya `?page=2`. Daftar lalu tidak lagi dibatasi oleh pencarian dan filter semula.

**Kesimpulan**

`withQueryString()` mempertahankan konteks query saat berpindah halaman, sehingga pagination tetap konsisten dengan pencarian dan filter aktif.

## 6. Redirect Setelah Simpan Diganti dengan Render View

**Prediksi**

Jika controller merender halaman langsung sebagai respons POST, menekan F5 dapat meminta browser mengirim ulang formulir.

**Yang dirusak**

Pada `CourseController::store()`, alur saat ini menggunakan redirect:

```php
return redirect()
    ->route($this->routePrefix().'.show', $course)
    ->with('success', 'Mata kuliah berhasil ditambahkan.');
```

Ganti sementara dengan render view yang memang sesuai dan menyediakan seluruh data yang dibutuhkan view:

```php
return view('admin.courses.show', [
    'course' => $course,
    'lecturers' => User::where('role', 'dosen')->orderBy('name')->get(),
    // Sertakan juga data lain yang diwajibkan view detail.
]);
```

**Pengujian dan hasil**

Setelah form tersimpan, respons berasal langsung dari request POST, bukan dari request GET baru. Menekan F5 dapat menampilkan konfirmasi **Confirm Form Resubmission**. Jika pengguna melanjutkan, browser mengirim POST lagi.

Pada implementasi sekarang, kode mata kuliah memiliki validasi `unique:courses,code` dan indeks unik di database. Oleh sebab itu, pengiriman ulang dengan kode yang sama kemungkinan ditolak sebagai kode duplikat—tidak otomatis membuat baris kedua. Namun, request berulang tetap tidak diinginkan dan dapat mengulang efek samping lain.

Render langsung juga perlu menyiapkan semua data yang dibutuhkan view detail. Jika tidak, halaman dapat gagal dirender akibat variabel yang tidak tersedia.

**Kesimpulan**

Pola **Post-Redirect-Get (PRG)** mengalihkan pengguna ke request GET setelah POST berhasil. Redirect mencegah refresh mengulang pengiriman POST dan menghasilkan alur yang lebih konsisten.

## 7. `old(...)` Dihapus dari Input

**Prediksi**

Setelah validasi gagal, input yang sudah benar tidak akan terisi kembali.

**Yang dirusak**

Partial form tambah/edit mata kuliah berada di:

```text
resources/views/admin/courses/_form.blade.php
```

Hapus sementara `old(...)` dari atribut input dan textarea, contohnya:

```blade
value="{{ old('code', $c?->code) }}"
```

menjadi nilai kosong atau hanya nilai model untuk mode edit. Lakukan hal yang sama pada `name`, `description`, `sks`, `lecturer_id`, dan `status`.

**Pengujian dan hasil**

Isi beberapa field, lalu kirim form dengan satu field tidak valid, misalnya SKS di luar rentang. Laravel mengarahkan kembali ke form dengan pesan kesalahan. Tanpa `old(...)`, nilai yang sebelumnya diketik tidak dipulihkan dan pengguna harus mengisi ulang field tersebut.

**Kesimpulan**

`old()` menampilkan kembali input yang disimpan sementara oleh Laravel setelah redirect validasi. Fitur ini mengurangi pengisian berulang dan membantu pengguna memperbaiki hanya field yang salah.

## 8. Pengelompokan Kondisi Pencarian Dihapus

**Prediksi**

Jika kondisi pencarian tidak dikelompokkan, prioritas SQL `AND` terhadap `OR` dapat membuat sebagian hasil melewati filter status.

**Yang dirusak**

Pada `CourseController::index()`, pencarian saat ini berada di dalam closure yang juga memuat kondisi kode/nama yang mengandung teks dan kondisi kode/nama yang berawalan teks. Hapus sementara pembungkus closure sehingga seluruh kondisi masuk ke query utama:

```php
// Rusak: OR pencarian bercampur dengan filter lain
$query->where('code', 'like', "%{$search}%")
    ->orWhere('name', 'like', "%{$search}%");

$query->where('code', 'like', "{$search}%")
    ->orWhere('name', 'like', "{$search}%");

if ($request->filled('status')) {
    $query->where('status', $request->input('status'));
}
```

**Pengujian dan hasil**

Buat/cari mata kuliah yang kodenya cocok dengan kata kunci tetapi statusnya `active`, lalu buka daftar dengan filter status `archived` dan kata kunci tersebut. Tanpa closure pengelompokan, query dapat dievaluasi seperti:

```sql
WHERE code LIKE '%IF%'
   OR name LIKE '%IF%' AND code LIKE 'IF%'
   OR name LIKE 'IF%' AND status = 'archived'
```

Karena `AND` dievaluasi sebelum `OR`, baris yang kodenya cocok dengan pencarian pertama dapat tampil walaupun statusnya bukan `archived`. Kondisi `OR` berikutnya juga tidak lagi berada dalam satu kelompok pencarian.

Dengan closure yang digunakan saat ini, seluruh ekspresi pencarian dibungkus sehingga filter status tetap membatasi hasil:

```sql
WHERE (code LIKE '%IF%'
    OR name LIKE '%IF%' AND code LIKE 'IF%'
    OR name LIKE 'IF%')
  AND status = 'archived'
```

**Kesimpulan**

Closure `where(function ($query) { ... })` membuat tanda kurung pada kondisi pencarian. Pengelompokan tersebut memastikan filter lain berlaku pada seluruh cabang pencarian, bukan hanya sebagian.

## Kesimpulan Umum

Delapan percobaan ini menunjukkan bahwa proteksi aplikasi bekerja pada beberapa lapisan: CSRF dan validasi request di Laravel, mass-assignment guard pada model, foreign key dan enum pada database, serta pengelompokan query untuk menjaga logika filter. Beberapa percobaan tidak menimbulkan dampak persis seperti prediksi sederhana karena masih ada lapisan perlindungan lain; hal tersebut terlihat jelas pada penggunaan Form Request di percobaan 2 dan foreign key di percobaan 3.

Setelah setiap BREAK diamati, pulihkan kode ke implementasi semula. Jangan menjalankan percobaan pada database produksi atau menggunakan cookie/session milik orang lain.
