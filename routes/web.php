<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\AdminGradeController;

Route::get('/', fn () => view('welcome'));

Route::get('/tentang', fn () => view('tentang'))->name('tentang');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('courses', CourseController::class);

    Route::get('/materials', [MaterialController::class, 'adminIndex'])->name('materials.index');
    Route::get('/materials/create', [MaterialController::class, 'adminCreate'])->name('materials.create');
    Route::post('/materials', [MaterialController::class, 'adminStore'])->name('materials.store');
    Route::get('/materials/{material}/edit', [MaterialController::class, 'adminEdit'])->name('materials.edit');
    Route::put('/materials/{material}', [MaterialController::class, 'adminUpdate'])->name('materials.update');
    Route::delete('/materials/{material}', [MaterialController::class, 'adminDestroy'])->name('materials.destroy');

    Route::get('/assignments', [AssignmentController::class, 'adminIndex'])->name('assignments.index');
    Route::get('/assignments/create', [AssignmentController::class, 'adminCreate'])->name('assignments.create');
    Route::post('/assignments', [AssignmentController::class, 'adminStore'])->name('assignments.store');
    Route::get('/assignments/{assignment}/edit', [AssignmentController::class, 'adminEdit'])->name('assignments.edit');
    Route::put('/assignments/{assignment}', [AssignmentController::class, 'adminUpdate'])->name('assignments.update');
    Route::delete('/assignments/{assignment}', [AssignmentController::class, 'adminDestroy'])->name('assignments.destroy');

    Route::get('/grades', [AdminGradeController::class, 'index'])->name('grades.index');
});

/*
|--------------------------------------------------------------------------
| Route Dosen
|--------------------------------------------------------------------------
*/
Route::prefix('dosen')->name('dosen.')->scopeBindings()->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dosenDashboard'])->name('dashboard');

    Route::get('/grades', fn () => view('dosen.grades.index'))->name('grades.index');

    Route::get('/courses', [CourseController::class, 'dosenIndex'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'dosenShow'])->name('courses.show');

    Route::get('/courses/{course}/materials', [MaterialController::class, 'dosenIndex'])->name('courses.materials.index');
    Route::get('/courses/{course}/materials/create', [MaterialController::class, 'dosenCreate'])->name('courses.materials.create');
    Route::post('/courses/{course}/materials', [MaterialController::class, 'dosenStore'])->name('courses.materials.store');
    Route::get('/courses/{course}/materials/{material}/edit', [MaterialController::class, 'dosenEdit'])->name('courses.materials.edit');
    Route::put('/courses/{course}/materials/{material}', [MaterialController::class, 'dosenUpdate'])->name('courses.materials.update');
    Route::delete('/courses/{course}/materials/{material}', [MaterialController::class, 'dosenDestroy'])->name('courses.materials.destroy');

    Route::get('/courses/{course}/assignments', [AssignmentController::class, 'dosenIndex'])->name('courses.assignments.index');
    Route::get('/courses/{course}/assignments/create', [AssignmentController::class, 'dosenCreate'])->name('courses.assignments.create');
    Route::post('/courses/{course}/assignments', [AssignmentController::class, 'dosenStore'])->name('courses.assignments.store');
    Route::get('/courses/{course}/assignments/{assignment}/edit', [AssignmentController::class, 'dosenEdit'])->name('courses.assignments.edit');
    Route::put('/courses/{course}/assignments/{assignment}', [AssignmentController::class, 'dosenUpdate'])->name('courses.assignments.update');
    Route::delete('/courses/{course}/assignments/{assignment}', [AssignmentController::class, 'dosenDestroy'])->name('courses.assignments.destroy');
    Route::get('/courses/{course}/assignments/{assignment}/submissions', [SubmissionController::class, 'dosenIndex'])->name('courses.assignments.submissions.index');
});

/*
|--------------------------------------------------------------------------
| Route Mahasiswa
|--------------------------------------------------------------------------
*/
Route::prefix('mahasiswa')->name('mahasiswa.')->scopeBindings()->group(function () {
    Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
    Route::get('/courses', [MahasiswaController::class, 'courses'])->name('courses.index');
    Route::get('/courses/{course}', [MahasiswaController::class, 'showCourse'])->name('courses.show');
    Route::get('/courses/{course}/assignments', [MahasiswaController::class, 'assignments'])->name('courses.assignments.index');
    Route::get('/courses/{course}/materials', [MahasiswaController::class, 'materials'])->name('courses.materials.index');
    Route::get('/courses/{course}/assignments/{assignment}/submissions', [MahasiswaController::class, 'submissions'])->name('courses.assignments.submissions.index');
    Route::get('/courses/{course}/assignments/{assignment}/submissions/create', [MahasiswaController::class, 'createSubmission'])->name('courses.assignments.submissions.create');
});

/*
|--------------------------------------------------------------------------
| Route umum yang masih dipakai halaman simulasi/dashboard lama
|--------------------------------------------------------------------------
*/
Route::resource('courses', CourseController::class);
Route::resource('users', UserController::class);

Route::scopeBindings()->group(function () {
    Route::resource('courses.materials', MaterialController::class);
    Route::resource('courses.assignments', AssignmentController::class);
    Route::resource('courses.assignments.submissions', SubmissionController::class);
});