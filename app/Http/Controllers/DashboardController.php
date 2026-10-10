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