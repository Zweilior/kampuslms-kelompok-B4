<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\GradeComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate; // BARU

class GradeComponentController extends Controller
{
    // ❌ HAPUS method ensureLecturerOwnsCourse yang lama karena sudah digantikan oleh Policy

    /**
     * Menampilkan daftar rubrik/komponen penilaian mata kuliah.
     */
    public function dosenIndex(Course $course)
    {
        Gate::authorize('viewAny', [GradeComponent::class, $course]); // BARU (Menggantikan ensureLecturerOwnsCourse)

        $gradeComponents = $course->gradeComponents()
            ->withCount('assignments')
            ->orderByDesc('created_at')
            ->get();

        return view('dosen.grade-components.index', compact('course', 'gradeComponents'));
    }

    /**
     * Menyimpan komponen penilaian baru.
     */
    public function dosenStore(Request $request, Course $course)
    {
        Gate::authorize('create', [GradeComponent::class, $course]); // BARU

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $course->gradeComponents()->create($validated);

        return redirect()->route('dosen.courses.grade-components.index', $course)
            ->with('success', 'Komponen penilaian berhasil ditambahkan.');
    }

    /**
     * Memperbarui komponen penilaian.
     */
    public function dosenUpdate(Request $request, Course $course, GradeComponent $gradeComponent)
    {
        Gate::authorize('update', $gradeComponent); // BARU

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $gradeComponent->update($validated);

        return redirect()->route('dosen.courses.grade-components.index', $course)
            ->with('success', 'Komponen penilaian berhasil diperbarui.');
    }

    /**
     * Menghapus komponen penilaian.
     */
    public function dosenDestroy(Course $course, GradeComponent $gradeComponent)
    {
        Gate::authorize('delete', $gradeComponent); // BARU

        // Cegah hapus jika masih ada tugas yang menggunakan komponen ini
        if ($gradeComponent->assignments()->count() > 0) {
            return back()->with('error', 'Komponen penilaian tidak dapat dihapus karena masih memiliki tugas yang terkait.');
        }

        $gradeComponent->delete();

        return redirect()->route('dosen.courses.grade-components.index', $course)
            ->with('success', 'Komponen penilaian berhasil dihapus.');
    }
}