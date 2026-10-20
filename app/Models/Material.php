<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Support\FileSize;                            
use Illuminate\Database\Eloquent\Casts\Attribute; 

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'type',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
        'external_url',
    ];

    protected function fileSizeForHumans(): Attribute
    {
        return Attribute::get(fn () => FileSize::format($this->file_size));
    }

    protected static function booted(): void
    {
        static::deleting(function (Material $material) {
            if ($material->file_path) {
                Storage::disk('local')->delete($material->file_path);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}