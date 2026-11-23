# Keamanan KampusLMS: Daftar Titik Rawan IDOR
**Kelompok B4 - EduSpace (KampusLMS)**

Dokumen ini adalah kelanjutan tabel "Daftar Titik Rawan IDOR" dari Minggu 5. Pada Minggu 5 kolom perlindungannya masih "belum ada". Setelah Minggu 7 (Policy) dan Minggu 14 (Audit Pengujian), setiap titik dipetakan ke Policy atau query yang menutupnya beserta nama *automated test* (`NamaClass::test_method`) yang membuktikannya.

Sumber pemetaan: `routes/web.php`, `routes/api.php`, seluruh controller (web dan `Api/V1`), `app/Policies/*`, serta seluruh *test suite* di `tests/Feature/*`.

---

## 1. Cara membaca tabel

IDOR terjadi ketika pengguna mengganti ID di URL, body, atau parameter lain, lalu memperoleh data atau aksi milik orang lain.

Lapisan pertahanan yang dipakai di repo ini:

| Lapisan | Fungsi | Lokasi |
| :--- | :--- | :--- |
| Middleware `auth` | Menolak tamu (`401`) | Grup route web, `auth:sanctum` di API |
| Middleware `role:<peran>` | Menolak peran yang salah (`403`) | `EnsureUserHasRole`, grup `admin`, `dosen`, `mahasiswa` |
| `scopeBindings()` | Memastikan *child* cocok dengan *parent* di URL (`{assignment}` milik `{course}`). Bukan pengecekan hak akses | Grup dosen dan mahasiswa |
| Policy | Menentukan siapa boleh melakukan apa pada objek tertentu (admin, pemilik, terdaftar) | `app/Policies/*`, dipanggil lewat `Gate::authorize()` |
| Query terfilter | Hanya mengambil baris milik pengguna (`where('user_id', auth()->id())`, `where('lecturer_id', ...)`, `whereHas('students', ...)`) | Controller |

### Kolom Status:

| Simbol | Arti |
| :--- | :--- |
| ✔ Policy | Ditutup oleh Policy yang dipanggil dari controller |
| ✔ Query | Ditutup oleh query terfilter atau pengecekan inline di controller (setara Policy, tetapi belum memakai Policy) |
| ▲ Sebagian | Data sensitif sudah terlindungi, tetapi akses ke objek induknya belum dicek |
| ✖ Terbuka | Belum ada pemeriksaan hak akses. Rencana penutupan ada di bagian 4 |

> **Catatan:** Policy sengaja tidak memakai `Gate::before` untuk admin. Matriks akses menolak admin pada "mengumpulkan tugas" dan "memberi nilai", sehingga admin dicek per method (lihat `ChecksCourseRelation`).

---

## 2. Tabel Titik Rawan IDOR

### 2.1 Web: area Admin (`/admin/...`, middleware `auth` + `role:admin`)

| No | Method & URI | Parameter | Seharusnya boleh | Ditutup oleh (Policy / query) | Status | Test |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| 1 | `GET admin/users/{user}`, `GET admin/users/{user}/edit` | `user` | Admin | Middleware `role:admin`. Tidak ada `UserPolicy`; seluruh area ini khusus admin | ✔ Query (middleware) | `AdminUserManagementTest::test_admin_can_manage_users_from_the_admin_views`, `AdminUserManagementTest::test_users_are_admin_only_and_general_user_route_is_removed` |
| 2 | `PUT/PATCH`, `DELETE admin/users/{user}` | `user` | Admin | Middleware `role:admin`. Kolom `role` tidak ada di `$fillable`, diisi eksplisit setelah validasi `in:admin,dosen,mahasiswa` | ✔ Query (middleware) | `AdminUserManagementTest::test_admin_can_manage_users_from_the_admin_views`, `AdminUserManagementTest::test_admin_cannot_delete_a_user_who_is_enrolled_in_a_course` |
| 3 | `GET admin/courses/{course}`, `GET admin/courses/{course}/edit` | `course` | Admin | `CoursePolicy@view` dan `@update` (`Gate::authorize` di `CourseController::show/edit`) | ✔ Policy | `AdminCourseManagementTest::test_admin_can_view_courses_index_create_and_show_pages`, `AdminCourseManagementTest::test_dosen_cannot_access_admin_course_crud_operations`, `AdminCourseManagementTest::test_mahasiswa_cannot_access_admin_course_crud_operations` |
| 4 | `PUT/PATCH admin/courses/{course}` | `course` | Admin | `CoursePolicy@update` (hanya admin) | ✔ Policy | `AdminCourseManagementTest::test_admin_can_update_a_course`, `AdminCourseManagementTest::test_dosen_cannot_access_admin_course_crud_operations`, `AdminCourseManagementTest::test_mahasiswa_cannot_access_admin_course_crud_operations`, `PolicyAccessTest::test_only_admin_can_create_or_delete_course_but_owner_can_update_it` |
| 5 | `DELETE admin/courses/{course}` | `course` | Admin | `CoursePolicy@delete` (hanya admin) | ✔ Policy | `AdminCourseManagementTest::test_admin_can_delete_a_course`, `AdminCourseManagementTest::test_admin_cannot_delete_a_course_with_existing_submissions`, `AdminCourseManagementTest::test_dosen_cannot_access_admin_course_crud_operations`, `AdminCourseManagementTest::test_mahasiswa_cannot_access_admin_course_crud_operations`, `PolicyAccessTest::test_only_admin_can_create_or_delete_course_but_owner_can_update_it` |
| 6 | `GET admin/materials/{material}/edit` | `material` | Admin | `MaterialPolicy@update` (`adminEdit`) | ✔ Policy | `PolicyAccessTest::test_material_write_access_admin_and_owning_lecturer_only` *(HTTP test penolakan role non-admin belum ada)* |
| 7 | `PUT admin/materials/{material}` | `material` | Admin | `MaterialPolicy@update` (`adminUpdate`) | ✔ Policy | `PolicyAccessTest::test_material_write_access_admin_and_owning_lecturer_only` *(HTTP test penolakan role non-admin belum ada)* |
| 8 | `DELETE admin/materials/{material}` | `material` | Admin | `MaterialPolicy@delete` (`adminDestroy`) | ✔ Policy | `MaterialFileCleanupTest::test_admin_destroy_removes_physical_file`, `PolicyAccessTest::test_material_write_access_admin_and_owning_lecturer_only` |
| 9 | `POST admin/materials` | `course_id` (body) | Admin | `Course::findOrFail(course_id)` lalu `MaterialPolicy@create` dengan `Course` tujuan | ✔ Policy | `MaterialFileCleanupTest::test_admin_upload_rejects_disallowed_type`, `PolicyAccessTest::test_material_write_access_admin_and_owning_lecturer_only` |
| 10 | `GET admin/assignments/{assignment}/edit` | `assignment` | Admin | `AssignmentPolicy@update` (`adminEdit`) | ✔ Policy | `PolicyAccessTest::test_assignment_write_access_admin_and_owning_lecturer_only` *(HTTP test penolakan role non-admin belum ada)* |
| 11 | `PUT admin/assignments/{assignment}` | `assignment` | Admin | `AssignmentPolicy@update` (`adminUpdate`) | ✔ Policy | `PolicyAccessTest::test_assignment_write_access_admin_and_owning_lecturer_only` *(HTTP test penolakan role non-admin belum ada)* |
| 12 | `DELETE admin/assignments/{assignment}` | `assignment` | Admin | `AssignmentPolicy@delete`. Penghapusan tugas yang sudah punya pengumpulan ditolak di controller (keputusan Q6), bukan soal hak akses | ✔ Policy | `PolicyAccessTest::test_assignment_write_access_admin_and_owning_lecturer_only` *(HTTP test penolakan role non-admin belum ada)* |
| 13 | `POST admin/assignments` | `course_id` (body) | Admin | Validasi `exists:courses,id` (`422`) lalu `AssignmentPolicy@create` dengan `Course` tujuan | ✔ Policy | `AssignmentPublishedTest::test_admin_creating_published_assignment_dispatches_event`, `PolicyAccessTest::test_assignment_write_access_admin_and_owning_lecturer_only` |
| 14 | `GET admin/grades` | tanpa ID | Admin | `GradePolicy@viewAny` ditambah middleware `role:admin`. Admin memang boleh melihat semua nilai | ✔ Policy | `PolicyAccessTest::test_grade_visibility_matches_matrix` *(HTTP test route `/admin/grades` belum ada)* |

---

### 2.2 Web: area Dosen (`/dosen/courses/{course}/...`, middleware `auth` + `role:dosen` + `scopeBindings`)

| No | Method & URI | Parameter | Seharusnya boleh | Ditutup oleh (Policy / query) | Status | Test |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| 15 | `GET dosen/courses/{course}` | `course` | Dosen pemilik MK | `CoursePolicy@view` (`dosenShow`). Dosen lain mendapat `403` | ✔ Policy | `DosenCourseEditTest::test_course_detail_links_to_edit_form_for_its_lecturer`, `PolicyAccessTest::test_course_view_is_limited_to_admin_owner_and_enrolled` |
| 16 | `GET dosen/courses` dan `GET dosen/grades` | tanpa ID | Dosen | `Course::where('lecturer_id', user.id)`. Hanya MK yang diajar yang keluar | ✔ Query | `DosenDashboardTest::test_course_menu_shows_only_courses_taught_by_the_logged_in_lecturer`, `DosenDashboardTest::test_grades_page_separates_active_and_archived_classes_and_shows_class_statistics` |
| 17 | `GET dosen/courses/{course}/materials` | `course` | Dosen pemilik MK | `MaterialPolicy@viewAny` | ✔ Policy | `DosenMaterialsTest::test_material_index_renders_styled_list_and_confirmation_dialog`, `PolicyAccessTest::test_material_read_access_follows_course_membership` |
| 18 | `GET .../materials/create`, `POST .../materials` | `course` | Dosen pemilik MK | `MaterialPolicy@create` dengan `Course` dari URL | ✔ Policy | `DosenMaterialsTest::test_material_create_and_edit_pages_render_styled_forms_with_existing_values`, `MaterialFileCleanupTest::test_dosen_can_upload_pdf_docx_and_pptx`, `PolicyAccessTest::test_material_write_access_admin_and_owning_lecturer_only` |
| 19 | `GET .../materials/{material}/edit`, `PUT .../materials/{material}` | `course`, `material` | Dosen pemilik MK | `MaterialPolicy@update` (lewat `material->course->lecturer_id`). `scopeBindings` memastikan `{material}` memang milik `{course}` di URL | ✔ Policy | `DosenMaterialsTest::test_updating_material_without_replacement_file_keeps_existing_file_metadata`, `MaterialFileCleanupTest::test_dosen_update_with_new_file_replaces_old_file`, `PolicyAccessTest::test_material_write_access_admin_and_owning_lecturer_only` |
| 20 | `DELETE .../materials/{material}` | `course`, `material` | Dosen pemilik MK | `MaterialPolicy@delete` + `scopeBindings` | ✔ Policy | `MaterialFileCleanupTest::test_dosen_destroy_removes_physical_file`, `PolicyAccessTest::test_material_write_access_admin_and_owning_lecturer_only` |
| 21 | `GET .../assignments` | `course` | Dosen pemilik MK | `AssignmentPolicy@viewAny` | ✔ Policy | `DosenAssignmentsTest::test_lecturer_cannot_access_assignment_list_for_another_lecturers_course`, `DosenAssignmentsTest::test_assignment_list_shows_submission_totals_and_only_offers_grading_after_the_deadline` |
| 22 | `GET .../assignments/create`, `POST .../assignments` | `course` | Dosen pemilik MK | `AssignmentPolicy@create` dengan `Course` dari URL. `created_by` diisi dari `auth()->id()`, bukan dari body | ✔ Policy | `DosenAssignmentsTest::test_assignment_create_page_uses_the_styled_form_and_keeps_submitted_values`, `DosenAssignmentsTest::test_assignment_create_and_edit_save_course_rubric_selection`, `PolicyAccessTest::test_assignment_write_access_admin_and_owning_lecturer_only` |
| 23 | `GET .../assignments/{assignment}/edit`, `PUT .../assignments/{assignment}` | `course`, `assignment` | Dosen pemilik MK | `AssignmentPolicy@update` + `scopeBindings` | ✔ Policy | `DosenAssignmentsTest::test_assignment_edit_page_renders_styled_form_with_existing_values`, `AssignmentPublishedTest::test_publishing_a_draft_dispatches_event_but_editing_published_does_not`, `PolicyAccessTest::test_assignment_write_access_admin_and_owning_lecturer_only` |
| 24 | `DELETE .../assignments/{assignment}` | `course`, `assignment` | Dosen pemilik MK | `AssignmentPolicy@delete` + `scopeBindings` | ✔ Policy | `PolicyAccessTest::test_assignment_write_access_admin_and_owning_lecturer_only` *(HTTP test delete assignment dosen belum ada)* |
| 25 | `GET .../assignments/{assignment}/submissions` | `course`, `assignment` | Dosen pemilik MK | Pengecekan inline `abort_unless($course->lecturer_id === Auth::id(), 403)`. Query `$assignment->submissions()` sudah dibatasi `scopeBindings`. Setara `SubmissionPolicy@viewAny` | ✔ Query | `DosenAssignmentsTest::test_submission_list_can_be_filtered_sorted_by_nim_and_exported_as_csv`, `DosenAssignmentsTest::test_lecturer_cannot_export_submissions_for_another_lecturers_course`, `PolicyAccessTest::test_lecturer_reads_submissions_of_own_course_only` |
| 26 | `PUT .../submissions/{submission}/grade` | `course`, `assignment`, `submission` | Dosen pemilik MK, setelah tenggat | Inline: pemilik MK (`403`), `submission->assignment_id === $assignment->id` (`404`), `due_at` sudah lewat (`403`). Setara `GradePolicy@create/update` | ✔ Query | `DosenAssignmentsTest::test_lecturer_can_grade_a_submission_after_deadline_and_cannot_grade_before_it`, `GradeNotificationFlowTest::test_grading_dispatches_event_and_marks_regrade_as_updated`, `PolicyAccessTest::test_only_owning_lecturer_can_give_or_change_grades` |
| 27 | `GET/POST .../courses/{course}/grade-components` | `course` | Dosen pemilik MK | `GradeComponentPolicy@viewAny` dan `@create` | ✔ Policy | `DosenGradeComponentsTest::test_lecturer_can_view_and_manage_course_rubric_components` |
| 28 | `PUT/DELETE .../grade-components/{gradeComponent}` | `course`, `gradeComponent` | Dosen pemilik MK | `GradeComponentPolicy@update` dan `@delete` (lewat `component->course`) + `scopeBindings` | ✔ Policy | `DosenGradeComponentsTest::test_lecturer_can_view_and_manage_course_rubric_components` |

---

### 2.3 Web: area Mahasiswa (`/mahasiswa/courses/{course}/...`, middleware `auth` + `role:mahasiswa` + `scopeBindings`)

| No | Method & URI | Parameter | Seharusnya boleh | Ditutup oleh (Policy / query) | Status | Test |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| 29 | `GET mahasiswa/courses`, `GET mahasiswa/dashboard`, `GET mahasiswa/monitoring-nilai` | tanpa ID | Mahasiswa | Query `auth()->user()->courses()`. Hanya MK yang diikuti. `finalGrades` difilter `where('user_id', auth id)` | ✔ Query | `MahasiswaGradesTest::test_monitoring_page_shows_enrolled_courses_and_final_grade_for_selected_status`, `MahasiswaGradesTest::test_course_list_separates_active_and_archived_courses_in_alphabetical_order`, `MahasiswaGradesTest::test_dashboard_summarizes_courses_assignments_and_links_to_monitoring` |
| 30 | `GET mahasiswa/courses/{course}` | `course` | Mahasiswa terdaftar | `CoursePolicy@view` (`Gate::authorize` di `showCourse`). Non-terdaftar mendapat `403` | ✔ Policy | `MahasiswaAccessTest::test_student_not_enrolled_gets_403_on_every_course_route`, `MahasiswaAccessTest::test_enrolled_student_can_open_course_routes`, `PolicyAccessTest::test_course_view_is_limited_to_admin_owner_and_enrolled` |
| 31 | `GET .../courses/{course}/grade-components` | `course` | Mahasiswa terdaftar | Query `auth()->user()->courses()->whereKey($course->id)->exists()`, jika tidak maka `403`. Isi submission juga difilter `where('user_id', auth id)` | ✔ Query | `MahasiswaGradesTest::test_student_rubric_page_shows_database_components_published_assignments_and_own_grade` *(IDOR non-terdaftar pada route ini belum diuji eksplisit)* |
| 32 | `GET mahasiswa/monitoring-nilai/{course}/grades` (dan redirect legacy `GET .../courses/{course}/grades`) | `course` | Mahasiswa terdaftar | `CoursePolicy@view` (`Gate::authorize` di `grades`). Nilai juga difilter query submissions `where('user_id', auth id)`. Redirect legacy meneruskan ke route ini sehingga ikut terlindungi | ✔ Policy | `MahasiswaAccessTest::test_student_not_enrolled_gets_403_on_every_course_route`, `MahasiswaGradesTest::test_course_grade_detail_shows_assignment_scores_and_category_weights` |
| 33 | `GET .../courses/{course}/assignments` | `course` | Mahasiswa terdaftar | `AssignmentPolicy@viewAny` (keanggotaan MK). Tugas draft disembunyikan oleh query `where('status', 'published')` | ✔ Policy | `MahasiswaAccessTest::test_student_not_enrolled_gets_403_on_every_course_route`, `MahasiswaAccessTest::test_non_published_assignment_is_not_listed_for_student` |
| 34 | `GET .../courses/{course}/materials` | `course` | Mahasiswa terdaftar | `MaterialPolicy@viewAny` (`Gate::authorize` di `materials`) | ✔ Policy | `MahasiswaAccessTest::test_student_not_enrolled_gets_403_on_every_course_route`, `MahasiswaAccessTest::test_enrolled_student_can_open_course_routes`, `PolicyAccessTest::test_material_read_access_follows_course_membership` |
| 35 | `GET .../assignments/{assignment}/submissions` | `course`, `assignment` | Mahasiswa terdaftar, hanya submission miliknya | `SubmissionPolicy@viewAny` (terdaftar dan tugas published; draft `403`) ditambah query `$assignment->submissions()->where('user_id', auth()->id())` | ✔ Policy | `MahasiswaAccessTest::test_student_not_enrolled_gets_403_on_every_course_route`, `MahasiswaAccessTest::test_draft_assignment_is_blocked_for_enrolled_student`, `PolicyAccessTest::test_student_cannot_view_or_download_another_students_submission` |
| 36 | `GET .../assignments/{assignment}/submissions/create` | `course`, `assignment` | Mahasiswa terdaftar, tugas published | `SubmissionPolicy@create` (`Gate::authorize` di `createSubmission`, sama dengan `store`). Non-terdaftar `403`, draft `404` | ✔ Policy | `MahasiswaAccessTest::test_student_not_enrolled_gets_403_on_every_course_route`, `MahasiswaAccessTest::test_draft_assignment_is_blocked_for_enrolled_student` |
| 37 | `POST .../assignments/{assignment}/submissions` | `course`, `assignment` | Mahasiswa terdaftar, tugas published | `SubmissionPolicy@create` (`Gate::authorize`): tidak terdaftar `403`, draft `404`. `user_id` diisi dari `request->user()`, bukan dari body | ✔ Policy | `MahasiswaSubmissionRouteTest::test_enrolled_student_can_submit_using_the_mahasiswa_route`, `MahasiswaSubmissionRouteTest::test_student_not_enrolled_in_course_cannot_submit`, `PolicyAccessTest::test_only_enrolled_students_can_submit` |

---

### 2.4 API `/api/v1/...` (middleware `auth:sanctum` + `throttle:60,1`)

> Semua controller API memakai pengecekan inline (belum memanggil Policy). Logikanya sama dengan Policy yang bersangkutan, tetapi duplikat.

| No | Method & URI | Parameter | Seharusnya boleh | Ditutup oleh (Policy / query) | Status | Test |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| 38 | `GET /courses` | tanpa ID | Admin semua, dosen yang diajar, mahasiswa yang diikuti | Query: `where('lecturer_id', user.id)` untuk dosen, `whereHas('students', users.id)` untuk mahasiswa | ✔ Query | *Belum ada PHPUnit feature test API* (diuji via skrip `scripts/test-authz.sh`) |
| 39 | `GET /courses/{course}` | `course` | Admin, dosen pemilik, mahasiswa terdaftar | `CourseController::canAccessCourse()` (setara `CoursePolicy@view`) -> `403`. `assignments_count` untuk mahasiswa hanya menghitung published | ✔ Query | *Belum ada PHPUnit feature test API* (Policy setara: `PolicyAccessTest::test_course_view_is_limited_to_admin_owner_and_enrolled`) |
| 40 | `GET /courses/{course}/materials` | `course` | Admin, dosen pemilik, mahasiswa terdaftar | `canAccessCourse()` (setara `MaterialPolicy@viewAny`) -> `403` | ✔ Query | *Belum ada PHPUnit feature test API* (Policy setara: `PolicyAccessTest::test_material_read_access_follows_course_membership`) |
| 41 | `GET /courses/{course}/assignments` | `course` | Admin, dosen pemilik, mahasiswa terdaftar (hanya published) | `canAccessCourse()` (setara `AssignmentPolicy@viewAny`) + query `where('status', 'published')` untuk mahasiswa. Filter `?status=draft` tidak bisa menembusnya karena kondisi published ditambahkan lebih dulu | ✔ Query | *Belum ada PHPUnit feature test API* (Policy setara: `PolicyAccessTest::test_student_sees_published_assignment_but_draft_is_hidden_as_404`) |
| 42 | `GET /courses/{course}/grade-components` | `course` | Admin, dosen pemilik, mahasiswa terdaftar | Inline: mahasiswa harus ada di `course->students`, dosen harus `lecturer_id` sama (`403`) | ✔ Query | `ApiLazyLoadingTest::test_grade_component_endpoints_do_not_lazy_load_course_or_assignments` *(otorisasi negatif 403 belum ada feature test)* |
| 43 | `POST /assignments` | `course_id` (body) | Dosen pemilik MK | Peran harus dosen (`403`), lalu `Course::findOrFail(course_id)` dan `lecturer_id === user.id` (`403`). Setara `AssignmentPolicy@create`. `created_by` dari `user->id` | ✔ Query | *Belum ada PHPUnit feature test API* (diuji via `scripts/test-authz.sh`; Policy setara: `PolicyAccessTest::test_assignment_write_access_admin_and_owning_lecturer_only`) |
| 44 | `PUT/PATCH /assignments/{assignment}` | `assignment` | Dosen pemilik MK | Inline `assignment->course->lecturer_id !== auth()->id()` -> `403`. Setara `AssignmentPolicy@update`. Otorisasi dijalankan sebelum validasi | ✔ Query | `ApiLazyLoadingTest::test_assignment_update_and_delete_do_not_lazy_load_course`, `AssignmentPublishedTest::test_api_publish_flow_dispatches_event_only_on_transition` *(otorisasi negatif 403 belum ada feature test)* |
| 45 | `DELETE /assignments/{assignment}` | `assignment` | Dosen pemilik MK | Inline peran dosen dan `course->lecturer_id === user.id` -> `403`. Setara `AssignmentPolicy@delete` | ✔ Query | `ApiLazyLoadingTest::test_assignment_update_and_delete_do_not_lazy_load_course` *(otorisasi negatif 403 belum ada feature test)* |
| 46 | `GET /assignments/{assignment}/submissions` | `assignment` | Dosen pemilik MK | `isOwningLecturer()` `403`, lalu query `$assignment->submissions()`. Setara `SubmissionPolicy@viewAny` | ✔ Query | *Belum ada PHPUnit feature test API* (Policy setara: `PolicyAccessTest::test_lecturer_reads_submissions_of_own_course_only`) |
| 47 | `POST /assignments/{assignment}/submissions` | `assignment` | Mahasiswa terdaftar | Inline: harus mahasiswa dan `course->students()->where('users.id', user.id)->exists()` -> `403`; tugas draft -> `404`. `user_id` dari token, bukan body. Setara `SubmissionPolicy@create` | ✔ Query | *Belum ada PHPUnit feature test API* (Policy setara: `PolicyAccessTest::test_only_enrolled_students_can_submit`) |
| 48 | `PUT /submissions/{submission}/grade` | `submission` | Dosen pemilik MK | `isOwningLecturer()` lewat `submission->assignment->course->lecturer_id` -> `403`. Otorisasi sebelum validasi (`403` menang dari `422`). Setara `GradePolicy@create/update` | ✔ Query | *Belum ada PHPUnit feature test API* (diuji via `scripts/test-authz.sh`; Policy setara: `PolicyAccessTest::test_only_owning_lecturer_can_give_or_change_grades`) |
| 49 | `GET /notifications` | tanpa ID | Pemilik notifikasi | Query `$request->user()->notifications()` | ✔ Query | *Belum ada PHPUnit feature test API* (`NotificationWebTest::test_index_lists_only_own_notifications` hanya menguji web) |
| 50 | `POST /notifications/{id}/read` | `id` (UUID) | Pemilik notifikasi | `Notification::findOrFail($id)` (`404`), lalu cek `notifiable_type` dan `notifiable_id` sama dengan pengguna -> `403` | ✔ Query | *Belum ada PHPUnit feature test API* (`NotificationWebTest::test_user_cannot_mark_someone_elses_notification_as_read` hanya menguji web) |
| 51 | `POST /grade-components` | `course_id` (body) | Dosen pemilik MK | Peran dosen (`403`), `Course::findOrFail` lalu `lecturer_id === user.id` (`403`). Setara `GradeComponentPolicy@create` | ✔ Query | *Belum ada PHPUnit feature test API* (diuji via `scripts/test-authz.sh`) |
| 52 | `GET /grade-components/{gradeComponent}` | `gradeComponent` | Admin, dosen pemilik, mahasiswa terdaftar | Inline lewat `gradeComponent->course` (mahasiswa di `students`, dosen `lecturer_id`). Setara `GradeComponentPolicy@viewAny` | ✔ Query | `ApiLazyLoadingTest::test_grade_component_endpoints_do_not_lazy_load_course_or_assignments` *(otorisasi negatif 403 belum ada feature test)* |
| 53 | `PUT/PATCH, DELETE /grade-components/{gradeComponent}` | `gradeComponent` | Dosen pemilik MK | Inline peran dosen dan `course->lecturer_id === user.id` -> `403`. Setara `GradeComponentPolicy@update/delete` | ✔ Query | `ApiLazyLoadingTest::test_grade_component_endpoints_do_not_lazy_load_course_or_assignments` *(otorisasi negatif 403 belum ada feature test)* |

---

### 2.5 File yang diunggah

| No | Objek | Seharusnya boleh | Ditutup oleh (Policy / query) | Status | Test |
| :---: | :--- | :--- | :--- | :---: | :--- |
| 54 | File pengumpulan (`submissions/{assignment_id}/<nama-acak>`) | Pemilik, dosen pemilik MK, admin | Disimpan di disk `local` (privat), nama diacak Laravel, dilindungi oleh `SubmissionPolicy@download` pada route `submissions.download` | ✔ Policy | `MahasiswaSubmissionRouteTest::test_enrolled_student_can_submit_using_the_mahasiswa_route`, `PolicyAccessTest::test_student_cannot_view_or_download_another_students_submission` *(HTTP test route `submissions.download` belum ada)* |
| 55 | File materi (`materials/<hash>` di disk `local`, privat) | Admin, dosen pemilik, mahasiswa terdaftar | Disimpan di disk privat (`store('materials')` tanpa `'public'`), sehingga tidak ada URL langsung. Satu-satunya jalan adalah route `courses.materials.download` -> `MaterialController::download` -> `MaterialPolicy@download` (`403` bila tidak berhak, `404` bila bukan file atau file hilang) + `scopeBindings` agar `{material}` harus milik `{course}` | ✔ Policy | `MaterialDownloadTest::test_admin_owner_and_enrolled_student_can_download`, `MaterialDownloadTest::test_outsiders_cannot_download`, `MaterialDownloadTest::test_guest_gets_401`, `MaterialDownloadTest::test_material_cannot_be_reached_through_another_course_in_the_url` |

---

### 2.6 Ringkasan

| Status | Jumlah baris | Nomor |
| :--- | :---: | :--- |
| ✔ Policy | 32 | 3-15, 17-24, 27, 28, 30, 32-37, 54, 55 |
| ✔ Query | 23 | 1, 2, 16, 25, 26, 29, 31, 38-53 |
| ▲ Sebagian | 0 | - |
| ✖ Terbuka | 0 | - |

---

## 3. Policy yang tersedia dan di mana dipakai

| Policy | Ability | Aturan singkat | Dipanggil dari |
| :--- | :--- | :--- | :--- |
| `CoursePolicy` | `viewAny` | admin, dosen, mahasiswa (isi daftar difilter query) | `CourseController::index/dosenIndex/dosenGradesIndex` |
| | `view` | admin, dosen pemilik, mahasiswa terdaftar | `CourseController::show/dosenShow`, `MahasiswaController::showCourse/grades` |
| | `create`/`update`/`delete` | hanya admin | `CourseController` |
| | `manageEnrollment` | admin, dosen pemilik | belum ada route |
| `MaterialPolicy` | `viewAny`/`view` | admin, dosen pemilik, mahasiswa terdaftar | `MaterialController` (dosen dan admin), `MahasiswaController::materials` |
| | `download` | sama dengan `view` | `MaterialController::download` (route `courses.materials.download`) |
| | `create`/`update`/`delete` | admin, dosen pemilik MK tujuan | `MaterialController` |
| `AssignmentPolicy` | `viewAny` | admin, dosen pemilik, mahasiswa terdaftar | `AssignmentController::dosenIndex`, `MahasiswaController::assignments` |
| | `view` | admin/dosen pemilik semua. Mahasiswa terdaftar hanya `published`, draft dijawab `404` | tidak ada route |
| | `create`/`update`/`delete` | admin, dosen pemilik | `AssignmentController` |
| `SubmissionPolicy` | `viewAny` | admin, dosen pemilik, mahasiswa terdaftar (`published`) | `MahasiswaController::submissions` |
| | `view`/`download` | mahasiswa hanya miliknya, dosen pemilik MK, admin | `SubmissionController::download` (route `submissions.download`) |
| | `create` | hanya mahasiswa terdaftar, draft `404` | `SubmissionController::store` (web), `MahasiswaController::createSubmission` |
| | `update` | hanya pemilik, belum dinilai, published, masih terdaftar | belum dipanggil |
| | `delete` | ditolak semua | belum dipanggil |
| `GradePolicy` | `viewAny` | admin, dosen, mahasiswa | `AdminGradeController::index` |
| | `view` | admin semua, dosen pemilik MK, mahasiswa hanya nilainya | belum dipanggil |
| | `create`/`update` | hanya dosen pemilik MK setelah `due_at` lewat | belum dipanggil (web dan API memakai inline) |
| | `delete` | ditolak semua | belum dipanggil |
| `GradeComponentPolicy` | `viewAny`/`create`/`update`/`delete` | `viewAny` untuk admin, dosen pemilik, mahasiswa terdaftar. Lainnya admin dan dosen pemilik | `GradeComponentController` (web) |

> Policy ditemukan otomatis lewat konvensi nama `App\Policies\<Model>Policy`; ini dikunci oleh `PolicyAccessTest::test_policies_are_auto_discovered`.

---

## 4. Penutupan yang sudah dikerjakan

* **Area mahasiswa (No 30, 32-36):** `MahasiswaController` memanggil `Gate::authorize(...)` di `showCourse`, `grades`, `assignments`, `materials`, `submissions`, dan `createSubmission`, memakai Policy yang sudah ada. Diuji di `tests/Feature/MahasiswaAccessTest.php`: mahasiswa non-terdaftar mendapat `403` di semua route tersebut, mahasiswa terdaftar mendapat `200`, dan tugas draft diblokir (`403` pada daftar submission, `404` pada form pengumpulan).
* **Area CRUD Mata Kuliah Admin & Penolakan Role (No 3-5):** Diuji melalui HTTP di `tests/Feature/AdminCourseManagementTest.php` (`8 passed, 46 assertions`): Admin berhasil melakukan Create, Read, Update, dan Delete mata kuliah (serta ditolak menghapus mata kuliah yang memiliki pengumpulan tugas), sedangkan Dosen dan Mahasiswa mendapat `403 Forbidden` dan Guest mendapat `401 Unauthorized`.
* **File materi (No 55):**
  1. `MaterialController` menyimpan file dengan `store('materials')` (disk `local`, privat) dan menghapusnya dengan `Storage::delete(...)`, bukan lagi disk `public`.
  2. Method `download` memanggil `Gate::authorize('download', $material)` dan mengirim file lewat `Storage::download(...)`.
  3. Route `GET /courses/{course}/materials/{material}/download` (nama `courses.materials.download`) dengan middleware `auth` dan `scopeBindings()`.
  4. Tautan unduh ditambahkan di tampilan dosen, mahasiswa, dan admin. Diuji di `tests/Feature/MaterialDownloadTest.php`.

---

## 5. Catatan di luar IDOR (ditemukan saat pemetaan)

1. Aturan tenggat nilai tidak sama di web dan API. Web (`dosenGrade`) menolak penilaian sebelum `due_at`, sedangkan `Api\V1\SubmissionController::grade` tidak. `GradePolicy@create/update` sudah memuat aturan ini; memakainya di kedua tempat akan menyamakan perilaku.
2. `Api\V1\AssignmentController::destroy` tidak memblokir tugas yang sudah punya pengumpulan (`409`), padahal komentar `AssignmentPolicy@delete` dan versi web menyatakan demikian.
3. Pesan `403` `Api\V1\AssignmentController::update` (`"Forbidden. Anda bukan pemilik tugas ini."`) berbeda dari kontrak (`"Anda tidak memiliki akses ke sumber daya ini."`).
4. Perbandingan ketat `===` pada `$course->lecturer_id === Auth::id()` di beberapa bagian web controller bergantung pada driver DB yang mengembalikan integer. Sebaiknya diseragamkan dengan casting `(int)` seperti di Policy.
5. Method "standard/legacy" di `MaterialController` dan `AssignmentController` (`index`, `show`, `create`, `store`, `edit`, `update`, `destroy`) sudah memanggil `Gate`, tetapi route-nya tidak lagi terdaftar di `routes/web.php`, sehingga tidak dapat dijangkau.

---

## 6. Cara memverifikasi

```bash
# Daftar route berparameter model
php artisan route:list --except-vendor

# Uji CRUD Mata Kuliah Admin, Policy, dan akses peran
php artisan test --filter=AdminCourseManagementTest
php artisan test --filter=PolicyAccessTest
php artisan test --filter=MahasiswaAccessTest
php artisan test --filter=MaterialDownloadTest

# Uji otorisasi API end-to-end (isi token di dalam skrip lebih dulu)
bash scripts/test-authz.sh
```

---

## 7. Daftar Titik Keamanan yang Masih Belum Teruji

Berdasarkan pemetaan 55 baris di atas terhadap seluruh hasil eksekusi `php artisan test`, berikut adalah daftar titik keamanan yang sudah ditutup pada kode produksi tetapi **belum memiliki *automated feature test* HTTP yang membuktikannya secara eksplisit**:

1. **Seluruh Endpoint REST API `/api/v1/*` (Baris #38–#53):**
   - Belum ada file *PHPUnit Feature Test* khusus untuk `/api/v1/*` yang menguji skenario otorisasi negatif (`401`/`403`/`404`) lintas dosen maupun mahasiswa non-terdaftar via Sanctum token. Saat ini pengujian otorisasi API hanya mengandalkan skrip shell manual (`scripts/test-authz.sh`) serta pengujian N+1 query (`ApiLazyLoadingTest`) dan event publish (`AssignmentPublishedTest::test_api_publish_flow_dispatches_event_only_on_transition`).
2. **Route Pengunduhan Berkas Submission via Web (Baris #54 — `GET /submissions/{submission}/download`):**
   - `SubmissionPolicy@download` sudah diuji pada level unit Policy di `PolicyAccessTest::test_student_cannot_view_or_download_another_students_submission`, tetapi belum ada *feature test* HTTP yang memanggil route `submissions.download` secara langsung untuk memverifikasi mahasiswa lain mendapat `403`, sedangkan pemilik submission, dosen pengampu, dan admin mendapat `200`.
3. **Penolakan Akses Role Non-Admin pada Route Materi, Tugas, dan Nilai Admin (Baris #6–#14):**
   - Route `/admin/materials/*`, `/admin/assignments/*`, dan `/admin/grades` belum memiliki *feature test* HTTP yang membuktikan bahwa akun `dosen` dan `mahasiswa` mendapat `403 Forbidden` saat mencoba mengakses URL admin tersebut (baru teruji pada `/admin/users/*` dan `/admin/courses/*`).
4. **Skenario IDOR Lintas Dosen pada Operasi Delete Assignment & Grade Component (Baris #24, #27, #28):**
   - `DosenAssignmentsTest` menguji penolakan dosen lain untuk melihat daftar tugas dan mengekspor submission, namun belum menguji secara eksplisit lewat HTTP bahwa dosen lain ditolak (`403`) saat memanggil `DELETE /dosen/courses/{course}/assignments/{assignment}` serta operasi CRUD pada `/dosen/courses/{course}/grade-components` (baru teruji di `PolicyAccessTest`).
5. **Skenario IDOR Mahasiswa Non-Terdaftar pada Route Komponen Nilai (Baris #31 — `GET /mahasiswa/courses/{course}/grade-components`):**
   - `MahasiswaGradesTest::test_student_rubric_page_shows_database_components_published_assignments_and_own_grade` baru menguji mahasiswa yang terdaftar; skenario saat mahasiswa dari mata kuliah lain mengakses URL tersebut (`403`) belum teruji.