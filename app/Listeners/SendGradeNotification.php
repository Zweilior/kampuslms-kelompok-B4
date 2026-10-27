<?php

namespace App\Listeners;

use App\Events\SubmissionGraded;
use App\Notifications\NilaiDiberikan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendGradeNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(SubmissionGraded $event): void
    {
        $submission = $event->grade->submission;
        $student = $submission?->student;

        if ($student) {
            $student->notify(new NilaiDiberikan($submission, $event->grade, $event->updated));
        }
    }

    public function failed(SubmissionGraded $event, \Throwable $exception): void
    {
        Log::error('Gagal mengirim notifikasi nilai diberikan/diperbarui', [
            'grade_id' => $event->grade->id,
            'submission_id' => $event->grade->submission_id,
            'error' => $exception->getMessage(),
        ]);
    }
}