<?php

namespace App\Notifications;

use App\Models\Grade;
use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NilaiDiberikan extends Notification implements ShouldQueue
{
    use Queueable;

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
            'submission_id' => $this->submission->id,
        ];
    }
}