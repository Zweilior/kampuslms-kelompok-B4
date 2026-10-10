<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Support\Facades\Gate; // BARU

class AdminGradeController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Grade::class); // BARU

        $grades = Grade::with([
            'submission.student',
            'submission.assignment.course',
            'grader',
        ])
            ->orderByDesc('graded_at')
            ->paginate(15);

        $totalGrades = Grade::count();
        $averageScore = Grade::avg('score');

        return view('admin.grades.index', compact('grades', 'totalGrades', 'averageScore'));
    }
}