<?php

namespace App\Notifications;

use App\Models\Grade;
use App\Models\Submission;
use Illuminate\Notifications\Notification;

/**
 * Dikirim ke mahasiswa saat dosen memberi atau memperbarui nilai.
 * Disimpan ke tabel notifications (channel database), tampil di GET /notifications.
 */
class NilaiDiberikan extends Notification
{
    public function __construct(
        public Submission $submission,
        public Grade $grade,
        public bool $updated = false,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $title = $this->submission->assignment->title;

        return [
            'message' => $this->updated
                ? "Nilai tugas \"{$title}\" telah diperbarui."
                : "Tugas \"{$title}\" telah dinilai.",
            'assignment_id' => $this->submission->assignment_id,
            'submission_id' => $this->submission->id,
        ];
    }
}
