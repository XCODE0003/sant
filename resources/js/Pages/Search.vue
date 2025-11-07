<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useShopStore } from '@/stores/useShopStore';

const props = defineProps({
    query: {
        type: String,
        default: '',
    },
    products: {
        type: Array,
        default: () => [],
    },
    news: {
        type: Array,
        default: () => [],
    },
});

const breadcrumbs = computed(() => [
    { label: 'Главная', href: '/' },
    { label: 'Поиск' },
]);

const shopStore = useShopStore();
shopStore.initialize();

const hasProducts = computed(() => props.products.length > 0);
const hasNews = computed(() => props.news.length > 0);
const hasResults = computed(() => hasProducts.value || hasNews.value);

const priceFormatter = new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    maximumFractionDigits: 0,
});

const formatCurrency = (value) => priceFormatter.format(value ?? 0);
const getProductImage = (product) => product?.images?.[0] ?? null;

const isFavorite = (product) => shopStore.isFavorite(product.id);
const isInCart = (product) => shopStore.isInCart(product.id);

const addProductToCart = (product) => {
    if (isInCart(product)) {
        router.visit('/cart');
        return;
    }

    shopStore.addToCart(product, 1);
};

const toggleFavorite = (product) => {
    shopStore.toggleFavorite(product);
};

const formatDate = (value) => {
    if (!value) {
        return 'Скоро';
    }

    try {
        return new Date(value).toLocaleDateString('ru-RU', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        });
    } catch (error) {
        return value;
    }
};

const getTagStyle = (tag) => ({
    backgroundColor: tag?.color ?? '#3B82F6',
});
</script>

<template>
    <AppLayout :title="query ? `Поиск: ${query}` : 'Поиск'">
        <Breadcrumbs :items="breadcrumbs" />

        <div class="container mx-auto px-4 py-6 md:py-12">
            <div class="mb-6 md:mb-10">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Результаты поиска</h1>
                <p class="text-gray-500 text-sm md:text-base">
                    {{ query ? `По запросу «${query}» найдено ${products.length} товаров и ${news.length} новостей.` : 'Введите запрос в поле поиска наверху.' }}
                </p>
            </div>

            <div v-if="!query" class="bg-white rounded-2xl shadow-sm p-10 text-center text-gray-500">
                Введите ключевое слово или артикул в поле поиска, чтобы найти товары и новости.
            </div>

            <div v-else>
                <div v-if="hasProducts" class="mb-10 md:mb-16">
                    <div class="flex items-center justify-between mb-4 md:mb-6">
                        <h2 class="text-xl md:text-2xl font-semibold text-gray-800">Товары</h2>
                        <Link href="/catalog" class="text-sm text-primary hover:underline">
                            Перейти в каталог
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
                        <article
                            v-for="product in products"
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
                                </div>
                            </Link>
                            <div class="px-6 pb-6 flex items-center justify-between border-t border-gray-100 pt-4">
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
                        </article>
                    </div>
                </div>

                <div v-if="hasNews" class="mb-16">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-semibold text-gray-800">Новости</h2>
                        <Link href="/news" class="text-sm text-primary hover:underline">
                            Все новости
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <article
                            v-for="newsItem in news"
                            :key="newsItem.id"
                            class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-shadow duration-300"
                        >
                            <div class="bg-primary h-44 flex items-center justify-center text-white">
                                <i class="fas fa-newspaper text-4xl"></i>
                            </div>
                            <div class="p-6 flex flex-col h-full">
                                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                    <span>
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        {{ formatDate(newsItem.published_at) }}
                                    </span>
                                    <span>
                                        <i class="fas fa-eye mr-1"></i>
                                        {{ newsItem.views ?? 0 }}
                                    </span>
                                </div>
                                <h3 class="text-xl font-semibold mb-3 text-gray-900">
                                    {{ newsItem.title }}
                                </h3>
                                <p class="text-gray-600 mb-4 line-clamp-3">
                                    {{ newsItem.excerpt || newsItem.content?.slice(0, 160) + '…' }}
                                </p>
                                <div v-if="newsItem.tags?.length" class="flex flex-wrap gap-2 mb-4">
                                    <span
                                        v-for="tag in newsItem.tags"
                                        :key="`${newsItem.id}-${tag.label}`"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold text-white"
                                        :style="getTagStyle(tag)"
                                    >
                                        {{ tag.label }}
                                    </span>
                                </div>
                                <div class="mt-auto">
                                    <Link
                                        :href="`/news/${newsItem.slug}`"
                                        class="text-primary hover:underline font-semibold"
                                    >
                                        Читать полностью
                                        <i class="fas fa-arrow-right ml-2"></i>
                                    </Link>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

                <div v-if="query && !hasResults" class="bg-white rounded-2xl shadow-sm p-12 text-center text-gray-500">
                    По запросу «{{ query }}» ничего не найдено. Попробуйте изменить формулировку или используйте более общие слова.
                </div>
            </div>
        </div>
    </AppLayout>
</template>

