<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\GradeComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeComponentController extends Controller
{
    private function ensureLecturerOwnsCourse(Course $course): void
    {
        abort_unless($course->lecturer_id === Auth::id(), 403, 'Anda tidak memiliki akses ke mata kuliah ini.');
    }

    /**
     * Menampilkan daftar rubrik/komponen penilaian mata kuliah.
     */
    public function dosenIndex(Course $course)
    {
        $this->ensureLecturerOwnsCourse($course);

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
        $this->ensureLecturerOwnsCourse($course);

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
        $this->ensureLecturerOwnsCourse($course);

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
        $this->ensureLecturerOwnsCourse($course);

        // Cegah hapus jika masih ada tugas yang menggunakan komponen ini
        if ($gradeComponent->assignments()->count() > 0) {
            return back()->with('error', 'Komponen penilaian tidak dapat dihapus karena masih memiliki tugas yang terkait.');
        }

        $gradeComponent->delete();

        return redirect()->route('dosen.courses.grade-components.index', $course)
            ->with('success', 'Komponen penilaian berhasil dihapus.');
    }
}