<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { storeToRefs } from 'pinia';
import { computed } from 'vue';
import { useShopStore } from '@/stores/useShopStore';

const shopStore = useShopStore();
shopStore.initialize();

const { favorites, favoritesCount } = storeToRefs(shopStore);

const hasFavorites = computed(() => favorites.value.length > 0);

const priceFormatter = new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    maximumFractionDigits: 0,
});

const formatCurrency = (value) => priceFormatter.format(value ?? 0);

const toggleFavorite = (item) => {
    shopStore.toggleFavorite(item);
};
</script>

<template>
    <AppLayout title="Избранное - АкватЭрия">
        <section class="bg-primary text-white py-10 md:py-16">
            <div class="container mx-auto px-4">
                <h1 class="text-2xl md:text-4xl font-bold">Избранные товары</h1>
                <p class="mt-2 text-sm md:text-base text-blue-100">{{ favoritesCount }} товар(ов) в списке</p>
            </div>
        </section>

        <div class="container mx-auto px-4 py-6 md:py-12">
            <div v-if="hasFavorites" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
                <article
                    v-for="item in favorites"
                    :key="item.id"
                    class="bg-white rounded-xl md:rounded-2xl shadow-lg hover:shadow-2xl active:shadow-xl transition-all duration-300 overflow-hidden group"
                >
                    <Link :href="`/products/${item.id}`" class="block">
                        <div class="relative h-44 sm:h-52 md:h-56 bg-gray-100 flex items-center justify-center overflow-hidden">
                            <img
                                v-if="item.image"
                                :src="item.image"
                                :alt="item.title"
                                class="object-cover w-full h-full"
                            />
                            <i v-else class="fas fa-faucet text-6xl text-gray-300"></i>
                        </div>
                        <div class="p-4 md:p-6">
                            <h3 class="font-semibold text-base md:text-lg mb-2 text-gray-900 group-hover:text-primary transition-colors line-clamp-2">
                                {{ item.title }}
                            </h3>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-primary font-semibold text-base md:text-lg">
                                    {{ formatCurrency(item.final_price ?? item.price) }}
                                </span>
                                <button
                                    type="button"
                                    class="text-red-500 hover:text-red-600 active:text-red-700 transition-colors p-2 touch-manipulation"
                                    @click.prevent="toggleFavorite(item)"
                                >
                                    <i class="fas fa-heart text-lg md:text-xl"></i>
                                </button>
                            </div>
                        </div>
                    </Link>
                </article>
            </div>
            <div v-else class="bg-white rounded-2xl shadow-sm p-12 text-center text-gray-500">
                В избранном пока пусто. Добавьте понравившиеся товары из <Link href="/catalog" class="text-primary hover:underline">каталога</Link>.
            </div>
        </div>
    </AppLayout>
</template>

