<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        return view('mahasiswa.dashboard');
    }

    public function courses()
    {
        $courses = Course::with('lecturer')
            ->orderBy('code')
            ->get();

        return view('mahasiswa.courses.index', compact('courses'));
    }

    public function showCourse(Course $course)
    {
        $course->load('lecturer');

        return view('mahasiswa.courses.show', compact('course'));
    }

    public function assignments(Course $course)
    {
        $assignments = $course->assignments()
            ->latest()
            ->get();

        return view('mahasiswa.assignments.index', compact(
            'course',
            'assignments'
        ));
    }

    public function materials(Course $course)
    {
        $materials = $course->materials()
            ->latest()
            ->get();

        return view('mahasiswa.materials.index', compact(
            'course',
            'materials'
        ));
    }

    public function submissions(Course $course, Assignment $assignment)
    {
        $studentId = auth()->id() ?? User::where('role', 'mahasiswa')->value('id');
        $submissions = $assignment->submissions()
            ->where('user_id', $studentId)
            ->with('grade')
            ->latest()
            ->get()
            ->each(function (Submission $submission) {
                $submission->setAttribute('score', $submission->grade?->score);
            });

        return view('mahasiswa.submissions.index', compact(
            'course',
            'assignment',
            'submissions'
        ));
    }

    public function createSubmission(Course $course, Assignment $assignment)
    {
        return view('mahasiswa.submissions.create', compact(
            'course',
            'assignment'
        ));
    }
}