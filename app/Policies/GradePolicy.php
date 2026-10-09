<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use App\Policies\Concerns\ChecksCourseRelation;
use Illuminate\Auth\Access\Response;

/**
 * Relasi dipakai:
 *   Grade->submission->user_id (mahasiswa pemilik nilai)
 *   Grade->submission->assignment->course->lecturer_id (dosen pemilik MK)
 *
 * create menerima Submission yang akan dinilai:
 *   $this->authorize('create', [Grade::class, $submission]);
 *
 * Aturan tenggat (keputusan Q5): nilai hanya boleh diberikan atau diubah
 * SETELAH due_at tugas lewat. Aturan ini ada di policy supaya web dan API
 * memakai aturan yang sama.
 */
class GradePolicy
{
    use ChecksCourseRelation;

    /**
     * Admin, dosen, dan mahasiswa boleh membuka halaman nilai. ISI wajib
     * difilter lewat query: dosen hanya MK yang diajar, mahasiswa hanya
     * nilainya sendiri, admin semua.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'dosen', 'mahasiswa'], true);
    }

    /**
     * Admin boleh melihat semua nilai. Dosen boleh jika nilai itu berasal dari
     * MK yang dia ajar. Mahasiswa hanya boleh melihat nilainya sendiri;
     * nilai mahasiswa lain ditolak.
     * [relasi] grade->submission->user_id,
     *          grade->submission->assignment->course->lecturer_id
     */
    public function view(User $user, Grade $grade): bool
    {
        $submission = $grade->submission;

        if ($this->isAdmin($user)) {
            return true;
        }

        if ($user->role === 'mahasiswa') {
            return (int) $submission->user_id === (int) $user->id;
        }

        return $this->teaches($user, $submission->assignment->course);
    }

    /**
     * Memberi nilai: hanya dosen pemilik MK dari submission tersebut, dan
     * hanya setelah tenggat tugas lewat. Admin ditolak (matriks: "—"),
     * mahasiswa ditolak.
     * [relasi] submission->assignment->course->lecturer_id
     */
    public function create(User $user, Submission $submission): bool|Response
    {
        return $this->canGrade($user, $submission);
    }

    /**
     * Mengubah/merevisi nilai: bagian dari "memberi nilai" (API memakai
     * upsert), jadi aturannya sama dengan create.
     * [relasi] grade->submission->assignment->course->lecturer_id
     */
    public function update(User $user, Grade $grade): bool|Response
    {
        return $this->canGrade($user, $grade->submission);
    }

    private function canGrade(User $user, Submission $submission): bool|Response
    {
        $assignment = $submission->assignment;

        if (! $this->teaches($user, $assignment->course)) {
            return false;
        }

        return $assignment->due_at->lte(now())
            ? true
            : Response::deny('Nilai hanya dapat diubah setelah tenggat tugas selesai.');
    }

    /** Ditolak untuk semua peran (keputusan Q4): nilai hanya diubah, tidak dihapus. */
    public function delete(User $user, Grade $grade): bool
    {
        return false;
    }
}
