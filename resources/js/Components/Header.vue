<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { storeToRefs } from 'pinia';
import { useShopStore } from '@/stores/useShopStore';

const props = defineProps({
    initialSearch: {
        type: String,
        default: '',
    },
});

const searchQuery = ref(props.initialSearch ?? '');
const shopStore = useShopStore();
shopStore.initialize();

const { cartCount, favoritesCount } = storeToRefs(shopStore);

const MIN_QUERY_LENGTH = 2;
const suggestions = ref([]);
const isSuggestionsOpen = ref(false);
const isSuggestionsLoading = ref(false);
const searchError = ref('');
const showMobileModal = ref(false);
const isMobileScreen = ref(false);
const desktopSearchWrapper = ref(null);
const mobileSearchInput = ref(null);

let fetchSuggestionsTimeoutId = null;
let activeAbortController = null;

const priceFormatter = new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    maximumFractionDigits: 0,
});

const formatCurrency = (value) => priceFormatter.format(value ?? 0);

const getSuggestionImage = (product) => {
    if (!product) {
        return null;
    }

    if (Array.isArray(product.images) && product.images.length > 0) {
        return product.images[0];
    }

    return product.image ?? null;
};

const hasSuggestions = computed(() => suggestions.value.length > 0);
const showDesktopSuggestions = computed(
    () => !isMobileScreen.value && isSuggestionsOpen.value && (hasSuggestions.value || searchError.value || isSuggestionsLoading.value),
);

const updateScreen = () => {
    if (typeof window === 'undefined') {
        return;
    }

    isMobileScreen.value = window.matchMedia('(max-width: 767px)').matches;
};

const clearFetchTimeout = () => {
    if (fetchSuggestionsTimeoutId) {
        clearTimeout(fetchSuggestionsTimeoutId);
        fetchSuggestionsTimeoutId = null;
    }
};

const cancelActiveRequest = () => {
    if (activeAbortController) {
        activeAbortController.abort();
        activeAbortController = null;
    }
};

const scheduleFetchSuggestions = (query) => {
    clearFetchTimeout();

    if (query.length < MIN_QUERY_LENGTH) {
        suggestions.value = [];
        isSuggestionsOpen.value = false;
        searchError.value = '';
        cancelActiveRequest();
        return;
    }

    fetchSuggestionsTimeoutId = setTimeout(() => {
        fetchSuggestions(query);
    }, 250);
};

const fetchSuggestions = async (query) => {
    if (typeof window === 'undefined') {
        return;
    }

    cancelActiveRequest();
    activeAbortController = new AbortController();
    isSuggestionsLoading.value = true;
    searchError.value = '';
    isSuggestionsOpen.value = true;

    try {
        const response = await fetch(`/search/suggestions?q=${encodeURIComponent(query)}`, {
            headers: {
                Accept: 'application/json',
            },
            signal: activeAbortController.signal,
        });

        if (!response.ok) {
            throw new Error(`Ошибка загрузки (${response.status})`);
        }

        const payload = await response.json();

        if (searchQuery.value.trim() !== query) {
            return;
        }

        suggestions.value = Array.isArray(payload?.data) ? payload.data : [];
        isSuggestionsOpen.value = true;
    } catch (error) {
        if (error?.name === 'AbortError') {
            return;
        }

        console.error('SEARCH::SUGGEST_ERROR', error);
        searchError.value = 'Не удалось загрузить результаты';
        isSuggestionsOpen.value = true;
    } finally {
        isSuggestionsLoading.value = false;
        activeAbortController = null;
    }
};

const handleSearch = () => {
    const query = searchQuery.value.trim();

    if (!query) {
        return;
    }

    closeMobileModal();
    router.get('/search', { q: query }, { preserveState: true, replace: true });
};

const handleFocusSearch = () => {
    if (isMobileScreen.value) {
        openMobileModal();
        return;
    }

    if (hasSuggestions.value || searchError.value) {
        isSuggestionsOpen.value = true;
    }
};

const handleBlurSearch = () => {
    if (isMobileScreen.value) {
        return;
    }

    setTimeout(() => {
        isSuggestionsOpen.value = false;
    }, 150);
};

const handleClickOutside = (event) => {
    if (!desktopSearchWrapper.value || isMobileScreen.value) {
        return;
    }

    if (desktopSearchWrapper.value.contains(event.target)) {
        return;
    }

    isSuggestionsOpen.value = false;
};

const closeSuggestions = () => {
    isSuggestionsOpen.value = false;
};

const openMobileModal = () => {
    showMobileModal.value = true;
    isSuggestionsOpen.value = true;

    nextTick(() => {
        if (mobileSearchInput.value) {
            mobileSearchInput.value.focus({ preventScroll: true });
        }
    });
};

const closeMobileModal = () => {
    showMobileModal.value = false;
    closeSuggestions();
};

const handleSuggestionSelect = () => {
    closeSuggestions();
    closeMobileModal();
};

watch(
    () => props.initialSearch,
    (value) => {
        if (value !== undefined && value !== null) {
            searchQuery.value = value;
        }
    },
);

watch(searchQuery, (value) => {
    const query = value.trim();
    scheduleFetchSuggestions(query);
});

watch(showMobileModal, (isOpen) => {
    if (isOpen) {
        nextTick(() => {
            if (mobileSearchInput.value) {
                mobileSearchInput.value.focus({ preventScroll: true });
            }
        });
    }
});

onMounted(() => {
    updateScreen();
    if (typeof window !== 'undefined') {
        window.addEventListener('resize', updateScreen, { passive: true });
    }

    document.addEventListener('click', handleClickOutside, true);
});

onBeforeUnmount(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('resize', updateScreen);
    }

    document.removeEventListener('click', handleClickOutside, true);
    clearFetchTimeout();
    cancelActiveRequest();
});
</script>

<template>
    <header class="sticky top-0 z-50 bg-white shadow-lg">
        <div class="container px-4 mx-auto">
            <!-- Top bar -->
            <div class="max-md:flex-col flex gap-3 justify-between items-center py-2 text-sm text-gray-600 border-b">
                <div class="max-md:grid flex grid-cols-2 items-center space-x-6">
                    <div class="max-md:justify-center flex items-center">
                        <i class="fas fa-phone text-primary mr-2"></i>
                        <a href="tel:+79512353226" class="hover:text-primary">+7 (951) 235-32-26</a>
                    </div>
<!--                    <div class="max-md:hidden flex items-center">-->
<!--                        <i class="fas fa-envelope text-primary mr-2"></i>-->
<!--                        <a href="mailto:qwer-75@mail.ru" class="hover:text-primary">qwer-75@mail.ru</a>-->
<!--                    </div>-->
                 <div class="max-md:hidden flex items-center text-xs">
                     <i class="fas fa-user-tie text-primary mr-2" aria-hidden="true"></i>
                     <span class="hover:text-primary">ИП Нурисламова Наталья Владимировна</span>
                 </div>
                 <div class="max-md:justify-center flex items-center text-xs">
                     <i class="fas fa-id-card text-primary mr-2" aria-hidden="true"></i>
                     <span class="hover:text-primary">ИНН 744808080440</span>
                 </div>
                </div>
                <div class="max-md:grid flex grid-cols-2 items-center space-x-4">
                    <span class="max-md:justify-center flex items-center text-xs">
                        <i class="fas fa-clock text-primary mr-2"></i>
                        Пн-Пт: 8:30-17:30, Сб-Вс: 9:00-16:30
                    </span>
                    <a href="https://yandex.ru/maps/-/CLSA4MNv" class="max-md:justify-center flex items-center text-xs">
                        <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                        г.Челябинск, Ул.Работниц 89/1 павильон 3306 СК "Перекресток"
                    </a>
                    <div class="flex gap-2 items-center">
                        <a href="https://vk.com/club232308766" class="text-primary rounded-lg transition-colors">
                            <i class="fab fa-vk"></i>
                        </a>
                        <a href="https://t.me/san3306" class="text-primary rounded-lg transition-colors">
                            <i class="fab fa-telegram"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main header -->
            <div class="max-md:flex-col max-md:gap-5 flex justify-between items-center py-4">
                <Link href="/" class="hover:opacity-80 flex gap-2 items-center transition-opacity">
                <img src="/images/logo.jpg" alt="Акватэрия" class="w-14 h-14 rounded-full">
                    <div>
                        <div class="bg-accent w-fit p-0.5 px-1 rounded-xl">
                            <h1 class="text-2xl font-bold text-[#f1e811]">Акват<span class="text-primary">Э</span>рия</h1>
                        </div>
                        <p class="text-sm text-gray-600">Территория воды и тепла</p>
                    </div>
                </Link>

                <div class="max-md:flex hidden gap-5 justify-between items-center w-full">
                    <button
                        type="button"
                        class="touch-manipulation focus:outline-none focus:ring-2 focus:ring-primary relative flex-1 px-4 py-3 w-full max-w-xl text-sm text-left text-gray-600 bg-white rounded-lg border border-gray-300 transition"
                        @click="openMobileModal"
                    >
                        <i class="fas fa-search absolute left-4 top-1/2 text-gray-400 -translate-y-1/2"></i>
                        <span v-if="!searchQuery" class="block ml-8 text-gray-500 truncate">Поиск товаров...</span>
                        <span v-else class="block ml-8 text-gray-800 truncate">{{ searchQuery }}</span>
                    </button>

                    <div class="flex items-center space-x-6">
                        <Link href="/favorites" class="relative">
                            <div class="hover:text-primary flex items-center text-gray-600 transition-colors cursor-pointer">
                                <i class="fas fa-heart mr-2 text-xl"></i>
                                <span class="md:block hidden">Избранное</span>
                                <span
                                    v-if="favoritesCount > 0"
                                    class="bg-primary flex absolute -top-2 -right-2 justify-center items-center w-5 h-5 text-xs text-white rounded-full"
                                >
                                    {{ favoritesCount }}
                                </span>
                            </div>
                        </Link>

                        <Link href="/cart" class="relative">
                            <div class="hover:text-primary flex items-center text-gray-600 transition-colors cursor-pointer">
                                <i class="fas fa-shopping-cart mr-2 text-xl"></i>
                                <span class="md:block hidden">Корзина</span>
                                <span
                                    v-if="cartCount > 0"
                                    class="bg-accent flex absolute -top-2 -right-2 justify-center items-center w-5 h-5 text-xs text-white rounded-full"
                                >
                                    {{ cartCount }}
                                </span>
                            </div>
                        </Link>
                    </div>
                </div>

                <div ref="desktopSearchWrapper" class="max-md:hidden flex relative flex-1 mx-8 w-full max-w-xl">
                    <form @submit.prevent="handleSearch" class="relative w-full">
                        <input
                            v-model="searchQuery"
                            type="search"
                            placeholder="Поиск товаров..."
                            class="focus:border-transparent focus:outline-none focus:ring-2 focus:ring-primary px-4 py-3 pl-12 w-full text-sm rounded-lg border border-gray-300"
                            @focus="handleFocusSearch"
                            @blur="handleBlurSearch"
                            autocomplete="off"
                        />
                        <i class="fas fa-search absolute left-4 top-1/2 text-gray-400 -translate-y-1/2"></i>
                    </form>

                    <div
                        v-if="showDesktopSuggestions"
                        class="overflow-hidden overflow-y-auto absolute max-h-[55vh] right-0 left-0 top-full z-50 mt-2 bg-white rounded-2xl border border-gray-200 shadow-2xl"
                    >
                        <div v-if="searchError && !isSuggestionsLoading" class="px-4 py-2 text-xs text-red-600 bg-red-50 border-b border-red-100">
                            {{ searchError }}
                        </div>
                        <div v-if="isSuggestionsLoading" class="p-4 text-sm text-gray-500">Ищем товары…</div>
                        <template v-else>
                            <ul v-if="hasSuggestions" class="divide-y divide-gray-100">
                                <li v-for="product in suggestions" :key="product.id" class="hover:bg-gray-50">
                                    <Link
                                        :href="`/products/${product.id}`"
                                        class="flex gap-4 items-center px-4 py-3"
                                        @click="handleSuggestionSelect"
                                    >
                                        <div class="flex overflow-hidden flex-shrink-0 justify-center items-center w-14 h-14 bg-gray-100 rounded-lg">
                                            <img
                                                v-if="getSuggestionImage(product)"
                                                :src="getSuggestionImage(product)"
                                                :alt="product.title"
                                                class="object-cover w-full h-full"
                                            />
                                            <i v-else class="fas fa-faucet text-2xl text-gray-300"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-semibold text-gray-800 truncate">{{ product.title }}</div>
                                            <div class="flex flex-wrap gap-x-2 items-center mt-1 text-xs text-gray-500">
                                                <span v-if="product.category?.title" class="truncate">{{ product.category.title }}</span>
                                                <span v-if="product.article_id" class="truncate">Артикул: {{ product.article_id }}</span>
                                            </div>
                                            <div class="text-primary mt-1 text-sm font-semibold">
                                                {{ formatCurrency(product.final_price ?? product.price ?? 0) }}
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right text-gray-300"></i>
                                    </Link>
                                </li>
                            </ul>
                            <div v-else class="p-4 text-sm" :class="searchError ? 'text-red-600' : 'text-gray-500'">
                                {{ searchError || 'Ничего не найдено' }}
                            </div>
                        </template>
                        <div v-if="hasSuggestions && !isSuggestionsLoading" class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                            <button
                                type="button"
                                class="bg-primary hover:bg-secondary active:bg-secondary px-4 py-2 w-full text-sm font-semibold text-white rounded-lg transition-colors"
                                @click="handleSearch"
                            >
                                Смотреть все результаты
                            </button>
                        </div>
                    </div>
                </div>

                <div class="max-md:hidden flex items-center space-x-6">
                    <Link href="/favorites" class="relative">
                        <div class="hover:text-primary flex items-center text-gray-600 transition-colors cursor-pointer">
                            <i class="fas fa-heart mr-2 text-xl"></i>
                            <span class="md:block hidden">Избранное</span>
                            <span
                                v-if="favoritesCount > 0"
                                class="bg-primary flex absolute -top-2 -right-2 justify-center items-center w-5 h-5 text-xs text-white rounded-full"
                            >
                                {{ favoritesCount }}
                            </span>
                        </div>
                    </Link>

                    <Link href="/cart" class="relative">
                        <div class="btn bg-accent text-yellow flex items-center p-2 px-10 rounded-xl transition-colors cursor-pointer">
                            <i class="fas fa-shopping-cart mr-2 text-xl"></i>
                            <span class="md:block hidden">Корзина</span>
                            <span
                                v-if="cartCount > 0"
                                class="bg-yellow flex absolute -top-2 -right-2 justify-center items-center w-5 h-5 text-xs text-white rounded-full"
                            >
                                {{ cartCount }}
                            </span>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="max-md:hidden py-4 border-t">
                <ul class="max-md:hidden flex justify-center items-center space-x-8 text-gray-700">
                    <li>
                        <Link href="/" class="hover:text-primary transition-colors">На главную</Link>
                    </li>
                    <li>
                        <Link href="/catalog" class="hover:text-primary transition-colors">Каталог товаров</Link>
                    </li>
                    <li>
                        <Link href="/payment" class="hover:text-primary transition-colors">Оплата и доставка</Link>
                    </li>
                    <li>
                        <Link href="/about" class="hover:text-primary transition-colors">О нас</Link>
                    </li>
                    <li>
                        <Link href="/services" class="hover:text-primary transition-colors">Услуги</Link>
                    </li>
                    <li>
                        <Link href="/news" class="hover:text-primary transition-colors">Новости и Акции</Link>
                    </li>
                    <li>
                        <Link href="/contacts" class="hover:text-primary transition-colors">Контакты</Link>
                    </li>
                    <div class="flex justify-center space-x-4">
                            <a href="https://t.me/san3306" class="bg-primary flex justify-center items-center p-1.5 w-8 h-8 text-white rounded-full transition-colors">
                                <i class="fab fa-telegram"></i>
                            </a>
                            <a href="https://vk.com/club232308766" class="bg-primary flex justify-center items-center p-1.5 w-8 h-8 text-white rounded-full transition-colors">
                                <i class="fab fa-vk"></i>
                            </a>
                        </div>
                </ul>
            </nav>
        </div>

        <Transition
            enter-active-class="transition-opacity duration-200"
            leave-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showMobileModal" class="fixed inset-0 z-[60] flex flex-col bg-black/40 backdrop-blur-sm">
                <div class="mt-auto max-h-[85vh] overflow-hidden rounded-t-3xl bg-white shadow-xl">
                    <div class="flex justify-between items-center px-4 py-3 border-b border-gray-100">
                        <h2 class="text-base font-semibold text-gray-800">Поиск товаров</h2>
                        <button
                            type="button"
                            class="hover:text-gray-600 p-2 text-gray-400 rounded-full transition"
                            @click="closeMobileModal"
                        >
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <div class="px-4 pt-4 pb-2">
                        <form @submit.prevent="handleSearch" class="relative">
                            <input
                                ref="mobileSearchInput"
                                v-model="searchQuery"
                                type="search"
                                placeholder="Введите запрос"
                                class="focus:border-transparent focus:outline-none focus:ring-2 focus:ring-primary px-4 py-3 pl-12 w-full text-sm rounded-lg border border-gray-300"
                                autocomplete="off"
                            />
                            <i class="fas fa-search absolute left-4 top-1/2 text-gray-400 -translate-y-1/2"></i>
                        </form>
                    </div>
                    <div class="max-h-[55vh] overflow-y-auto px-4 pb-4">
                        <div v-if="isSuggestionsLoading" class="py-6 text-sm text-center text-gray-500">Ищем товары…</div>
                        <template v-else>
                            <ul v-if="hasSuggestions" class="divide-y divide-gray-100">
                                <li v-for="product in suggestions" :key="`mobile-${product.id}`" class="hover:bg-gray-50">
                                    <Link
                                        :href="`/products/${product.id}`"
                                        class="flex gap-4 items-center px-2 py-3"
                                        @click="handleSuggestionSelect"
                                    >
                                        <div class="flex overflow-hidden flex-shrink-0 justify-center items-center w-14 h-14 bg-gray-100 rounded-lg">
                                            <img
                                                v-if="getSuggestionImage(product)"
                                                :src="getSuggestionImage(product)"
                                                :alt="product.title"
                                                class="object-cover w-full h-full"
                                            />
                                            <i v-else class="fas fa-faucet text-2xl text-gray-300"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-semibold text-gray-800 truncate">{{ product.title }}</div>
                                            <div class="flex flex-wrap gap-x-2 items-center mt-1 text-xs text-gray-500">
                                                <span v-if="product.category?.title" class="truncate">{{ product.category.title }}</span>
                                                <span v-if="product.article_id" class="truncate">Артикул: {{ product.article_id }}</span>
                                            </div>
                                            <div class="text-primary mt-1 text-sm font-semibold">
                                                {{ formatCurrency(product.final_price ?? product.price ?? 0) }}
                                            </div>
                                        </div>
                                    </Link>
                                </li>
                            </ul>
                            <div v-else class="py-6 text-sm text-center" :class="searchError ? 'text-red-600' : 'text-gray-500'">
                                {{ searchError || 'Ничего не найдено' }}
                            </div>
                        </template>
                    </div>
                    <div v-if="hasSuggestions && !isSuggestionsLoading" class="px-4 py-3 border-t border-gray-100">
                        <button
                            type="button"
                            class="bg-primary hover:bg-secondary active:bg-secondary px-4 py-3 w-full text-base font-semibold text-white rounded-xl transition-colors"
                            @click="handleSearch"
                        >
                            Смотреть все результаты
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </header>
</template>
