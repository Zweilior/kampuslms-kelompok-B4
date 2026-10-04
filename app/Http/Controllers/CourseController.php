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

        return view($request->routeIs('admin.courses.*') ? 'admin.courses.index' : 'courses.index', compact('courses', 'lecturers'));
    }

    /**
     * Menampilkan form tambah mata kuliah.
     */
    public function create()
    {
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
        $course = Course::create($request->validated());

        return redirect()
            ->route($this->isAdminRoute() ? 'admin.courses.show' : 'courses.show', $course)
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

        return view($this->isAdminRoute() ? 'admin.courses.show' : 'courses.show', compact('course', 'lecturers'));
    }

    public function dosenIndex(Request $request)
    {
        $lecturer = User::where('role', 'dosen')
            ->orderBy('id')
            ->first();

        $courses = Course::where('lecturer_id', $lecturer?->id)
            ->with('lecturer')
            ->orderBy('code')
            ->get();

        $focus = $request->input('focus', 'materials');

        return view('dosen.courses.index', compact('courses', 'focus'));
    }

    public function dosenShow(Course $course)
    {
        return view('dosen.courses.show', compact('course'));
    }

    /**
     * Menampilkan form edit mata kuliah.
     */
    public function edit(Course $course)
    {
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
            ->route($this->isAdminRoute() ? 'admin.courses.index' : 'courses.index')
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }
}