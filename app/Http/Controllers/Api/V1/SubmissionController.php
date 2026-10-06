<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GradeResource;
use App\Http\Resources\SubmissionCollection;
use App\Http\Resources\SubmissionResource;
use App\Models\Assignment;
use App\Models\Grade;
use App\Models\Submission;
use App\Notifications\NilaiDiberikan;
use App\Notifications\PengumpulanBaru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
     * POST /api/v1/assignments/{assignment}/submissions   (multipart, field "file")
     *
     * Aturan:
     *  - hanya mahasiswa yang TERDAFTAR di course tugas ini        -> selain itu 403
     *  - tugas harus published (draft disembunyikan)               -> selain itu 404
     *  - lewat tenggat & allow_late = false                        -> 422
     *  - kirim ulang: menimpa selama belum dinilai (200),
     *                 sudah dinilai -> 409
     *  - file: pdf/doc/docx/zip, maks 5 MB, disimpan di disk privat
     */
    public function store(Request $request, Assignment $assignment)
    {
        $user = $request->user();
        $assignment->loadMissing('course');

        // 1. Otorisasi dulu (403 harus menang atas 422).
        $enrolled = $user->role === 'mahasiswa'
            && $assignment->course->students()->where('users.id', $user->id)->exists();

        if (! $enrolled) {
            abort(403);
        }

        // 2. Mahasiswa tidak boleh tahu tugas draft itu ada.
        if ($assignment->status !== 'published') {
            abort(404);
        }

        // 3. Validasi input.
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,zip', 'max:5120'], // KB
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        // 4. Tenggat.
        $isLate = now()->greaterThan($assignment->due_at);

        if ($isLate && ! $assignment->allow_late) {
            throw ValidationException::withMessages([
                'assignment' => ['Batas waktu pengumpulan sudah lewat.'],
            ]);
        }

        // 5. Sudah dinilai -> tidak boleh diubah lagi.
        $existing = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing && $existing->grade()->exists()) {
            abort(409, 'Tugas ini sudah dinilai dan tidak dapat dikumpulkan ulang.');
        }

        // 6. Simpan file di disk privat dengan nama acak buatan Laravel
        //    (nama dari klien TIDAK dipakai sebagai path -> aman dari path traversal).
        $file = $request->file('file');
        $size = $file->getSize();
        $originalName = Str::limit(basename($file->getClientOriginalName()), 255, '');
        $path = $file->store("submissions/{$assignment->id}");

        try {
            $submission = Submission::updateOrCreate(
                ['assignment_id' => $assignment->id, 'user_id' => $user->id],
                [
                    'file_path'     => $path,
                    'original_name' => $originalName,
                    'file_size'     => $size,
                    'note'          => $validated['note'] ?? null,
                    'submitted_at'  => now(),
                    'is_late'       => $isLate,
                ]
            );
        } catch (\Throwable $e) {
            Storage::delete($path);   // jangan tinggalkan file yatim
            throw $e;
        }

        $created = $submission->wasRecentlyCreated;

        // Kirim ulang: hapus file lama.
        if ($existing && $existing->file_path !== $path) {
            Storage::delete($existing->file_path);
        }

        // Beri tahu dosen pemilik course.
        $assignment->course->lecturer?->notify(
            new PengumpulanBaru($submission, $assignment, $user, updated: ! $created)
        );

        return (new SubmissionResource($submission))
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
