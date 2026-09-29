<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    /**
     * Menampilkan daftar tugas dari suatu mata kuliah.
     */
    public function index(Course $course)
    {
        $assignments = $course->assignments()
            ->latest()
            ->paginate(15);

        return view('assignments.index', compact('course', 'assignments'));
    }

    public function adminIndex(Request $request)
    {
        $query = Assignment::with('course')->withCount('submissions');

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($assignmentQuery) use ($search) {
                $assignmentQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('instructions', 'like', "%{$search}%")
                    ->orWhereHas('course', function ($courseQuery) use ($search) {
                        $courseQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->input('course_id'));
        }

        $assignments = $query->orderBy('due_at')->orderByDesc('id')->paginate(15)->withQueryString();
        $courses = Course::query()->orderBy('name')->get(['id', 'name']);
        $totalAssignments = Assignment::count();

        return view('admin.assignments.index', compact('assignments', 'courses', 'totalAssignments'));
    }

    public function adminCreate()
    {
        $courses = Course::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.assignments.form', ['assignment' => null, 'courses' => $courses]);
    }

    public function adminStore(Request $request)
    {
        $validated = $request->validate($this->adminAssignmentRules());
        $creatorId = Auth::id() ?? User::where('role', 'admin')->value('id');

        if (!$creatorId) {
            return back()->withErrors(['created_by' => 'Akun admin tidak ditemukan.'])->withInput();
        }

        $assignment = new Assignment($validated);
        $assignment->created_by = $creatorId;
        $assignment->save();

        return redirect()->route('admin.assignments.index')->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function adminEdit(Assignment $assignment)
    {
        $courses = Course::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.assignments.form', compact('assignment', 'courses'));
    }

    public function adminUpdate(Request $request, Assignment $assignment)
    {
        $assignment->update($request->validate($this->adminAssignmentRules()));

        return redirect()->route('admin.assignments.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    public function adminDestroy(Assignment $assignment)
    {
        if ($assignment->submissions()->exists()) {
            return redirect()->route('admin.assignments.index')
                ->with('error', 'Tugas tidak dapat dihapus karena sudah memiliki submission.');
        }

        $assignment->delete();

        return redirect()->route('admin.assignments.index')->with('success', 'Tugas berhasil dihapus.');
    }

    private function adminAssignmentRules(): array
    {
        return [
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string'],
            'due_at' => ['required', 'date'],
            'max_score' => ['required', 'integer', 'min:0', 'max:255'],
            'allow_late' => ['required', 'boolean'],
            'status' => ['required', 'in:draft,published'],
        ];
    }

    /**
     * Menampilkan form tambah tugas.
     */
    public function create(Course $course)
    {
        return view('assignments.create', compact('course'));
    }

    /**
     * Menyimpan tugas baru.
     */
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'due_at' => ['required', 'date'],
            'max_score' => ['required', 'integer', 'min:0'],
            'allow_late' => ['required', 'boolean'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        $course->assignments()->create($validated);

        return redirect()
            ->route('courses.assignments.index', $course)
            ->with('success', 'Tugas berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail tugas.
     */
    public function show(Course $course, Assignment $assignment)
    {
        return view('assignments.show', compact('course', 'assignment'));
    }

    /**
     * Menampilkan form edit tugas.
     */
    public function edit(Course $course, Assignment $assignment)
    {
        return view('assignments.edit', compact('course', 'assignment'));
    }

    /**
     * Memperbarui tugas.
     */
    public function update(
        Request $request,
        Course $course,
        Assignment $assignment
    ) {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'due_at' => ['required', 'date'],
            'max_score' => ['required', 'integer', 'min:0'],
            'allow_late' => ['required', 'boolean'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        $assignment->update($validated);

        return redirect()
            ->route('courses.assignments.show', [$course, $assignment])
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    /**
     * Menghapus tugas.
     */
    public function destroy(Course $course, Assignment $assignment)
    {
        $assignment->delete();

        return redirect()
            ->route('courses.assignments.index', $course)
            ->with('success', 'Tugas berhasil dihapus.');
    }
}