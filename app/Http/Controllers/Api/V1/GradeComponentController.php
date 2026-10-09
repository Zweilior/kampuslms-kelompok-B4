<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GradeComponentCollection;
use App\Http\Resources\GradeComponentResource;
use App\Models\Course;
use App\Models\GradeComponent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GradeComponentController extends Controller
{
    /**
     * POST /api/v1/grade-components
     *
     * Dosen membuat komponen penilaian pada course miliknya.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Hanya dosen yang boleh membuat grade component.
        if ($user->role !== 'dosen') {
            return $this->forbidden();
        }

        $validated = $request->validate([
            'course_id' => [
                'required',
                'integer',
                Rule::exists('courses', 'id'),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'weight' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
        ]);

        // Pastikan course memang milik dosen yang sedang login.
        $course = Course::findOrFail($validated['course_id']);
        if ((int) $course->lecturer_id !== (int) $user->id) {
            return $this->forbidden();
        }

        // Buat grade component melalui relasi course.
        $gradeComponent = $course->gradeComponents()->create([
            'name' => $validated['name'],
            'weight' => $validated['weight'],
        ]);

        return (new GradeComponentResource($gradeComponent))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/v1/courses/{course}/grade-components
     *
     * Mendapatkan daftar komponen penilaian dari sebuah course.
     */
    public function index(Course $course)
    {
        $user = request()->user();

        // Authorization check
        if ($user->role === 'mahasiswa') {
            // Mahasiswa hanya bisa lihat course yang diikuti
            if (!$course->students->contains($user->id)) {
                return $this->forbidden();
            }
        } elseif ($user->role === 'dosen') {
            // Dosen hanya bisa lihat course miliknya
            if ((int) $course->lecturer_id !== (int) $user->id) {
                return $this->forbidden();
            }
        }

        $gradeComponents = $course->gradeComponents()
            ->latest()
            ->paginate(15);

        return new GradeComponentCollection($gradeComponents);
    }

    /**
     * GET /api/v1/grade-components/{gradeComponent}
     *
     * Mendapatkan detail komponen penilaian.
     */
    public function show(GradeComponent $gradeComponent)
    {
        $user = request()->user();
        $course = $gradeComponent->course;

        // Authorization check
        if ($user->role === 'mahasiswa') {
            if (!$course->students->contains($user->id)) {
                return $this->forbidden();
            }
        } elseif ($user->role === 'dosen') {
            if ((int) $course->lecturer_id !== (int) $user->id) {
                return $this->forbidden();
            }
        }

        return new GradeComponentResource($gradeComponent);
    }

    /**
     * PUT/PATCH /api/v1/grade-components/{gradeComponent}
     *
     * Dosen mengubah komponen penilaian miliknya.
     */
    public function update(Request $request, GradeComponent $gradeComponent)
    {
        $user = $request->user();

        // Pastikan hanya dosen pemilik course yang boleh mengubah.
        if (
            $user->role !== 'dosen' ||
            (int) $gradeComponent->course->lecturer_id !== (int) $user->id
        ) {
            return $this->forbidden();
        }

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],
            'weight' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
        ]);

        $gradeComponent->update($validated);

        return new GradeComponentResource($gradeComponent);
    }

    /**
     * DELETE /api/v1/grade-components/{gradeComponent}
     *
     * Dosen menghapus komponen penilaian miliknya.
     */
    public function destroy(Request $request, GradeComponent $gradeComponent)
    {
        $user = $request->user();

        // Pastikan hanya dosen pemilik course yang boleh menghapus.
        if (
            $user->role !== 'dosen' ||
            (int) $gradeComponent->course->lecturer_id !== (int) $user->id
        ) {
            return $this->forbidden();
        }

        // Cek apakah ada assignment yang menggunakan grade component ini
        if ($gradeComponent->assignments()->count() > 0) {
            return response()->json([
                'message' => 'Tidak dapat menghapus komponen penilaian karena masih memiliki tugas.',
            ], 422);
        }

        $gradeComponent->delete();

        return response()->noContent();
    }

    /**
     * Response untuk user yang tidak punya akses.
     */
    private function forbidden()
    {
        return response()->json([
            'message' => 'Anda tidak memiliki akses ke sumber daya ini.',
        ], 403);
    }
}