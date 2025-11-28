<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { computed, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { useShopStore } from '@/stores/useShopStore';

const breadcrumbs = [
    { label: 'Главная', href: '/' },
    { label: 'Оформление заказа' }
];

const shopStore = useShopStore();
shopStore.initialize();

const { cart, cartTotal } = storeToRefs(shopStore);

const priceFormatter = new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    maximumFractionDigits: 0
});

const normalizeNumber = (value) => {
    const numeric = Number.parseFloat(value ?? 0);
    return Number.isFinite(numeric) ? numeric : 0;
};

const formatPrice = (value) => priceFormatter.format(Math.max(0, normalizeNumber(value)));
const formatItemTotal = (item) => formatPrice((item.final_price ?? item.price ?? 0) * (item.quantity ?? 1));
const formatUnitPrice = (item) => formatPrice(item.final_price ?? item.price ?? 0);

const hasItems = computed(() => cart.value.length > 0);

const form = useForm({
    first_name: '',
    last_name: '',
    phone: '',
    email: '',
    delivery_method: 'courier',
    payment_method: 'cash',
    address: {
        city: 'Челябинск',
        street: '',
        house: '',
        apartment: '',
        entrance: '',
        comment: '',
        is_private_house: false
    },
    items: [],
    comment: '',
    agreement: false
});

const isCourierDelivery = computed(() => form.delivery_method === 'courier');

watch(cart, (items) => {
    form.items = items.map((item) => ({
        id: item.id,
        title: item.title,
        slug: item.slug ?? null,
        quantity: item.quantity ?? 1,
        price: item.price ?? 0,
        final_price: item.final_price ?? item.price ?? 0,
        image: item.image ?? null
    }));
}, { immediate: true, deep: true });

watch(
    () => form.address.is_private_house,
    (isPrivateHouse) => {
        if (isPrivateHouse) {
            form.address.apartment = '';
            form.address.entrance = '';
        }
    }
);

watch(
    () => form.delivery_method,
    (method) => {
        if (method !== 'courier') {
            form.address.street = '';
            form.address.house = '';
            form.address.apartment = '';
            form.address.entrance = '';
            form.address.comment = '';
            form.address.is_private_house = false;
        }
    }
);

const page = usePage();
const flash = computed(() => page.props.flash ?? {});

const submitOrder = () => {
    if (!hasItems.value || form.processing) {
        return;
    }

    form.post('/checkout', {
        preserveScroll: true,
        onSuccess: (success) => {
            shopStore.clearCart();
            form.reset();
            form.address.city = 'Челябинск';
            form.payment_method = 'cash';

        }
    });
};
</script>

<template>
    <AppLayout title="Оформление заказа - Акватэрия">
        <Breadcrumbs :items="breadcrumbs" />

        <div v-if="flash.success" class="bg-green-50 border-b border-green-200">
            <div class="container px-4 py-4 mx-auto">
                <p class="font-semibold text-green-800">{{ flash.success }}</p>
                <p v-if="flash.orderNumber" class="mt-1 text-sm text-green-700">Номер заказа: <span class="font-semibold">{{ flash.orderNumber }}</span></p>
            </div>
        </div>

        <!-- Checkout Steps -->
        <div class="bg-white border-b">
            <div class="md:py-6 container px-4 py-4 mx-auto">
                <div class="flex overflow-x-auto justify-center items-center">
                    <div class="md:space-x-8 flex items-center space-x-4 min-w-max">
                        <div class="flex items-center">
                            <div class="bg-primary md:mr-3 md:w-8 md:h-8 md:text-base flex justify-center items-center mr-2 w-7 h-7 text-sm font-semibold text-white rounded-full">1</div>
                            <span class="text-primary md:text-base text-sm font-semibold">Корзина</span>
                        </div>
                        <div class="bg-primary md:w-16 w-8 h-0.5"></div>
                        <div class="flex items-center">
                            <div class="bg-primary md:mr-3 md:w-8 md:h-8 md:text-base flex justify-center items-center mr-2 w-7 h-7 text-sm font-semibold text-white rounded-full">2</div>
                            <span class="text-primary md:text-base text-sm font-semibold">Оформление</span>
                        </div>
                        <div class="md:w-16 w-8 h-0.5 bg-gray-300"></div>
                        <div class="flex items-center">
                            <div class="md:mr-3 md:w-8 md:h-8 md:text-base flex justify-center items-center mr-2 w-7 h-7 text-sm font-semibold text-gray-600 bg-gray-300 rounded-full">3</div>
                            <span class="md:text-base text-sm text-gray-600">Подтверждение</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:py-8 container px-4 py-6 mx-auto">
            <form @submit.prevent="submitOrder">
                <div class="lg:grid-cols-3 md:gap-8 grid grid-cols-1 gap-6">
                    <!-- Order Form -->
                    <div class="lg:col-span-2 md:space-y-8 space-y-6">
                        <!-- Contact Information -->
                        <div class="md:p-8 md:rounded-2xl p-5 bg-white rounded-xl shadow-lg">
                            <h2 class="md:mb-6 md:text-2xl mb-4 text-xl font-semibold">Контактная информация</h2>
                            <div class="md:grid-cols-2 md:gap-6 grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Имя *</label>
                                    <input
                                        v-model="form.first_name"
                                        type="text"
                                        required
                                        class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent md:py-3 px-4 py-2.5 w-full text-base rounded-lg border border-gray-300"
                                        placeholder="Введите ваше имя"
                                    />
                                    <div v-if="form.errors.first_name" class="mt-1 text-sm text-red-500">
                                        {{ form.errors.first_name }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Фамилия *</label>
                                    <input
                                        v-model="form.last_name"
                                        type="text"
                                        required
                                        class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent md:py-3 px-4 py-2.5 w-full text-base rounded-lg border border-gray-300"
                                        placeholder="Введите вашу фамилию"
                                    />
                                    <div v-if="form.errors.last_name" class="mt-1 text-sm text-red-500">
                                        {{ form.errors.last_name }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Телефон *</label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        required
                                        class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent md:py-3 px-4 py-2.5 w-full text-base rounded-lg border border-gray-300"
                                        placeholder="+7 (___) ___-__-__"
                                    />
                                    <div v-if="form.errors.phone" class="mt-1 text-sm text-red-500">
                                        {{ form.errors.phone }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Email *</label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent md:py-3 px-4 py-2.5 w-full text-base rounded-lg border border-gray-300"
                                        placeholder="example@email.com"
                                    />
                                    <div v-if="form.errors.email" class="mt-1 text-sm text-red-500">
                                        {{ form.errors.email }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Method -->
                        <div class="md:p-8 md:rounded-2xl p-5 bg-white rounded-xl shadow-lg">
                            <h2 class="md:mb-6 md:text-2xl mb-4 text-xl font-semibold">Способ получения</h2>
                            <div class="md:space-y-4 space-y-3">
                                <label class="hover:border-primary hover:bg-blue-50 active:bg-blue-100 md:p-4 touch-manipulation flex items-start p-3 rounded-lg border border-gray-200 transition-colors cursor-pointer">
                                    <input
                                        v-model="form.delivery_method"
                                        type="radio"
                                        value="courier"
                                        class="text-primary focus:ring-primary mt-1"
                                    />
                                    <div class="md:ml-4 flex-1 ml-3">
                                        <div class="sm:flex-row sm:items-center flex flex-col gap-2 justify-between">
                                            <div>
                                                <div class="md:text-base text-sm font-semibold text-gray-800">Курьерская доставка</div>
                                                <div class="md:text-sm mt-1 text-xs text-gray-600">Доставим завтра с 10:00 до 18:00</div>
                                            </div>
                                            <div class="sm:text-right text-left">
                                                <div class="md:text-base text-sm font-semibold text-green-600">Бесплатно</div>
                                                <div class="md:text-sm text-xs text-gray-500">от 5 000 ₽</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <label class="hover:border-primary hover:bg-blue-50 active:bg-blue-100 md:p-4 touch-manipulation flex items-start p-3 rounded-lg border border-gray-200 transition-colors cursor-pointer">
                                    <input
                                        v-model="form.delivery_method"
                                        type="radio"
                                        value="pickup"
                                        class="text-primary focus:ring-primary mt-1"
                                    />
                                    <div class="md:ml-4 flex-1 ml-3">
                                        <div class="sm:flex-row sm:items-center flex flex-col gap-2 justify-between">
                                            <div>
                                                <div class="md:text-base text-sm font-semibold text-gray-800">Самовывоз из магазина</div>
                                                <div class="md:text-sm mt-1 text-xs text-gray-600">г. Челябинск, ул. Работниц, 89/1</div>
                                            </div>
                                            <div class="sm:text-right text-left">
                                                <div class="md:text-base text-sm font-semibold text-green-600">Бесплатно</div>
                                                <div class="md:text-sm text-xs text-gray-500">сегодня</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Delivery Address (shown when courier delivery is selected) -->
                            <div v-if="isCourierDelivery" class="md:p-6 md:mt-6 p-4 mt-4 bg-gray-50 rounded-lg">
                                <h3 class="md:mb-4 md:text-base mb-3 text-sm font-semibold">Адрес доставки</h3>
                                <div class="md:grid-cols-2 md:gap-4 grid grid-cols-1 gap-3">
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-700">Город *</label>
                                        <input
                                            v-model="form.address.city"
                                            type="text"
                                            required
                                            class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent md:py-3 px-4 py-2.5 w-full text-base rounded-lg border border-gray-300"
                                            placeholder="Город доставки"
                                        />
                                        <div v-if="form.errors['address.city']" class="mt-1 text-sm text-red-500">
                                            {{ form.errors['address.city'] }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-700">Улица *</label>
                                        <input
                                            v-model="form.address.street"
                                            type="text"
                                            :required="isCourierDelivery"
                                            class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent md:py-3 px-4 py-2.5 w-full text-base rounded-lg border border-gray-300"
                                            placeholder="Название улицы"
                                        />
                                        <div v-if="form.errors['address.street']" class="mt-1 text-sm text-red-500">
                                            {{ form.errors['address.street'] }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-700">Дом *</label>
                                        <input
                                            v-model="form.address.house"
                                            type="text"
                                            :required="isCourierDelivery"
                                            class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent md:py-3 px-4 py-2.5 w-full text-base rounded-lg border border-gray-300"
                                            placeholder="Номер дома"
                                        />
                                        <div v-if="form.errors['address.house']" class="mt-1 text-sm text-red-500">
                                            {{ form.errors['address.house'] }}
                                        </div>
                                    </div>
                                </div>

                                <label class="md:mt-4 md:p-4 touch-manipulation flex gap-3 items-start p-3 mt-3 bg-white rounded-lg border border-gray-200 cursor-pointer">
                                    <input
                                        v-model="form.address.is_private_house"
                                        type="checkbox"
                                        class="text-primary focus:ring-primary mt-0.5 w-5 h-5"
                                    />
                                    <span class="md:text-base text-sm text-gray-700">Это частный дом, квартиры нет</span>
                                </label>

                                <div class="md:grid-cols-2 md:gap-4 md:mt-4 grid grid-cols-1 gap-3 mt-3">
                                    <div v-if="!form.address.is_private_house">
                                        <label class="block mb-2 text-sm font-medium text-gray-700">Квартира</label>
                                        <input
                                            v-model="form.address.apartment"
                                            type="text"
                                            class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent md:py-3 px-4 py-2.5 w-full text-base rounded-lg border border-gray-300"
                                            placeholder="Номер квартиры"
                                        />
                                        <div v-if="form.errors['address.apartment']" class="mt-1 text-sm text-red-500">
                                            {{ form.errors['address.apartment'] }}
                                        </div>
                                    </div>
                                    <div v-if="!form.address.is_private_house">
                                        <label class="block mb-2 text-sm font-medium text-gray-700">Подъезд</label>
                                        <input
                                            v-model="form.address.entrance"
                                            type="text"
                                            class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent md:py-3 px-4 py-2.5 w-full text-base rounded-lg border border-gray-300"
                                            placeholder="Номер подъезда"
                                        />
                                        <div v-if="form.errors['address.entrance']" class="mt-1 text-sm text-red-500">
                                            {{ form.errors['address.entrance'] }}
                                        </div>
                                    </div>
                                </div>

                                <div class="md:mt-4 mt-3">
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Комментарий для курьера</label>
                                    <textarea
                                        v-model="form.address.comment"
                                        rows="3"
                                        class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent md:py-3 px-4 py-2.5 w-full text-base rounded-lg border border-gray-300 resize-none"
                                        placeholder="Например, код домофона или ориентиры"
                                    ></textarea>
                                    <div v-if="form.errors['address.comment']" class="mt-1 text-sm text-red-500">
                                        {{ form.errors['address.comment'] }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="md:p-8 md:rounded-2xl p-5 bg-white rounded-xl shadow-lg">
                            <h2 class="md:mb-6 md:text-2xl mb-4 text-xl font-semibold">Способ оплаты</h2>
                            <div class="md:space-y-4 space-y-3">
                                <label class="hover:border-primary hover:bg-blue-50 md:p-4 touch-manipulation flex items-center p-3 rounded-lg border border-gray-200 transition-colors cursor-pointer">
                                    <input
                                        v-model="form.payment_method"
                                        type="radio"
                                        value="card"
                                        class="text-primary focus:ring-primary"
                                    />
                                    <div class="md:ml-4 flex items-center ml-3">
                                        <i class="fas fa-credit-card md:mr-4 md:text-2xl mr-3 text-xl text-gray-400"></i>
                                        <div>
                                            <div class="md:text-base text-sm font-semibold text-gray-800">Банковская карта</div>
                                            <div class="md:text-sm text-xs text-gray-600">Оплата банковской картой сейчас</div>
                                        </div>
                                    </div>
                                </label>

                                <label class="hover:border-primary hover:bg-blue-50 active:bg-blue-100 md:p-4 touch-manipulation flex items-center p-3 rounded-lg border border-gray-200 transition-colors cursor-pointer">
                                    <input
                                        v-model="form.payment_method"
                                        type="radio"
                                        value="cash"
                                        class="text-primary focus:ring-primary"
                                    />
                                    <div class="md:ml-4 flex items-center ml-3">
                                        <i class="fas fa-money-bill-wave md:mr-4 md:text-2xl mr-3 text-xl text-gray-400"></i>
                                        <div>
                                            <div class="md:text-base text-sm font-semibold text-gray-800">Наличными</div>
                                            <div class="md:text-sm text-xs text-gray-600">Оплата курьеру при получении</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Order Comment -->
                        <div class="md:p-8 md:rounded-2xl p-5 bg-white rounded-xl shadow-lg">
                            <h2 class="md:mb-6 md:text-2xl mb-4 text-xl font-semibold">Комментарий к заказу</h2>
                            <textarea
                                v-model="form.comment"
                                rows="4"
                                class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent md:py-3 px-4 py-2.5 w-full text-base rounded-lg border border-gray-300 resize-none"
                                placeholder="Напишите дополнительные пожелания к заказу"
                            ></textarea>
                            <div v-if="form.errors.comment" class="mt-1 text-sm text-red-500">
                                {{ form.errors.comment }}
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="lg:sticky lg:top-24 md:p-8 md:rounded-2xl p-5 bg-white rounded-xl shadow-lg">
                            <h2 class="md:mb-6 md:text-2xl mb-4 text-xl font-semibold">Ваш заказ</h2>

                            <!-- Cart Items -->
                            <div v-if="hasItems" class="md:mb-6 md:space-y-4 mb-4 space-y-3">
                                <div
                                    v-for="item in cart"
                                    :key="item.id"
                                    class="md:p-4 md:space-x-4 flex items-center p-3 space-x-3 rounded-lg border border-gray-200"
                                >
                                    <div class="md:w-16 md:h-16 flex flex-shrink-0 justify-center items-center w-14 h-14 bg-gray-200 rounded-lg">
                                        <i class="fas fa-faucet text-2xl text-gray-400"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="md:text-sm line-clamp-2 text-xs font-semibold text-gray-800 break-words">{{ item.title }}</h3>
                                        <p class="md:text-sm mt-1 text-xs text-gray-600">{{ item.quantity }} шт.</p>
                                        <p class="text-primary md:text-base mt-1 text-sm font-semibold">{{ formatItemTotal(item) }}</p>
                                        <p class="text-xs text-gray-500">{{ formatUnitPrice(item) }} за шт.</p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="md:p-6 md:mb-6 md:text-sm p-4 mb-4 text-xs text-center text-gray-500 bg-gray-50 rounded-lg">
                                Корзина пуста. Перейдите в
                                <Link href="/catalog" class="text-primary hover:underline">каталог</Link>,
                                чтобы выбрать товары.
                            </div>

                            <!-- Order Total -->
                            <div class="md:mb-6 md:space-y-3 mb-4 space-y-2">
                                <div class="md:text-base flex justify-between text-sm">
                                    <span class="text-gray-600">Товары ({{ cart.length }} шт.):</span>
                                    <span class="font-medium">{{ formatPrice(cartTotal) }}</span>
                                </div>
                                <div class="md:text-base flex justify-between text-sm">
                                    <span class="text-gray-600">Доставка:</span>
                                    <span class="font-medium text-green-600">Бесплатно</span>
                                </div>
                                <div class="md:pt-3 pt-2 border-t">
                                    <div class="md:text-xl flex justify-between text-lg font-bold">
                                        <span>Итого:</span>
                                        <span class="text-primary">{{ formatPrice(cartTotal) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Button -->
                            <button
                                type="submit"
                                :disabled="form.processing || !hasItems"
                                class="bg-primary hover:bg-secondary active:bg-secondary disabled:opacity-50 md:py-4 md:mb-4 md:text-lg touch-manipulation py-3 mb-3 w-full text-base font-semibold text-white rounded-lg transition-colors"
                            >
                                <span v-if="!form.processing">Оформить заказ</span>
                                <span v-else><i class="fas fa-spinner fa-spin mr-2"></i>Обработка...</span>
                            </button>

                            <!-- Agreement -->
                            <div>
                                <label class="md:text-sm touch-manipulation flex items-start text-xs text-gray-600 cursor-pointer">
                                    <input
                                        v-model="form.agreement"
                                        type="checkbox"
                                        required
                                        class="text-primary focus:ring-primary flex-shrink-0 mt-0.5 mr-2 w-5 h-5 rounded"
                                    />
                                    <span>
                                        Я согласен с
                                        <a href="#" class="text-primary hover:underline">условиями обработки персональных данных</a>
                                    </span>
                                </label>
                                <div v-if="form.errors.agreement" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.agreement }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
