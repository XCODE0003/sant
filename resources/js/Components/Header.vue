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
    <header class="sticky top-0 z-50 bg-white shadow-lg">
        <div class="container px-4 mx-auto">
            <!-- Top bar -->
            <div class="flex justify-between items-center py-2 text-sm text-gray-600 border-b">
                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <i class="fas fa-phone text-primary mr-2"></i>
                        <a href="tel:+79512353226" class="hover:text-primary">+7 (951) 235-32-26</a>
                    </div>
                    <div class="max-md:hidden flex items-center">
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
            <div class="max-md:flex-col max-md:gap-5 flex justify-between items-center py-4">
                <Link href="/" class="hover:opacity-80 flex items-center transition-opacity">
                <div class="from-primary to-secondary p-3 mr-4 text-white bg-gradient-to-r rounded-lg">
                    <i class="fas fa-wrench text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">СантехникаЧелябинск</h1>
                    <p class="text-sm text-gray-600">ВОДА и ТЕПЛО в вашем доме</p>
                </div>
                </Link>

                <div class="max-md:flex hidden gap-5 justify-between items-center">
                    <div class="flex-1 w-full max-w-xl">
                        <form @submit.prevent="handleSearch" class="relative">
                            <input v-model="searchQuery" type="text" placeholder="Поиск товаров..." class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent px-4 py-3 pl-12 w-full rounded-lg border border-gray-300" />
                            <i class="fas fa-search absolute left-4 top-1/2 text-gray-400 transform -translate-y-1/2"></i>
                        </form>
                    </div>

                    <div class="flex items-center space-x-6">
                        <Link href="/favorites" class="relative">
                        <div class="hover:text-primary flex items-center text-gray-600 transition-colors cursor-pointer">
                            <i class="fas fa-heart mr-2 text-xl"></i>
                            <span class="md:block hidden">Избранное</span>
                            <span v-if="favoritesCount > 0" class="bg-primary flex absolute -top-2 -right-2 justify-center items-center w-5 h-5 text-xs text-white rounded-full">
                                {{ favoritesCount }}
                            </span>
                        </div>
                        </Link>

                        <Link href="/cart" class="relative">
                        <div class="hover:text-primary flex items-center text-gray-600 transition-colors cursor-pointer">
                            <i class="fas fa-shopping-cart mr-2 text-xl"></i>
                            <span class="md:block hidden">Корзина</span>
                            <span v-if="cartCount > 0" class="bg-accent flex absolute -top-2 -right-2 justify-center items-center w-5 h-5 text-xs text-white rounded-full">
                                {{ cartCount }}
                            </span>
                        </div>
                        </Link>
                    </div>
                </div>
                <div class="max-md:hidden flex-1 mx-8 max-w-xl">
                    <form @submit.prevent="handleSearch" class="relative">
                        <input v-model="searchQuery" type="text" placeholder="Поиск товаров..." class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent px-4 py-3 pl-12 w-full rounded-lg border border-gray-300" />
                        <i class="fas fa-search absolute left-4 top-1/2 text-gray-400 transform -translate-y-1/2"></i>
                    </form>
                </div>

                <div class="max-md:hidden flex items-center space-x-6">
                    <Link href="/favorites" class="relative">
                    <div class="hover:text-primary flex items-center text-gray-600 transition-colors cursor-pointer">
                        <i class="fas fa-heart mr-2 text-xl"></i>
                        <span class="md:block hidden">Избранное</span>
                        <span v-if="favoritesCount > 0" class="bg-primary flex absolute -top-2 -right-2 justify-center items-center w-5 h-5 text-xs text-white rounded-full">
                            {{ favoritesCount }}
                        </span>
                    </div>
                    </Link>

                    <Link href="/cart" class="relative">
                    <div class="hover:text-primary flex items-center text-gray-600 transition-colors cursor-pointer">
                        <i class="fas fa-shopping-cart mr-2 text-xl"></i>
                        <span class="md:block hidden">Корзина</span>
                        <span v-if="cartCount > 0" class="bg-accent flex absolute -top-2 -right-2 justify-center items-center w-5 h-5 text-xs text-white rounded-full">
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
                </ul>
            </nav>
        </div>
    </header>
</template>
