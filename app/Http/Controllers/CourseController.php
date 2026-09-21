<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;

class CourseController extends Controller
{
    /**
     * Menampilkan daftar semua mata kuliah.
     */
    public function index()
    {
        $courses = Course::with('lecturer')
            ->orderBy('code')
            ->get();

        // Ambil data dosen untuk modal tambah mata kuliah
        $lecturers = User::where('role', 'dosen')
            ->orderBy('name')
            ->get();

        return view('courses.index', compact('courses', 'lecturers'));
    }

    /**
     * Menampilkan form tambah mata kuliah.
     */
    public function create()
    {
        $lecturers = User::where('role', 'dosen')
            ->orderBy('name')
            ->get();

        return view('courses.create', compact('lecturers'));
    }

    /**
     * Menyimpan mata kuliah baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:courses,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'sks' => ['required', 'integer', 'min:1'],
            'lecturer_id' => ['required', 'exists:users,id'],
            'status' => ['required', 'in:draft,active,archived'],
        ]);

        $course = Course::create($validated);

        return redirect()
            ->route('courses.show', $course)
            ->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail mata kuliah.
     */
    public function show(Course $course)
    {
        $course->load('lecturer');

        // Ambil data dosen agar dropdown dosen di modal edit terisi
        $lecturers = User::where('role', 'dosen')
            ->orderBy('name')
            ->get();

        return view('courses.show', compact('course', 'lecturers'));
    }

    /**
     * Menampilkan form edit mata kuliah.
     */
    public function edit(Course $course)
    {
        $lecturers = User::where('role', 'dosen')
            ->orderBy('name')
            ->get();

        return view('courses.edit', compact('course', 'lecturers'));
    }

    /**
     * Memperbarui mata kuliah.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses', 'code')->ignore($course->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'sks' => ['required', 'integer', 'min:1'],
            'lecturer_id' => ['required', 'exists:users,id'],
            'status' => ['required', 'in:draft,active,archived'],
        ]);

        $course->update($validated);

        return redirect()
            ->route('courses.show', $course)
            ->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    /**
     * Menghapus mata kuliah.
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()
            ->route('courses.index')
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }
}