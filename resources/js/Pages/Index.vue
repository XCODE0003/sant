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
    <AppLayout title="АкватЭрия - Территория воды и тепла">
        <!-- Hero Section -->
        <section class="bg-primary md:py-16 lg:py-20 py-10 text-white">
            <div class="container px-4 mx-auto">
                <div class="md:grid-cols-2 md:gap-12 grid gap-8 items-center">

                    <div>
                        <div class="sm:flex-row mb-5 md:gap-4 flex flex-col gap-3">
                            <Link
                                href="/catalog"
                                class="bg-accent text-yellow !text-2xl active:bg-yellow-600 md:px-8 md:py-4 md:text-base touch-manipulation inline-flex justify-center items-center px-6 py-3 font-semibold text-white rounded-lg transition-colors"
                            >
                                <i class="fas fa-shopping-bag mr-2 "></i>
                                Каталог товаров
                            </Link>
                            <Link
                                href="/services"
                                class="hover:bg-white hover:text-primary active:bg-white active:text-primary md:px-8 md:py-4 md:text-base touch-manipulation inline-flex justify-center items-center px-6 py-3 text-sm font-semibold text-white rounded-lg border-2 border-white transition-colors"
                            >
                                <i class="fas fa-tools mr-2"></i>
                                Наши услуги
                            </Link>
                        </div>
                        <h2 class="sm:text-3xl md:text-4xl lg:text-5xl md:mb-6 mb-4 text-2xl font-bold leading-tight">
                            ВОДА и ТЕПЛО в Вашем доме начинаются с нас
                        </h2>
                        <p class="sm:text-base md:text-lg lg:text-xl md:mb-8 mb-6 text-sm text-blue-100">
                            С 2001 года мы продаем качественное санитарно-техническое оборудование,
                            комплектующие и запчасти для ремонта. Профессионализм и надежность.
                        </p>

                    </div>
                    <div class="md:block hidden relative">
                        <div class="bg-white/10 md:p-8 p-6 rounded-2xl backdrop-blur-sm">
                            <div class="md:gap-6 grid grid-cols-2 gap-4">
                                <div class="bg-white/20 md:p-6 p-4 text-center rounded-xl">
                                    <i class="fas fa-truck md:text-4xl md:mb-4 mb-3 text-3xl"></i>
                                    <h3 class="md:text-base mb-2 text-sm font-semibold">Быстрая доставка</h3>
                                    <p class="md:text-sm text-xs text-blue-100">От 1 дня по Москве</p>
                                </div>
                                <div class="bg-white/20 p-6 text-center rounded-xl">
                                    <i class="fas fa-shield-alt mb-4 text-4xl"></i>
                                    <h3 class="mb-2 font-semibold">Гарантия качества</h3>
                                    <p class="text-sm text-blue-100">До 5 лет на товары</p>
                                </div>
                                <div class="bg-white/20 p-6 text-center rounded-xl">
                                    <i class="fas fa-headset mb-4 text-4xl"></i>
                                    <h3 class="mb-2 font-semibold">Поддержка 24/7</h3>
                                    <p class="text-sm text-blue-100">Консультации экспертов</p>
                                </div>
                                <div class="bg-white/20 md:p-6 p-4 text-center rounded-xl">
                                    <i class="fas fa-medal md:text-4xl md:mb-4 mb-3 text-3xl"></i>
                                    <h3 class="md:text-base mb-2 text-sm font-semibold">С 2001 года</h3>
                                    <p class="md:text-sm text-xs text-blue-100">Проверенное качество</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories -->
        <section class="md:py-16 py-10">
            <div class="container px-4 mx-auto">
                <div class="md:mb-12 mb-8 text-center">
                    <h2 class="md:text-3xl lg:text-4xl md:mb-4 mb-2 text-2xl font-bold text-gray-800">Популярные категории</h2>
                    <p class="md:text-lg lg:text-xl text-sm text-gray-600">Направления, которые выбирают чаще всего</p>
                </div>

                <div v-if="hasTopCategories" class="sm:grid-cols-2 lg:grid-cols-3 md:gap-6 lg:gap-8 grid grid-cols-1 gap-4">
                    <Link
                        v-for="category in topCategories"
                        :key="category.id"
                        :href="`/catalog?category=${category.slug}`"
                        class="group md:rounded-2xl hover:shadow-2xl active:shadow-xl overflow-hidden bg-white rounded-xl shadow-lg transition-all duration-300"
                    >
                        <div class="bg-primary md:p-8 p-6 text-center">
                            <i class="fas fa-layer-group md:text-4xl md:mb-4 mb-3 text-3xl text-white"></i>
                            <h3 class="md:text-xl text-lg font-semibold text-white">{{ category.title }}</h3>
                        </div>
                        <div class="md:p-6 p-4">
                            <p class="md:text-base line-clamp-2 text-sm text-gray-600">
                                {{ category.description || 'Категория с широким выбором товаров для вашего проекта' }}
                            </p>
                            <div class="md:mt-6 flex justify-between items-center mt-4">
                                <span class="md:text-sm text-xs text-gray-500">Товаров: {{ category.products_count ?? 0 }}</span>
                                <i class="fas fa-arrow-right text-primary group-hover:translate-x-2 md:text-base text-sm transition-transform"></i>
                            </div>
                        </div>
                    </Link>
                </div>
                <div v-else class="p-12 text-center text-gray-500 bg-white rounded-2xl shadow-sm">
                    Категории появятся здесь, как только вы добавите их в систему.
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="md:py-16 py-10 bg-gray-50">
            <div class="container px-4 mx-auto">
                <div class="md:mb-12 mb-8 text-center">
                    <h2 class="md:text-3xl lg:text-4xl md:mb-4 mb-2 text-2xl font-bold text-gray-800">Хиты продаж</h2>
                    <p class="md:text-lg lg:text-xl text-sm text-gray-600">
                        Популярные товары, которые выбирают наши клиенты прямо сейчас
                    </p>
                </div>

                <div v-if="hasFeaturedProducts" class="sm:grid-cols-2 lg:grid-cols-4 md:gap-6 lg:gap-8 grid grid-cols-1 gap-4">
                    <div
                        v-for="product in featuredProducts"
                        :key="product.id"
                        class="md:rounded-2xl hover:shadow-2xl active:shadow-xl group overflow-hidden bg-white rounded-xl shadow-lg transition-all duration-300"
                    >
                        <Link :href="`/products/${product.id}`" class="block">
                            <div class="relative">
                                <div class="sm:h-48 md:h-56 flex overflow-hidden justify-center items-center h-44 bg-gray-100">
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
                            </div>
                        </Link>
                        <div class="md:px-6 md:pb-6 md:pt-4 flex gap-2 justify-between items-center px-4 pt-3 pb-4 border-t border-gray-100">
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
                </div>
                <div v-else class="p-12 text-center text-gray-500 bg-white rounded-2xl shadow-sm">
                    Пока нет отмеченных хитов продаж. Добавьте товары в категории, и они появятся здесь.
                </div>
            </div>
        </section>

        <!-- Latest News -->
        <section class="py-16">
            <div class="container px-4 mx-auto">
                <div class="sm:flex-row sm:items-center sm:justify-between flex flex-col gap-4 mb-12">
                    <div>
                        <h2 class="mb-2 text-4xl font-bold text-gray-800">Новости и акции</h2>
                        <p class="text-xl text-gray-600">
                            Следите за новинками, специальными предложениями и мероприятиями
                        </p>
                    </div>
                    <Link
                        href="/news"
                        class="border-primary text-primary hover:bg-primary hover:text-white inline-flex justify-center items-center px-6 py-3 font-semibold rounded-lg border transition-colors"
                    >
                        Все новости
                        <i class="fas fa-arrow-right ml-2"></i>
                    </Link>
                </div>

                <div v-if="hasNews" class="md:grid-cols-2 lg:grid-cols-3 grid grid-cols-1 gap-8">
                    <article
                        v-for="newsItem in latestNews"
                        :key="newsItem.id"
                        class="hover:shadow-2xl overflow-hidden bg-white rounded-2xl shadow-lg transition-shadow duration-300"
                    >
                        <div class="from-primary to-secondary flex justify-center items-center h-40 text-white bg-gradient-to-br">
                            <i class="fas fa-newspaper text-5xl"></i>
                        </div>
                        <div class="p-6">
                            <h3 class="mb-3 text-xl font-semibold text-gray-800">
                                {{ newsItem.title }}
                            </h3>
                            <p class="line-clamp-3 mb-4 text-gray-600">
                                {{ newsItem.excerpt || newsItem.content?.slice(0, 160) + '...' }}
                            </p>
                            <div class="flex justify-between items-center text-sm text-gray-500">
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
                <div v-else class="p-12 text-center text-gray-500 bg-white rounded-2xl shadow-sm">
                    Новости пока не добавлены. Как только появится первая публикация, она отобразится здесь.
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="py-16">
            <div class="container px-4 mx-auto">
                <div class="md:grid-cols-2 lg:grid-cols-4 grid grid-cols-1 gap-8">
                    <div class="text-center">
                        <div class="bg-primary/10 flex justify-center items-center mx-auto mb-4 w-20 h-20 rounded-full">
                            <i class="fas fa-shipping-fast text-primary text-3xl"></i>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold">Быстрая доставка</h3>
                        <p class="text-gray-600">Доставим ваш заказ в течение 24 часов по Москве</p>
                    </div>
                    <div class="text-center">
                        <div class="flex justify-center items-center mx-auto mb-4 w-20 h-20 bg-green-100 rounded-full">
                            <i class="fas fa-tools text-3xl text-green-600"></i>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold">Установка</h3>
                        <p class="text-gray-600">Профессиональная установка сантехники нашими мастерами</p>
                    </div>
                    <div class="text-center">
                        <div class="flex justify-center items-center mx-auto mb-4 w-20 h-20 bg-yellow-100 rounded-full">
                            <i class="fas fa-percent text-3xl text-yellow-600"></i>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold">Скидки</h3>
                        <p class="text-gray-600">Регулярные акции и спецпредложения для постоянных клиентов</p>
                    </div>
                    <div class="text-center">
                        <div class="flex justify-center items-center mx-auto mb-4 w-20 h-20 bg-purple-100 rounded-full">
                            <i class="fas fa-headset text-3xl text-purple-600"></i>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold">Поддержка</h3>
                        <p class="text-gray-600">Консультации экспертов по выбору и эксплуатации товаров</p>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

