<?php

namespace App\Policies\Concerns;

use App\Models\Course;
use App\Models\User;

/**
 * Pengecekan peran dan kepemilikan yang dipakai bersama oleh semua Policy.
 *
 * Sengaja TIDAK memakai Gate::before untuk admin: matriks akses menolak
 * admin pada "mengumpulkan tugas" dan "memberi nilai", jadi admin harus
 * dicek per method, bukan diloloskan global.
 */
trait ChecksCourseRelation
{
    protected function isAdmin(User $user): bool
    {
        return $user->role === 'admin';
    }

    /** Dosen pemilik mata kuliah: Course::lecturer_id == user.id. */
    protected function teaches(User $user, Course $course): bool
    {
        return $user->role === 'dosen'
            && (int) $course->lecturer_id === (int) $user->id;
    }

    /** Mahasiswa terdaftar: ada baris di pivot course_user. */
    protected function isEnrolled(User $user, Course $course): bool
    {
        return $user->role === 'mahasiswa'
            && $course->students()->where('users.id', $user->id)->exists();
    }

    /** Admin, dosen pemilik, atau mahasiswa terdaftar. */
    protected function canAccessCourse(User $user, Course $course): bool
    {
        return $this->isAdmin($user)
            || $this->teaches($user, $course)
            || $this->isEnrolled($user, $course);
    }

    /** Admin, atau dosen pemilik. Dipakai untuk aksi tulis (CUD). */
    protected function canManageCourseContent(User $user, Course $course): bool
    {
        return $this->isAdmin($user) || $this->teaches($user, $course);
    }
}
