<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;

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
            ->whereHas('assignment', fn ($query) => $query
                ->where('status', 'published')
                ->where('due_at', '<=', now()))
            ->whereDoesntHave('grade')
            ->count();
        $gradingAssignments = Assignment::query()
            ->with('course')
            ->withCount([
                'submissions as ungraded_submissions_count' => fn ($query) => $query->whereDoesntHave('grade'),
            ])
            ->whereHas('course', fn ($query) => $query->where('lecturer_id', $lecturerId))
            ->where('status', 'published')
            ->where('due_at', '<=', now())
            ->whereHas('submissions', fn ($query) => $query->whereDoesntHave('grade'))
            ->orderBy('due_at')
            ->limit(5)
            ->get();

        return view('dosen.dashboard', compact(
            'totalCourses',
            'openAssignments',
            'ungradedSubmissions',
            'gradingAssignments'
        ));
    }
}
