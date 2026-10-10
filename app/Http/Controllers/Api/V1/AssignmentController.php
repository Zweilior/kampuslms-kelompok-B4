<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{
    /**
     * POST /api/v1/assignments
     *
     * Dosen membuat assignment pada course miliknya.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Hanya dosen yang boleh membuat assignment.
        if ($user->role !== 'dosen') {
            return $this->forbidden();
        }

        $validated = $request->validate([
            'course_id' => [
                'required',
                'integer',
                Rule::exists('courses', 'id'),
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'instructions' => [
                'required',
                'string',
            ],
            'due_at' => [
                'required',
                'date',
            ],
            'max_score' => [
                'required',
                'integer',
                'min:0',
            ],
            'allow_late' => [
                'required',
                'boolean',
            ],
            'status' => [
                'required',
                Rule::in(['draft', 'published']),
            ],
        ]);

        // Pastikan course memang milik dosen yang sedang login.
        $course = Course::findOrFail($validated['course_id']);

        if ((int) $course->lecturer_id !== (int) $user->id) {
            return $this->forbidden();
        }

        // Buat assignment melalui relasi course.
        $assignment = $course->assignments()->create([
            'title' => $validated['title'],
            'instructions' => $validated['instructions'],
            'due_at' => $validated['due_at'],
            'max_score' => $validated['max_score'],
            'allow_late' => $validated['allow_late'],
            'status' => $validated['status'],
            'created_by' => $user->id,
        ]);

        return (new AssignmentResource($assignment))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * PUT/PATCH /api/v1/assignments/{assignment}
     *
     * Dosen mengubah assignment miliknya.
     */
    public function update(Request $request, Assignment $assignment)
    {
        $user = $request->user();

        // Pastikan hanya dosen pemilik course yang boleh mengubah.
        if ($assignment->course->lecturer_id !== auth()->id()) {
        return response()->json(['message' => 'Forbidden. Anda bukan pemilik tugas ini.'], 403);
    }

        $validated = $request->validate([
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],
            'instructions' => [
                'sometimes',
                'required',
                'string',
            ],
            'due_at' => [
                'sometimes',
                'required',
                'date',
            ],
            'max_score' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
            ],
            'allow_late' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'status' => [
                'sometimes',
                'required',
                Rule::in(['draft', 'published']),
            ],
        ]);

        $assignment->update($validated);

        return new AssignmentResource($assignment);
    }

    /**
     * DELETE /api/v1/assignments/{assignment}
     *
     * Dosen menghapus assignment miliknya.
     */
    public function destroy(Request $request, Assignment $assignment)
    {
        $user = $request->user();

        // Pastikan hanya dosen pemilik course yang boleh menghapus.
        if (
            $user->role !== 'dosen' ||
            (int) $assignment->course->lecturer_id !== (int) $user->id
        ) {
            return $this->forbidden();
        }

        $assignment->delete();

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