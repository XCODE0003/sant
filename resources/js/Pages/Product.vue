<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed, ref, watch, reactive } from 'vue';
import axios from 'axios';
import { useShopStore } from '@/stores/useShopStore';

const props = defineProps({
    product: {
        type: Object,
        required: true,
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

const images = computed(() => props.product.images ?? []);
const currentImage = ref(images.value[0] ?? null);

watch(images, (value) => {
    currentImage.value = value?.[0] ?? null;
});

const selectImage = (image) => {
    currentImage.value = image;
};

const reviews = ref([...(props.product.reviews ?? [])]);

const ratingAverage = computed(() => {
    if (reviews.value.length > 0) {
        const total = reviews.value.reduce((sum, review) => sum + (Number(review.rating) || 0), 0);
        return Number((total / reviews.value.length).toFixed(1));
    }

    return props.product.rating_avg ? Number(props.product.rating_avg) : null;
});

const ratingStars = computed(() => Math.round(ratingAverage.value ?? 0));
const hasRating = computed(() => ratingAverage.value !== null);
const reviewsCount = computed(() => reviews.value.length || props.product.reviews_count || 0);

const characteristics = computed(() => {
    const source = props.product.characteristics ?? {};

    if (Array.isArray(source)) {
        return source.map((item, index) => {
            if (typeof item === 'object' && item !== null) {
                const [[key, value] = [`Характеристика ${index + 1}`, '']] = Object.entries(item);
                return { key, value };
            }

            return {
                key: `Характеристика ${index + 1}`,
                value: item,
            };
        });
    }

    return Object.entries(source).map(([key, value]) => ({ key, value }));
});

const isFavoriteProduct = computed(() => shopStore.isFavorite(props.product.id));
const isProductInCart = computed(() => shopStore.isInCart(props.product.id));

const breadcrumbs = computed(() => {
    const items = [
        { label: 'Главная', href: '/' },
        { label: 'Каталог', href: '/catalog' },
    ];

    if (props.product.category) {
        items.push({
            label: props.product.category.title,
            href: `/catalog?category=${props.product.category.slug}`,
        });
    }

    items.push({ label: props.product.title });

    return items;
});

const quantity = ref(1);
const activeTab = ref('description');

const changeQuantity = (delta) => {
    quantity.value = Math.max(1, quantity.value + delta);
};

const showTab = (tab) => {
    activeTab.value = tab;
};

const hasDiscount = computed(() => (props.product.discount ?? 0) > 0);
const finalPrice = computed(() => props.product.final_price ?? props.product.price ?? 0);
const originalPrice = computed(() => props.product.price ?? finalPrice.value);

const addToCart = () => {
    if (isProductInCart.value) {
        router.visit('/cart');
        return;
    }

    shopStore.addToCart(props.product, quantity.value);
};

const toggleFavorite = () => {
    shopStore.toggleFavorite(props.product);
};

const reviewForm = reactive({
    author_name: '',
    author_email: '',
    rating: 5,
    body: '',
});

const reviewErrors = reactive({});
const reviewSubmitting = ref(false);
const reviewSuccess = ref('');
const reviewError = ref('');
const showReviewForm = ref(false);

const resetReviewForm = () => {
    reviewForm.author_name = '';
    reviewForm.author_email = '';
    reviewForm.rating = 5;
    reviewForm.body = '';
};

const clearReviewErrors = () => {
    Object.keys(reviewErrors).forEach((key) => {
        delete reviewErrors[key];
    });
};

const submitReview = async () => {
    reviewSuccess.value = '';
    reviewError.value = '';
    clearReviewErrors();
    reviewSubmitting.value = true;

    try {
        const response = await axios.post('/api/v1/reviews', {
            product_id: props.product.id,
            author_name: reviewForm.author_name,
            author_email: reviewForm.author_email || null,
            rating: reviewForm.rating,
            body: reviewForm.body,
        });

        const payload = response.data?.data ?? response.data;
        if (payload) {
            reviews.value = [payload, ...reviews.value];
        }

        reviewSuccess.value = response.data?.message ?? 'Спасибо! Ваш отзыв отправлен на модерацию.';
        showReviewForm.value = false;
        resetReviewForm();
    } catch (error) {
        if (error.response?.status === 422) {
            Object.assign(reviewErrors, error.response.data?.errors ?? {});
        } else {
            reviewError.value = error.response?.data?.message ?? 'Не удалось отправить отзыв. Попробуйте позже.';
        }
    } finally {
        reviewSubmitting.value = false;
    }
};

const toggleReviewForm = () => {
    showReviewForm.value = !showReviewForm.value;
    reviewSuccess.value = '';
    reviewError.value = '';
    if (!showReviewForm.value) {
        clearReviewErrors();
    }
};

watch(
    () => props.product.reviews,
    (value) => {
        reviews.value = [...(value ?? [])];
    },
    { immediate: true },
);

watch(
    () => props.product.id,
    () => {
        quantity.value = 1;
        reviews.value = [...(props.product.reviews ?? [])];
        resetReviewForm();
        clearReviewErrors();
        reviewSuccess.value = '';
        reviewError.value = '';
        showReviewForm.value = false;
    },
    { immediate: true },
);
</script>

<template>
    <AppLayout :title="`${product.title} - СантехникаЧелябинск`">
        <Breadcrumbs :items="breadcrumbs" />

        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
                <!-- Product Images -->
                <div class="space-y-4">
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <div class="bg-gray-100 rounded-xl h-96 flex items-center justify-center overflow-hidden mb-4">
                            <img
                                v-if="currentImage"
                                :src="currentImage"
                                :alt="product.title"
                                class="object-cover w-full h-full"
                            />
                            <i v-else class="fas fa-faucet text-8xl text-gray-300"></i>
                        </div>
                        <div class="grid grid-cols-4 gap-2">
                            <button
                                v-for="(image, index) in images"
                                :key="image || index"
                                type="button"
                                class="bg-gray-100 rounded-lg h-20 flex items-center justify-center overflow-hidden border-2"
                                :class="currentImage === image ? 'border-primary' : 'border-transparent hover:border-primary'"
                                @click="selectImage(image)"
                            >
                                <img v-if="image" :src="image" :alt="product.title" class="object-cover w-full h-full" />
                                <i v-else class="fas fa-faucet text-2xl text-gray-300"></i>
                            </button>
                            <div
                                v-if="images.length === 0"
                                class="bg-gray-100 rounded-lg h-20 flex items-center justify-center text-sm text-gray-500"
                            >
                                Фото пока нет
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="space-y-6">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span
                                v-if="hasDiscount"
                                class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold"
                            >
                                -{{ product.discount }}%
                            </span>
                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                {{ product.is_active ? 'В наличии' : 'Нет в наличии' }}
                            </span>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-800 mb-3">{{ product.title }}</h1>
                        <p v-if="product.category" class="text-sm text-primary mb-2">
                            <Link :href="`/catalog?category=${product.category.slug}`" preserve-scroll>
                                {{ product.category.title }}
                            </Link>
                        </p>
                        <p class="text-gray-500">Артикул: {{ product.article_id }}</p>

                        <div v-if="hasRating" class="flex items-center mt-4">
                            <div class="flex text-yellow-400 mr-2">
                                <i
                                    v-for="i in 5"
                                    :key="`star-${i}`"
                                    :class="[
                                        'fas fa-star',
                                        i <= ratingStars ? 'text-yellow-500' : 'text-gray-300',
                                    ]"
                                ></i>
                            </div>
                            <span class="text-sm text-gray-600">
                                {{ ratingAverage !== null ? ratingAverage.toFixed(1) : '0.0' }} ({{ reviewsCount }} отзывов)
                            </span>
                        </div>
                        <div v-else class="text-sm text-gray-500 mt-4">
                            Пока нет оценок — станьте первым, кто оставит отзыв
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="bg-gray-100 rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <div class="flex items-center space-x-4 mb-2">
                                    <span class="text-4xl font-bold text-primary">{{ formatCurrency(finalPrice) }}</span>
                                    <span v-if="hasDiscount" class="text-xl text-gray-500 line-through">
                                        {{ formatCurrency(originalPrice) }}
                                    </span>
                                </div>
                                <p v-if="hasDiscount" class="text-sm text-gray-600">
                                    Экономия: {{ formatCurrency(originalPrice - finalPrice) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Quantity and Actions -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-700">Количество:</span>
                            <div class="flex items-center border border-gray-300 rounded-lg">
                                <button @click="changeQuantity(-1)" class="px-3 py-2 hover:bg-gray-100" type="button">-</button>
                                <input
                                    v-model.number="quantity"
                                    type="number"
                                    min="1"
                                    class="w-16 text-center border-0 focus:outline-none"
                                />
                                <button @click="changeQuantity(1)" class="px-3 py-2 hover:bg-gray-100" type="button">+</button>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4">
                            <button
                                type="button"
                                class="flex-1 min-w-[220px] py-4 px-6 rounded-lg font-semibold text-lg transition-colors"
                                :class="isProductInCart
                                    ? 'bg-green-500 hover:bg-green-600 text-white'
                                    : 'bg-primary hover:bg-secondary text-white'"
                                @click="addToCart"
                            >
                                <i :class="[isProductInCart ? 'fas fa-check' : 'fas fa-shopping-cart', 'mr-2']"></i>
                                {{ isProductInCart ? 'В корзине' : 'Добавить в корзину' }}
                            </button>
                            <button
                                type="button"
                                class="bg-accent hover:bg-yellow-500 text-white py-4 px-6 rounded-lg font-semibold transition-colors"
                            >
                                <i class="fas fa-bolt mr-2"></i>
                                Купить в 1 клик
                            </button>
                        </div>

                        <div class="flex gap-2">
                            <button
                                type="button"
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 px-4 rounded-lg font-medium transition-colors"
                                :class="isFavoriteProduct ? 'text-red-500' : 'text-gray-700'"
                                @click="toggleFavorite"
                            >
                                <i :class="[isFavoriteProduct ? 'fas fa-heart' : 'far fa-heart', 'mr-2']"></i>
                                {{ isFavoriteProduct ? 'В избранном' : 'В избранное' }}
                            </button>
                            <button type="button" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 px-4 rounded-lg font-medium transition-colors">
                                <i class="fas fa-balance-scale mr-2"></i>
                                Сравнить
                            </button>
                        </div>
                    </div>

                    <!-- Delivery Info -->
                    <div class="bg-blue-50 rounded-xl p-6 space-y-3">
                        <h3 class="font-semibold text-gray-800">Доставка и самовывоз</h3>
                        <div class="flex items-start">
                            <i class="fas fa-truck text-primary mr-3 mt-1"></i>
                            <div>
                                <div class="font-medium">Курьерская доставка</div>
                                <div class="text-sm text-gray-600">Бесплатно от 5 000 ₽, доставка на следующий день</div>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-store text-primary mr-3 mt-1"></i>
                            <div>
                                <div class="font-medium">Самовывоз</div>
                                <div class="text-sm text-gray-600">Заберите заказ в магазине уже сегодня</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details Tabs -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="border-b">
                    <nav class="flex flex-wrap">
                        <button
                            @click="showTab('description')"
                            :class="[
                                'px-8 py-4 font-semibold transition-colors',
                                activeTab === 'description'
                                    ? 'text-primary border-b-2 border-primary bg-blue-50'
                                    : 'text-gray-600 hover:text-primary hover:bg-gray-50',
                            ]"
                            type="button"
                        >
                            Описание
                        </button>
                        <button
                            @click="showTab('specifications')"
                            :class="[
                                'px-8 py-4 font-semibold transition-colors',
                                activeTab === 'specifications'
                                    ? 'text-primary border-b-2 border-primary bg-blue-50'
                                    : 'text-gray-600 hover:text-primary hover:bg-gray-50',
                            ]"
                            type="button"
                        >
                            Характеристики
                        </button>
                        <button
                            @click="showTab('reviews')"
                            :class="[
                                'px-8 py-4 font-semibold transition-colors',
                                activeTab === 'reviews'
                                    ? 'text-primary border-b-2 border-primary bg-blue-50'
                                    : 'text-gray-600 hover:text-primary hover:bg-gray-50',
                            ]"
                            type="button"
                        >
                            Отзывы ({{ reviewsCount }})
                        </button>
                    </nav>
                </div>

                <div class="p-8">
                    <!-- Description Tab -->
                    <div v-show="activeTab === 'description'" class="prose max-w-none">
                        <h3 class="text-2xl font-semibold mb-4">Описание товара</h3>
                        <p class="text-gray-600" v-html="product.description" v-if="product.description">
                        </p>
                        <p v-else class="text-gray-500">
                            Описание для данного товара пока не добавлено.
                        </p>
                    </div>

                    <!-- Specifications Tab -->
                    <div v-show="activeTab === 'specifications'">
                        <h3 class="text-2xl font-semibold mb-6">Технические характеристики</h3>
                        <div v-if="characteristics.length" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                v-for="item in characteristics"
                                :key="item.key"
                                class="flex items-start justify-between border-b border-gray-100 pb-2"
                            >
                                <span class="text-gray-500 pr-4">{{ item.key }}</span>
                                <span class="font-medium text-gray-800 text-right">{{ item.value }}</span>
                            </div>
                        </div>
                        <p v-else class="text-gray-500">
                            Характеристики для этого товара ещё не заполнены.
                        </p>
                    </div>

                    <!-- Reviews Tab -->
                    <div v-show="activeTab === 'reviews'">
                        <div class="flex items-center justify-between flex-wrap gap-4 mb-8">
                            <div>
                                <h3 class="text-2xl font-semibold">Отзывы покупателей</h3>
                                <p class="text-sm text-gray-500">Поделитесь мнением о товаре — это поможет другим покупателям</p>
                            </div>
                            <button
                                type="button"
                                class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-lg font-semibold transition-colors"
                                @click="toggleReviewForm"
                            >
                                {{ showReviewForm ? 'Скрыть форму' : 'Написать отзыв' }}
                            </button>
                        </div>

                        <div v-if="reviewSuccess" class="mb-6 rounded-lg bg-green-100 text-green-800 px-4 py-3">
                            {{ reviewSuccess }}
                        </div>
                        <div v-if="reviewError" class="mb-6 rounded-lg bg-red-100 text-red-700 px-4 py-3">
                            {{ reviewError }}
                        </div>

                        <form
                            v-if="showReviewForm"
                            class="bg-gray-50 border border-gray-200 rounded-2xl p-6 mb-8 space-y-4"
                            @submit.prevent="submitReview"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Имя *</label>
                                    <input
                                        v-model="reviewForm.author_name"
                                        type="text"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                        :class="reviewErrors.author_name ? 'border-red-400' : 'border-gray-300'"
                                        required
                                    />
                                    <p v-if="reviewErrors.author_name" class="text-xs text-red-500 mt-1">
                                        {{ reviewErrors.author_name[0] }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input
                                        v-model="reviewForm.author_email"
                                        type="email"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                        :class="reviewErrors.author_email ? 'border-red-400' : 'border-gray-300'"
                                        placeholder="name@example.com"
                                    />
                                    <p v-if="reviewErrors.author_email" class="text-xs text-red-500 mt-1">
                                        {{ reviewErrors.author_email[0] }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Оценка *</label>
                                    <select
                                        v-model.number="reviewForm.rating"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                        :class="reviewErrors.rating ? 'border-red-400' : 'border-gray-300'"
                                        required
                                    >
                                        <option v-for="value in [5, 4, 3, 2, 1]" :key="value" :value="value">
                                            {{ value }}
                                        </option>
                                    </select>
                                    <p v-if="reviewErrors.rating" class="text-xs text-red-500 mt-1">
                                        {{ reviewErrors.rating[0] }}
                                    </p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Комментарий *</label>
                                    <textarea
                                        v-model="reviewForm.body"
                                        rows="4"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                        :class="reviewErrors.body ? 'border-red-400' : 'border-gray-300'"
                                        required
                                    ></textarea>
                                    <p v-if="reviewErrors.body" class="text-xs text-red-500 mt-1">
                                        {{ reviewErrors.body[0] }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center justify-end gap-4">
                                <button
                                    type="button"
                                    class="text-gray-500 hover:text-gray-700"
                                    @click="toggleReviewForm"
                                >
                                    Отменить
                                </button>
                                <button
                                    type="submit"
                                    class="bg-primary hover:bg-secondary text-white px-6 py-3 rounded-lg font-semibold transition-colors"
                                    :disabled="reviewSubmitting"
                                >
                                    <span v-if="reviewSubmitting" class="flex items-center"><i class="fas fa-spinner fa-spin mr-2"></i>Отправка…</span>
                                    <span v-else>Отправить отзыв</span>
                                </button>
                            </div>
                        </form>

                        <div v-if="reviews.length" class="space-y-6">
                            <div
                                v-for="review in reviews"
                                :key="review.id"
                                class="border border-gray-100 rounded-2xl p-6"
                            >
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ review.author_name }}</h4>
                                        <p v-if="review.created_at" class="text-sm text-gray-500">
                                            {{ new Date(review.created_at).toLocaleDateString('ru-RU') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center text-yellow-500">
                                        <i class="fas fa-star mr-1"></i>
                                        <span class="font-semibold">{{ review.rating }}</span>
                                    </div>
                                </div>
                                <p class="text-gray-600">{{ review.body }}</p>
                            </div>
                        </div>
                        <p v-else class="text-gray-500">
                            Отзывов пока нет. Станьте первым, поделившись мнением о товаре.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

