# Catatan READ Minggu 13: Laudya Aprilia Khoirum

**Nama:** Laudya Aprilia Khoirum  
**NIM:** 10241038  
**Program Studi:** Sistem Informasi

Saya membuat catatan ini berdasarkan riwayat Git repository KampusLMS /
EduSpace dan hasil review PR pada 17 November 2026.

## 1. Jumlah commit dan perbandingan anggota

Jumlah commit kerja pribadi saya adalah **37**. Jika dibandingkan dengan
anggota lain, jumlah ini sama dengan Irsyad, tetapi lebih sedikit daripada
Arif dan Marchelino.

## 2. Minggu tanpa commit

Commit kerja pribadi saya berlangsung dari 2 September sampai 17 November
2026. Dari 12 minggu kalender dalam rentang tersebut, ada **1 minggu tanpa
commit**, yaitu:

- 12–18 Oktober 2026.

## 3. Persentase Conventional Commits

Dari 37 commit kerja pribadi, 26 mengikuti format Conventional Commits.

| Kategori | Jumlah | Persentase |
| --- | ---: | ---: |
| Mengikuti Conventional Commits | 26 | **70,3%** |
| Belum mengikuti | 11 | 29,7% |

Contoh pesan yang sudah baik:

- `feat(dosen): lengkapi dashboard dan pengelolaan perkuliahan`
- `feat(about): add developer profiles and role details`
- `docs(test): updating pre-review verification report`

## 4. Contoh pesan commit yang perlu diperbaiki

Salah satu pesan commit saya yang kurang baik adalah:

```text
Update tombol hapus mata kuliah agar lebih rapi
```

Pesan tersebut belum menyebut tipe dan scope perubahan. Penulisan yang lebih
baik:

```text
fix(courses): improve delete course button
```

Contoh lain:

| Hash | Pesan lama | Saran |
| --- | --- | --- |
| `c718034` | `Pengumpulan Break Week 3` | `docs(w03): add weekly submission notes` |
| `6d58ae2` | `Perubahan tampilan create dan edit pada course` | `style(courses): update create and edit views` |
| `b68f945` | `.` | Tulis pesan sesuai isi perubahan |
| `c1b0a40` | `laudya` | Tulis tipe, scope, dan tujuan commit |

Saya tidak perlu mengubah pesan commit lama karena sudah masuk ke riwayat
branch. Perbaikannya saya gunakan untuk commit berikutnya.

## 5. Commit terbesar

Berdasarkan `git show --stat`, commit kerja terbesar saya adalah:

| Hash | Pesan | File | Baris berubah |
| --- | --- | ---: | ---: |
| `e4a1e11` | `refactor: mengubah struktur user ke folder admin dan menghapus dashboard general juga course general` | 32 | **4.367** |

Menurut saya, commit ini sebaiknya dipecah jika dibuat ulang karena mencampur:

1. Pemindahan file admin.
2. Penghapusan dashboard dan route umum.
3. Perubahan CSS dan view.
4. Penambahan feature test.

Contoh pembagiannya:

```text
refactor(admin): move user assets and views
refactor(routes): remove general dashboard and course routes
test(admin): cover admin user and dashboard routing
```

Commit terbesar kedua adalah `bb3cbcb` dengan 3.567 baris. Commit
`92df8e8` berjumlah 2.341 baris karena banyak berisi file hasil instalasi
Tailwind.

## 6. Ringkasan

- Saya memiliki 37 commit kerja pribadi.
- 70,3% pesan commit saya mengikuti Conventional Commits.
- Saya tidak memiliki commit pada 12–18 Oktober 2026.
- Commit terbesar saya sebaiknya dipecah jika dibuat ulang.
- Saya akan menggunakan hasil analisis ini untuk memperbaiki cara membuat
  pesan commit pada pekerjaan berikutnya.
