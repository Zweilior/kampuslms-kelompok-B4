<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubmissionRequest;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Submission;
use App\Notifications\NilaiDiberikan;
use App\Notifications\PengumpulanBaru;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SubmissionController extends Controller
{
    public function dosenIndex(Request $request, Course $course, Assignment $assignment)
    {
        $this->authorizeDosenAssignment($course, $assignment);
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['all', 'graded', 'ungraded'])],
        ]);
        $status = $validated['status'] ?? 'all';

        $submissionCounts = [
            'all' => $assignment->submissions()->count(),
            'graded' => $assignment->submissions()->whereHas('grade')->count(),
            'ungraded' => $assignment->submissions()->whereDoesntHave('grade')->count(),
            'late' => $assignment->submissions()->where('is_late', true)->count(), // BARU
        ];
        $submissions = $this->dosenSubmissionsQuery($assignment, $status)->get();

        // BARU: mahasiswa terdaftar yang belum mengumpulkan tugas ini
        $notSubmitted = $course->students()
            ->whereDoesntHave('submissions', fn ($q) => $q->where('assignment_id', $assignment->id))
            ->orderBy('nim_nip')
            ->get();

        return view('dosen.assignments.show', compact(
            'course',
            'assignment',
            'submissions',
            'submissionCounts',
            'status',
            'notSubmitted' // BARU
        ));
    }

    public function dosenExport(Request $request, Course $course, Assignment $assignment)
    {
        $this->authorizeDosenAssignment($course, $assignment);
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['all', 'graded', 'ungraded'])],
        ]);
        $submissions = $this->dosenSubmissionsQuery($assignment, $validated['status'] ?? 'all')->get();
        $filename = sprintf(
            'nilai-%s-%s.csv',
            Str::slug($assignment->title) ?: 'tugas',
            $assignment->id
        );

        return response()->streamDownload(function () use ($submissions, $assignment): void {
            $output = fopen('php://output', 'w');
            if ($output === false) {
                throw new \RuntimeException('Tidak dapat menyiapkan file CSV nilai.');
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'NIM',
                'Nama Mahasiswa',
                'Status Penilaian',
                'Nilai',
                'Nilai Maksimal',
                'Umpan Balik',
                'File Pengumpulan',
                'Waktu Pengumpulan',
                'Waktu Penilaian',
            ], ',', '"', '\\');

            foreach ($submissions as $submission) {
                fputcsv($output, [
                    $this->safeCsvText($submission->student?->nim_nip),
                    $this->safeCsvText($submission->student?->name),
                    $submission->grade ? 'Sudah dinilai' : 'Belum dinilai',
                    $submission->grade?->score,
                    $assignment->max_score,
                    $this->safeCsvText($submission->grade?->feedback),
                    $this->safeCsvText($submission->original_name),
                    $submission->submitted_at?->format('Y-m-d H:i:s'),
                    $submission->grade?->graded_at?->format('Y-m-d H:i:s'),
                ], ',', '"', '\\');
            }

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function dosenExportCourseGrades(Request $request, Course $course)
    {
        abort_unless($course->lecturer_id === $request->user()->id, 403);

        $submissions = Submission::query()
            ->join('assignments', 'submissions.assignment_id', '=', 'assignments.id')
            ->join('users as students', 'submissions.user_id', '=', 'students.id')
            ->where('assignments.course_id', $course->id)
            ->select('submissions.*')
            ->with(['student', 'grade', 'assignment'])
            ->orderBy('students.nim_nip')
            ->orderBy('assignments.title')
            ->get();

        return response()->streamDownload(function () use ($submissions): void {
            $output = fopen('php://output', 'w');
            if ($output === false) {
                throw new \RuntimeException('Tidak dapat menyiapkan file CSV nilai.');
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'Tugas',
                'NIM',
                'Nama Mahasiswa',
                'Status Penilaian',
                'Nilai',
                'Nilai Maksimal',
                'Umpan Balik',
                'Waktu Pengumpulan',
                'Waktu Penilaian',
            ], ',', '"', '\\');

            foreach ($submissions as $submission) {
                $assignment = $submission->assignment;

                fputcsv($output, [
                    $this->safeCsvText($assignment?->title),
                    $this->safeCsvText($submission->student?->nim_nip),
                    $this->safeCsvText($submission->student?->name),
                    $submission->grade ? 'Sudah dinilai' : 'Belum dinilai',
                    $submission->grade?->score,
                    $assignment?->max_score,
                    $this->safeCsvText($submission->grade?->feedback),
                    $submission->submitted_at?->format('Y-m-d H:i:s'),
                    $submission->grade?->graded_at?->format('Y-m-d H:i:s'),
                ], ',', '"', '\\');
            }

            fclose($output);
        }, 'nilai-'.(Str::slug($course->name) ?: 'mata-kuliah').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function dosenGrade(Request $request, Course $course, Assignment $assignment, Submission $submission)
    {
        $this->authorizeDosenAssignment($course, $assignment);
        abort_unless($submission->assignment_id === $assignment->id, 404);
        abort_unless($assignment->due_at->lte(now()), 403, 'Nilai hanya dapat diubah setelah tenggat tugas selesai.');

        $validated = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:'.$assignment->max_score],
            'feedback' => ['nullable', 'string', 'max:2000'],
        ]);

        $grade = Grade::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'graded_by' => $request->user()->id,
                'score' => $validated['score'],
                'feedback' => $validated['feedback'] ?? null,
                'graded_at' => now(),
            ]
        );

        $submission->loadMissing('student');
        $submission->student?->notify(
            new NilaiDiberikan($submission, $grade, updated: ! $grade->wasRecentlyCreated)
        );

        return redirect()
            ->route('dosen.courses.assignments.submissions.index', [$course, $assignment])
            ->with('success', 'Nilai berhasil disimpan.');
    }

    private function authorizeDosenAssignment(Course $course, Assignment $assignment): void
    {
        abort_unless($course->lecturer_id === Auth::id(), 403);
        abort_unless($assignment->course_id === $course->id, 404);
    }

    private function dosenSubmissionsQuery(Assignment $assignment, string $status): HasMany
    {
        return $assignment->submissions()
            ->join('users as students', 'submissions.user_id', '=', 'students.id')
            ->select('submissions.*')
            ->with(['student', 'grade'])
            ->when($status === 'graded', fn (Builder $query) => $query->whereHas('grade'))
            ->when($status === 'ungraded', fn (Builder $query) => $query->whereDoesntHave('grade'))
            ->orderBy('students.nim_nip')
            ->orderBy('students.name');
    }

    private function safeCsvText(?string $value): string
    {
        $value ??= '';

        return preg_match('/^[\s]*[=+\-@]/u', $value) === 1
            ? "'".$value
            : $value;
    }

    /**
     * Menyimpan pengumpulan tugas (unggah file sungguhan).
     */
    public function store(
        StoreSubmissionRequest $request,
        Course $course,
        Assignment $assignment
    ) {
        Gate::authorize('create', [Submission::class, $assignment]);

        // Cek pengumpulan sebelumnya
        $existingSubmission = $assignment->submissions()
            ->where('user_id', $request->user()->id)
            ->first();

        // 1. Blokir kumpul ulang jika tugas sudah dinilai
        if ($existingSubmission && $existingSubmission->grade()->exists()) {
            throw ValidationException::withMessages([
                'file' => 'Tugas sudah dinilai dan tidak dapat dikumpulkan ulang.',
            ]);
        }

        // 2. Cek keterlambatan & batasan allow_late
        $isLate = now()->gt($assignment->due_at);
        if ($isLate && ! $assignment->allow_late) {
            throw ValidationException::withMessages([
                'file' => 'Batas waktu pengumpulan telah berakhir dan tugas ini tidak menerima keterlambatan.',
            ]);
        }

        // 3. Simpan file baru ke penyimpanan lokal
        $file = $request->file('file');
        $newFilePath = $file->store("submissions/{$assignment->id}", 'local');
        $oldFilePath = $existingSubmission?->file_path;

        try {
            // 4. Update atau buat baru (updateOrCreate)
            $submission = Submission::updateOrCreate(
                [
                    'assignment_id' => $assignment->id,
                    'user_id' => $request->user()->id,
                ],
                [
                    'file_path' => $newFilePath,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'note' => $request->validated()['note'] ?? null,
                    'submitted_at' => now(),
                    'is_late' => $isLate,
                ]
            );
        } catch (\Throwable $e) {
            // Jika ada error saat simpan DB, hapus file baru yang terlanjur ter-upload
            if (Storage::disk('local')->exists($newFilePath)) {
                Storage::disk('local')->delete($newFilePath);
            }

            throw $e;
        }

        $created = $submission->wasRecentlyCreated;

        // Hapus file lama hanya setelah record baru berhasil disimpan.
        if ($oldFilePath && $oldFilePath !== $newFilePath) {
            Storage::disk('local')->delete($oldFilePath);
        }

        // Kirim notifikasi yang membedakan pengumpulan baru dan pengumpulan ulang.
        $lecturer = $course->lecturer;
        if ($lecturer) {
            $lecturer->notify(new PengumpulanBaru(
                $submission,
                $assignment,
                $request->user(),
                updated: ! $created
            ));
        }

        return redirect()
            ->route(
                'mahasiswa.courses.assignments.submissions.index',
                [$course, $assignment]
            )
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }

    /**
     * Mengunduh file submission mahasiswa.
     */
    public function download(Submission $submission)
    {
        Gate::authorize('download', $submission);

        abort_unless(
            $submission->file_path && Storage::disk('local')->exists($submission->file_path),
            404
        );

        return Storage::disk('local')->download(
            $submission->file_path,
            $submission->original_name ?: basename($submission->file_path)
        );
    }
}
