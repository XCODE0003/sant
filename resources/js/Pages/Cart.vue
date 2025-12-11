<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { storeToRefs } from 'pinia';
import { computed } from 'vue';
import { useShopStore } from '@/stores/useShopStore';

const shopStore = useShopStore();
shopStore.initialize();

const { cart, cartCount, cartTotal } = storeToRefs(shopStore);

const priceFormatter = new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    maximumFractionDigits: 0,
});

const formatCurrency = (value) => priceFormatter.format(value ?? 0);

const hasItems = computed(() => cart.value.length > 0);

const increaseQuantity = (item) => {
    shopStore.updateCartQuantity(item.id, (item.quantity ?? 1) + 1);
};

const decreaseQuantity = (item) => {
    shopStore.updateCartQuantity(item.id, (item.quantity ?? 1) - 1);
};

const removeItem = (item) => {
    shopStore.removeFromCart(item.id);
};

const clearCart = () => {
    shopStore.clearCart();
};
</script>

<template>
    <AppLayout title="Корзина - Акватэрия">
        <section class="bg-primary text-white py-10 md:py-16">
            <div class="container mx-auto px-4">
                <h1 class="text-2xl md:text-4xl font-bold">Корзина</h1>
                <p class="mt-2 text-sm md:text-base text-blue-100">{{ cartCount }} товар(ов) в корзине</p>
            </div>
        </section>

        <div class="container mx-auto px-4 py-6 md:py-12 grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
            <section class="lg:col-span-2 space-y-4 md:space-y-6">
                <div v-if="hasItems" class="space-y-4">
                    <article
                        v-for="item in cart"
                        :key="item.id"
                        class="bg-white rounded-xl md:rounded-2xl shadow-sm p-4 md:p-6 flex flex-col sm:flex-row gap-4 md:gap-6"
                    >
                        <div class="w-full sm:w-20 md:w-24 h-20 md:h-24 bg-gray-100 rounded-xl flex items-center justify-center overflow-hidden flex-shrink-0">
                            <img
                                v-if="item.image"
                                :src="item.image"
                                :alt="item.title"
                                class="w-full h-full"
                            />
                            <i v-else class="fas fa-faucet text-3xl text-gray-300"></i>
                        </div>

                        <div class="flex-1 space-y-2">
                            <div class="flex items-start justify-between gap-2 md:gap-4">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base md:text-lg font-semibold text-gray-800 break-words">{{ item.title }}</h3>
                                    <Link
                                        :href="`/products/${item.id}`"
                                        class="text-sm text-primary hover:underline"
                                    >
                                        Открыть страницу товара
                                    </Link>
                                </div>
                                <button
                                    type="button"
                                    class="text-gray-400 hover:text-red-500 transition-colors"
                                    @click="removeItem(item)"
                                >
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
                                <div class="flex items-center border border-gray-300 rounded-lg w-fit">
                                    <button type="button" class="px-4 py-2 hover:bg-gray-100 active:bg-gray-200 touch-manipulation" @click="decreaseQuantity(item)">
                                        <span class="text-lg">-</span>
                                    </button>
                                    <input
                                        :value="item.quantity"
                                        class="w-14 text-center border-0 focus:outline-none text-base font-medium"
                                        readonly
                                    />
                                    <button type="button" class="px-4 py-2 hover:bg-gray-100 active:bg-gray-200 touch-manipulation" @click="increaseQuantity(item)">
                                        <span class="text-lg">+</span>
                                    </button>
                                </div>
                                <div class="text-right sm:text-right">
                                    <div class="text-lg font-semibold text-primary">
                                        {{ formatCurrency((item.final_price ?? item.price) * (item.quantity ?? 1)) }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ formatCurrency(item.final_price ?? item.price) }} за шт.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
                <div v-else class="bg-white rounded-xl md:rounded-2xl shadow-sm p-8 md:p-12 text-center text-gray-500 text-sm md:text-base">
                    Корзина пуста. Перейдите в <Link href="/catalog" class="text-primary hover:underline">каталог</Link>,
                    чтобы добавить товары.
                </div>
            </section>

            <aside class="bg-white rounded-xl md:rounded-2xl shadow-lg p-5 md:p-6 space-y-4 md:space-y-6">
                <h2 class="text-lg md:text-xl font-semibold text-gray-800">Итого</h2>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Количество</span>
                    <span class="font-medium text-gray-800">{{ cartCount }}</span>
                </div>
                <div class="flex items-center justify-between text-lg">
                    <span class="text-gray-500">Сумма</span>
                    <span class="font-semibold text-primary">{{ formatCurrency(cartTotal) }}</span>
                </div>
                <Link
                    as="button"
                    :href="hasItems ? '/checkout' : '#'"
                    class="w-full bg-primary hover:bg-secondary active:bg-secondary text-white py-3 md:py-3 rounded-lg font-semibold transition-colors text-base md:text-base touch-manipulation"
                    :class="{ 'opacity-60 pointer-events-none': !hasItems }"
                >
                    Оформить заказ
                </Link>
                <button
                    type="button"
                    class="w-full hover:bg-gray-100 active:bg-gray-200 text-gray-600 py-3 md:py-3 rounded-lg font-semibold transition-colors text-base md:text-base touch-manipulation"
                    :disabled="!hasItems"
                    @click="clearCart"
                >
                    Очистить корзину
                </button>
            </aside>
        </div>
    </AppLayout>
</template>
