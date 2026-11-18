# Catatan READ Minggu 13: Marchelino Senduk Kaunang

Analisis riwayat Git pribadi pada repo **KampusLMS / EduSpace (Kelompok B4)**.

**Sumber data:** `git log --all --no-merges --author="Marchelino Senduk Kaunang"` dan `git log --shortstat`, dijalankan pada 18 November 2026 dari branch `Marchelino-1040`.

> **Catatan identitas Git:** Commit kerja saya tersimpan dengan nama Git lokal **`Marchelino Senduk Kaunang`** (`marchelk119@gmail.com`). Pada `git shortlog -sn --all` tercatat total **87 commit** atas nama saya, yang terdiri dari **50 commit non-merge** (kerja kode/dokumen asli) dan **37 merge commit** (commit otomatis saat *Merge Pull Request* di GitHub atau sinkronisasi branch). Karena *merge commit* bukan merupakan hasil kerja kode baru, seluruh analisis di bawah dihitung berdasarkan **50 commit non-merge** tersebut.

---

## 1. Jumlah Commit

| Keterangan | Jumlah / Keterangan |
| :--- | :--- |
| Commit non-merge (kerja asli) | **50** |
| Commit merge (GitHub PR / sinkronisasi) | **37** |
| Hari berbeda yang memiliki commit | **15 hari** |
| Rentang waktu | **2 September 2026** sampai **17 November 2026** |
| Peringkat di kelompok (non-merge) | **Peringkat 2** dari 4 anggota aktif (**23,1%** dari total 216 commit non-merge repo) |

**Perbandingan dengan Anggota Lain (Non-Merge):**
* `Zweilior` (`izenzhk@gmail.com`): 90 commit (41,7%)
* **`Marchelino Senduk Kaunang` (`marchelk119@gmail.com`): 50 commit (23,1%)**
* `Laudya Aprilia` (`luludea95@gmail.com`): 38 commit (17,6%)
* `ferdiansyaafqgh` (`ferdiansyaafq36@gmail.com`): 37 commit (17,1%)
* `anothersyachoo` (`ferdiansyaafq36@gmail.com`): 1 commit (0,5%)

---

## 2. Minggu Tanpa Commit

Dihitung per minggu kalender (Senin sampai Minggu) selama **12 minggu** rentang pengerjaan:

| Minggu mulai (Senin) | Rentang Tanggal | Jumlah Commit |
| :--- | :--- | :---: |
| 31 Agu 2026 | 31 Agu – 6 Sep 2026 | 13 |
| 7 Sep 2026 | 7 Sep – 13 Sep 2026 | 1 |
| 14 Sep 2026 | 14 Sep – 20 Sep 2026 | 1 |
| 21 Sep 2026 | 21 Sep – 27 Sep 2026 | 2 |
| 28 Sep 2026 | 28 Sep – 4 Okt 2026 | 1 |
| 5 Okt 2026 | 5 Okt – 11 Okt 2026 | 4 |
| 12 Okt 2026 | 12 Okt – 18 Okt 2026 | **0** |
| 19 Okt 2026 | 19 Okt – 25 Okt 2026 | 5 |
| 26 Okt 2026 | 26 Okt – 1 Nov 2026 | 11 |
| 2 Nov 2026 | 2 Nov – 8 Nov 2026 | 5 |
| 9 Nov 2026 | 9 Nov – 15 Nov 2026 | 5 |
| 16 Nov 2026 | 16 Nov – 22 Nov 2026 | 2 |

* **Minggu tanpa commit:** **1 minggu**, yaitu pada rentang **12 Oktober sampai 18 Oktober 2026**.
* **Analisis pola distribusi:** Commit saya tersebar cukup konsisten di 11 dari 12 minggu pengerjaan. Namun, terdapat lonjakan di awal proyek (13 commit pada minggu 31 Agustus, mayoritas berupa revisi beruntun laporan Minggu 1) dan pada minggu 26 Oktober (11 commit dalam satu hari saat implementasi sistem event/queue asinkron dan optimasi rekap nilai). Sementara pada minggu kosong (12–18 Oktober), saya fokus melakukan perancangan dan persiapan logika unggah file *submission* sebelum dieksekusi sekaligus pada 20 Oktober.

---

## 3. Persentase Conventional Commits

Tipe standar yang dihitung: `feat`, `fix`, `docs`, `style`, `refactor`, `perf`, `test`, `build`, `ci`, `chore`, `revert` (dengan huruf kecil dan diikuti tanda titik dua `:`).

| Keterangan | Jumlah | Persentase |
| :--- | :---: | :---: |
| Sesuai Conventional Commits | **33** | **66,0%** |
| Tidak sesuai | **17** | **34,0%** |
| **Total Commit Non-Merge** | **50** | **100%** |

**Rincian tipe pada 33 commit yang sesuai:**
* `refactor`: 10 commit (`0254645`, `9b80c8c`, `e4eaea9`, `865aaf9`, `659571f`, `e9b8868`, `39ee1df`, `bf6edf3`, `d055839`, `f4dcf9c`)
* `feat`: 10 commit (`fbaccd5`, `00b3f9e`, `b6a499f`, `fb8e013`, `cf2956b`, `5f2d497`, `9dc259c`, `17cc70f`, `381a869`, `b40a14b`, `0334a0f` — total 11 commit)
* `perf`: 5 commit (`51aa901`, `2143cec`, `ad1d13c`, `f36bf41`, `44afe01`)
* `docs`: 4 commit (`b355766`, `5711665`, `49c7b9a`, `cb2d275`)
* `chore`: 2 commit (`b139efa`, `3f49598`)
* `test`: 1 commit (`899afad`)

*(Rincian tepat: `feat` 11, `refactor` 10, `perf` 5, `docs` 4, `chore` 2, `test` 1 = 33 commit).*

**Catatan:**
* **Penggunaan Scope:** Sebanyak **29 dari 33 commit** yang sesuai (87,9%) sudah menggunakan *scope* modul yang sangat jelas, seperti `feat(submission):`, `perf(database):`, `refactor(grades):`, dan `chore(config):`. Hanya 4 commit (`fbaccd5`, `5711665`, `b355766`, `899afad`) yang tidak memakai *scope*.
* **Analisis Commit Tidak Sesuai (17 commit):** Sebanyak 15 dari 17 commit yang tidak sesuai terjadi pada 3 minggu pertama (2–15 September) saat saya masih mengunggah file tugas mingguan langsung melalui antarmuka web GitHub (`Update Week-01`, `Add files via upload`). Selain itu, ada 1 commit *stash/index* tidak sengaja (`c7417f2`) dan 1 commit (`b3b4821`) yang memakai huruf kapital dan tanpa spasi setelah titik dua (`Fix:CourseController...`). Sejak 29 September hingga 17 November, tingkat kepatuhan saya mencapai **96,9%** (31 dari 32 commit).

---

## 4. Commit yang Perlu Ditulis Ulang

Berikut adalah beberapa commit milik saya yang pesannya bermasalah beserta saran perbaikannya:

| Hash | Pesan sekarang | Masalah | Saran pesan |
| :--- | :--- | :--- | :--- |
| `74af671`, `1f7294e`, `edb074e`, `8fb93ef`, `44a82b8`, `6788084` | `Update Week-01` (6 commit beruntun) | Tanpa tipe, berulang dengan pesan identik hanya untuk revisi kecil, membuat riwayat kotor | Di-*squash* menjadi satu commit: `docs(week-01): add and refine initial weekly progress report` |
| `2c975f7`, `5567800`, `56b965b` | `Update Minggu-01` (3 commit beruntun) | Tanpa tipe, bahasa tidak konsisten dengan `Week-01`, tidak menjelaskan isi perubahan | Di-*squash* menjadi satu commit: `docs(week-01): complete week 1 reflection notes` |
| `6923809`, `aa6b5f9` | `Add files via upload` | Pesan bawaan unggahan web GitHub; tanpa tipe dan tidak menyebut file apa yang diunggah | `docs(week-01): upload initial weekly report markdown file` |
| `72d304b` | `Marchelino Senduk Kaunang` | Hanya berisi nama penulis, sama sekali tidak menjelaskan tujuan atau isi perubahan | `docs(setup): initialize contributor file for Marchelino` |
| `b3b4821` | `Fix:CourseController (isAdminRoute) dan hubungkan form admin mata kuliah ke database` | Tipe diawali huruf kapital (`Fix:`), tanpa spasi setelah titik dua, dan menggabungkan perbaikan bug dengan fitur baru | Pecah menjadi dua commit: `fix(course): resolve isAdminRoute check in CourseController` dan `feat(admin): connect course creation form to database` |
| `381a869`, `b40a14b` | `feat(notification): buat event dan listener untuk pengumpulan tugas` (duplikat) | Dua commit berurutan memiliki pesan yang identik persis | Gabungkan (*squash*) atau bedakan pesannya: `feat(notification): create PengumpulanBaru event class` dan `feat(notification): create listener for submission notification` |
| `51aa901` | `perf(database):mengaktifkan fitur prevent lazy loading` | Format sudah hampir benar, tetapi tidak ada spasi setelah tanda titik dua `:` | `perf(database): mengaktifkan fitur prevent lazy loading` |

> *Catatan: Commit di atas sudah di-push ke repositori bersama sehingga tidak dilakukan `git rebase` / `force-push` agar tidak merusak riwayat anggota tim lain. Evaluasi ini dijadikan acuan untuk menjaga kebersihan commit berikutnya.*

---

## 5. Commit Terbesar

Ukuran dihitung dari total jumlah baris yang ditambah dan dihapus per commit (`git log --shortstat`). Lima commit terbesar dari **50 commit non-merge**:

| Peringkat | Hash | Pesan | File | Baris Berubah (Ins + Del) |
| :---: | :--- | :--- | :---: | :---: |
| 1 | `cb2d275` | `docs(auth): clarify demo account configuration` | 1 | **278** (143+, 135-) |
| 2 | `5711665` | `docs: Break Week 4` | 9 | **244** (244+, 0-) |
| 3 | `49c7b9a` | `docs(readme): improve project setup instructions and demo configuration` | 1 | **222** (155+, 67-) |
| 4 | `b3b4821` | `Fix:CourseController (isAdminRoute) dan hubungkan form admin mata kuliah ke database` | 6 | **217** (157+, 60-) |
| 5 | `c7417f2` | `index on main: 1df8724 Merge branch 'main' of https://github.com/Zweilior/kampuslms-kelompok-B4` | 6 | **152** (109+, 43-) |

*(Catatan: Untuk commit kode fitur murni di luar dokumentasi dan stash, commit terbesar berikutnya adalah `865aaf9` (`refactor(dosen): optimasi rekap nilai pada CourseController`, 2 file, 126 baris) dan `ee3a8cb` (`Update Week-03`, 1 file, 117 baris)).*

### Analisis Commit Terbesar (`cb2d275`) dan Commit Multi-File (`5711665` & `b3b4821`)

1. **Commit Terbesar Berdasarkan Jumlah Baris (`cb2d275`):**
   * **Statistik:** 1 file diubah (`README.md`), 143 penambahan dan 135 penghapusan (total 278 baris berubah).
   * **Apakah seharusnya dipecah?** **Tidak perlu dipecah.** Meskipun jumlah baris berubahnya paling besar (278 baris), commit ini hanya menyentuh 1 file dokumentasi (`README.md`) untuk merapikan dan memperjelas bagian konfigurasi akun demo secara menyeluruh. Satu commit untuk satu tujuan dokumentasi yang utuh sudah tepat.

2. **Commit yang Seharusnya Dipecah (`b3b4821` & `5711665`):**
   * **`b3b4821` (6 file, 217 baris berubah):** **Ya, seharusnya dipecah.** Commit ini mencampur dua perubahan berbeda dalam satu waktu: perbaikan *bug* pengecekan `isAdminRoute` pada `CourseController` dan penambahan fitur koneksi formulir admin mata kuliah ke database. Keduanya memiliki tujuan logis yang berbeda sehingga seharusnya dipecah menjadi satu commit `fix` dan satu commit `feat` agar mudah ditinjau maupun di-*revert* secara terpisah.
   * **`5711665` (9 file, 244 baris berubah):** **Sebaiknya dipecah.** Commit ini menambahkan laporan *Break Week 4* sekaligus berbagai aset gambar pendukung dalam satu commit besar. Pemisahan antara penambahan aset gambar (`docs(assets): ...`) dan penulisan dokumen laporan (`docs(week-04): ...`) akan membuat riwayat lebih rapi.

---

## 6. Ringkasan dan Tindak Lanjut

* **Pola yang baik:**
  * Tingkat kepatuhan terhadap *Conventional Commits* mencapai **66,0%** secara keseluruhan dan meningkat drastis menjadi **96,9%** sejak pertengahan hingga akhir proyek.
  * Penggunaan *scope* modul (`perf(database):`, `feat(submission):`, `refactor(grades):`) sangat konsisten dan deskriptif.
  * Mayoritas commit kode fitur dan optimasi performa dibuat secara atomik (rata-rata hanya mengubah 1–2 file dengan 10–90 baris per commit), sehingga mudah di-*review*.
* **Pola yang perlu diperbaiki:**
  * Melakukan banyak commit kecil beruntun dengan pesan generik (`Update Week-01`, `Add files via upload`) lewat antarmuka web GitHub di awal proyek.
  * Menggabungkan perbaikan *bug* dan fitur baru ke dalam satu commit (`b3b4821`), serta masuknya commit otomatis *git stash* (`c7417f2`) ke dalam riwayat branch.
* **Rencana perbaikan ke depan:**
  1. Menghindari pengeditan langsung melalui antarmuka web GitHub dan selalu melakukan commit dari terminal lokal setelah memeriksa perubahan dengan `git status` dan `git diff`.
  2. Menggunakan `git commit --amend` atau *interactive rebase* (`git rebase -i`) di branch pribadi untuk merapikan commit percobaan sebelum membuat *Pull Request*.
  3. Memastikan setiap commit hanya mencakup satu jenis perubahan (`feat` dipisah dari `fix`) dengan format `tipe(scope): deskripsi` yang konsisten.