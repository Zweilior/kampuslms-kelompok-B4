<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Support\Facades\Auth;

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

        return view($this->routePrefix() . '.index', compact('courses', 'lecturers'));
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
        $data = $request->validated();
        $data['description'] = $data['description'] ?? '';

        $course = Course::create($data);

        return redirect()
            ->route($this->routePrefix() . '.show', $course)
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
        $courses = Course::where('lecturer_id', $request->user()->id)
            ->with('lecturer')
            ->orderBy('code')
            ->get();

        $focus = $request->input('focus', 'materials');

        return view('dosen.courses.index', compact('courses', 'focus'));
    }

    public function dosenShow(Course $course)
    {
        abort_unless($course->lecturer_id === Auth::id(), 403);

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
        $data = $request->validated();
        $data['description'] = $data['description'] ?? '';

        $course->update($data);

        return redirect()
            ->route($this->routePrefix() . '.show', $course)
            ->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    /**
     * Menghapus mata kuliah.
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()
            ->route($this->routePrefix() . '.index')
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }
}