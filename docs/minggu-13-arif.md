# Catatan READ Minggu 13: Muhammad Arif Saputra

Analisis riwayat Git pribadi pada repo KampusLMS / EduSpace (Kelompok B4).

**Sumber data:** `git log --no-merges --author="Zweilior"` dan `git show --numstat` per commit, dijalankan pada 17 November 2026 di branch `chore/pre-review-arif`.

**Catatan identitas Git.** Commit kerja saya tersimpan dengan nama Git lokal `Zweilior` (`izenzhk@gmail.com`). Nama `Muhammad Arif Saputra` pada `git shortlog` adalah nama profil GitHub dengan email yang sama; nama itu muncul pada 79 *merge commit* yang dibuat GitHub saat tombol Merge Pull Request ditekan (committer: `GitHub`, dua parent). Merge commit tersebut bukan hasil kerja baru, jadi **tidak dihitung** di bawah. Semua angka berikut berasal dari 88 commit non-merge atas nama `Zweilior`.

---

## 1. Jumlah Commit

| Keterangan | Jumlah |
| --- | --- |
| Commit non-merge (kerja asli) | **88** |
| Hari berbeda yang memiliki commit | 23 |
| Rentang waktu | 28 Agustus 2026 sampai 17 November 2026 |

---

## 2. Minggu Tanpa Commit

Dihitung per minggu kalender (Senin sampai Minggu): 13 minggu.

| Minggu mulai | Commit |
| --- | --- |
| 24 Agu | 1 |
| 31 Agu | 5 |
| 7 Sep | 2 |
| 14 Sep | 3 |
| 21 Sep | 3 |
| 28 Sep | 3 |
| 5 Okt | 15 |
| **12 Okt** | **0** |
| 19 Okt | 12 |
| 26 Okt | 7 |
| 2 Nov | 6 |
| 9 Nov | 28 |
| 16 Nov | 3 |

- **Minggu tanpa commit: 1**, yaitu 12 sampai 18 Oktober 2026.
- **Distribusi tidak merata.** Minggu 9 November memuat 28 dari 88 commit (32%), dan 24 di antaranya dibuat pada satu hari (10 November) saat proses deployment ke Railway. Pola ini muncul karena percobaan konfigurasi dicommit satu per satu langsung ke riwayat.

---

## 3. Persentase Conventional Commits

Tipe yang dihitung: `feat`, `fix`, `docs`, `style`, `refactor`, `perf`, `test`, `build`, `ci`, `chore`, `revert`.

| Keterangan | Jumlah | Persentase |
| --- | --- | --- |
| Sesuai Conventional Commits | 68 | **77,3%** |
| Tidak sesuai | 20 | 22,7% |

Rincian tipe pada 68 commit yang sesuai: `fix` 22, `feat` 20, `docs` 13, `test` 5, `chore` 3, `perf` 2, `ci` 2, `refactor` 1.

Catatan:

- Hanya 29 dari 68 commit yang sesuai memakai scope, misalnya `feat(api):`, `feat(authz):`, `fix(deploy):`. Scope pada 13 commit lainnya berupa nomor minggu (`w09`, `w10`), padahal scope sebaiknya nama modul (misalnya `notifications`, `materials`).
- 3 dari 20 commit yang tidak sesuai memakai awalan `deploy:`. Bentuknya mirip, tetapi `deploy` bukan tipe standar. Jika dihitung sebagai tipe, angkanya 71 dari 88 (80,7%).
- Commit yang tidak sesuai terkonsentrasi di awal proyek (Agustus sampai pertengahan September) dan pada hari deployment (10 November).

---

## 4. Commit yang Perlu Ditulis Ulang

| Hash | Pesan sekarang | Masalah | Saran pesan |
| --- | --- | --- | --- |
| `01410e0`, `78e1e50`, `55b9377`, `e5ab694`, `95cba04`, `0306406`, `610978a`, `e320231` | `fix: remove conflicting Apache MPM configuration` + `part 2` sampai `part 8` | Delapan commit percobaan yang sama; riwayat jadi berisik | Satu commit: `fix(docker): remove conflicting Apache MPM configuration` (sebelum merge, bukan setelah di-push) |
| `85a4750` | `fix production deployment and database seeding` | Tanpa tipe konsisten, tanpa scope, memuat dua hal | Pecah: `fix(deploy): ...` dan `fix(seeder): ...` |
| `a46782a` | `deploy: Dockerfile multi-stage, Supervisor, dan skrip start` | `deploy` bukan tipe standar; memuat tiga hal | Tiga commit: `build(docker): ...`, `feat(deploy): run web and queue worker with supervisor`, `chore(docker): add startup script` |
| `94df399` | `prevent reseeding on every deployment` | Tanpa tipe | `fix(deploy): prevent reseeding on every deployment` |
| `a53173e` | `fix validation for empty assignment update` | Tanpa tanda titik dua | `fix(assignments): reject empty update request` |
| `a632c47` | `set application timezone to WITA` | Tanpa tipe | `feat(config): set application timezone to WITA` |
| `1bab733` | `Fix: Status from Hardcoded to Dynamic` | Kapital dan tanpa scope | `fix(status): compute status dynamically instead of hardcoding` |
| `37279aa` | `READ Kelompok dan Break Kerjaan Arif` | Tanpa tipe | `docs: add group READ and individual BREAK notes` |
| `9e670bd` | `Nambah CRUD Users` | Tanpa tipe | `feat(users): add user CRUD` |
| `8a7aa04` | `fix: dockerfile error fixed` | Tipe benar, tetapi tidak menyebut apa yang diperbaiki (sebenarnya salah ketik `-- force` pada `migrate`) | `fix(docker): correct --force flag in migrate command` |

Catatan: commit di atas sudah dipush; menulis ulang riwayat di branch bersama tidak disarankan. Tujuannya sebagai pola untuk commit berikutnya.

---

## 5. Commit Terbesar

Ukuran dihitung dari jumlah baris yang ditambah dan dihapus per commit (`git show --numstat`). Lima terbesar dari 88 commit:

| Peringkat | Hash | Pesan | File | Baris berubah |
| --- | --- | --- | --- | --- |
| 1 | `de223e9` | `commit pertama: setup laravel` | 56 | **10.692** |
| 2 | `fbafd6d` | `Nambah Database/Migration, Seeder, dan database/factories, halaman 404` | 28 | 1.378 |
| 3 | `b7e3e7c` | `Update Database, dan Menyesuaikan Courses dengan Database` | 9 | 1.305 |
| 4 | `eb5e284` | `feat: performance measurement command` | 3 | 1.203 |
| 5 | `21d81c9` | `feat(policy): implement strict role-based authorization & IDOR prevention` | 9 | 1.036 |

Catatan: hitungan di atas memasukkan file hasil generate seperti `composer.lock` dan `package-lock.json`, sehingga ukuran kode buatan sendiri lebih kecil dari angka mentah ini.

**Commit terbesar secara angka adalah `de223e9`** (56 file, 10.692 baris), yaitu commit pertama berisi setup proyek Laravel. Commit awal seperti ini wajar berukuran besar karena memuat kerangka framework, sehingga tidak perlu dipecah. Sebaiknya kerangka bawaan Laravel dicommit tersendiri, terpisah dari perubahan buatan sendiri.

**Commit terbesar buatan sendiri adalah `fbafd6d`** (28 file, 1.308 penambahan dan 70 penghapusan). Komposisi file menurut `git show --stat`:

| Kelompok | File | Contoh |
| --- | --- | --- |
| Migration | 11 | create_users, courses, course_user, materials, assignments, submissions, grades, notifications, final_grades, sessions (diganti nama) |
| Model Eloquent | 9 | Assignment, Course, FinalGrade, Grade, GradeComponent, Material, Notification, Submission, User |
| Factory | 5 | Assignment, Course, Grade, Submission, User |
| Seeder | 1 | DatabaseSeeder (317 baris) |
| View | 1 | halaman 404 |
| Lain-lain | 1 | package-lock.json (satu baris, tidak berkaitan) |

Pesan commit hanya menyebut migration, seeder, factory, dan halaman 404; model dan perubahan `package-lock.json` tidak disebut, sehingga pesannya tidak mencerminkan isi commit. **Sebaiknya dipecah** menjadi: `feat(database): add schema migrations`, `feat(models): add Eloquent models and relations`, `feat(database): add model factories`, `feat(seeder): add demo data to DatabaseSeeder`, `feat(views): add custom 404 page`, dan perubahan `package-lock.json` dikeluarkan atau dicommit terpisah sebagai `chore: update package-lock.json`. Dengan begitu tiap bagian bisa ditinjau dan dibatalkan sendiri.

Catatan lain:

- `b7e3e7c` (9 file, 1.305 baris) memuat perubahan database sekaligus penyesuaian course; layak dipisah per tujuan.
- `eb5e284` (`feat: performance measurement command`) terlihat besar (1.203 baris), tetapi sebagian besarnya file hasil generate. Rinciannya: `app/Console/Commands/MeasurePerformance.php` 210 baris (kode command), `composer.json` 2 baris, dan `composer.lock` 991 baris (sekitar 82% dari total). Ukuran kode yang sebenarnya hanya sekitar 212 baris, sehingga commit ini tidak perlu dipecah berdasarkan ukuran. Namun perubahan dependency sebaiknya dipisah dari commit fitur, misalnya `build(deps): ...` untuk `composer.json` dan `composer.lock`, lalu `feat(performance): add measurement command` untuk command-nya.

---

## 6. Ringkasan dan Tindak Lanjut

- Pola yang baik: 77,3% commit sudah mengikuti Conventional Commits, commit API, notifikasi, dan authz memakai scope yang jelas, dan commit tersebar di 13 minggu dengan hanya satu minggu kosong.
- Pola yang perlu diperbaiki: delapan commit percobaan beruntun saat debugging deployment, pesan tanpa tipe pada commit awal, scope berupa nomor minggu, dan commit campuran seperti `fbafd6d` dan `a46782a`.
- Perbaikan ke depan: debugging konfigurasi dilakukan di branch sendiri lalu di-squash sebelum merge; satu commit untuk satu perubahan logis; tulis pesan dari isi diff; samakan nama Git lokal dengan nama profil GitHub supaya riwayat tidak terpecah dua nama.
- Commit untuk tugas Minggu 13 ini memakai pesan Conventional Commits dan identitas Git saya sendiri.