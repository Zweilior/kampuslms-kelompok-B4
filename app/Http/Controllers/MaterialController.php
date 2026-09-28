<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Menampilkan daftar materi dari suatu mata kuliah.
     */
    public function index(Course $course)
    {
        $materials = $course->materials()
            ->latest()
            ->paginate(15);

        return view('materials.index', compact('course', 'materials'));
    }

    /**
     * Menampilkan form tambah materi.
     */
    public function create(Course $course)
    {
        return view('materials.create', compact('course'));
    }

    /**
     * Menyimpan materi baru.
     */
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:50'],
            'external_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $course->materials()->create($validated);

        return redirect()
            ->route('courses.materials.index', $course)
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail materi.
     */
    public function show(Course $course, Material $material)
    {
        return view('materials.show', compact('course', 'material'));
    }

    /**
     * Menampilkan form edit materi.
     */
    public function edit(Course $course, Material $material)
    {
        return view('materials.edit', compact('course', 'material'));
    }

    /**
     * Memperbarui materi.
     */
    public function update(
        Request $request,
        Course $course,
        Material $material
    ) {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:50'],
            'external_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $material->update($validated);

        return redirect()
            ->route('courses.materials.show', [$course, $material])
            ->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * Menghapus materi.
     */
    public function destroy(Course $course, Material $material)
    {
        $material->delete();

        return redirect()
            ->route('courses.materials.index', $course)
            ->with('success', 'Materi berhasil dihapus.');
    }
}