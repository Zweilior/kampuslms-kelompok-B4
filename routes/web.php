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
