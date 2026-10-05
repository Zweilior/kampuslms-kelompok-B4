<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssignmentCollection;
use App\Http\Resources\CourseCollection;
use App\Http\Resources\CourseResource;
use App\Http\Resources\MaterialCollection;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * GET /api/v1/courses
     *
     * Dosen      : hanya mata kuliah yang diajar
     * Mahasiswa  : hanya mata kuliah yang diikuti
     * Admin      : semua mata kuliah
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Course::query()
            ->with('lecturer')
            ->withCount([
                'materials',
                'assignments',
            ]);

        if ($user->role === 'dosen') {
            $query->where('lecturer_id', $user->id);
        } elseif ($user->role === 'mahasiswa') {
            $query->whereHas('students', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            });
        } elseif ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke sumber daya ini.',
            ], 403);
        }

        $courses = $query
            ->orderBy('code')
            ->paginate(15);

        return new CourseCollection($courses);
    }

    /**
     * GET /api/v1/courses/{course}
     */
    public function show(Request $request, Course $course)
    {
        $user = $request->user();

        if (! $this->canAccessCourse($user, $course)) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke sumber daya ini.',
            ], 403);
        }

        $course->load('lecturer')
            ->loadCount([
                'materials',
                'assignments',
            ]);

        return new CourseResource($course);
    }

    /**
     * GET /api/v1/courses/{course}/materials
     */
    public function materials(Request $request, Course $course)
    {
        $user = $request->user();

        if (! $this->canAccessCourse($user, $course)) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke sumber daya ini.',
            ], 403);
        }

        $materials = $course->materials()
            ->latest()
            ->paginate(15);

        return new MaterialCollection($materials);
    }

    /**
     * GET /api/v1/courses/{course}/assignments
     */
    public function assignments(Request $request, Course $course)
    {
        $user = $request->user();

        if (! $this->canAccessCourse($user, $course)) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke sumber daya ini.',
            ], 403);
        }

        $query = $course->assignments();

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->input('status')
            );
        }

        $assignments = $query
            ->latest()
            ->paginate(15);

        return new AssignmentCollection($assignments);
    }

    /**
     * Mengecek apakah user boleh mengakses course tertentu.
     */
    private function canAccessCourse($user, Course $course): bool
    {
        // Admin boleh melihat semua course.
        if ($user->role === 'admin') {
            return true;
        }

        // Dosen hanya boleh melihat course yang dia ajar.
        if ($user->role === 'dosen') {
            return (int) $course->lecturer_id === (int) $user->id;
        }

        // Mahasiswa hanya boleh melihat course yang dia ikuti.
        if ($user->role === 'mahasiswa') {
            return $course->students()
                ->where('users.id', $user->id)
                ->exists();
        }

        return false;
    }
}