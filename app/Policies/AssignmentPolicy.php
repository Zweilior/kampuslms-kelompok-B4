<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use App\Policies\Concerns\ChecksCourseRelation;
use Illuminate\Auth\Access\Response;

/**
 * Relasi dipakai: Assignment->course, lalu Course->lecturer_id / Course->students().
 *
 * viewAny dan create menerima Course sebagai argumen tambahan:
 *   $this->authorize('viewAny', [Assignment::class, $course]);
 *   $this->authorize('create',  [Assignment::class, $course]);
 */
class AssignmentPolicy
{
    use ChecksCourseRelation;

    /**
     * Admin boleh. Dosen boleh pada MK sendiri. Mahasiswa boleh pada MK yang
     * diikuti, tetapi isi daftarnya hanya tugas berstatus published
     * (filter di query, seperti Api\V1\CourseController::assignments).
     */
    public function viewAny(User $user, Course $course): bool
    {
        return $this->canAccessCourse($user, $course);
    }

    /**
     * Admin dan dosen pemilik boleh melihat semua tugas (termasuk draft).
     * Mahasiswa terdaftar hanya boleh melihat tugas published; tugas draft
     * dijawab 404 supaya keberadaannya tidak bocor. Mahasiswa yang tidak
     * terdaftar ditolak (403).
     */
    public function view(User $user, Assignment $assignment): bool|Response
    {
        $course = $assignment->course;

        if ($this->canManageCourseContent($user, $course)) {
            return true;
        }

        if (! $this->isEnrolled($user, $course)) {
            return false;
        }

        return $assignment->status === 'published'
            ? true
            : Response::denyAsNotFound();
    }

    /**
     * Admin boleh. Dosen boleh hanya pada MK sendiri. Mahasiswa ditolak.
     * [relasi] course->lecturer_id (pada MK tujuan)
     */
    public function create(User $user, Course $course): bool
    {
        return $this->canManageCourseContent($user, $course);
    }

    /**
     * Admin boleh. Dosen boleh jika tugas itu milik MK-nya. Mahasiswa ditolak.
     * [relasi] assignment->course->lecturer_id
     */
    public function update(User $user, Assignment $assignment): bool
    {
        return $this->canManageCourseContent($user, $assignment->course);
    }

    /**
     * Sama dengan update. Tugas yang sudah punya pengumpulan tidak boleh
     * dihapus (keputusan Q6), tetapi itu bukan soal hak akses: controller
     * menolaknya dengan pesan jelas (web) atau 409 (API).
     */
    public function delete(User $user, Assignment $assignment): bool
    {
        return $this->update($user, $assignment);
    }
}
