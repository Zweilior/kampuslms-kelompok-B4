<?php

namespace App\Listeners;

use App\Events\AssignmentPublished;
use App\Models\User;
use App\Notifications\TugasBaru;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendAssignmentNotification implements ShouldQueue
{
    public int $tries = 3;      // maksimal 3 kali percobaan
    public int $backoff = 10;   // jeda 10 detik antar percobaan

    public function handle(AssignmentPublished $event): void
    {
        $assignment = $event->assignment;

        // Satu query untuk semua mahasiswa terdaftar di mata kuliah tugas ini.
        // Relasi tidak dimuat ke $assignment, jadi tidak ikut terserialisasi
        // ke tiap job TugasBaru.
        $students = User::whereHas(
            'courses',
            fn ($query) => $query->whereKey($assignment->course_id)
        )->get();

        if ($students->isEmpty()) {
            return;
        }

        Notification::send($students, new TugasBaru($assignment));
    }

    public function failed(AssignmentPublished $event, Throwable $exception): void
    {
        Log::error('Gagal mengirim notifikasi tugas baru ke mahasiswa.', [
            'assignment_id' => $event->assignment->id,
            'error' => $exception->getMessage(),
        ]);
    }
}