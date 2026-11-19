# Catatan READ Minggu 13: Moh Irsyad Fiqi Ferdiansyah Difa Nanda

**Nama:** Moh Irsyad Fiqi Ferdiansyah Difa Nanda  
**NIM:** 10241042  
**Program Studi:** Sistem Informasi

Catatan ini saya susun setelah memeriksa riwayat Git pada repository KampusLMS / EduSpace, khususnya pada branch kerja saya (`ferdiansyaa-b2` dan `ferdiansyaa-w01`), per tanggal 17 November 2026.

## 1. Jumlah commit dan perbandingan anggota

Total commit kerja pribadi saya (di luar commit merge) ada **38 commit** (atau 37 commit sebelum saya menambahkan berkas konfigurasi CI kemarin). 

Jika dibandingkan dengan teman sekelompok:
- Jumlah commit saya hampir sama dengan Laudya yang memiliki 37 commit.
- Namun masih lebih sedikit daripada Arif dan Marchelino, karena mereka berdua memegang bagian backend, API, dan integrasi modul yang lebih sering melakukan commit.

## 2. Minggu tanpa commit

Riwayat commit saya tercatat mulai tanggal 2 September sampai 17 November 2026, dari rentang waktu tersebut, ada **1 minggu di mana saya tidak melakukan commit**, yaitu pada *12–18 Oktober 2026* karena pada rentang tanggal tersebut sedang berlangsung masa Ujian Tengah Semester (UTS), sehingga waktu pengerjaan dilakukan pada minggu sebelumnya.

## 3. Persentase Conventional Commits

Dari total 38 commit kerja yang saya buat, ada 21 commit yang sudah mengikuti format Conventional Commits, sedangkan 17 commit lainnya masih menggunakan format bebas.

| Kategori | Jumlah | Persentase |
| --- | ---: | ---: |
| Mengikuti Conventional Commits | 21 | **55,3%** |
| Belum mengikuti format | 17 | 44,7% |

Beberapa contoh pesan commit saya yang formatnya sudah benar:

- `feat(notifications): penambahan notifikasi dan perbaikan penyimpanan nilai akhir`
- `feat(admin): perbaikan fungsionalitas manajemen user dan manajemen matakuliah`
- `fix(ui): perbaiki layout pada tampilan mobile, navbar, pagination capsule dan error pages`

Sebagian besar commit yang belum berformat rapi ada di minggu-minggu awal perkuliahan karena saya masih belum terbiasa dengan aturan prefix commit.

## 4. Contoh pesan commit yang perlu diperbaiki

Salah satu pesan commit saya yang masih kurang jelas adalah:

```text
update css lagi
```
Pesan di atas kurang deskriptif, karena memakai kata ("lagi"), dan tidak menyebutkan halaman mana yang tampilannya sedang diubah. Seharusnya bisa ditulis seperti ini:

```text
style(courses): improve style pada halaman course
```

Berikut beberapa contoh commit lain yang pesannya juga perlu dirapikan:

| Hash | Pesan lama | Saran perbaikan |
| --- | --- | --- |
| `a82d3a6` | `A` | `docs(m02): add weekly assignment report` |
| `655dea6` | `update` | `style(submissions): update submission view layout` |
| `84acab8` | `pecah style css` | `refactor(assets): split user and course stylesheets` |
| `89ebd6e` | `update halaman manajemen user` | `feat(admin): add user management table and filters` |

Commit lama ini tidak perlu saya rebase atau ubah agar riwayat Git tim tidak berantakandan hal ini akan saya jadikan acuan untuk pengerjaan fitur-fitur selanjutnya.

## 5. Commit terbesar

Setelah saya cek menggunakan perintah `git show --stat`, commit terbesar yang pernah saya buat adalah:

| Hash | Pesan | File | Baris berubah |
| --- | --- | ---: | ---: |
| `fc70d25` | `style(css): migrasi css native dosen dan mahasiswa ke tailwind dan hapus kode tidak terpakai` | 22 | **5.356** |

Jika dilihat kembali, commit ini ukurannya terlalu besar dan sebaiknya dipecah menjadi beberapa bagian karena menggabungkan beberapa pekerjaan sekaligus:
1. Migrasi tampilan halaman dosen ke Tailwind.
2. Migrasi tampilan halaman mahasiswa ke Tailwind.
3. Menghapus file CSS native lama yang sudah tidak dipakai.

Jika dipecah, pembagiannya bisa seperti ini:
```text
style(dosen): migrate halaman dosen ke tailwind css
style(mahasiswa): migrate halaman mahasiswa ke tailwind css
refactor(assets): hapus css native yang tidak digunakan
```

Dengan informasi commit diatas dapat memudahkan teman saya untuk mengetahui perubahan yang telah saya lakukan.

Selain itu, commit terbesar kedua saya ada pada `378cb56` (2.916 baris di 25 file) saat mengerjakan modul admin, yang sebenarnya juga sebaiknya dipisah antara fitur user dan mata kuliah. Commit ketiga adalah `9aa0e24` dengan 2.453 baris saat menambahkan tampilan nilai mahasiswa.

## 6. Ringkasan

- Saya membuat 38 commit kerja di branch saya (jumlahnya mirip dengan Laudya, di bawah Arif dan Marchelino).
- Tingkat kepatuhan Conventional Commits saya sekitar 55,3%.
- Saya tidak melakukan commit pada 12–18 Oktober 2026 karena fokus ke UTS.
- Commit terbesar saya (`fc70d25`) seharusnya dipecah per modul agar tidak terlalu menumpuk.
- Ke depannya saya akan lebih membiasakan membuat commit yang atomik (kecil dan fokus) dengan pesan yang konsisten mengikuti standar Conventional Commits.
