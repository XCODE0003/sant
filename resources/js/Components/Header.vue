<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
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

const handleSearch = () => {
    const query = searchQuery.value.trim();

    if (!query) {
        return;
    }

    router.get('/search', { q: query }, { preserveState: true, replace: true });
};

watch(
    () => props.initialSearch,
    (value) => {
        if (value !== undefined && value !== null) {
            searchQuery.value = value;
        }
    },
);
</script>

<template>
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <!-- Top bar -->
            <div class="flex items-center justify-between py-2 text-sm text-gray-600 border-b">
                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <i class="fas fa-phone text-primary mr-2"></i>
                        <a href="tel:+79512353226" class="hover:text-primary">+7 (951) 235-32-26</a>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-primary mr-2"></i>
                        <a href="mailto:info@santehchel.ru" class="hover:text-primary">info@santehchel.ru</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="flex items-center">
                        <i class="fas fa-clock text-primary mr-2"></i>
                        Пн-Пт: 8:30-18:00
                    </span>
                </div>
            </div>

            <!-- Main header -->
            <div class="flex items-center justify-between py-4">
                <Link href="/" class="flex items-center hover:opacity-80 transition-opacity">
                    <div class="bg-gradient-to-r from-primary to-secondary text-white p-3 rounded-lg mr-4">
                        <i class="fas fa-wrench text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">СантехникаЧелябинск</h1>
                        <p class="text-sm text-gray-600">ВОДА и ТЕПЛО в вашем доме</p>
                    </div>
                </Link>

                <div class="flex-1 max-w-xl mx-8">
                    <form @submit.prevent="handleSearch" class="relative">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Поиск товаров..."
                            class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                        />
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </form>
                </div>

                <div class="flex items-center space-x-6">
                    <Link href="/favorites" class="relative">
                        <div class="flex items-center text-gray-600 hover:text-primary cursor-pointer transition-colors">
                            <i class="fas fa-heart text-xl mr-2"></i>
                            <span class="hidden md:block">Избранное</span>
                            <span
                                v-if="favoritesCount > 0"
                                class="absolute -top-2 -right-2 bg-primary text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
                            >
                                {{ favoritesCount }}
                            </span>
                        </div>
                    </Link>

                    <Link href="/cart" class="relative">
                        <div class="flex items-center text-gray-600 hover:text-primary cursor-pointer transition-colors">
                            <i class="fas fa-shopping-cart text-xl mr-2"></i>
                            <span class="hidden md:block">Корзина</span>
                            <span
                                v-if="cartCount > 0"
                                class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
                            >
                                {{ cartCount }}
                            </span>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="py-4 border-t">
                <ul class="flex items-center justify-center space-x-8 text-gray-700">
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
                </ul>
            </nav>
        </div>
    </header>
</template>

