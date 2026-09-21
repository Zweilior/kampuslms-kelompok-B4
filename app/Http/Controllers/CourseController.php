<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
// use Illuminate\Validation\Rule;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;

class CourseController extends Controller
{
    /**
     * Menampilkan daftar semua mata kuliah.
     */
    public function index(Request $request)
    {
        $query = Course::with('lecturer');

        // Pencarian berdasarkan kode atau nama mata kuliah
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $courses = $query
            ->orderBy('code')
            ->paginate(15)
            ->withQueryString();

        // Data dosen untuk form tambah/edit
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
    public function store(StoreCourseRequest $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:courses,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'sks' => ['required', 'integer', 'min:1'],
            'lecturer_id' => ['required', 'exists:users,id'],
            'status' => ['required', 'in:draft,active,archived'],
        ]);

        $course = Course::create($request->validated());

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
    public function update(UpdateCourseRequest $request, Course $course)
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

        $course->update($request->validated());

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