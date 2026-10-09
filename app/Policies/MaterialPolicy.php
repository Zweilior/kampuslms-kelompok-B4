<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Material;
use App\Models\User;
use App\Policies\Concerns\ChecksCourseRelation;

/**
 * Relasi dipakai: Material->course, lalu Course->lecturer_id / Course->students().
 *
 * viewAny dan create menerima Course sebagai argumen tambahan:
 *   $this->authorize('viewAny', [Material::class, $course]);
 *   $this->authorize('create',  [Material::class, $course]);
 */
class MaterialPolicy
{
    use ChecksCourseRelation;

    /**
     * Admin boleh. Dosen boleh pada MK sendiri. Mahasiswa boleh pada MK yang
     * diikuti (lihat). [relasi] course->lecturer_id, course->students()
     */
    public function viewAny(User $user, Course $course): bool
    {
        return $this->canAccessCourse($user, $course);
    }

    /** Aturan sama dengan viewAny, lewat material->course. */
    public function view(User $user, Material $material): bool
    {
        return $this->canAccessCourse($user, $material->course);
    }

    /**
     * Unduh file materi (custom ability). Siapa yang boleh melihat materi
     * boleh mengunduhnya; pengguna lain ditolak. File harus berada di disk
     * privat dan dikirim lewat controller yang memanggil ability ini.
     */
    public function download(User $user, Material $material): bool
    {
        return $this->view($user, $material);
    }

    /**
     * Admin boleh. Dosen boleh hanya jika MK tujuan miliknya. Mahasiswa ditolak.
     * [relasi] course->lecturer_id (pada MK tujuan)
     */
    public function create(User $user, Course $course): bool
    {
        return $this->canManageCourseContent($user, $course);
    }

    /**
     * Admin boleh. Dosen boleh jika materi itu milik MK-nya. Mahasiswa ditolak.
     * [relasi] material->course->lecturer_id
     */
    public function update(User $user, Material $material): bool
    {
        return $this->canManageCourseContent($user, $material->course);
    }

    /** Sama dengan update. */
    public function delete(User $user, Material $material): bool
    {
        return $this->update($user, $material);
    }
}
