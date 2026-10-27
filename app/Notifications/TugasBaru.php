<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Dikirim ke setiap mahasiswa terdaftar saat dosen mempublikasikan tugas.
 * Disimpan ke tabel notifications (channel database).
 */
class TugasBaru extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(public Assignment $assignment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message'       => "Tugas baru \"{$this->assignment->title}\" telah dipublikasikan.",
            'title'         => $this->assignment->title,
            'assignment_id' => $this->assignment->id,
            'course_id'     => $this->assignment->course_id,
        ];
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Gagal menyimpan notifikasi tugas baru.', [
            'assignment_id' => $this->assignment->id,
            'error' => $exception->getMessage(),
        ]);
    }
}