<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung total data mata kuliah
        $totalCourses   = Course::count();
        $activeCourses  = Course::where('status', 'active')->count();
        $draftCourses   = Course::where('status', 'draft')->count();

        // Hitung total dosen pengampu unik dari tabel courses
        $totalLecturers = Course::whereNotNull('lecturer_id')->distinct('lecturer_id')->count();

        // Ambil 5 mata kuliah terbaru beserta relasi lecturer (User)
        $recentCourses  = Course::with('lecturer')->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalCourses',
            'activeCourses',
            'draftCourses',
            'totalLecturers',
            'recentCourses'
        ));
    }
}