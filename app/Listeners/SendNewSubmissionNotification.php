<?php

namespace App\Listeners;

use App\Events\SubmissionCreated;
use App\Notifications\PengumpulanBaru;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendNewSubmissionNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(SubmissionCreated $event): void
    {
        $submission = $event->submission;
        $submission->loadMissing(['assignment.course.lecturer', 'student']);

        // Penerima: dosen pemilik mata kuliah dari tugas yang dikumpulkan.
        $lecturer = $submission->assignment?->course?->lecturer;

        if ($lecturer) {
            $lecturer->notify(new PengumpulanBaru(
                $submission,
                $submission->assignment,
                $submission->student,
                $event->updated
            ));
        }
    }

    public function failed(SubmissionCreated $event, \Throwable $exception): void
    {
        Log::error('Gagal mengirim notifikasi pengumpulan baru ke dosen', [
            'submission_id' => $event->submission->id,
            'error' => $exception->getMessage(),
        ]);
    }
}