<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request; // <-- Tambahkan ini
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\MahasiswaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tentang', function () {
    return view('tentang');
})->name('tentang');

// --- ROUTE LOGIN (Tidak Perlu Auth) ---
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// --- ROUTE SWITCH ROLE (Untuk Simulasi Tanpa Login) ---
Route::get('/switch-role/{role}', function ($role) {
    // Validasi role yang diizinkan
    if (!in_array($role, ['admin', 'dosen', 'mahasiswa'])) {
        abort(404);
    }
    
    // Simpan role di session
    session(['simulated_role' => $role]);
    
    return redirect()->back(); // Kembali ke halaman sebelumnya
})->name('switch.role');

Route::prefix('mahasiswa')->name('mahasiswa.')->scopeBindings()->group(function () {
    Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])
        ->name('dashboard');
    Route::get('/courses', [MahasiswaController::class, 'courses'])
        ->name('courses.index');
    Route::get('/courses/{course}', [MahasiswaController::class, 'showCourse'])
        ->name('courses.show');
    Route::get('/courses/{course}/assignments', [MahasiswaController::class, 'assignments'])
        ->name('courses.assignments.index');
    Route::get('/courses/{course}/materials', [MahasiswaController::class, 'materials'])
        ->name('courses.materials.index');
    Route::get('/courses/{course}/assignments/{assignment}/submissions', [MahasiswaController::class, 'submissions'])
        ->name('courses.assignments.submissions.index');
    Route::get('/courses/{course}/assignments/{assignment}/submissions/create', [MahasiswaController::class, 'createSubmission'])
        ->name('courses.assignments.submissions.create');
});


Route::resource('courses', CourseController::class);
Route::resource('users', UserController::class);

Route::scopeBindings()->group(function () {
    Route::resource('courses.materials', MaterialController::class);
});

Route::scopeBindings()->group(function () {
    Route::resource('courses.assignments', AssignmentController::class);
});
Route::scopeBindings()->group(function () {
    Route::resource(
        'courses.assignments.submissions',
        SubmissionController::class
    );
});
Route::scopeBindings()->group(function () {
    Route::resource(
        'courses.assignments.submissions',
        SubmissionController::class
    );
});
