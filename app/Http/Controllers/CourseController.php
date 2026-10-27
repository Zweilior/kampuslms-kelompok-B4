<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\FinalGrade;
use App\Models\User;
use App\Support\CourseGradeCalculator;
use App\Support\GradeScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use App\Models\Submission;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    private function isAdminRoute(): bool
    {
        return request()->routeIs('admin.*');
    }

    private function routePrefix(): string
    {
        return $this->isAdminRoute() ? 'admin.courses' : 'courses';
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Course::class);

        $user = $request->user();
        $query = Course::with('lecturer');

        if ($user->role === 'dosen') {
            $query->where('lecturer_id', $user->id);
        } elseif ($user->role === 'mahasiswa') {
            $query->whereHas('students', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
                $q->where('code', 'like', "{$search}%")
                  ->orWhere('name', 'like', "{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $courses = $query
            ->orderBy('code')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        $lecturers = User::where('role', 'dosen')
            ->orderBy('name')
            ->get();

        return view($this->routePrefix().'.index', compact('courses', 'lecturers'));
    }

    public function create()
    {
        Gate::authorize('create', Course::class);

        $lecturers = User::where('role', 'dosen')
            ->orderBy('name')
            ->get();

        return view($this->isAdminRoute() ? 'admin.courses.create' : 'courses.create', compact('lecturers'));
    }

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
                            $assignment->submissions->where('user_id', $student->id)->values()
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

    public function dosenGradesShow(Course $course)
    {
        Gate::authorize('view', $course);

        $course->load([
            'students' => fn ($query) => $query->orderBy('nim_nip'),
            'assignments.gradeComponent',
            'assignments.submissions.grade'
        ]);

        foreach ($course->students as $student) {
            $assignments = $course->assignments->map(function ($assignment) use ($student) {
                $studentAssignment = clone $assignment;
                $studentAssignment->setRelation(
                    'submissions',
                    $assignment->submissions->where('user_id', $student->id)->values()
                );
                return $studentAssignment;
            });

            $score = CourseGradeCalculator::finalScore($assignments);

            if ($score !== null) {
                FinalGrade::updateOrCreate(
                    ['course_id' => $course->id, 'user_id' => $student->id],
                    [
                        'total_score' => $score,
                        'letter_grade' => GradeScale::letterGradeForScore($score),
                    ]
                );
            }
        }

        $students = $course->students;
        $finalGrades = $course->finalGrades()->get()->keyBy('user_id');

        return view('dosen.grades.show', compact('course', 'students', 'finalGrades'));
    }

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

    public function edit(Course $course)
    {
        Gate::authorize('update', $course);

        $lecturers = User::where('role', 'dosen')
            ->orderBy('name')
            ->get();

        return view($this->isAdminRoute() ? 'admin.courses.edit' : 'courses.edit', compact('course', 'lecturers'));
    }

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

    public function destroy(Course $course)
    {
        Gate::authorize('delete', $course);

        $hasSubmissions = Submission::whereHas(
            'assignment',
            fn ($q) => $q->where('course_id', $course->id)
        )->exists();

        if ($hasSubmissions) {
            return back()->with('error', 'Mata kuliah tidak dapat dihapus karena sudah memiliki pengumpulan tugas.');
        }

        $paths = $course->materials()->whereNotNull('file_path')->pluck('file_path');

        $course->delete();

        Storage::disk('local')->delete($paths->all());

        return redirect()
            ->route($this->routePrefix().'.index')
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }
}