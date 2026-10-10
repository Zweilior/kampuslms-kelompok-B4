<?php

use App\Http\Controllers\AdminGradeController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseEnrollmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GradeComponentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::get('/tentang', fn () => view('tentang'))->name('tentang');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::get('forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('forgot-password', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [LoginController::class, 'resetPassword'])->name('password.update');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/dashboard', function () {
    $route = match (auth()->user()->role) {
        'admin' => 'admin.dashboard',
        'dosen' => 'dosen.dashboard',
        'mahasiswa' => 'mahasiswa.dashboard',
        default => 'login',
    };

    return redirect()->route($route);
})->middleware('auth')->name('dashboard');

Route::get('/courses', function () {
    $route = match (auth()->user()->role) {
        'admin' => 'admin.courses.index',
        'dosen' => 'dosen.courses.index',
        default => 'mahasiswa.courses.index',
    };

    return redirect()->route($route);
})->middleware('auth')->name('courses.legacy');

// Unduh file materi: boleh admin, dosen pemilik, atau mahasiswa terdaftar.
// Hak akses diputuskan MaterialPolicy@download di MaterialController::download.
Route::get('/courses/{course}/materials/{material}/download', [MaterialController::class, 'download'])
    ->middleware('auth')
    ->scopeBindings()
    ->name('courses.materials.download');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
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
Route::prefix('dosen')->name('dosen.')->middleware(['auth', 'role:dosen'])->scopeBindings()->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dosenDashboard'])->name('dashboard');

    Route::get('/grades', [CourseController::class, 'dosenGradesIndex'])->name('grades.index');

    Route::get('/courses', [CourseController::class, 'dosenIndex'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'dosenShow'])->name('courses.show');
    Route::get('/courses/{course}/edit', [CourseController::class, 'dosenEdit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'dosenUpdate'])->name('courses.update');
    Route::get('/courses/{course}/enrollments', [CourseEnrollmentController::class, 'dosenIndex'])->name('courses.enrollments.index');
    Route::post('/courses/{course}/enrollments', [CourseEnrollmentController::class, 'dosenStore'])->name('courses.enrollments.store');
    Route::delete('/courses/{course}/enrollments/{student}', [CourseEnrollmentController::class, 'dosenDestroy'])->name('courses.enrollments.destroy');

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
    Route::put('/courses/{course}/assignments/{assignment}/submissions/{submission}/grade', [SubmissionController::class, 'dosenGrade'])->name('courses.assignments.submissions.grade');

    // Tambahkan route ini di dalam group dosen, setelah route assignments/submissions
    Route::get('/courses/{course}/grade-components', [GradeComponentController::class, 'dosenIndex'])->name('courses.grade-components.index');
    Route::post('/courses/{course}/grade-components', [GradeComponentController::class, 'dosenStore'])->name('courses.grade-components.store');
    Route::put('/courses/{course}/grade-components/{gradeComponent}', [GradeComponentController::class, 'dosenUpdate'])->name('courses.grade-components.update');
    Route::delete('/courses/{course}/grade-components/{gradeComponent}', [GradeComponentController::class, 'dosenDestroy'])->name('courses.grade-components.destroy');
});

/*
|--------------------------------------------------------------------------
| Route Mahasiswa
|--------------------------------------------------------------------------
*/
Route::prefix('mahasiswa')->name('mahasiswa.')->middleware(['auth', 'role:mahasiswa'])->scopeBindings()->group(function () {
    Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
    Route::get('/courses', [MahasiswaController::class, 'courses'])->name('courses.index');
    Route::get('/monitoring-nilai', [MahasiswaController::class, 'monitoringGrades'])->name('grades.index');
    Route::get('/courses/{course}', [MahasiswaController::class, 'showCourse'])->name('courses.show');
    Route::get('/courses/{course}/grade-components', [MahasiswaController::class, 'gradeComponents'])->name('courses.grade-components.index');
    Route::get('/monitoring-nilai/{course}/grades', [MahasiswaController::class, 'grades'])->name('grades.courses.show');
    Route::get('/courses/{course}/grades', fn (string $course) => redirect()
        ->route('mahasiswa.grades.courses.show', ['course' => $course]))
        ->name('courses.grades.legacy');
    Route::get('/courses/{course}/assignments', [MahasiswaController::class, 'assignments'])->name('courses.assignments.index');
    Route::get('/courses/{course}/materials', [MahasiswaController::class, 'materials'])->name('courses.materials.index');
    Route::get('/courses/{course}/assignments/{assignment}/submissions', [MahasiswaController::class, 'submissions'])->name('courses.assignments.submissions.index');
    Route::get('/courses/{course}/assignments/{assignment}/submissions/create', [MahasiswaController::class, 'createSubmission'])->name('courses.assignments.submissions.create');
    Route::post('/courses/{course}/assignments/{assignment}/submissions', [SubmissionController::class, 'store'])->name('courses.assignments.submissions.store');
});