<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\User;
use App\Support\CourseGradeCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Cek apakah request datang dari route area admin (admin.courses.*).
     */
    private function isAdminRoute(): bool
    {
        return request()->routeIs('admin.*');
    }

    /**
     * Prefix nama route sesuai area (admin.courses atau courses).
     */
    private function routePrefix(): string
    {
        return $this->isAdminRoute() ? 'admin.courses' : 'courses';
    }

    /**
     * Menampilkan daftar mata kuliah.
     * [POIN 4] Disaring di level query sesuai peran:
     * - Admin: Melihat semua MK.
     * - Dosen: Hanya MK yang diajar.
     * - Mahasiswa: Hanya MK yang diikuti.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Course::class);

        $user = $request->user(); // Ambil user yang sedang login
        $query = Course::with('lecturer');

        // --- FILTER BERDASARKAN PERAN (POIN 4) ---
        if ($user->role === 'dosen') {
            $query->where('lecturer_id', $user->id);
        } elseif ($user->role === 'mahasiswa') {
            $query->whereHas('students', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }
        // Jika admin, tidak difilter (boleh lihat semua)

        // Pencarian berdasarkan kode atau nama mata kuliah
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
                $q->where('code', 'like', "{$search}%")
                  ->orWhere('name', 'like', "{$search}%");
            });
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $courses = $query
            ->orderBy('code')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        // Data dosen untuk form tambah/edit (hanya admin yang butuh ini, tapi aman dibiarkan)
        $lecturers = User::where('role', 'dosen')
            ->orderBy('name')
            ->get();

        return view($this->routePrefix().'.index', compact('courses', 'lecturers'));
    }

    /**
     * Menampilkan form tambah mata kuliah.
     */
    public function create()
    {
        Gate::authorize('create', Course::class);

        $lecturers = User::where('role', 'dosen')
            ->orderBy('name')
            ->get();

        return view($this->isAdminRoute() ? 'admin.courses.create' : 'courses.create', compact('lecturers'));
    }

    /**
     * Menyimpan mata kuliah baru.
     */
    public function store(StoreCourseRequest $request)
    {
        Gate::authorize('create', Course::class);

        $data = $request->validated();
        $data['description'] = $data['description'] ?? '';
        $course = Course::create($data);

        return redirect()
            ->route($this->routePrefix().'.show', $course)
            ->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail mata kuliah.
     */
    public function show(Course $course)
    {
        Gate::authorize('view', $course);

        $course->load('lecturer');

        $lecturers = User::where('role', 'dosen')
            ->orderBy('name')
            ->get();

        if ($this->isAdminRoute()) {
            $students = $course->students()
                ->where('users.role', 'mahasiswa')
                ->orderBy('users.name')
                ->get();
            $course->load([
                'materials' => fn ($query) => $query->orderByRaw('LOWER(title)')->orderBy('id'),
                'assignments' => fn ($query) => $query
                    ->withCount('submissions')
                    ->orderByRaw('LOWER(title)')
                    ->orderBy('id'),
            ]);
            $finalGrades = $course->finalGrades()
                ->whereIn('user_id', $students->modelKeys())
                ->get()
                ->keyBy('user_id');
            $availableStudents = User::query()
                ->where('role', 'mahasiswa')
                ->whereDoesntHave('courses', fn ($query) => $query->where('courses.id', $course->id))
                ->orderBy('name')
                ->get();

            return view('admin.courses.show', compact(
                'course',
                'lecturers',
                'students',
                'finalGrades',
                'availableStudents'
            ));
        }

        return view('courses.show', compact('course', 'lecturers'));
    }

    /**
     * Daftar MK milik dosen yang sedang login.
     */
    public function dosenIndex(Request $request)
    {
        Gate::authorize('viewAny', Course::class);

        $courses = Course::where('lecturer_id', $request->user()->id)
            ->with('lecturer')
            ->whereIn('status', ['active', 'archived'])
            ->orderBy('name')
            ->get();

        $activeCourses = $courses->where('status', 'active')->values();
        $archivedCourses = $courses->where('status', 'archived')->values();

        return view('dosen.courses.index', compact('activeCourses', 'archivedCourses'));
    }

    /**
     * Daftar nilai MK milik dosen yang sedang login.
     */
    public function dosenGradesIndex(Request $request)
    {
        Gate::authorize('viewAny', Course::class);

        $courses = Course::where('lecturer_id', $request->user()->id)
            ->withCount('students')
            ->with([
                'students:id',
                'assignments.gradeComponent',
                'assignments.submissions.grade',
            ])
            ->orderBy('name')
            ->get();

        $courses->each(function (Course $course): void {
            $studentScores = $course->students
                ->map(function (User $student) use ($course): ?float {
                    $assignments = $course->assignments->map(function ($assignment) use ($student) {
                        $studentAssignment = clone $assignment;
                        $studentAssignment->setRelation(
                            'submissions',
                            $assignment->submissions
                                ->where('user_id', $student->id)
                                ->values()
                        );

                        return $studentAssignment;
                    });

                    return CourseGradeCalculator::finalScore($assignments);
                })
                ->filter(fn (?float $score): bool => $score !== null)
                ->values();

            $course->setAttribute(
                'monitoring_average',
                $studentScores->isNotEmpty() ? round($studentScores->avg(), 2) : null
            );
            $course->setAttribute('monitoring_graded_students_count', $studentScores->count());
        });

        $activeCourses = $courses->where('status', 'active')->values();
        $archivedCourses = $courses->where('status', 'archived')->values();

        return view('dosen.grades.index', compact(
            'activeCourses',
            'archivedCourses'
        ));
    }

    /**
     * Detail MK untuk dosen pemilik.
     */
    public function dosenShow(Course $course)
    {
        Gate::authorize('view', $course);

        return view('dosen.courses.show', compact('course'));
    }

    public function dosenEdit(Course $course)
    {
        Gate::authorize('update', $course);

        return view('dosen.courses.edit', compact('course'));
    }

    public function dosenUpdate(Request $request, Course $course)
    {
        Gate::authorize('update', $course);

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('courses', 'code')->ignore($course),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sks' => ['required', 'integer', 'between:1,6'],
        ]);

        $course->update([
            ...$validated,
            'description' => $validated['description'] ?? '',
        ]);

        return redirect()
            ->route('dosen.courses.show', $course)
            ->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    /**
     * Menampilkan form edit mata kuliah.
     */
    public function edit(Course $course)
    {
        Gate::authorize('update', $course);

        $lecturers = User::where('role', 'dosen')
            ->orderBy('name')
            ->get();

        return view($this->isAdminRoute() ? 'admin.courses.edit' : 'courses.edit', compact('course', 'lecturers'));
    }

    /**
     * Memperbarui mata kuliah.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        Gate::authorize('update', $course);

        $data = $request->validated();
        $data['description'] = $data['description'] ?? '';
        $course->update($data);

        return redirect()
            ->route($this->routePrefix().'.show', $course)
            ->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    /**
     * Menghapus mata kuliah.
     */
    public function destroy(Course $course)
    {
        Gate::authorize('delete', $course);

        $course->delete();

        return redirect()
            ->route($this->routePrefix().'.index')
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
