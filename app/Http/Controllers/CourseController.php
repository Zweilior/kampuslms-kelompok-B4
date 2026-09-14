<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

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
        $course = Course::create([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'sks' => $request->sks,
            'lecturer_id' => $request->lecturer_id,
            'status' => $request->status,
        ]);

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
        $course->update([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'sks' => $request->sks,
            'lecturer_id' => $request->lecturer_id,
            'status' => $request->status,
        ]);

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