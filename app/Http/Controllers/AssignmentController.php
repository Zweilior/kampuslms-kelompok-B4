<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate; 
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{
    /**
     * Menampilkan daftar tugas dari suatu mata kuliah.
     */
    public function index(Course $course)
    {
        Gate::authorize('viewAny', [Assignment::class, $course]); // BARU

        $assignments = $course->assignments()
            ->latest()
            ->paginate(15);

        return view('assignments.index', compact('course', 'assignments'));
    }

    /**
     * [ADMIN] Menampilkan daftar semua tugas (Global).
     * CATATAN: Keamanan bergantung pada middleware 'role:admin'.
     */
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

    /**
     * [ADMIN] Form tambah tugas.
     * CATATAN: Keamanan bergantung pada middleware 'role:admin'.
     */
    public function adminCreate()
    {
        $courses = Course::query()->orderBy('name')->get(['id', 'name']);
        return view('admin.assignments.form', ['assignment' => null, 'courses' => $courses]);
    }

    /**
     * [ADMIN] Simpan tugas baru.
     */
    public function adminStore(Request $request)
    {
        // 1. Validasi dulu agar error 'course_id tidak ada' melempar 422, bukan 404
        $validated = $request->validate($this->adminAssignmentRules());

        // 2. Ambil course tujuan untuk otorisasi
        $course = Course::findOrFail($validated['course_id']);
        
        // 3. Cek hak akses
        Gate::authorize('create', [Assignment::class, $course]); // BARU

        $creatorId = Auth::id() ?? User::where('role', 'admin')->value('id');
        if (!$creatorId) {
            return back()->withErrors(['created_by' => 'Akun admin tidak ditemukan.'])->withInput();
        }

        $assignment = new Assignment($validated);
        $assignment->created_by = $creatorId;
        $assignment->save();

        return redirect()->route('admin.assignments.index')->with('success', 'Tugas berhasil ditambahkan.');
    }

    /**
     * [ADMIN] Edit tugas.
     */
    public function adminEdit(Assignment $assignment)
    {
        Gate::authorize('update', $assignment); // BARU

        $courses = Course::query()->orderBy('name')->get(['id', 'name']);
        return view('admin.assignments.form', compact('assignment', 'courses'));
    }

    /**
     * [ADMIN] Update tugas.
     */
    public function adminUpdate(Request $request, Assignment $assignment)
    {
        Gate::authorize('update', $assignment); // BARU

        $assignment->update($request->validate($this->adminAssignmentRules()));
        return redirect()->route('admin.assignments.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    /**
     * [ADMIN] Hapus tugas.
     */
    public function adminDestroy(Assignment $assignment)
    {
        Gate::authorize('delete', $assignment); // BARU

        // Keputusan Q6: Blokir hapus jika sudah ada submission
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

    // --------------------------------------------------------------------------
    // DOSEN METHODS
    // --------------------------------------------------------------------------

    public function dosenIndex(Course $course)
    {
        Gate::authorize('viewAny', [Assignment::class, $course]); // BARU (Menggantikan ensureLecturerOwnsCourse)

        $assignments = $course->assignments()
            ->withCount('submissions')
            ->orderByDesc('created_at')
            ->get();
        
        $studentCount = $course->students()->count();

        return view('dosen.assignments.index', compact('course', 'assignments', 'studentCount'));
    }

    public function dosenCreate(Course $course)
    {
        Gate::authorize('create', [Assignment::class, $course]); // BARU

        $gradeComponents = $course->gradeComponents()
            ->orderBy('name')
            ->get();

        return view('dosen.assignments.create', compact('course', 'gradeComponents'));
    }

    public function dosenStore(Request $request, Course $course)
    {
        Gate::authorize('create', [Assignment::class, $course]); // BARU

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'grade_component_id' => [
                'nullable',
                Rule::exists('grade_components', 'id')->where('course_id', $course->id),
            ],
            'due_at' => ['required', 'date'],
            'instructions' => ['nullable', 'string'],
            'max_score' => ['required', 'integer', 'min:0', 'max:255'],
            'allow_late' => ['required', 'boolean'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $course->assignments()->create([
            ...$validated,
            'created_by' => auth()->id() ?? User::where('role', 'dosen')->value('id'),
        ]);

        return redirect()->route('dosen.courses.assignments.index', $course)
            ->with('success', 'Tugas berhasil dibuat.');
    }

    public function dosenEdit(Course $course, Assignment $assignment)
    {
        Gate::authorize('update', $assignment); // BARU

        $gradeComponents = $course->gradeComponents()
            ->orderBy('name')
            ->get();

        return view('dosen.assignments.edit', compact('course', 'assignment', 'gradeComponents'));
    }

    public function dosenUpdate(Request $request, Course $course, Assignment $assignment)
    {
        Gate::authorize('update', $assignment); // BARU

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'grade_component_id' => [
                'nullable',
                Rule::exists('grade_components', 'id')->where('course_id', $course->id),
            ],
            'due_at' => ['required', 'date'],
            'instructions' => ['nullable', 'string'],
            'max_score' => ['required', 'integer', 'min:0', 'max:255'],
            'allow_late' => ['required', 'boolean'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $assignment->update($validated);

        return redirect()->route('dosen.courses.assignments.index', $course)
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    public function dosenDestroy(Course $course, Assignment $assignment)
    {
        Gate::authorize('delete', $assignment); // BARU

        // Keputusan Q6: Blokir hapus jika sudah ada submission
        if ($assignment->submissions()->exists()) {
            return back()->with('error', 'Tugas tidak dapat dihapus karena sudah memiliki submission.');
        }

        $assignment->delete();

        return redirect()->route('dosen.courses.assignments.index', $course)
            ->with('success', 'Tugas berhasil dihapus.');
    }

    // --------------------------------------------------------------------------
    // STANDARD METHODS (Legacy/General)
    // --------------------------------------------------------------------------

    /**
     * Menampilkan form tambah tugas.
     */
    public function create(Course $course)
    {
        Gate::authorize('create', [Assignment::class, $course]); // BARU
        return view('assignments.create', compact('course'));
    }

    /**
     * Menyimpan tugas baru.
     */
    public function store(Request $request, Course $course)
    {
        Gate::authorize('create', [Assignment::class, $course]); // BARU

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
        Gate::authorize('view', $assignment); // BARU
        return view('assignments.show', compact('course', 'assignment'));
    }

    /**
     * Menampilkan form edit tugas.
     */
    public function edit(Course $course, Assignment $assignment)
    {
        Gate::authorize('update', $assignment); // BARU
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
        Gate::authorize('update', $assignment); // BARU

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
        Gate::authorize('delete', $assignment); // BARU
        
        $assignment->delete();

        return redirect()
            ->route('courses.assignments.index', $course)
            ->with('success', 'Tugas berhasil dihapus.');
    }
}