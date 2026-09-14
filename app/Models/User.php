<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nim_nip',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function taughtCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'lecturer_id');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_user')
            ->withPivot('enrolled_at')
            ->withTimestamps();
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'uploaded_by');
    }

    public function gradesGiven(): HasMany
    {
        return $this->hasMany(Grade::class, 'graded_by');
    }

    public function finalGrade(): HasOne
    {
        return $this->hasOne(FinalGrade::class);
    }
}