<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Notifications\Notification;

/**
 * Dikirim ke dosen pemilik course saat mahasiswa mengumpulkan
 * (atau mengumpulkan ulang) tugas.
 */
class PengumpulanBaru extends Notification
{
    public function __construct(
        public Submission $submission,
        public Assignment $assignment,
        public User $student,
        public bool $updated = false,
    ) {
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
            'submission_id' => $this->submission->id,
        ];
    }
}
