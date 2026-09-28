<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;

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