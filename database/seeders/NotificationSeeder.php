<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Data contoh notifikasi untuk akun demo.
 * Aman dijalankan berulang: notifikasi lama akun demo dihapus dulu.
 * Disesuaikan dengan struktur payload bersih (menyertakan ID referensi).
 *
 *   php artisan db:seed --class=NotificationSeeder
 */
class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa@kampuslms.test')->first();
        $dosen = User::where('email', 'dosen@kampuslms.test')->first();

        // Ambil sampel data assignment dan submission yang relevan
        $mCourse = $mahasiswa?->courses()->first();
        $mAssignment = $mCourse?->assignments()->first() ?? Assignment::first();
        $mSubmission = $mAssignment ? Submission::where('assignment_id', $mAssignment->id)->where('user_id', $mahasiswa?->id)->first() : null;

        if ($mahasiswa) {
            $mahasiswa->notifications()->delete();

            $this->make($mahasiswa, 'TugasBaru', 'Tugas baru telah diterbitkan.', null, [
                'course_id' => $mCourse?->id ?? $mAssignment?->course_id,
                'assignment_id' => $mAssignment?->id,
            ]);
            $this->make($mahasiswa, 'NilaiDiberikan', 'Tugas Anda telah dinilai.', null, [
                'course_id' => $mCourse?->id ?? $mAssignment?->course_id,
                'assignment_id' => $mAssignment?->id,
                'submission_id' => $mSubmission?->id,
            ]);
            $this->make($mahasiswa, 'PengingatTenggat', 'Tenggat tugas tinggal 2 hari lagi.', now()->subDay(), [
                'course_id' => $mCourse?->id ?? $mAssignment?->course_id,
                'assignment_id' => $mAssignment?->id,
            ]);
        }

        if ($dosen) {
            $dosenCourse = $dosen->taughtCourses()->first();
            $dosenAssignment = $dosenCourse?->assignments()->first() ?? Assignment::first();
            $dosenSubmission = $dosenAssignment?->submissions()->first() ?? Submission::first();

            $dosen->notifications()->delete();

            $this->make($dosen, 'PengumpulanBaru', 'Ada pengumpulan tugas baru dari mahasiswa.', null, [
                'course_id' => $dosenCourse?->id ?? $dosenAssignment?->course_id,
                'assignment_id' => $dosenAssignment?->id,
                'submission_id' => $dosenSubmission?->id,
            ]);
            $this->make($dosen, 'PengumpulanBaru', 'Ada pengumpulan tugas baru dari mahasiswa.', now()->subHours(3), [
                'course_id' => $dosenCourse?->id ?? $dosenAssignment?->course_id,
                'assignment_id' => $dosenAssignment?->id,
                'submission_id' => $dosenSubmission?->id,
            ]);
        }
    }

    private function make(User $user, string $type, string $message, $readAt, array $additionalData = []): void
    {
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\' . $type,
            'data' => array_merge(['message' => $message], $additionalData),
            'read_at' => $readAt,
        ]);
    }
}