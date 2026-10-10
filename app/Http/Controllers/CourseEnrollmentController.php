<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CourseEnrollmentController extends Controller
{
    public function dosenIndex(Course $course)
    {
        Gate::authorize('manageEnrollment', $course);

        $students = $course->students()
            ->where('users.role', 'mahasiswa')
            ->orderBy('users.name')
            ->get();
        $availableStudents = User::query()
            ->where('role', 'mahasiswa')
            ->whereDoesntHave('courses', fn ($query) => $query->where('courses.id', $course->id))
            ->orderBy('name')
            ->get();

        return view('dosen.courses.enrollments', compact(
            'course',
            'students',
            'availableStudents'
        ));
    }

    public function dosenStore(Request $request, Course $course)
    {
        Gate::authorize('manageEnrollment', $course);

        $validated = $request->validate([
            'student_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', 'mahasiswa'),
            ],
        ], [
            'student_id.required' => 'Pilih mahasiswa yang akan didaftarkan.',
            'student_id.exists' => 'Mahasiswa yang dipilih tidak valid.',
        ]);

        $course->students()->syncWithoutDetaching([
            $validated['student_id'] => ['enrolled_at' => now()],
        ]);

        return redirect()
            ->route('dosen.courses.enrollments.index', $course)
            ->with('success', 'Mahasiswa berhasil didaftarkan ke mata kuliah.');
    }

    public function dosenDestroy(Course $course, User $student)
    {
        Gate::authorize('manageEnrollment', $course);

        abort_unless(
            $student->role === 'mahasiswa'
                && $course->students()->whereKey($student->id)->exists(),
            404
        );

        $course->students()->detach($student->id);

        return redirect()
            ->route('dosen.courses.enrollments.index', $course)
            ->with('success', 'Mahasiswa berhasil dikeluarkan dari mata kuliah.');
    }
}
