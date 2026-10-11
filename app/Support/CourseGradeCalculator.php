<?php

namespace App\Support;

use App\Models\Assignment;
use Illuminate\Support\Collection;

class CourseGradeCalculator
{
    public static function finalScore(Collection $assignments): ?float
    {
        $gradedAssignments = $assignments->filter(
            fn (Assignment $assignment) => $assignment->grade_component_id !== null
                && $assignment->submissions->first()?->grade
        );
        $gradedComponentGroups = $gradedAssignments->groupBy('grade_component_id');
        $gradedWeight = $gradedComponentGroups->sum(
            fn ($group) => (float) $group->first()->gradeComponent->weight
        );

        if ($gradedWeight <= 0) {
            return null;
        }

        $totalWeightedScore = $gradedComponentGroups->sum(function ($group) {
            $weight = (float) $group->first()->gradeComponent->weight;
            $componentAverage = $group->avg(function (Assignment $assignment) {
                $submission = $assignment->submissions->first();

                return ((float) $submission->grade->score / max($assignment->max_score, 1)) * 100;
            });

            return ($componentAverage * $weight) / 100;
        });

        return round(
            (($totalWeightedScore / $gradedWeight) * 100) + 1e-9,
            2,
            PHP_ROUND_HALF_UP
        );
    }
}
