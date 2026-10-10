<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\GradeComponent;
use App\Models\User;
use App\Policies\Concerns\ChecksCourseRelation;

class GradeComponentPolicy
{
    use ChecksCourseRelation;

    public function viewAny(User $user, Course $course): bool
    {
        return $this->canAccessCourse($user, $course);
    }

    public function create(User $user, Course $course): bool
    {
        return $this->canManageCourseContent($user, $course);
    }

    public function update(User $user, GradeComponent $component): bool
    {
        return $this->canManageCourseContent($user, $component->course);
    }

    public function delete(User $user, GradeComponent $component): bool
    {
        return $this->update($user, $component);
    }
}