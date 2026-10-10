<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate; 

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
     * Menampilkan daftar mata kuliah.
     * [POIN 4] Disaring di level query sesuai peran:
     * - Admin: Melihat semua MK.
     * - Dosen: Hanya MK yang diajar.
     * - Mahasiswa: Hanya MK yang diikuti.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Course::class);

        $user = $request->user(); // Ambil user yang sedang login
        $query = Course::with('lecturer');

        // --- FILTER BERDASARKAN PERAN (POIN 4) ---
        if ($user->role === 'dosen') {
            $query->where('lecturer_id', $user->id);
        } elseif ($user->role === 'mahasiswa') {
            $query->whereHas('students', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }
        // Jika admin, tidak difilter (boleh lihat semua)

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

        // Data dosen untuk form tambah/edit (hanya admin yang butuh ini, tapi aman dibiarkan)
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
        Gate::authorize('create', Course::class);

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
        Gate::authorize('create', Course::class);

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
        Gate::authorize('view', $course);

        $course->load('lecturer');

        $lecturers = User::where('role', 'dosen')
            ->orderBy('name')
            ->get();

        return view($this->isAdminRoute() ? 'admin.courses.show' : 'courses.show', compact('course', 'lecturers'));
    }

    /**
     * Daftar MK milik dosen yang sedang login.
     */
    public function dosenIndex(Request $request)
    {
        Gate::authorize('viewAny', Course::class);

        $courses = Course::where('lecturer_id', $request->user()->id)
            ->with('lecturer')
            ->orderBy('code')
            ->get();

        $focus = $request->input('focus', 'materials');

        return view('dosen.courses.index', compact('courses', 'focus'));
    }

    /**
     * Daftar nilai MK milik dosen yang sedang login.
     */
    public function dosenGradesIndex(Request $request)
    {
        Gate::authorize('viewAny', Course::class);

        $courses = Course::where('lecturer_id', $request->user()->id)
            ->withCount('students')
            ->withAvg('finalGrades', 'total_score')
            ->orderBy('code')
            ->get();

        $activeCourses = $courses->where('status', 'active')->values();
        $archivedCourses = $courses->where('status', 'archived')->values();

        return view('dosen.grades.index', compact(
            'activeCourses',
            'archivedCourses'
        ));
    }

    /**
     * Detail MK untuk dosen pemilik.
     */
    public function dosenShow(Course $course)
    {
        Gate::authorize('view', $course);

        return view('dosen.courses.show', compact('course'));
    }

    /**
     * Menampilkan form edit mata kuliah.
     */
    public function edit(Course $course)
    {
        Gate::authorize('update', $course);

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
        Gate::authorize('update', $course);

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
        Gate::authorize('delete', $course);

        $course->delete();

        return redirect()
            ->route($this->routePrefix() . '.index')
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }
}