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
    <AppLayout :title="`${product.title} - Акватэрия`">
        <Breadcrumbs :items="breadcrumbs" />

        <div class="md:py-8 container px-4 py-6 mx-auto">
            <div class="lg:grid-cols-2 md:gap-10 lg:gap-12 md:mb-12 grid grid-cols-1 gap-6 mb-8">
                <!-- Product Images -->
                <div class="md:space-y-4 space-y-3">
                    <div class="md:rounded-2xl md:p-6 lg:p-8 p-4 bg-white rounded-xl shadow-lg">
                        <div class="md:rounded-xl md:h-80 lg:h-96 md:mb-4 flex overflow-hidden justify-center items-center mb-3 h-64 bg-gray-100 rounded-lg">
                            <img
                                v-if="currentImage"
                                :src="currentImage"
                                :alt="product.title"
                                class="object-contain w-full h-full"
                            />
                            <i v-else class="fas fa-faucet text-8xl text-gray-300"></i>
                        </div>
                        <div class="md:gap-2 grid grid-cols-4 gap-1.5">
                            <button
                                v-for="(image, index) in images"
                                :key="image || index"
                                type="button"
                                class="md:rounded-lg md:h-20 touch-manipulation flex overflow-hidden justify-center items-center h-16 bg-gray-100 rounded-md border-2"
                                :class="currentImage === image ? 'border-primary' : 'border-transparent hover:border-primary active:border-primary'"
                                @click="selectImage(image)"
                            >
                                <img v-if="image" :src="image" :alt="product.title" class="object-cover w-full h-full" />
                                <i v-else class="fas fa-faucet text-2xl text-gray-300"></i>
                            </button>
                            <div
                                v-if="images.length === 0"
                                class="max-md:text-xs max-md:text-center flex justify-center items-center h-20 text-sm text-gray-500 bg-gray-100 rounded-lg"
                            >
                                Фото пока нет
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="md:space-y-6 space-y-4">
                    <div>
                        <div class="md:mb-3 flex gap-2 items-center mb-2">
                            <span
                                v-if="hasDiscount"
                                class="md:px-3 md:text-sm px-2 py-1 text-xs font-semibold text-white bg-red-500 rounded-full"
                            >
                                -{{ product.discount }}%
                            </span>
                            <span class="md:px-3 md:text-sm px-2 py-1 text-xs font-semibold text-white bg-green-500 rounded-full">
                                {{ product.is_active ? 'В наличии' : 'Нет в наличии' }}
                            </span>
                        </div>
                        <h1 class="md:text-2xl lg:text-3xl md:mb-3 mb-2 text-xl font-bold text-gray-800">{{ product.title }}</h1>
                        <p v-if="product.category" class="md:text-sm text-primary mb-2 text-xs">
                            <Link :href="`/catalog?category=${product.category.slug}`" preserve-scroll class="hover:underline">
                                {{ product.category.title }}
                            </Link>
                        </p>
                        <p class="md:text-sm text-xs text-gray-500">Артикул: {{ product.article_id }}</p>

                        <div v-if="hasRating" class="md:mt-4 flex items-center mt-3">
                            <div class="flex mr-2 text-yellow-400">
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
                        <div v-else class="mt-4 text-sm text-gray-500">
                            Пока нет оценок — станьте первым, кто оставит отзыв
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="md:rounded-xl md:p-6 p-4 bg-gray-100 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="sm:flex-row sm:items-center sm:space-x-4 flex flex-col mb-2">
                                    <span class="md:text-3xl lg:text-4xl text-primary text-2xl font-bold">{{ formatCurrency(finalPrice) }}</span>
                                    <span v-if="hasDiscount" class="md:text-lg lg:text-xl text-base text-gray-500 line-through">
                                        {{ formatCurrency(originalPrice) }}
                                    </span>
                                </div>
                                <p v-if="hasDiscount" class="md:text-sm text-xs text-gray-600">
                                    Экономия: {{ formatCurrency(originalPrice - finalPrice) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Quantity and Actions -->
                    <div class="md:space-y-4 space-y-3">
                        <div class="md:gap-4 flex gap-3 items-center">
                            <span class="md:text-base text-sm text-gray-700">Количество:</span>
                            <div class="flex items-center rounded-lg border border-gray-300">
                                <button @click="changeQuantity(-1)" class="hover:bg-gray-100 active:bg-gray-200 touch-manipulation px-4 py-2" type="button">
                                    <span class="text-lg">-</span>
                                </button>
                                <input
                                    v-model.number="quantity"
                                    type="number"
                                    min="1"
                                    class="md:w-16 focus:outline-none w-14 text-base font-medium text-center border-0"
                                />
                                <button @click="changeQuantity(1)" class="hover:bg-gray-100 active:bg-gray-200 touch-manipulation px-4 py-2" type="button">
                                    <span class="text-lg">+</span>
                                </button>
                            </div>
                        </div>

                        <div class="sm:flex-row md:gap-4 flex flex-col gap-3">
                            <button
                                type="button"
                                class="md:py-4 md:px-6 md:text-lg touch-manipulation flex-1 px-4 py-3 text-base font-semibold rounded-lg transition-colors"
                                :class="isProductInCart
                                    ? 'bg-green-500 hover:bg-green-600 active:bg-green-700 text-white'
                                    : 'bg-primary hover:bg-secondary active:bg-secondary text-white'"
                                @click="addToCart"
                            >
                                <i :class="[isProductInCart ? 'fas fa-check' : 'fas fa-shopping-cart', 'mr-2']"></i>
                                <span class="sm:inline hidden">{{ isProductInCart ? 'В корзине' : 'Добавить в корзину' }}</span>
                                <span class="sm:hidden">{{ isProductInCart ? 'В корзине' : 'В корзину' }}</span>
                            </button>
                            <button
                                type="button"
                                class="bg-accent hover:bg-yellow-500 active:bg-yellow-600 md:py-4 md:px-6 md:text-lg touch-manipulation px-4 py-3 text-base font-semibold text-white rounded-lg transition-colors"
                            >
                                <i class="fas fa-bolt mr-2"></i>
                                <span class="sm:inline hidden">Купить в 1 клик</span>
                                <span class="sm:hidden">Быстрая покупка</span>
                            </button>
                        </div>

                        <div class="md:gap-3 flex gap-2">
                            <button
                                type="button"
                                class="hover:bg-gray-200 active:bg-gray-300 md:py-3 md:px-4 md:text-base touch-manipulation flex-1 px-3 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg transition-colors"
                                :class="isFavoriteProduct ? 'text-red-500' : 'text-gray-700'"
                                @click="toggleFavorite"
                            >
                                <i :class="[isFavoriteProduct ? 'fas fa-heart' : 'far fa-heart', 'mr-1 md:mr-2']"></i>
                                <span class="sm:inline hidden">{{ isFavoriteProduct ? 'В избранном' : 'В избранное' }}</span>
                            </button>

                        </div>
                    </div>

                    <!-- Delivery Info -->
                    <div class="md:rounded-xl md:p-6 md:space-y-3 p-4 space-y-2 bg-blue-50 rounded-lg">
                        <h3 class="md:text-base text-sm font-semibold text-gray-800">Доставка и самовывоз</h3>
                        <div class="flex items-start">
                            <i class="fas fa-truck text-primary md:mr-3 md:text-base flex-shrink-0 mt-1 mr-2 text-sm"></i>
                            <div>
                                <div class="md:text-base text-sm font-medium">Курьерская доставка</div>
                                <div class="md:text-sm text-xs text-gray-600">Доставка на следующий день</div>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-store text-primary md:mr-3 md:text-base flex-shrink-0 mt-1 mr-2 text-sm"></i>
                            <div>
                                <div class="md:text-base text-sm font-medium">Самовывоз</div>
                                <div class="md:text-sm text-xs text-gray-600">Заберите заказ в магазине уже сегодня</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details Tabs -->
            <div class="md:rounded-2xl overflow-hidden bg-white rounded-xl shadow-lg">
                <div class="overflow-x-auto border-b">
                    <nav class="sm:min-w-0 flex flex-nowrap min-w-max">
                        <button
                            @click="showTab('description')"
                            :class="[
                                'px-4 md:px-6 lg:px-8 py-3 md:py-4 font-semibold transition-colors text-sm md:text-base touch-manipulation whitespace-nowrap',
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
                                'px-4 md:px-6 lg:px-8 py-3 md:py-4 font-semibold transition-colors text-sm md:text-base touch-manipulation whitespace-nowrap',
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
                                'px-4 md:px-6 lg:px-8 py-3 md:py-4 font-semibold transition-colors text-sm md:text-base touch-manipulation whitespace-nowrap',
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

                <div class="md:p-6 lg:p-8 p-4">
                    <!-- Description Tab -->
                    <div v-show="activeTab === 'description'" class="prose max-w-none">
                        <h3 class="md:text-2xl md:mb-4 mb-3 text-xl font-semibold">Описание товара</h3>
                        <p class="text-gray-600" v-html="product.description" v-if="product.description">
                        </p>
                        <p v-else class="text-gray-500">
                            Описание для данного товара пока не добавлено.
                        </p>
                    </div>

                    <!-- Specifications Tab -->
                    <div v-show="activeTab === 'specifications'">
                        <h3 class="md:text-2xl md:mb-6 mb-4 text-xl font-semibold">Технические характеристики</h3>
                        <div v-if="characteristics.length" class="md:grid-cols-2 md:gap-6 grid grid-cols-1 gap-4">
                            <div
                                v-for="item in characteristics"
                                :key="item.key"
                                class="md:text-base flex justify-between items-start pb-2 text-sm border-b border-gray-100"
                            >
                                <span class="md:pr-4 pr-2 text-gray-500 break-words">{{ item.key }}</span>
                                <span class="font-medium text-right text-gray-800 break-words">{{ item.value }}</span>
                            </div>
                        </div>
                        <p v-else class="text-gray-500">
                            Характеристики для этого товара ещё не заполнены.
                        </p>
                    </div>

                    <!-- Reviews Tab -->
                    <div v-show="activeTab === 'reviews'">
                        <div class="md:gap-4 md:mb-8 flex flex-wrap gap-3 justify-between items-center mb-6">
                            <div>
                                <h3 class="md:text-2xl text-xl font-semibold">Отзывы покупателей</h3>
                                <p class="md:text-sm text-xs text-gray-500">Поделитесь мнением о товаре — это поможет другим покупателям</p>
                            </div>
                            <button
                                type="button"
                                class="bg-primary hover:bg-secondary active:bg-secondary md:px-6 md:py-3 md:text-base touch-manipulation px-4 py-2 text-sm font-semibold text-white whitespace-nowrap rounded-lg transition-colors"
                                @click="toggleReviewForm"
                            >
                                {{ showReviewForm ? 'Скрыть форму' : 'Написать отзыв' }}
                            </button>
                        </div>

                        <div v-if="reviewSuccess" class="px-4 py-3 mb-6 text-green-800 bg-green-100 rounded-lg">
                            {{ reviewSuccess }}
                        </div>
                        <div v-if="reviewError" class="px-4 py-3 mb-6 text-red-700 bg-red-100 rounded-lg">
                            {{ reviewError }}
                        </div>

                        <form
                            v-if="showReviewForm"
                            class="md:rounded-2xl md:p-6 md:mb-8 md:space-y-4 p-4 mb-6 space-y-3 bg-gray-50 rounded-xl border border-gray-200"
                            @submit.prevent="submitReview"
                        >
                            <div class="md:grid-cols-2 md:gap-4 grid grid-cols-1 gap-3">
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">Имя *</label>
                                    <input
                                        v-model="reviewForm.author_name"
                                        type="text"
                                        class="md:py-2 focus:outline-none focus:ring-2 focus:ring-primary px-4 py-2.5 w-full text-base rounded-lg border"
                                        :class="reviewErrors.author_name ? 'border-red-400' : 'border-gray-300'"
                                        required
                                    />
                                    <p v-if="reviewErrors.author_name" class="mt-1 text-xs text-red-500">
                                        {{ reviewErrors.author_name[0] }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">Email</label>
                                    <input
                                        v-model="reviewForm.author_email"
                                        type="email"
                                        class="focus:outline-none focus:ring-2 focus:ring-primary px-4 py-2 w-full rounded-lg border"
                                        :class="reviewErrors.author_email ? 'border-red-400' : 'border-gray-300'"
                                        placeholder="name@example.com"
                                    />
                                    <p v-if="reviewErrors.author_email" class="mt-1 text-xs text-red-500">
                                        {{ reviewErrors.author_email[0] }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">Оценка *</label>
                                    <select
                                        v-model.number="reviewForm.rating"
                                        class="focus:outline-none focus:ring-2 focus:ring-primary px-4 py-2 w-full rounded-lg border"
                                        :class="reviewErrors.rating ? 'border-red-400' : 'border-gray-300'"
                                        required
                                    >
                                        <option v-for="value in [5, 4, 3, 2, 1]" :key="value" :value="value">
                                            {{ value }}
                                        </option>
                                    </select>
                                    <p v-if="reviewErrors.rating" class="mt-1 text-xs text-red-500">
                                        {{ reviewErrors.rating[0] }}
                                    </p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block mb-1 text-sm font-medium text-gray-700">Комментарий *</label>
                                    <textarea
                                        v-model="reviewForm.body"
                                        rows="4"
                                        class="focus:outline-none focus:ring-2 focus:ring-primary px-4 py-2 w-full rounded-lg border"
                                        :class="reviewErrors.body ? 'border-red-400' : 'border-gray-300'"
                                        required
                                    ></textarea>
                                    <p v-if="reviewErrors.body" class="mt-1 text-xs text-red-500">
                                        {{ reviewErrors.body[0] }}
                                    </p>
                                </div>
                            </div>
                            <div class="md:gap-4 flex gap-3 justify-end items-center">
                                <button
                                    type="button"
                                    class="hover:text-gray-700 active:text-gray-800 md:text-base touch-manipulation text-sm text-gray-500"
                                    @click="toggleReviewForm"
                                >
                                    Отменить
                                </button>
                                <button
                                    type="submit"
                                    class="bg-primary hover:bg-secondary active:bg-secondary md:px-6 md:py-3 md:text-base touch-manipulation px-4 py-2 text-sm font-semibold text-white rounded-lg transition-colors"
                                    :disabled="reviewSubmitting"
                                >
                                    <span v-if="reviewSubmitting" class="flex items-center"><i class="fas fa-spinner fa-spin mr-2"></i>Отправка…</span>
                                    <span v-else>Отправить отзыв</span>
                                </button>
                            </div>
                        </form>

                        <div v-if="reviews.length" class="md:space-y-6 space-y-4">
                            <div
                                v-for="review in reviews"
                                :key="review.id"
                                class="md:rounded-2xl md:p-6 p-4 rounded-xl border border-gray-100"
                            >
                                <div class="flex justify-between items-center mb-3">
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
