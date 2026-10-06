<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * file_path SENGAJA tidak dikeluarkan: itu path internal di storage.
 * Pengunduhan file akan dibuat sebagai endpoint terpisah (minggu 9).
 */
class SubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'assignment_id' => $this->assignment_id,
            'student'       => new UserResource($this->whenLoaded('student')),
            'original_name' => $this->original_name,
            'file_size'     => $this->file_size,
            'note'          => $this->note,
            'submitted_at'  => $this->submitted_at?->toIso8601String(),
            'is_late'       => $this->is_late,
            'grade'         => new GradeResource($this->whenLoaded('grade')),
        ];
    }
}
