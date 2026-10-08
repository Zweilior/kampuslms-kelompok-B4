<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\NilaiDiberikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function dosenIndex(Course $course, Assignment $assignment)
    {
        abort_unless($course->lecturer_id === Auth::id(), 403);

        $submissions = $assignment->submissions()
            ->with('student', 'grade')
            ->latest()
            ->get();

        return view('dosen.assignments.show', compact('course', 'assignment', 'submissions'));
    }

    public function dosenGrade(Request $request, Course $course, Assignment $assignment, Submission $submission)
    {
        abort_unless($course->lecturer_id === Auth::id(), 403);
        abort_unless($submission->assignment_id === $assignment->id, 404);
        abort_unless($assignment->due_at->lte(now()), 403, 'Nilai hanya dapat diubah setelah tenggat tugas selesai.');

        $validated = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:' . $assignment->max_score],
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

    /**
     * Menampilkan daftar pengumpulan dari suatu tugas.
     */
    public function index(Course $course, Assignment $assignment)
    {
        $submissions = $assignment->submissions()
            ->latest()
            ->paginate(15);

        return view('submissions.index', compact(
            'course',
            'assignment',
            'submissions'
        ));
    }

    /**
     * Menampilkan form pengumpulan tugas.
     */
    public function create(Course $course, Assignment $assignment)
    {
        return view('submissions.create', compact(
            'course',
            'assignment'
        ));
    }

    /**
     * Menyimpan pengumpulan tugas.
     */
    public function store(
        Request $request,
        Course $course,
        Assignment $assignment
    ) {
        $validated = $request->validate([
            'file_path' => ['required', 'string', 'max:255'],
            'original_name' => ['required', 'string', 'max:255'],
            'file_size' => ['nullable', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        $assignment->submissions()->create([
            ...$validated,
            'user_id' => auth()->id() ?? User::where('role', 'mahasiswa')->value('id'),
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route(
                'courses.assignments.submissions.index',
                [$course, $assignment]
            )
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }

    /**
     * Menampilkan detail pengumpulan.
     */
    public function show(
        Course $course,
        Assignment $assignment,
        Submission $submission
    ) {
        abort_unless(
            $submission->user_id === auth()->id()
            || auth()->user()->role === 'admin'
            || $submission->assignment->course->lecturer_id === auth()->id(),
            403
        );

        return view('submissions.show', compact(
            'course',
            'assignment',
            'submission'
        ));
    }

    /**
     * Menampilkan form edit pengumpulan.
     */
    public function edit(
        Course $course,
        Assignment $assignment,
        Submission $submission
    ) {
        abort_unless(
            $submission->user_id === auth()->id()
            || auth()->user()->role === 'admin'
            || $submission->assignment->course->lecturer_id === auth()->id(),
            403
        );

        return view('submissions.edit', compact(
            'course',
            'assignment',
            'submission'
        ));
    }

    /**
     * Memperbarui pengumpulan.
     */
    public function update(
        Request $request,
        Course $course,
        Assignment $assignment,
        Submission $submission
    ) {
        abort_unless(
            $submission->user_id === auth()->id()
            || auth()->user()->role === 'admin'
            || $submission->assignment->course->lecturer_id === auth()->id(),
            403
        );

        $validated = $request->validate([
            'file_path' => ['required', 'string', 'max:255'],
            'original_name' => ['required', 'string', 'max:255'],
            'file_size' => ['nullable', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        $submission->update($validated);

        return redirect()
            ->route(
                'courses.assignments.submissions.show',
                [$course, $assignment, $submission]
            )
            ->with('success', 'Pengumpulan berhasil diperbarui.');
    }

    /**
     * Menghapus pengumpulan.
     */
    public function destroy(
        Course $course,
        Assignment $assignment,
        Submission $submission
    ) {
        abort_unless(
            $submission->user_id === auth()->id()
            || auth()->user()->role === 'admin'
            || $submission->assignment->course->lecturer_id === auth()->id(),
            403
        );

        $submission->delete();

        return redirect()
            ->route(
                'courses.assignments.submissions.index',
                [$course, $assignment]
            )
            ->with('success', 'Pengumpulan berhasil dihapus.');
    }
}