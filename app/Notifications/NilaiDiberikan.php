<?php

namespace App\Notifications;

use App\Models\Grade;
use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

class NilaiDiberikan extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(
        public Submission $submission,
        public Grade $grade,
        public bool $updated = false
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $assignment = $this->submission->assignment;
        $action = $this->updated ? 'diperbarui' : 'diberikan';

        // Payload bersih tanpa atribut score dan feedback
        return [
            'message' => "Nilai untuk tugas \"{$assignment?->title}\" telah {$action}.",
            'assignment_id' => $assignment?->id,
            'course_id' => $assignment?->course_id,
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