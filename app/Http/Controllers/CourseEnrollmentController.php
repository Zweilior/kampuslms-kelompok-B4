<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CourseEnrollmentController extends Controller
{
    public function adminStore(Request $request, Course $course)
    {
        return $this->storeEnrollment(
            $request,
            $course,
            'admin.courses.show'
        );
    }

    public function adminDestroy(Course $course, User $student)
    {
        return $this->destroyEnrollment(
            $course,
            $student,
            'admin.courses.show'
        );
    }

    public function dosenIndex(Request $request, Course $course)
    {
        Gate::authorize('manageEnrollment', $course);

        $students = $course->students()
            ->where('users.role', 'mahasiswa')
            ->orderBy('users.name')
            ->get(['users.*']);
        $availableStudentsQuery = User::query()
            ->where('role', 'mahasiswa')
            ->whereDoesntHave('courses', fn ($query) => $query->where('courses.id', $course->id));

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $availableStudentsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('nim_nip', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $availableStudents = $availableStudentsQuery
            ->orderBy('name')
            ->paginate(50)
            ->withQueryString();

        return view('dosen.courses.enrollments', compact(
            'course',
            'students',
            'availableStudents'
        ));
    }

    public function dosenStore(Request $request, Course $course)
    {
        return $this->storeEnrollment(
            $request,
            $course,
            'dosen.courses.enrollments.index'
        );
    }

    public function dosenDestroy(Course $course, User $student)
    {
        return $this->destroyEnrollment(
            $course,
            $student,
            'dosen.courses.enrollments.index'
        );
    }

    private function storeEnrollment(Request $request, Course $course, string $redirectRoute)
    {
        Gate::authorize('manageEnrollment', $course);

        $validated = $request->validate([
            'student_ids' => ['required', 'array', 'min:1', 'max:50'],
            'student_ids.*' => [
                'required',
                Rule::exists('users', 'id')->where('role', 'mahasiswa'),
            ],
        ], [
            'student_ids.required' => 'Pilih setidaknya satu mahasiswa yang akan didaftarkan.',
            'student_ids.min' => 'Pilih setidaknya satu mahasiswa yang akan didaftarkan.',
            'student_ids.max' => 'Paling banyak 50 mahasiswa dapat didaftarkan sekaligus.',
            'student_ids.*.exists' => 'Mahasiswa yang dipilih tidak valid.',
        ]);

        $enrollments = collect($validated['student_ids'])
            ->mapWithKeys(fn ($studentId) => [$studentId => ['enrolled_at' => now()]])
            ->all();
        $course->students()->syncWithoutDetaching($enrollments);

        return redirect()
            ->route($redirectRoute, $course)
            ->with('success', 'Mahasiswa berhasil didaftarkan ke mata kuliah.');
    }

    private function destroyEnrollment(Course $course, User $student, string $redirectRoute)
    {
        Gate::authorize('manageEnrollment', $course);

        abort_unless(
            $student->role === 'mahasiswa'
                && $course->students()->whereKey($student->id)->exists(),
            404
        );

        $hasSubmission = Submission::query()
            ->where('user_id', $student->id)
            ->whereHas('assignment', fn ($query) => $query->where('course_id', $course->id))
            ->exists();

        $hasFinalGrade = $course->finalGrades()
            ->where('user_id', $student->id)
            ->exists();

        if ($hasSubmission || $hasFinalGrade) {
            return redirect()
                ->route($redirectRoute, $course)
                ->with('error', 'Mahasiswa tidak dapat dikeluarkan karena masih memiliki riwayat pengumpulan tugas atau nilai pada mata kuliah ini.');
        }

        $course->students()->detach($student->id);

        return redirect()
            ->route($redirectRoute, $course)
            ->with('success', 'Mahasiswa berhasil dikeluarkan dari mata kuliah.');
    }
}
