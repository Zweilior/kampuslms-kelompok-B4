# Jawaban 5.3 READ
### Nama : Muhammad Arif Saputra
### NIM  : 10241044  

---

#### 1. Jalankan `php artisan route:list --except-vendor`. Salin keluarannya ke catatan.
```
php artisan route:list --except-vendor

  GET|HEAD        / .................................................................................................................................... routes/web.php:15
  GET|HEAD        admin/assignments ............................................................................ admin.assignments.index › AssignmentController@adminIndex
  POST            admin/assignments ............................................................................ admin.assignments.store › AssignmentController@adminStore
  GET|HEAD        admin/assignments/create ................................................................... admin.assignments.create › AssignmentController@adminCreate
  PUT             admin/assignments/{assignment} ............................................................. admin.assignments.update › AssignmentController@adminUpdate
  DELETE          admin/assignments/{assignment} ........................................................... admin.assignments.destroy › AssignmentController@adminDestroy
  GET|HEAD        admin/assignments/{assignment}/edit ............................................................ admin.assignments.edit › AssignmentController@adminEdit
  GET|HEAD        admin/courses ............................................................................................. admin.courses.index › CourseController@index
  POST            admin/courses ............................................................................................. admin.courses.store › CourseController@store
  GET|HEAD        admin/courses/create .................................................................................... admin.courses.create › CourseController@create
  GET|HEAD        admin/courses/{course} ...................................................................................... admin.courses.show › CourseController@show
  PUT|PATCH       admin/courses/{course} .................................................................................. admin.courses.update › CourseController@update
  DELETE          admin/courses/{course} ................................................................................ admin.courses.destroy › CourseController@destroy
  GET|HEAD        admin/courses/{course}/edit ................................................................................. admin.courses.edit › CourseController@edit
  GET|HEAD        admin/dashboard ................................................................................... admin.dashboard › DashboardController@adminDashboard
  GET|HEAD        admin/grades ........................................................................................... admin.grades.index › AdminGradeController@index
  GET|HEAD        admin/materials .................................................................................. admin.materials.index › MaterialController@adminIndex
  POST            admin/materials .................................................................................. admin.materials.store › MaterialController@adminStore
  GET|HEAD        admin/materials/create ......................................................................... admin.materials.create › MaterialController@adminCreate
  PUT             admin/materials/{material} ..................................................................... admin.materials.update › MaterialController@adminUpdate
  DELETE          admin/materials/{material} ................................................................... admin.materials.destroy › MaterialController@adminDestroy
  GET|HEAD        admin/materials/{material}/edit .................................................................... admin.materials.edit › MaterialController@adminEdit
  GET|HEAD        admin/users ................................................................................................... admin.users.index › UserController@index
  POST            admin/users ................................................................................................... admin.users.store › UserController@store
  GET|HEAD        admin/users/create .......................................................................................... admin.users.create › UserController@create
  GET|HEAD        admin/users/{user} .............................................................................................. admin.users.show › UserController@show
  PUT|PATCH       admin/users/{user} .......................................................................................... admin.users.update › UserController@update
  DELETE          admin/users/{user} ........................................................................................ admin.users.destroy › UserController@destroy
  GET|HEAD        admin/users/{user}/edit ......................................................................................... admin.users.edit › UserController@edit
  GET|HEAD        courses ......................................................................................................... courses.index › CourseController@index
  POST            courses ......................................................................................................... courses.store › CourseController@store
  GET|HEAD        courses/create ................................................................................................ courses.create › CourseController@create
  GET|HEAD        courses/{course} .................................................................................................. courses.show › CourseController@show
  PUT|PATCH       courses/{course} .............................................................................................. courses.update › CourseController@update
  DELETE          courses/{course} ............................................................................................ courses.destroy › CourseController@destroy
  GET|HEAD        courses/{course}/assignments .................................................................... courses.assignments.index › AssignmentController@index
  POST            courses/{course}/assignments .................................................................... courses.assignments.store › AssignmentController@store
  GET|HEAD        courses/{course}/assignments/create ........................................................... courses.assignments.create › AssignmentController@create
  GET|HEAD        courses/{course}/assignments/{assignment} ......................................................... courses.assignments.show › AssignmentController@show
  PUT|PATCH       courses/{course}/assignments/{assignment} ..................................................... courses.assignments.update › AssignmentController@update
  DELETE          courses/{course}/assignments/{assignment} ................................................... courses.assignments.destroy › AssignmentController@destroy
  GET|HEAD        courses/{course}/assignments/{assignment}/edit .................................................... courses.assignments.edit › AssignmentController@edit
  GET|HEAD        courses/{course}/assignments/{assignment}/submissions ............................... courses.assignments.submissions.index › SubmissionController@index
  POST            courses/{course}/assignments/{assignment}/submissions ............................... courses.assignments.submissions.store › SubmissionController@store
  GET|HEAD        courses/{course}/assignments/{assignment}/submissions/create ...................... courses.assignments.submissions.create › SubmissionController@create
  GET|HEAD        courses/{course}/assignments/{assignment}/submissions/{submission} .................... courses.assignments.submissions.show › SubmissionController@show
  PUT|PATCH       courses/{course}/assignments/{assignment}/submissions/{submission} ................ courses.assignments.submissions.update › SubmissionController@update
  DELETE          courses/{course}/assignments/{assignment}/submissions/{submission} .............. courses.assignments.submissions.destroy › SubmissionController@destroy
  GET|HEAD        courses/{course}/assignments/{assignment}/submissions/{submission}/edit ............... courses.assignments.submissions.edit › SubmissionController@edit
  GET|HEAD        courses/{course}/edit ............................................................................................. courses.edit › CourseController@edit
  GET|HEAD        courses/{course}/materials .......................................................................... courses.materials.index › MaterialController@index
  POST            courses/{course}/materials .......................................................................... courses.materials.store › MaterialController@store
  GET|HEAD        courses/{course}/materials/create ................................................................. courses.materials.create › MaterialController@create
  GET|HEAD        courses/{course}/materials/{material} ................................................................. courses.materials.show › MaterialController@show
  PUT|PATCH       courses/{course}/materials/{material} ............................................................. courses.materials.update › MaterialController@update
  DELETE          courses/{course}/materials/{material} ........................................................... courses.materials.destroy › MaterialController@destroy
  GET|HEAD        courses/{course}/materials/{material}/edit ............................................................ courses.materials.edit › MaterialController@edit
  GET|HEAD        dashboard ........................................................................................................ dashboard › DashboardController@index
  GET|HEAD        dosen/courses ........................................................................................ dosen.courses.index › CourseController@dosenIndex
  GET|HEAD        dosen/courses/{course} ................................................................................. dosen.courses.show › CourseController@dosenShow
  GET|HEAD        dosen/courses/{course}/assignments ........................................................ dosen.courses.assignments.index › AssignmentController@index
  POST            dosen/courses/{course}/assignments ........................................................ dosen.courses.assignments.store › AssignmentController@store
  GET|HEAD        dosen/courses/{course}/assignments/create ............................................... dosen.courses.assignments.create › AssignmentController@create
  GET|HEAD        dosen/courses/{course}/assignments/{assignment} ............................................. dosen.courses.assignments.show › AssignmentController@show
  PUT|PATCH       dosen/courses/{course}/assignments/{assignment} ......................................... dosen.courses.assignments.update › AssignmentController@update
  DELETE          dosen/courses/{course}/assignments/{assignment} ....................................... dosen.courses.assignments.destroy › AssignmentController@destroy
  GET|HEAD        dosen/courses/{course}/assignments/{assignment}/edit ........................................ dosen.courses.assignments.edit › AssignmentController@edit
  GET|HEAD        dosen/courses/{course}/assignments/{assignment}/submissions ................... dosen.courses.assignments.submissions.index › SubmissionController@index
  POST            dosen/courses/{course}/assignments/{assignment}/submissions ................... dosen.courses.assignments.submissions.store › SubmissionController@store
  GET|HEAD        dosen/courses/{course}/assignments/{assignment}/submissions/create .......... dosen.courses.assignments.submissions.create › SubmissionController@create
  GET|HEAD        dosen/courses/{course}/assignments/{assignment}/submissions/{submission} ........ dosen.courses.assignments.submissions.show › SubmissionController@show
  PUT|PATCH       dosen/courses/{course}/assignments/{assignment}/submissions/{submission} .... dosen.courses.assignments.submissions.update › SubmissionController@update
  DELETE          dosen/courses/{course}/assignments/{assignment}/submissions/{submission} .. dosen.courses.assignments.submissions.destroy › SubmissionController@destroy
  GET|HEAD        dosen/courses/{course}/assignments/{assignment}/submissions/{submission}/edit ... dosen.courses.assignments.submissions.edit › SubmissionController@edit
  GET|HEAD        dosen/courses/{course}/materials .............................................................. dosen.courses.materials.index › MaterialController@index
  POST            dosen/courses/{course}/materials .............................................................. dosen.courses.materials.store › MaterialController@store
  GET|HEAD        dosen/courses/{course}/materials/create ..................................................... dosen.courses.materials.create › MaterialController@create
  GET|HEAD        dosen/courses/{course}/materials/{material} ..................................................... dosen.courses.materials.show › MaterialController@show
  PUT|PATCH       dosen/courses/{course}/materials/{material} ................................................. dosen.courses.materials.update › MaterialController@update
  DELETE          dosen/courses/{course}/materials/{material} ............................................... dosen.courses.materials.destroy › MaterialController@destroy
  GET|HEAD        dosen/courses/{course}/materials/{material}/edit ................................................ dosen.courses.materials.edit › MaterialController@edit
  GET|HEAD        dosen/dashboard ................................................................................... dosen.dashboard › DashboardController@dosenDashboard
  GET|HEAD        dosen/grades .................................................................................................... dosen.grades.index › routes/web.php:64
  GET|HEAD        login ............................................................................................................ login › LoginController@showLoginForm
  POST            login ..................................................................................................................... LoginController@authenticate
  POST            logout ................................................................................................................. logout › LoginController@logout
  GET|HEAD        mahasiswa/courses ................................................................................ mahasiswa.courses.index › MahasiswaController@courses
  GET|HEAD        mahasiswa/courses/{course} ..................................................................... mahasiswa.courses.show › MahasiswaController@showCourse
  GET|HEAD        mahasiswa/courses/{course}/assignments ........................................... mahasiswa.courses.assignments.index › MahasiswaController@assignments
  GET|HEAD        mahasiswa/courses/{course}/assignments/{assignment}/submissions ...... mahasiswa.courses.assignments.submissions.index › MahasiswaController@submissions
  GET|HEAD        mahasiswa/courses/{course}/assignments/{assignment}/submissions/create mahasiswa.courses.assignments.submissions.create › MahasiswaController@createSub…
  GET|HEAD        mahasiswa/courses/{course}/materials ................................................. mahasiswa.courses.materials.index › MahasiswaController@materials
  GET|HEAD        mahasiswa/dashboard ................................................................................ mahasiswa.dashboard › MahasiswaController@dashboard
  GET|HEAD        switch-role/{role} ..................................................................................................... switch.role › routes/web.php:31
  GET|HEAD        tentang .................................................................................................................... tentang › routes/web.php:19
  GET|HEAD        users ............................................................................................................... users.index › UserController@index
  POST            users ............................................................................................................... users.store › UserController@store
  GET|HEAD        users/create ...................................................................................................... users.create › UserController@create
  GET|HEAD        users/{user} .......................................................................................................... users.show › UserController@show
  PUT|PATCH       users/{user} ...................................................................................................... users.update › UserController@update
  DELETE          users/{user} .................................................................................................... users.destroy › UserController@destroy
  GET|HEAD        users/{user}/edit ..................................................................................................... users.edit › UserController@edit

                                                                                                                                                      Showing [102] routes
```

#### 2. Route yang menerima parameter Model

- Admin: `/admin/users/{user}`, `/admin/courses/{course}`, `/admin/materials/{material}`, `/admin/assignments/{assignment}`.
- Dosen: `/dosen/courses/{course}` serta route turunannya dengan `{material}`, `{assignment}`, dan `{submission}`.
- Mahasiswa: `/mahasiswa/courses/{course}`, `/assignments/{assignment}`, dan route submission.
- Route umum: `/users/{user}`, `/courses/{course}` serta turunannya dengan `{material}`, `{assignment}`, dan `{submission}`.

#### 3–4. Daftar Titik Rawan IDOR

| Route/parameter                                | Yang seharusnya boleh mengakses                | Perlindungan saat ini / risiko                                                                                                                         |
| ---------------------------------------------- | ---------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `/admin/.../{user,course,material,assignment}` | Admin                                          | Prefix `admin` belum diberi middleware/otorisasi; berisiko diakses role lain.                                                                          |
| `/dosen/courses/{course}/...`                  | Dosen pengampu course                          | `scopeBindings()` mencocokkan relasi child-parent, tetapi tidak memastikan dosen adalah pengampu.                                                      |
| `/mahasiswa/courses/{course}/...`              | Mahasiswa yang terdaftar di course             | Belum ada pemeriksaan pendaftaran/role. Daftar submission juga belum difilter ke milik mahasiswa.                                                      |
| `/courses/...` dan `/users/{user}`             | Pengguna sesuai role dan kepemilikan data      | Route umum tidak memiliki middleware otorisasi; parameter ID dapat dicoba langsung.                                                                    |
| Route submission `{submission}`                | Pemilik submission, admin, atau dosen pengampu | `show`, `edit`, `update`, dan `destroy` memeriksa akses. `scopeBindings()` juga membatasi relasi; route lainnya belum memiliki perlindungan konsisten. |

**Kesimpulan:** `scopeBindings()` membatasi kecocokan relasi, bukan hak akses. Sebagian besar route berparameter masih rawan IDOR karena belum ada middleware atau pemeriksaan kepemilikan/role yang konsisten.