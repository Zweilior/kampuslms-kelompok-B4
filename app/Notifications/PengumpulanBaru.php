<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

class PengumpulanBaru extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public Assignment $assignment;
    public User $student;

    public function __construct(
        public Submission $submission,
        ?Assignment $assignment = null,
        ?User $student = null,
        public bool $updated = false,
    ) {
        $this->assignment = $assignment ?? $submission->assignment;
        $this->student = $student ?? $submission->student;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $verb = $this->updated ? 'memperbarui pengumpulan' : 'mengumpulkan';

        return [
            'message' => "{$this->student->name} {$verb} tugas \"{$this->assignment->title}\".",
            'assignment_id' => $this->assignment->id,
            'course_id' => $this->assignment->course_id,
            'submission_id' => $this->submission->id,
        ];
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Gagal menyimpan notifikasi nilai.', [   // PengumpulanBaru: 'pengumpulan baru'
            'submission_id' => $this->submission->id,
            'error' => $exception->getMessage(),
        ]);
    }
}