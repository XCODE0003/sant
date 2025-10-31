<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()
            ->withCount('products')
            ->when($request->boolean('only_active'), fn ($q) => $q->active())
            ->orderBy('title')
            ->paginate($request->integer('per_page', 50))
            ->appends($request->query());

        return CategoryResource::collection($categories)
            ->additional(['success' => true, 'message' => 'Список категорий']);
    }

    public function show(Category $category)
    {
        $category->loadCount('products');

        return CategoryResource::make($category)
            ->additional(['success' => true, 'message' => 'Категория']);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return CategoryResource::make($category)
            ->additional(['success' => true, 'message' => 'Категория создана'])
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return CategoryResource::make($category)
            ->additional(['success' => true, 'message' => 'Категория обновлена']);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Категория удалена',
        ]);
    }
}

