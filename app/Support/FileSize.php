<?php

namespace App\Support;

class FileSize
{
    public static function format(?int $bytes): ?string
    {
        if ($bytes === null) {
            return null;
        }

        if ($bytes < 1024) {
            return $bytes.' B';
        }

        $units = ['KB', 'MB', 'GB'];
        $size = $bytes;
        $i = -1;

        do {
            $size /= 1024;
            $i++;
        } while ($size >= 1024 && $i < count($units) - 1);

        return number_format($size, 1, ',', '.').' '.$units[$i];
    }
}