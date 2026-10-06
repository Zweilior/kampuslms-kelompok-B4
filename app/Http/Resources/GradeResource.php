<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'submission_id' => $this->submission_id,
            // kolom decimal dikembalikan Eloquent sebagai string ("85.00")
            'score'         => (float) $this->score,
            'feedback'      => $this->feedback,
            'graded_at'     => $this->graded_at?->toIso8601String(),
            'grader'        => new UserResource($this->whenLoaded('grader')),
        ];
    }
}
