<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderTrackingController;
use App\Models\Category;
use App\Models\News;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


$assetPath = static function (?string $path): ?string {
    if (! $path) {
        return null;
    }

    foreach (['http://', 'https://', '//'] as $prefix) {
        if (str_starts_with($path, $prefix)) {
            return $path;
        }
    }

    return asset('storage/' . ltrim($path, '/'));
};

$mapCategory = static function (Category $category) use ($assetPath): array {
    return [
        'id' => $category->id,
        'title' => $category->title,
        'slug' => $category->slug,
        'description' => $category->description,
        'image' => $assetPath($category->image),
        'is_active' => (bool) $category->is_active,
        'products_count' => $category->products_count ?? $category->products()->count(),
    ];
};

$mapReview = static function ($review): array {
    return [
        'id' => $review->id,
        'author_name' => $review->author_name,
        'author_email' => $review->author_email,
        'body' => $review->body,
        'rating' => (int) $review->rating,
        'created_at' => optional($review->created_at)?->toIso8601String(),
    ];
};

$mapProduct = static function (Product $product) use ($mapCategory, $mapReview, $assetPath): array {
    $rating = $product->reviews_avg_rating ?? $product->reviews?->avg('rating');

    return [
        'id' => $product->id,
        'title' => $product->title,
        'slug' => $product->slug,
        'article_id' => $product->article_id,
        'price' => (float) $product->price,
        'discount' => (int) $product->discount,
        'final_price' => round($product->price * (100 - $product->discount) / 100, 2),
        'characteristics' => $product->characteristics ?? [],
        'images' => collect($product->images ?? [])
            ->map($assetPath)
            ->filter()
            ->values()
            ->all(),
        'description' => $product->description,
        'is_active' => (bool) $product->is_active,
        'rating_avg' => $rating ? round($rating, 1) : null,
        'reviews_count' => $product->reviews_count ?? $product->reviews?->count(),
        'reviews' => $product->relationLoaded('reviews')
            ? $product->reviews->map($mapReview)->values()->all()
            : [],
        'category' => $product->relationLoaded('category') && $product->category
            ? $mapCategory($product->category)
            : null,
    ];
};

$mapProductPreview = static function (Product $product) use ($assetPath): array {
    $images = collect($product->images ?? [])
        ->map($assetPath)
        ->filter()
        ->values();

    return [
        'id' => $product->id,
        'title' => $product->title,
        'slug' => $product->slug,
        'article_id' => $product->article_id,
        'price' => (float) $product->price,
        'final_price' => round($product->price * (100 - $product->discount) / 100, 2),
        'discount' => (int) $product->discount,
        'image' => $images->first(),
        'images' => $images->all(),
        'category' => $product->relationLoaded('category') && $product->category
            ? [
                'id' => $product->category->id,
                'title' => $product->category->title,
            ]
            : null,
    ];
};

$normalizeTags = static function (?array $tags): array {
    return collect($tags ?? [])
        ->map(function ($tag) {
            if (is_string($tag)) {
                return [
                    'label' => $tag,
                    'color' => '#3B82F6',
                ];
            }

            if (is_array($tag)) {
                return [
                    'label' => $tag['label'] ?? $tag['name'] ?? $tag['value'] ?? 'Тег',
                    'color' => $tag['color'] ?? '#3B82F6',
                ];
            }

            return null;
        })
        ->filter()
        ->values()
        ->all();
};

$mapNews = static function (News $news) use ($assetPath, $normalizeTags): array {
    return [
        'id' => $news->id,
        'title' => $news->title,
        'slug' => $news->slug,
        'excerpt' => $news->excerpt,
        'content' => $news->content,
        'image' => $assetPath($news->image),
        'tags' => $normalizeTags($news->tags),
        'views' => (int) $news->views,
        'is_active' => (bool) $news->is_active,
        'published_at' => optional($news->published_at)?->toIso8601String(),
    ];
};

$escapeLikeWildcards = static function (string $value): string {
    return str_replace(
        ['\\', '%', '_'],
        ['\\\\', '\%', '\_'],
        $value,
    );
};

$extractSearchTerms = static function (string $value): array {
    return collect(preg_split('/\s+/u', trim($value), -1, PREG_SPLIT_NO_EMPTY))
        ->filter()
        ->unique(fn ($term) => mb_strtolower($term))
        ->values()
        ->all();
};

$applySearchFilter = static function (Builder $builder, array $terms, array $columns) use ($escapeLikeWildcards): void {
    if ($terms === []) {
        return;
    }

    $builder->where(function (Builder $query) use ($terms, $columns, $escapeLikeWildcards) {
        foreach ($terms as $term) {
            $escapedTerm = $escapeLikeWildcards($term);
            $like = '%' . $escapedTerm . '%';

            $query->where(function (Builder $nested) use ($columns, $like) {
                foreach ($columns as $index => $column) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $nested->{$method}($column, 'like', $like);
                }
            });
        }
    });
};

/*
|--------------------------------------------------------------------------
| Главная страница
|--------------------------------------------------------------------------
*/
Route::get('/', function (Request $request) use ($mapProduct, $mapCategory, $mapNews) {
    $featuredProducts = Product::query()
        ->active()
        ->with('category')
        ->withAvg('reviews', 'rating')
        ->latest()
        ->take(8)
        ->get();

    $topCategories = Category::query()
        ->active()
        ->withCount('products')
        ->orderByDesc('products_count')
        ->take(6)
        ->get();

    $latestNews = News::query()
        ->published()
        ->latest('published_at')
        ->take(3)
        ->get();

    $featuredProductsData = $featuredProducts
        ->map($mapProduct)
        ->all();

    $topCategoriesData = $topCategories
        ->map($mapCategory)
        ->all();

    $latestNewsData = $latestNews
        ->map($mapNews)
        ->all();

    return Inertia::render('Index', [
        'featuredProducts' => $featuredProductsData,
        'topCategories' => $topCategoriesData,
        'latestNews' => $latestNewsData,
    ]);
})->name('home');

/*
|--------------------------------------------------------------------------
| Каталог
|--------------------------------------------------------------------------
*/
Route::get('/catalog', function (Request $request) use ($mapProduct, $mapCategory) {
    $categorySlug = trim((string) $request->query('category', ''));

    $productsQuery = Product::query()
        ->active()
        ->with('category')
        ->withAvg('reviews', 'rating');

    if ($categorySlug !== '') {
        $selectedCategory = Category::query()->where('slug', $categorySlug)->first();

        if ($selectedCategory) {
            $productsQuery->where('category_id', $selectedCategory->id);
        } else {
            $productsQuery->whereRaw('0 = 1');
        }
    }

    $products = $productsQuery
        ->orderByRaw("
            CASE
                WHEN title REGEXP '^[А-Яа-яЁё]' THEN 1
                WHEN title REGEXP '^[A-Za-z]' THEN 2
                WHEN title REGEXP '^[0-9]' THEN 3
                ELSE 4
            END, title ASC
        ")
        ->paginate(12)
        ->withQueryString();

    $categories = Category::query()
        ->active()
        ->withCount('products')
        ->orderBy('title')
        ->get();

    $productsData = collect($products->items())
        ->map($mapProduct)
        ->all();

    $categoriesData = collect($categories)
        ->map($mapCategory)
        ->all();

    return Inertia::render('Catalog', [
        'products' => [
            'data' => $productsData,
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
                'next_page_url' => $products->nextPageUrl(),
                'prev_page_url' => $products->previousPageUrl(),
            ],
        ],
        'categories' => $categoriesData,
    ]);
})->name('catalog');

Route::get('/products/{id}', function (Request $request, $id) use ($mapProduct) {
    $product = Product::query()
        ->with(['category', 'reviews' => fn($q) => $q->latest()])
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->findOrFail($id);

    return Inertia::render('Product', [
        'product' => $mapProduct($product),
    ]);
})->name('product');

/*
|--------------------------------------------------------------------------
| Информационные страницы
|--------------------------------------------------------------------------
*/
Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

Route::get('/services', function () {
    return Inertia::render('Services');
})->name('services');

Route::get('/contacts', function (Request $request) use ($mapCategory) {
    $categories = Category::query()->active()->orderBy('title')->get();

    $categoriesData = $categories
        ->map($mapCategory)
        ->all();

    return Inertia::render('Contacts', [
        'categories' => $categoriesData,
    ]);
})->name('contacts');

Route::get('/payment', function () {
    return Inertia::render('Payment');
})->name('payment');

Route::get('/favorites', function () {
    return Inertia::render('Favorites');
})->name('favorites');

Route::get('/cart', function () {
    return Inertia::render('Cart');
})->name('cart');

Route::get('/search/suggestions', function (Request $request) use ($mapProductPreview, $extractSearchTerms, $applySearchFilter) {
    $query = trim((string) $request->query('q', ''));

    if ($query === '') {
        return response()->json([
            'data' => [],
        ]);
    }

    $terms = $extractSearchTerms($query);

    $productsQuery = Product::query()
        ->active()
        ->with(['category' => fn ($relation) => $relation->select('id', 'title')]);

    $applySearchFilter($productsQuery, $terms, ['title', 'article_id', 'slug']);

    $products = $productsQuery
        ->orderByRaw('CASE WHEN article_id = ? THEN 0 ELSE 1 END', [$query])
        ->orderByDesc('discount')
        ->orderBy('title')
        ->take(8)
        ->get();

    return response()->json([
        'data' => $products->map($mapProductPreview)->all(),
    ]);
})->name('search.suggestions');

Route::get('/search', function (Request $request) use ($mapProduct, $mapNews, $extractSearchTerms, $applySearchFilter) {
    $query = trim((string) $request->query('q', ''));

    if ($query === '') {
        return Inertia::render('Search', [
            'query' => '',
            'products' => [],
            'news' => [],
        ]);
    }

    $terms = $extractSearchTerms($query);

    $productsQuery = Product::query()
        ->active()
        ->with('category')
        ->withAvg('reviews', 'rating');

    $applySearchFilter($productsQuery, $terms, ['title', 'article_id', 'slug']);

    $products = $productsQuery->get();

    $newsQuery = News::query()
        ->published();

    $applySearchFilter($newsQuery, $terms, ['title', 'excerpt', 'content', 'tags']);

    $news = $newsQuery
        ->take(8)
        ->get();

    return Inertia::render('Search', [
        'query' => $query,
        'products' => $products->map($mapProduct)->all(),
        'news' => $news->map($mapNews)->all(),
    ]);
})->name('search');

Route::get('/news', function (Request $request) use ($mapNews) {
    $news = News::query()->published()->latest('published_at')->paginate(9)->withQueryString();
    $newsData = collect($news->items())
        ->map($mapNews)
        ->all();

    return Inertia::render('News', [
        'news' => [
            'data' => $newsData,
            'meta' => [
                'current_page' => $news->currentPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
                'last_page' => $news->lastPage(),
                'next_page_url' => $news->nextPageUrl(),
                'prev_page_url' => $news->previousPageUrl(),
            ],
        ],
    ]);
})->name('news');

Route::get('/news/{slug}', function (Request $request, string $slug) use ($mapNews) {
    $news = News::query()->where('slug', $slug)->firstOrFail();

    if ($news->is_active && optional($news->published_at)?->isPast()) {
        $news->increment('views');
        $news->refresh();
    }

    return Inertia::render('NewsShow', [
        'news' => $mapNews($news),
    ]);
})->name('news.show');

Route::get('/disclaimer', function () {
    return Inertia::render('Disclaimer');
})->name('disclaimer');

/*
|--------------------------------------------------------------------------
| Оформление заказа
|--------------------------------------------------------------------------
*/
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');

Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/orders/{order:uuid}', OrderTrackingController::class)->name('orders.show');

/*
|--------------------------------------------------------------------------
| Dashboard (защищенные маршруты)
|--------------------------------------------------------------------------
*/
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/settings.php';
