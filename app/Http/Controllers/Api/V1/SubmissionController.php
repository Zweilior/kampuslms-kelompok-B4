<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GradeResource;
use App\Http\Resources\SubmissionCollection;
use App\Models\Assignment;
use App\Models\Grade;
use App\Models\Submission;
use App\Notifications\NilaiDiberikan;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    /**
     * GET /api/v1/assignments/{assignment}/submissions
     *
     * Hanya dosen pemilik course dari tugas ini.
     */
    public function index(Request $request, Assignment $assignment)
    {
        $assignment->loadMissing('course');

        if (! $this->isOwningLecturer($request->user(), $assignment)) {
            abort(403);
        }

        $submissions = $assignment->submissions()
            ->with(['student', 'grade'])      // eager loading: tidak ada N+1
            ->latest('submitted_at')
            ->paginate(15);

        return new SubmissionCollection($submissions);
    }

    /**
     * PUT /api/v1/submissions/{submission}/grade
     *
     * Upsert: aman dipanggil berulang (penilaian ulang).
     * 201 saat nilai dibuat pertama kali, 200 saat diperbarui.
     * Hanya dosen pemilik course dari tugas tempat submission ini berada.
     */
    public function grade(Request $request, Submission $submission)
    {
        $user = $request->user();

        $submission->loadMissing('assignment.course');
        $assignment = $submission->assignment;

        // Otorisasi dulu, baru validasi (403 harus menang atas 422).
        if (! $this->isOwningLecturer($user, $assignment)) {
            abort(403);
        }

        $validated = $request->validate([
            'score'    => ['required', 'numeric', 'min:0', 'max:' . $assignment->max_score],
            'feedback' => ['nullable', 'string', 'max:2000'],
        ]);

        $grade = Grade::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'graded_by' => $user->id,
                'score'     => $validated['score'],
                'feedback'  => $validated['feedback'] ?? null,
                'graded_at' => now(),
            ]
        );

        $created = $grade->wasRecentlyCreated;

        // Beri tahu mahasiswa pemilik submission.
        $submission->student?->notify(
            new NilaiDiberikan($submission, $grade, updated: ! $created)
        );

        $grade->load('grader');

        return (new GradeResource($grade))
            ->response()
            ->setStatusCode($created ? 201 : 200);
    }

    /**
     * Dosen yang mengajar course dari tugas ini?
     * (Minggu 7: logika ini dipindah ke Policy.)
     */
    private function isOwningLecturer($user, Assignment $assignment): bool
    {
        return $user->role === 'dosen'
            && (int) $assignment->course->lecturer_id === (int) $user->id;
    }
}
