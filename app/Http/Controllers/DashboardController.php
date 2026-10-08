<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $totalUsers = User::count();
        $totalLecturers = User::where('role', 'dosen')->count();
        $totalStudents = User::where('role', 'mahasiswa')->count();
        $totalCourses = Course::count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalLecturers',
            'totalStudents',
            'totalCourses'
        ));
    }

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

    public function dosenDashboard()
    {
        $lecturerId = auth()->id();
        $totalCourses = Course::where('lecturer_id', $lecturerId)->count();
        $openAssignments = Assignment::whereHas('course', function ($query) use ($lecturerId) {
            $query->where('lecturer_id', $lecturerId);
        })
            ->where('status', 'published')
            ->where('due_at', '>=', now())
            ->count();
        $ungradedSubmissions = Submission::whereHas('assignment.course', function ($query) use ($lecturerId) {
            $query->where('lecturer_id', $lecturerId);
        })
            ->whereDoesntHave('grade')
            ->count();

        return view('dosen.dashboard', compact(
            'totalCourses',
            'openAssignments',
            'ungradedSubmissions'
        ));
    }
}