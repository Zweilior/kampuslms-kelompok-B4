<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Grade extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(function (Grade $grade): void {
            $lecturerId = $grade->submission()
                ->with('assignment.course')
                ->first()?->assignment?->course?->lecturer_id;

            if ($lecturerId) {
                Cache::forget("dashboard:dosen:{$lecturerId}:stats");
            }
        });
    }

    protected $fillable = [
        'submission_id',
        'graded_by',
        'score',
        'feedback',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'graded_at' => 'datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
