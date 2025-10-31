<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $sortable = ['title', 'price', 'created_at'];
        $sort = $request->get('sort', 'title');
        $direction = $request->get('direction', 'asc');
        if (! in_array($sort, $sortable, true)) {
            $sort = 'title';
        }
        if (! in_array(strtolower($direction), ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        $query = Product::query()
            ->with(['category'])
            ->withAvg('reviews', 'rating')
            ->when($request->boolean('only_active'), fn ($q) => $q->active())
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->whereHas('category', function ($sub) use ($request) {
                    $sub->where('slug', $request->string('category'));
                });
            })
            ->when($request->filled('min_price'), fn ($q) => $q->where('price', '>=', $request->float('min_price')))
            ->when($request->filled('max_price'), fn ($q) => $q->where('price', '<=', $request->float('max_price')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->string('search').'%';
                $q->where(function ($builder) use ($term) {
                    $builder->where('title', 'like', $term)
                        ->orWhere('article_id', 'like', $term)
                        ->orWhere('slug', 'like', $term);
                });
            });

        $products = $query
            ->orderBy($sort, $direction)
            ->paginate($request->integer('per_page', 15))
            ->appends($request->query());

        return ProductResource::collection($products)
            ->additional(['success' => true, 'message' => 'Список товаров']);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'reviews' => fn ($q) => $q->latest()])
            ->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        return ProductResource::make($product)
            ->additional(['success' => true, 'message' => 'Информация о товаре']);
    }

    public function store(StoreProductRequest $request)
    {
        $product = DB::transaction(function () use ($request) {
            $data = $request->validated();

            return Product::create($data);
        });

        return ProductResource::make($product->fresh(['category']))
            ->additional(['success' => true, 'message' => 'Товар успешно создан'])
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        DB::transaction(function () use ($request, $product) {
            $product->update($request->validated());
        });

        return ProductResource::make($product->fresh(['category'])->loadAvg('reviews', 'rating'))
            ->additional(['success' => true, 'message' => 'Товар обновлён']);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Товар удалён',
        ]);
    }
}

