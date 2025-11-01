<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useShopStore } from '@/stores/useShopStore';

const props = defineProps({
    featuredProducts: {
        type: Array,
        default: () => [],
    },
    topCategories: {
        type: Array,
        default: () => [],
    },
    latestNews: {
        type: Array,
        default: () => [],
    },
});

const shopStore = useShopStore();
shopStore.initialize();

const priceFormatter = new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    maximumFractionDigits: 0,
});

const formatCurrency = (value) => priceFormatter.format(value ?? 0);

const hasFeaturedProducts = computed(() => props.featuredProducts.length > 0);
const hasTopCategories = computed(() => props.topCategories.length > 0);
const hasNews = computed(() => props.latestNews.length > 0);

const getProductImage = (product) => product?.images?.[0] ?? null;

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
    <AppLayout title="СантехникаЧелябинск - ВОДА и ТЕПЛО в вашем доме">
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-primary to-secondary text-white py-10 md:py-16 lg:py-20">
            <div class="container mx-auto px-4">
                <div class="grid md:grid-cols-2 gap-8 md:gap-12 items-center">
                    <div>
                        <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-4 md:mb-6 leading-tight">
                            ВОДА и ТЕПЛО в вашем доме начинается с нас
                        </h2>
                        <p class="text-sm sm:text-base md:text-lg lg:text-xl mb-6 md:mb-8 text-blue-100">
                            С 2001 года мы продаем качественное санитарно-техническое оборудование,
                            комплектующие и запчасти для ремонта. Профессионализм и надежность.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 md:gap-4">
                            <Link
                                href="/catalog"
                                class="bg-accent hover:bg-yellow-500 active:bg-yellow-600 text-white px-6 md:px-8 py-3 md:py-4 rounded-lg font-semibold transition-colors inline-flex items-center justify-center text-sm md:text-base touch-manipulation"
                            >
                                <i class="fas fa-shopping-bag mr-2"></i>
                                Каталог товаров
                            </Link>
                            <Link
                                href="/services"
                                class="border-2 border-white hover:bg-white hover:text-primary active:bg-white active:text-primary text-white px-6 md:px-8 py-3 md:py-4 rounded-lg font-semibold transition-colors inline-flex items-center justify-center text-sm md:text-base touch-manipulation"
                            >
                                <i class="fas fa-tools mr-2"></i>
                                Наши услуги
                            </Link>
                        </div>
                    </div>
                    <div class="relative hidden md:block">
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 md:p-8">
                            <div class="grid grid-cols-2 gap-4 md:gap-6">
                                <div class="bg-white/20 rounded-xl p-4 md:p-6 text-center">
                                    <i class="fas fa-truck text-3xl md:text-4xl mb-3 md:mb-4"></i>
                                    <h3 class="font-semibold mb-2 text-sm md:text-base">Быстрая доставка</h3>
                                    <p class="text-xs md:text-sm text-blue-100">От 1 дня по Москве</p>
                                </div>
                                <div class="bg-white/20 rounded-xl p-6 text-center">
                                    <i class="fas fa-shield-alt text-4xl mb-4"></i>
                                    <h3 class="font-semibold mb-2">Гарантия качества</h3>
                                    <p class="text-sm text-blue-100">До 5 лет на товары</p>
                                </div>
                                <div class="bg-white/20 rounded-xl p-6 text-center">
                                    <i class="fas fa-headset text-4xl mb-4"></i>
                                    <h3 class="font-semibold mb-2">Поддержка 24/7</h3>
                                    <p class="text-sm text-blue-100">Консультации экспертов</p>
                                </div>
                                <div class="bg-white/20 rounded-xl p-4 md:p-6 text-center">
                                    <i class="fas fa-medal text-3xl md:text-4xl mb-3 md:mb-4"></i>
                                    <h3 class="font-semibold mb-2 text-sm md:text-base">С 2001 года</h3>
                                    <p class="text-xs md:text-sm text-blue-100">Проверенное качество</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories -->
        <section class="py-10 md:py-16">
            <div class="container mx-auto px-4">
                <div class="text-center mb-8 md:mb-12">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-2 md:mb-4">Популярные категории</h2>
                    <p class="text-sm md:text-lg lg:text-xl text-gray-600">Направления, которые выбирают чаще всего</p>
                </div>

                <div v-if="hasTopCategories" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
                    <Link
                        v-for="category in topCategories"
                        :key="category.id"
                        :href="`/catalog?category=${category.slug}`"
                        class="group bg-white rounded-xl md:rounded-2xl shadow-lg hover:shadow-2xl active:shadow-xl transition-all duration-300 overflow-hidden"
                    >
                        <div class="bg-gradient-to-br from-primary/90 to-secondary/90 p-6 md:p-8 text-center">
                            <i class="fas fa-layer-group text-3xl md:text-4xl text-white mb-3 md:mb-4"></i>
                            <h3 class="text-lg md:text-xl font-semibold text-white">{{ category.title }}</h3>
                        </div>
                        <div class="p-4 md:p-6">
                            <p class="text-gray-600 text-sm md:text-base line-clamp-2">
                                {{ category.description || 'Категория с широким выбором товаров для вашего проекта' }}
                            </p>
                            <div class="flex justify-between items-center mt-4 md:mt-6">
                                <span class="text-xs md:text-sm text-gray-500">Товаров: {{ category.products_count ?? 0 }}</span>
                                <i class="fas fa-arrow-right text-primary group-hover:translate-x-2 transition-transform text-sm md:text-base"></i>
                            </div>
                        </div>
                    </Link>
                </div>
                <div v-else class="bg-white rounded-2xl shadow-sm p-12 text-center text-gray-500">
                    Категории появятся здесь, как только вы добавите их в систему.
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="py-10 md:py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-8 md:mb-12">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-2 md:mb-4">Хиты продаж</h2>
                    <p class="text-sm md:text-lg lg:text-xl text-gray-600">
                        Популярные товары, которые выбирают наши клиенты прямо сейчас
                    </p>
                </div>

                <div v-if="hasFeaturedProducts" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 lg:gap-8">
                    <div
                        v-for="product in featuredProducts"
                        :key="product.id"
                        class="bg-white rounded-xl md:rounded-2xl shadow-lg hover:shadow-2xl active:shadow-xl transition-all duration-300 overflow-hidden group"
                    >
                        <Link :href="`/products/${product.id}`" class="block">
                            <div class="relative">
                                <div class="h-44 sm:h-48 md:h-56 bg-gray-100 flex items-center justify-center overflow-hidden">
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
                                    class="absolute top-2 md:top-4 left-2 md:left-4 bg-red-500 text-white px-2 md:px-3 py-1 rounded-full text-xs md:text-sm font-semibold"
                                >
                                    -{{ product.discount }}%
                                </span>
                            </div>
                            <div class="p-4 md:p-6">
                                <h3 class="font-semibold text-base md:text-lg mb-2 text-gray-900 group-hover:text-primary transition-colors line-clamp-2">
                                    {{ product.title }}
                                </h3>
                                <p v-if="product.category" class="text-xs md:text-sm text-gray-500 mb-2 md:mb-3">
                                    {{ product.category.title }}
                                </p>
                                <div class="flex items-center justify-between mb-3 md:mb-4">
                                    <div>
                                        <div class="text-lg md:text-2xl font-bold text-primary">
                                            {{ formatCurrency(product.final_price) }}
                                        </div>
                                        <div v-if="product.discount > 0" class="text-xs md:text-sm text-gray-400 line-through">
                                            {{ formatCurrency(product.price) }}
                                        </div>
                                    </div>
                                    <div v-if="product.rating_avg" class="flex items-center text-yellow-500">
                                        <i class="fas fa-star mr-1 text-xs md:text-sm"></i>
                                        <span class="text-xs md:text-sm font-semibold">{{ product.rating_avg }}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center text-xs md:text-sm text-gray-500">
                                    <span>Отзывы: {{ product.reviews_count ?? 0 }}</span>
                                    <span class="truncate ml-2">ID: {{ product.article_id }}</span>
                                </div>
                            </div>
                        </Link>
                        <div class="px-4 md:px-6 pb-4 md:pb-6 flex items-center justify-between border-t border-gray-100 pt-3 md:pt-4 gap-2">
                            <button
                                type="button"
                                class="flex-1 px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-semibold transition-colors touch-manipulation"
                                :class="isInCart(product)
                                    ? 'bg-green-500 hover:bg-green-600 active:bg-green-700 text-white'
                                    : 'bg-primary hover:bg-secondary active:bg-secondary text-white'"
                                @click.prevent.stop="addProductToCart(product)"
                            >
                                <i :class="[isInCart(product) ? 'fas fa-check' : 'fas fa-cart-plus', 'mr-1 md:mr-2']"></i>
                                <span class="hidden sm:inline">{{ isInCart(product) ? 'В корзине' : 'В корзину' }}</span>
                                <span class="sm:hidden">{{ isInCart(product) ? 'В корзине' : 'Купить' }}</span>
                            </button>
                            <button
                                type="button"
                                class="text-base md:text-lg transition-colors p-2 touch-manipulation"
                                :class="isFavorite(product) ? 'text-red-500' : 'text-gray-400 hover:text-red-500 active:text-red-600'"
                                @click.prevent.stop="toggleFavorite(product)"
                            >
                                <i :class="isFavorite(product) ? 'fas fa-heart' : 'far fa-heart'"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else class="bg-white rounded-2xl shadow-sm p-12 text-center text-gray-500">
                    Пока нет отмеченных хитов продаж. Добавьте товары в категории, и они появятся здесь.
                </div>
            </div>
        </section>

        <!-- Latest News -->
        <section class="py-16">
            <div class="container mx-auto px-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-12 gap-4">
                    <div>
                        <h2 class="text-4xl font-bold text-gray-800 mb-2">Новости и акции</h2>
                        <p class="text-xl text-gray-600">
                            Следите за новинками, специальными предложениями и мероприятиями
                        </p>
                    </div>
                    <Link
                        href="/news"
                        class="inline-flex items-center justify-center px-6 py-3 border border-primary text-primary hover:bg-primary hover:text-white rounded-lg font-semibold transition-colors"
                    >
                        Все новости
                        <i class="fas fa-arrow-right ml-2"></i>
                    </Link>
                </div>

                <div v-if="hasNews" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <article
                        v-for="newsItem in latestNews"
                        :key="newsItem.id"
                        class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-shadow duration-300 overflow-hidden"
                    >
                        <div class="bg-gradient-to-br from-primary to-secondary h-40 flex items-center justify-center text-white">
                            <i class="fas fa-newspaper text-5xl"></i>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-3 text-gray-800">
                                {{ newsItem.title }}
                            </h3>
                            <p class="text-gray-600 mb-4 line-clamp-3">
                                {{ newsItem.excerpt || newsItem.content?.slice(0, 160) + '...' }}
                            </p>
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    {{ newsItem.published_at ? new Date(newsItem.published_at).toLocaleDateString('ru-RU') : 'Скоро' }}
                                </span>
                                <span>
                                    <i class="fas fa-eye mr-1"></i>
                                    {{ newsItem.views ?? 0 }}
                                </span>
                            </div>
                        </div>
                    </article>
                </div>
                <div v-else class="bg-white rounded-2xl shadow-sm p-12 text-center text-gray-500">
                    Новости пока не добавлены. Как только появится первая публикация, она отобразится здесь.
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="py-16">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="bg-primary/10 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shipping-fast text-3xl text-primary"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Быстрая доставка</h3>
                        <p class="text-gray-600">Доставим ваш заказ в течение 24 часов по Москве</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-tools text-3xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Установка</h3>
                        <p class="text-gray-600">Профессиональная установка сантехники нашими мастерами</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-yellow-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-percent text-3xl text-yellow-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Скидки</h3>
                        <p class="text-gray-600">Регулярные акции и спецпредложения для постоянных клиентов</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-headset text-3xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Поддержка</h3>
                        <p class="text-gray-600">Консультации экспертов по выбору и эксплуатации товаров</p>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

