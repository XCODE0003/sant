<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Review
 */
class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'author_name' => $this->author_name,
            'author_email' => $this->author_email,
            'body' => $this->body,
            'rating' => $this->rating,
            'created_at' => optional($this->created_at)?->toIso8601String(),
        ];
    }
}

