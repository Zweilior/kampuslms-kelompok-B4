<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use App\Policies\Concerns\ChecksCourseRelation;
use Illuminate\Auth\Access\Response;

/**
 * Relasi dipakai:
 *   Submission->user_id (pemilik submission)
 *   Submission->assignment->course->lecturer_id (dosen pemilik MK)
 *   Assignment->course->students() (enrollment)
 *
 * viewAny dan create menerima Assignment sebagai argumen tambahan:
 *   $this->authorize('viewAny', [Submission::class, $assignment]);
 *   $this->authorize('create',  [Submission::class, $assignment]);
 *
 * Batas waktu (allow_late -> 422) dan konflik "sudah dinilai" (409) TETAP di
 * controller, karena kode status-nya bukan 403 dan 403 harus menang atas 422.
 */
class SubmissionPolicy
{
    use ChecksCourseRelation;

    /**
     * Admin boleh melihat semua pengumpulan (admin boleh membuat tugas, jadi
     * boleh melihat hasilnya; keputusan Q2). Dosen pemilik MK boleh melihat
     * semua pengumpulan pada tugasnya. Mahasiswa terdaftar boleh membuka
     * daftar, tetapi query WAJIB where('user_id', auth()->id()) supaya hanya
     * miliknya yang tampil.
     */
    public function viewAny(User $user, Assignment $assignment): bool
    {
        $course = $assignment->course;

        return $this->isAdmin($user)
            || $this->teaches($user, $course)
            || ($this->isEnrolled($user, $course) && $assignment->status === 'published');
    }

    /**
     * Mahasiswa hanya boleh melihat submission miliknya sendiri; submission
     * mahasiswa lain ditolak walau ID-nya ditebak. Dosen boleh jika
     * submission itu masuk MK yang dia ajar. Admin boleh (Q2).
     * [relasi] submission->user_id, submission->assignment->course->lecturer_id
     */
    public function view(User $user, Submission $submission): bool
    {
        if ($user->role === 'mahasiswa') {
            return (int) $submission->user_id === (int) $user->id;
        }

        return $this->isAdmin($user)
            || $this->teaches($user, $submission->assignment->course);
    }

    /** Unduh file pengumpulan (custom ability). Aturan sama dengan view. */
    public function download(User $user, Submission $submission): bool
    {
        return $this->view($user, $submission);
    }

    /**
     * Hanya mahasiswa yang terdaftar di MK pemilik tugas. Admin dan dosen
     * ditolak. Tugas draft dijawab 404 bagi mahasiswa terdaftar
     * (sama dengan Api\V1\SubmissionController::store).
     * [relasi] assignment->course->students()
     */
    public function create(User $user, Assignment $assignment): bool|Response
    {
        if (! $this->isEnrolled($user, $assignment->course)) {
            return false;
        }

        return $assignment->status === 'published'
            ? true
            : Response::denyAsNotFound();
    }

    /**
     * Kirim ulang: hanya pemilik submission, selama belum dinilai, dan
     * masih terdaftar pada tugas yang published. Dosen dan admin ditolak.
     */
    public function update(User $user, Submission $submission): bool
    {
        // 1. Jika user bukan pemilik submission (misal: admin/dosen), tolak langsung.
        if ((int) $submission->user_id !== (int) $user->id) {
            return false; 
        }

        // 2. Jika sudah ada nilai, submission terkunci (tidak boleh diubah).
        if ($submission->grade()->exists()) {
            return false;
        }

        $assignment = $submission->assignment;

        // 3. Harus published dan masih enrolled.
        return $assignment->status === 'published'
            && $this->isEnrolled($user, $assignment->course);
    }

    /**
     * Ditolak untuk semua peran (keputusan Q3). Kode lama di
     * SubmissionController::destroy mengizinkan pemilik/admin/dosen; sengaja
     * TIDAK ditiru.
     */
    public function delete(User $user, Submission $submission): bool
    {
        return false;
    }
}
