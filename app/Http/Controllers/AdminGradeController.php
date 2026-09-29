<?php

namespace App\Http\Controllers;

use App\Models\Grade;

class AdminGradeController extends Controller
{
    public function index()
    {
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