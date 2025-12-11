<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useShopStore } from '@/stores/useShopStore';

const props = defineProps({
    products: {
        type: Object,
        default: () => ({ data: [], meta: {} }),
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const breadcrumbs = [
    { label: 'Главная', href: '/' },
    { label: 'Каталог товаров' },
];

const priceFormatter = new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    maximumFractionDigits: 0,
});

const formatCurrency = (value) => priceFormatter.format(value ?? 0);
const getProductImage = (product) => product?.images?.[0] ?? null;

const productItems = computed(() => props.products?.data ?? []);
const meta = computed(() => props.products?.meta ?? {});
const hasProducts = computed(() => productItems.value.length > 0);
const hasCategories = computed(() => props.categories.length > 0);

const page = usePage();
const baseUrl = page.props?.ziggy?.url ?? (typeof window !== 'undefined' ? window.location.origin : 'http://localhost');
const activeCategorySlug = computed(() => {
    const parts = page.url.split('?');
    if (parts.length < 2) {
        return null;
    }
    const params = new URLSearchParams(parts[1]);
    return params.get('category');
});

const makePageLink = (url) => {
    if (!url) {
        return null;
    }

    try {
        const absolute = new URL(url, baseUrl);
        return absolute.pathname + absolute.search;
    } catch (error) {
        return url;
    }
};

// View mode: 'grid' or 'list'
const viewMode = ref('list');

const toggleViewMode = (mode) => {
    viewMode.value = mode;
};

// Pagination logic
const generatePaginationPages = computed(() => {
    const current = meta.value.current_page ?? 1;
    const last = meta.value.last_page ?? 1;
    const pages = [];

    if (last <= 7) {
        // Show all pages if total is 7 or less
        for (let i = 1; i <= last; i++) {
            pages.push(i);
        }
    } else {
        // Always show first page
        pages.push(1);

        if (current <= 3) {
            // Near start: 1 2 3 4 ... last
            pages.push(2, 3, 4);
            pages.push('...');
            pages.push(last);
        } else if (current >= last - 2) {
            // Near end: 1 ... last-3 last-2 last-1 last
            pages.push('...');
            pages.push(last - 3, last - 2, last - 1, last);
        } else {
            // Middle: 1 ... current-1 current current+1 ... last
            pages.push('...');
            pages.push(current - 1, current, current + 1);
            pages.push('...');
            pages.push(last);
        }
    }

    return pages;
});

const makePageLinkForPage = (pageNumber) => {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('page', pageNumber);
    return currentUrl.pathname + currentUrl.search;
};

const shopStore = useShopStore();
shopStore.initialize();

const addProductToCart = (product) => {
    if (shopStore.isInCart(product.id)) {
        router.visit('/cart');
        return;
    }

    shopStore.addToCart(product, 1);
};

const toggleFavorite = (product) => {
    shopStore.toggleFavorite(product);
};

const isFavorite = (product) => shopStore.isFavorite(product.id);
const isInCart = (product) => shopStore.isInCart(product.id);
</script>

<template>
    <AppLayout title="Каталог товаров - Акватэрия">
        <Breadcrumbs :items="breadcrumbs" />

        <div class="md:py-8 container px-4 py-6 mx-auto">
            <div class="lg:flex-row md:gap-8 flex flex-col gap-6">
                <!-- Sidebar Filters -->
                <aside class="lg:w-1/4">
                    <div class="md:rounded-2xl md:p-6 lg:sticky lg:top-24 p-4 bg-white rounded-xl shadow-lg">
                        <h3 class="md:text-xl md:mb-6 mb-4 text-lg font-semibold">Категории</h3>
                        <div v-if="hasCategories" class="md:space-y-2 space-y-1.5">
                            <Link
                                href="/catalog"
                                preserve-scroll
                                :class="[
                                    'flex items-center justify-between px-3 py-2 rounded-lg transition-colors text-sm md:text-base touch-manipulation',
                                    activeCategorySlug === null
                                        ? 'bg-primary text-white'
                                        : 'text-gray-700 hover:bg-gray-100 active:bg-gray-200',
                                ]"
                            >
                                <span>Все товары</span>
                                <i class="fas fa-layer-group text-sm"></i>
                            </Link>
                            <Link
                                v-for="category in categories"
                                :key="category.id"
                                :href="`/catalog?category=${category.slug}`"
                                preserve-state
                                preserve-scroll
                                :class="[
                                    'flex items-center justify-between px-3 py-2 rounded-lg transition-colors text-sm md:text-base touch-manipulation',
                                    activeCategorySlug === category.slug
                                        ? 'bg-primary text-white'
                                        : 'text-gray-700 hover:bg-gray-100 active:bg-gray-200',
                                ]"
                            >
                                <span class="mr-2 truncate">{{ category.title }}</span>
                                <span class="md:text-sm flex-shrink-0 text-xs opacity-80">{{ category.products_count ?? 0 }}</span>
                            </Link>
                        </div>
                        <div v-else class="text-sm text-gray-500">
                            Нет активных категорий. Добавьте их через панель администратора Filament.
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <section class="lg:w-3/4">
                    <div class="md:rounded-2xl md:p-6 md:mb-6 p-4 mb-4 bg-white rounded-xl shadow-lg">
                        <div class="sm:flex-row sm:items-center sm:justify-between md:gap-4 flex flex-col gap-3">
                            <h3 class="md:text-lg text-base font-semibold text-gray-800">
                                Найдено товаров: {{ meta.total ?? productItems.length }}
                            </h3>
                            <div class="flex gap-2 items-center">
                                <span class="md:text-sm text-xs text-gray-500">Вид:</span>
                                <button
                                    type="button"
                                    class="md:text-base touch-manipulation p-2 text-sm rounded-lg transition-colors"
                                    :class="viewMode === 'grid' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                    @click="toggleViewMode('grid')"
                                    title="Сетка"
                                >
                                    <i class="fas fa-th"></i>
                                </button>
                                <button
                                    type="button"
                                    class="md:text-base touch-manipulation p-2 text-sm rounded-lg transition-colors"
                                    :class="viewMode === 'list' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                    @click="toggleViewMode('list')"
                                    title="Список"
                                >
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Grid View -->
                    <div v-if="hasProducts && viewMode === 'grid'" class="sm:grid-cols-2 xl:grid-cols-3 md:gap-6 lg:gap-8 grid grid-cols-1 gap-4">
                        <article
                            v-for="product in productItems"
                            :key="product.id"
                            class="md:rounded-2xl hover:shadow-2xl active:shadow-xl group overflow-hidden bg-white rounded-xl shadow-lg transition-all duration-300"
                        >
                            <Link :href="`/products/${product.id}`" class="block">
                                <div class="relative">
                                    <div class="sm:h-52 md:h-60 flex overflow-hidden justify-center items-center h-44 bg-gray-100">
                                        <img
                                            v-if="getProductImage(product)"
                                            :src="getProductImage(product)"
                                            :alt="product.title"
                                            class="w-full h-full"
                                        />
                                        <i v-else class="fas fa-faucet text-6xl text-gray-300"></i>
                                    </div>
                                    <span
                                        v-if="product.discount > 0"
                                        class="md:top-4 md:left-4 md:px-3 md:text-sm absolute top-2 left-2 px-2 py-1 text-xs font-semibold text-white bg-red-500 rounded-full"
                                    >
                                        -{{ product.discount }}%
                                    </span>
                                </div>
                                <div class="md:p-6 p-4">
                                    <h3 class="md:text-lg group-hover:text-primary line-clamp-2 mb-2 text-base font-semibold text-gray-900 transition-colors">
                                        {{ product.title }}
                                    </h3>
                                    <p v-if="product.category" class="md:text-sm md:mb-3 mb-2 text-xs text-gray-500">
                                        {{ product.category.title }}
                                    </p>
                                    <p v-if="product.description" class="md:text-sm md:mb-4 line-clamp-2 mb-3 text-xs text-gray-500">
                                        <span v-html="product.description"></span>
                                    </p>
                                    <div class="md:mb-4 flex justify-between items-center mb-3">
                                        <div>
                                            <div class="md:text-2xl text-primary text-lg font-bold">
                                                {{ formatCurrency(product.final_price) }}
                                            </div>
                                            <div v-if="product.discount > 0" class="md:text-sm text-xs text-gray-400 line-through">
                                                {{ formatCurrency(product.price) }}
                                            </div>
                                        </div>
                                        <div v-if="product.rating_avg" class="flex items-center text-yellow-500">
                                            <i class="fas fa-star md:text-sm mr-1 text-xs"></i>
                                            <span class="md:text-sm text-xs font-semibold">{{ product.rating_avg }}</span>
                                        </div>
                                    </div>
                                    <div class="md:text-sm flex justify-between items-center text-xs text-gray-500">
                                        <span>Отзывы: {{ product.reviews_count ?? 0 }}</span>
                                        <span class="ml-2 truncate">ID: {{ product.article_id }}</span>
                                    </div>
                                    <div class="md:mt-4 flex gap-2 justify-between items-center mt-3">
                                        <button
                                            type="button"
                                            class="md:px-4 md:text-sm touch-manipulation flex-1 px-3 py-2 text-xs font-semibold rounded-lg transition-colors"
                                            :class="isInCart(product)
                                                ? 'bg-green-500 hover:bg-green-600 active:bg-green-700 text-white'
                                                : 'bg-primary hover:bg-secondary active:bg-secondary text-white'"
                                            @click.prevent.stop="addProductToCart(product)"
                                        >
                                            <i :class="[isInCart(product) ? 'fas fa-check' : 'fas fa-cart-plus', 'mr-1 md:mr-2']"></i>
                                            <span class="sm:inline hidden">{{ isInCart(product) ? 'В корзине' : 'В корзину' }}</span>
                                            <span class="sm:hidden">{{ isInCart(product) ? 'В корзине' : 'Купить' }}</span>
                                        </button>
                                        <button
                                            type="button"
                                            class="md:text-lg touch-manipulation p-2 text-base transition-colors"
                                            :class="isFavorite(product) ? 'text-red-500' : 'text-gray-400 hover:text-red-500 active:text-red-600'"
                                            @click.prevent.stop="toggleFavorite(product)"
                                        >
                                            <i :class="isFavorite(product) ? 'fas fa-heart' : 'far fa-heart'"></i>
                                        </button>
                                    </div>
                                </div>
                            </Link>
                        </article>
                    </div>

                    <!-- List View (Table) -->
                    <div v-if="hasProducts && viewMode === 'list'" class="md:rounded-2xl overflow-hidden bg-white rounded-xl shadow-lg">
                        <!-- Table Header (Desktop only) -->
                        <div class="md:grid md:gap-4 md:p-4 md:bg-gray-50 md:border-b md:border-gray-200 md:font-semibold md:text-sm hidden grid-cols-12 text-gray-700">
                            <div class="col-span-1">Фото</div>
                            <div class="col-span-4">Название</div>
                            <div class="col-span-2">Категория</div>
                            <div class="col-span-1 text-center">Рейтинг</div>
                            <div class="col-span-2 text-right">Цена</div>
                            <div class="col-span-2 text-center">Действия</div>
                        </div>

                        <!-- Table Rows -->
                        <div class="md:divide-y md:divide-gray-200 md:p-0 flex flex-col gap-3 p-3">
                            <article
                                v-for="product in productItems"
                                :key="product.id"
                                class="md:grid md:gap-4 md:p-4 md:rounded-none md:shadow-none md:hover:bg-gray-50 md:transition-colors hover:shadow-xl active:shadow-lg group grid-cols-12 items-center bg-white rounded-xl shadow-md transition-all"
                            >
                                <!-- Image -->
                                <div class="md:col-span-1 md:w-full md:h-16 relative w-full h-32">
                                    <Link :href="`/products/${product.id}`" class="block w-full h-full">
                                        <div class="md:rounded-lg flex overflow-hidden justify-center items-center w-full h-full bg-gray-100">
                                            <img
                                                v-if="getProductImage(product)"
                                                :src="getProductImage(product)"
                                                :alt="product.title"
                                                class="w-full h-full"
                                            />
                                            <i v-else class="fas fa-faucet md:text-2xl text-4xl text-gray-300"></i>
                                        </div>
                                        <span
                                            v-if="product.discount > 0"
                                            class="md:top-1 md:left-1 md:px-2 md:py-0.5 md:text-xs absolute top-2 left-2 px-2 py-1 text-xs font-semibold text-white bg-red-500 rounded-full"
                                        >
                                            -{{ product.discount }}%
                                        </span>
                                    </Link>
                                </div>

                                <!-- Title & Description -->
                                <div class="md:col-span-4 md:p-0 p-3">
                                    <Link :href="`/products/${product.id}`">
                                        <h3 class="md:text-base group-hover:text-primary md:mb-1 mb-1 text-sm font-semibold text-gray-900 transition-colors">
                                            {{ product.title }}
                                        </h3>
                                        <p v-if="product.description" class="md:text-xs line-clamp-2 text-xs text-gray-500">
                                            <span v-html="product.description"></span>
                                        </p>
                                        <p class="md:hidden mt-1 text-xs text-gray-400">
                                            ID: {{ product.article_id }}
                                        </p>
                                    </Link>
                                </div>

                                <!-- Category -->
                                <div class="md:col-span-2 md:block md:text-sm md:px-0 hidden text-xs text-gray-600">
                                    {{ product.category?.title ?? '—' }}
                                </div>

                                <!-- Rating -->
                                <div class="md:col-span-1 md:flex md:flex-col md:items-center md:gap-1 hidden">
                                    <div v-if="product.rating_avg" class="flex items-center text-yellow-500">
                                        <i class="fas fa-star mr-1 text-xs"></i>
                                        <span class="text-sm font-semibold">{{ product.rating_avg }}</span>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ product.reviews_count ?? 0 }} отз.</span>
                                </div>

                                <!-- Price -->
                                <div class="md:col-span-2 md:text-right md:px-0 md:pb-0 px-3 pb-3">
                                    <div class="md:text-xl text-primary text-lg font-bold">
                                        {{ formatCurrency(product.final_price) }}
                                    </div>
                                    <div v-if="product.discount > 0" class="md:text-sm text-xs text-gray-400 line-through">
                                        {{ formatCurrency(product.price) }}
                                    </div>
                                    <div class="md:hidden mt-1 text-xs text-gray-500">
                                        <span v-if="product.rating_avg" class="inline-flex items-center mr-2 text-yellow-500">
                                            <i class="fas fa-star mr-1"></i>
                                            {{ product.rating_avg }}
                                        </span>
                                        <span>{{ product.reviews_count ?? 0 }} отзывов</span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="md:col-span-2 md:flex-row md:justify-center md:px-0 md:pb-0 flex gap-2 justify-end px-3 pb-3">
                                    <button
                                        type="button"
                                        class="md:w-10 md:h-10 md:p-0 md:flex md:items-center md:justify-center touch-manipulation flex-1 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
                                        :class="isInCart(product)
                                            ? 'bg-green-500 hover:bg-green-600 active:bg-green-700 text-white'
                                            : 'bg-primary hover:bg-secondary active:bg-secondary text-white'"
                                        @click.prevent.stop="addProductToCart(product)"
                                        :title="isInCart(product) ? 'В корзине' : 'Добавить в корзину'"
                                    >
                                        <i :class="[isInCart(product) ? 'fas fa-check' : 'fas fa-cart-plus']"></i>
                                        <span class="md:hidden ml-2">{{ isInCart(product) ? 'В корзине' : 'В корзину' }}</span>
                                    </button>
                                    <button
                                        type="button"
                                        class="md:w-10 md:h-10 md:p-0 md:flex md:items-center md:justify-center touch-manipulation p-2 text-lg transition-colors"
                                        :class="isFavorite(product) ? 'text-red-500' : 'text-gray-400 hover:text-red-500 active:text-red-600'"
                                        @click.prevent.stop="toggleFavorite(product)"
                                        title="В избранное"
                                    >
                                        <i :class="isFavorite(product) ? 'fas fa-heart' : 'far fa-heart'"></i>
                                    </button>
                                </div>
                            </article>
                        </div>
                    </div>
                    <div v-if="productItems.length === 0" class="p-12 text-center text-gray-500 bg-white rounded-2xl shadow-sm">
                        По заданным параметрам товары не найдены. Попробуйте выбрать другую категорию.
                    </div>

                    <!-- Pagination -->
                    <nav v-if="meta.last_page > 1" class="md:mt-10 flex justify-center mt-8">
                        <div class="flex flex-wrap gap-2 justify-center items-center">
                            <!-- Previous Button -->
                            <Link
                                v-if="meta.current_page > 1"
                                :href="makePageLinkForPage(meta.current_page - 1)"
                                preserve-scroll
                                preserve-state
                                class="md:px-4 hover:bg-gray-100 active:bg-gray-200 md:text-sm touch-manipulation px-3 py-2 text-xs text-gray-600 rounded-lg border border-gray-200"
                            >
                                <i class="fas fa-chevron-left"></i>
                            </Link>

                            <!-- Page Numbers -->
                            <template v-for="(page, index) in generatePaginationPages" :key="index">
                                <span
                                    v-if="page === '...'"
                                    class="md:px-4 md:text-sm px-3 py-2 text-xs text-gray-400"
                                >
                                    ...
                                </span>
                                <Link
                                    v-else
                                    :href="makePageLinkForPage(page)"
                                    preserve-scroll
                                    preserve-state
                                    class="md:px-4 md:text-sm touch-manipulation px-3 py-2 text-xs font-medium rounded-lg border transition-colors"
                                    :class="page === meta.current_page
                                        ? 'bg-primary text-white border-primary'
                                        : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-100 active:bg-gray-200'"
                                >
                                    {{ page }}
                                </Link>
                            </template>

                            <!-- Next Button -->
                            <Link
                                v-if="meta.current_page < meta.last_page"
                                :href="makePageLinkForPage(meta.current_page + 1)"
                                preserve-scroll
                                preserve-state
                                class="md:px-4 hover:bg-gray-100 active:bg-gray-200 md:text-sm touch-manipulation px-3 py-2 text-xs text-gray-600 rounded-lg border border-gray-200"
                            >
                                <i class="fas fa-chevron-right"></i>
                            </Link>
                        </div>
                    </nav>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
