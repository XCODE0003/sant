<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Product
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'article_id' => $this->article_id,
            'price' => (float) $this->price,
            'discount' => $this->discount,
            'final_price' => round((float) $this->price * (100 - $this->discount) / 100, 2),
            'description' => $this->description,
            'characteristics' => $this->characteristics,
            'images' => $this->images,
            'is_active' => $this->is_active,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'reviews_count' => $this->whenCounted('reviews'),
            'rating_avg' => $this->when(
                fn () => isset($this->reviews_avg_rating) || $this->relationLoaded('reviews'),
                fn () => $this->reviews_avg_rating !== null
                    ? round($this->reviews_avg_rating, 1)
                    : ($this->relationLoaded('reviews') && $this->reviews->isNotEmpty()
                        ? round($this->reviews->avg('rating'), 1)
                        : null)
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

