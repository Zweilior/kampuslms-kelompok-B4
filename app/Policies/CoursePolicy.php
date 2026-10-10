<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use App\Policies\Concerns\ChecksCourseRelation;

/**
 * Relasi dipakai: Course->lecturer_id (dosen pemilik), Course->students() (enrollment).
 */
class CoursePolicy
{
    use ChecksCourseRelation;

    /**
     * Admin, dosen, dan mahasiswa boleh membuka daftar mata kuliah.
     * ISI daftar wajib difilter lewat query: dosen hanya MK yang diajar,
     * mahasiswa hanya MK yang diikuti (seperti Api\V1\CourseController::index).
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'dosen', 'mahasiswa'], true);
    }

    /**
     * Admin boleh. Dosen boleh jika dia pemilik MK. Mahasiswa boleh jika
     * terdaftar di MK itu. Selain itu ditolak.
     * [relasi] course->lecturer_id, course->students()
     */
    public function view(User $user, Course $course): bool
    {
        return $this->canAccessCourse($user, $course);
    }

    /** Hanya admin. Dosen tidak boleh membuat MK. */
    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    /** Admin boleh mengubah semua data; dosen hanya data mata kuliah yang diampu. */
    public function update(User $user, Course $course): bool
    {
        return $this->canManageCourseContent($user, $course);
    }

    /** Hanya admin. */
    public function delete(User $user, Course $course): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Kelola enrollment (custom ability): admin boleh; dosen boleh hanya
     * pada MK miliknya; mahasiswa tidak boleh.
     * [relasi] course->lecturer_id
     */
    public function manageEnrollment(User $user, Course $course): bool
    {
        return $this->canManageCourseContent($user, $course);
    }
}
