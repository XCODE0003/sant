<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::query()
            ->when($request->boolean('only_published', true), fn ($q) => $q->published())
            ->when($request->filled('tag'), function ($q) use ($request) {
                $q->whereJsonContains('tags', $request->string('tag'));
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->string('search').'%';
                $q->where(function ($builder) use ($term) {
                    $builder->where('title', 'like', $term)
                        ->orWhere('excerpt', 'like', $term);
                });
            });

        $news = $query
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($request->integer('per_page', 12))
            ->appends($request->query());

        return NewsResource::collection($news)
            ->additional(['success' => true, 'message' => 'Новости']);
    }

    public function show(Request $request, News $news)
    {
        if ($request->boolean('increment_views', true)) {
            $news->increment('views');
        }

        return NewsResource::make($news)
            ->additional(['success' => true, 'message' => 'Новость']);
    }

    public function store(StoreNewsRequest $request)
    {
        $news = News::create($request->validated());

        return NewsResource::make($news)
            ->additional(['success' => true, 'message' => 'Новость создана'])
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateNewsRequest $request, News $news)
    {
        $news->update($request->validated());

        return NewsResource::make($news)
            ->additional(['success' => true, 'message' => 'Новость обновлена']);
    }

    public function destroy(News $news): JsonResponse
    {
        $news->delete();

        return response()->json([
            'success' => true,
            'message' => 'Новость удалена',
        ]);
    }
}

