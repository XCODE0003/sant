<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
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
    <AppLayout title="Каталог товаров - СантехникаЧелябинск">
        <Breadcrumbs :items="breadcrumbs" />

        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filters -->
                <aside class="lg:w-1/4">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                        <h3 class="text-xl font-semibold mb-6">Категории</h3>
                        <div v-if="hasCategories" class="space-y-2">
                            <Link
                                href="/catalog"
                                preserve-scroll
                                :class="[
                                    'flex items-center justify-between px-3 py-2 rounded-lg transition-colors',
                                    activeCategorySlug === null
                                        ? 'bg-primary text-white'
                                        : 'text-gray-700 hover:bg-gray-100',
                                ]"
                            >
                                <span>Все товары</span>
                                <i class="fas fa-layer-group"></i>
                            </Link>
                            <Link
                                v-for="category in categories"
                                :key="category.id"
                                :href="`/catalog?category=${category.slug}`"
                                preserve-state
                                preserve-scroll
                                :class="[
                                    'flex items-center justify-between px-3 py-2 rounded-lg transition-colors',
                                    activeCategorySlug === category.slug
                                        ? 'bg-primary text-white'
                                        : 'text-gray-700 hover:bg-gray-100',
                                ]"
                            >
                                <span>{{ category.title }}</span>
                                <span class="text-sm opacity-80">{{ category.products_count ?? 0 }}</span>
                            </Link>
                        </div>
                        <div v-else class="text-sm text-gray-500">
                            Нет активных категорий. Добавьте их через панель администратора Filament.
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <section class="lg:w-3/4">
                    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                Найдено товаров: {{ meta.total ?? productItems.length }}
                            </h3>
                            <div class="text-sm text-gray-500">
                                Страница {{ meta.current_page ?? 1 }} из {{ meta.last_page ?? 1 }}
                            </div>
                        </div>
                    </div>

                    <div v-if="hasProducts" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                        <article
                            v-for="product in productItems"
                            :key="product.id"
                            class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group"
                        >
                            <Link :href="`/products/${product.id}`" class="block">
                                <div class="relative">
                                    <div class="h-60 bg-gray-100 flex items-center justify-center overflow-hidden">
                                        <img
                                            v-if="getProductImage(product)"
                                            :src="getProductImage(product)"
                                            :alt="product.title"
                                            class="object-cover w-full h-full"
                                        />
                                        <i v-else class="fas fa-faucet text-6xl text-gray-300"></i>
                                    </div>
                                    <span
                                        v-if="product.discount > 0"
                                        class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold"
                                    >
                                        -{{ product.discount }}%
                                    </span>
                                </div>
                                <div class="p-6">
                                    <h3 class="font-semibold text-lg mb-2 text-gray-900 group-hover:text-primary transition-colors">
                                        {{ product.title }}
                                    </h3>
                                    <p v-if="product.category" class="text-sm text-gray-500 mb-3">
                                        {{ product.category.title }}
                                    </p>
                                    <p v-if="product.description" class="text-sm text-gray-500 mb-4 line-clamp-2">
                                        <span v-html="product.description"></span>

                                    </p>
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <div class="text-2xl font-bold text-primary">
                                                {{ formatCurrency(product.final_price) }}
                                            </div>
                                            <div v-if="product.discount > 0" class="text-sm text-gray-400 line-through">
                                                {{ formatCurrency(product.price) }}
                                            </div>
                                        </div>
                                        <div v-if="product.rating_avg" class="flex items-center text-yellow-500">
                                            <i class="fas fa-star mr-1"></i>
                                            <span class="text-sm font-semibold">{{ product.rating_avg }}</span>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center text-sm text-gray-500">
                                        <span>Отзывы: {{ product.reviews_count ?? 0 }}</span>
                                        <span>ID: {{ product.article_id }}</span>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between">
                                        <button
                                            type="button"
                                            class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
                                            :class="isInCart(product)
                                                ? 'bg-green-500 hover:bg-green-600 text-white'
                                                : 'bg-primary hover:bg-secondary text-white'"
                                            @click.prevent.stop="addProductToCart(product)"
                                        >
                                            <i :class="[isInCart(product) ? 'fas fa-check' : 'fas fa-cart-plus', 'mr-2']"></i>
                                            {{ isInCart(product) ? 'В корзине' : 'В корзину' }}
                                        </button>
                                        <button
                                            type="button"
                                            class="text-lg transition-colors"
                                            :class="isFavorite(product) ? 'text-red-500' : 'text-gray-400 hover:text-red-500'"
                                            @click.prevent.stop="toggleFavorite(product)"
                                        >
                                            <i :class="isFavorite(product) ? 'fas fa-heart' : 'far fa-heart'"></i>
                                        </button>
                                    </div>
                                </div>
                            </Link>
                        </article>
                    </div>
                    <div v-else class="bg-white rounded-2xl shadow-sm p-12 text-center text-gray-500">
                        По заданным параметрам товары не найдены. Попробуйте выбрать другую категорию.
                    </div>

                    <!-- Pagination -->
                    <nav v-if="meta.last_page > 1" class="flex justify-center mt-10">
                        <div class="inline-flex items-center gap-2">
                            <Link
                                v-if="meta.prev_page_url"
                                :href="makePageLink(meta.prev_page_url)"
                                preserve-scroll
                                preserve-state
                                class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100"
                            >
                                <i class="fas fa-chevron-left mr-2"></i>
                                Назад
                            </Link>
                            <span class="px-4 py-2 text-sm text-gray-500">
                                Страница {{ meta.current_page }} из {{ meta.last_page }}
                            </span>
                            <Link
                                v-if="meta.next_page_url"
                                :href="makePageLink(meta.next_page_url)"
                                preserve-scroll
                                preserve-state
                                class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100"
                            >
                                Вперёд
                                <i class="fas fa-chevron-right ml-2"></i>
                            </Link>
                        </div>
                    </nav>
                </section>
            </div>
        </div>
    </AppLayout>
</template>

