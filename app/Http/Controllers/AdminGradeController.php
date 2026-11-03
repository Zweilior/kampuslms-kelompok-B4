<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Support\Facades\Gate;

class AdminGradeController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Grade::class);

        $grades = Grade::with([
            'submission.student',
            'submission.assignment.course',
            'grader',
        ])
            ->orderByDesc('graded_at')
            ->paginate(15);

        // Optimasi: Gunakan $grades->total() bawaan paginate alih-alih query count() baru
        $totalGrades = $grades->total();
        $averageScore = Grade::avg('score');

        return view('admin.grades.index', compact('grades', 'totalGrades', 'averageScore'));
    }
}