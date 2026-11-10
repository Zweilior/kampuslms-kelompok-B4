<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CourseEnrollmentController extends Controller
{
    public function adminStore(Request $request, Course $course)
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
            ->route('admin.courses.show', $course)
            ->with('success', 'Mahasiswa berhasil didaftarkan ke mata kuliah.');
    }

    public function adminDestroy(Course $course, User $student)
    {
        Gate::authorize('manageEnrollment', $course);

        abort_unless(
            $student->role === 'mahasiswa'
                && $course->students()->whereKey($student->id)->exists(),
            404
        );

        $course->students()->detach($student->id);

        return redirect()
            ->route('admin.courses.show', $course)
            ->with('success', 'Mahasiswa berhasil dikeluarkan dari mata kuliah.');
    }

    public function dosenIndex(Course $course)
    {
        Gate::authorize('manageEnrollment', $course);

        $students = $course->students()
            ->where('users.role', 'mahasiswa')
            ->orderBy('users.name')
            ->get(['users.*']);
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
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => [
                'required',
                Rule::exists('users', 'id')->where('role', 'mahasiswa'),
            ],
        ], [
            'student_ids.required' => 'Pilih setidaknya satu mahasiswa yang akan didaftarkan.',
            'student_ids.min' => 'Pilih setidaknya satu mahasiswa yang akan didaftarkan.',
            'student_ids.*.exists' => 'Mahasiswa yang dipilih tidak valid.',
        ]);

        $enrollments = collect($validated['student_ids'])
            ->mapWithKeys(fn ($studentId) => [$studentId => ['enrolled_at' => now()]])
            ->all();
        $course->students()->syncWithoutDetaching($enrollments);

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
