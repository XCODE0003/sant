<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request)
    {
        $review = Review::create($request->validated());

        return ReviewResource::make($review)
            ->additional(['success' => true, 'message' => 'Отзыв добавлен'])
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Review $review): JsonResponse
    {
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Отзыв удалён',
        ]);
    }
}

