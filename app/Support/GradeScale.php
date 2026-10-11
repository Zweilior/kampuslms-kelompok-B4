<?php

namespace App\Support;

class GradeScale
{
    public static function letterGradeForScore(?float $score): ?string
    {
        return match (true) {
            $score === null => null,
            $score >= 86 => 'A',
            $score >= 76 => 'AB',
            $score >= 66 => 'B',
            $score >= 56 => 'BC',
            $score >= 46 => 'C',
            $score >= 36 => 'D',
            default => 'E',
        };
    }
}
