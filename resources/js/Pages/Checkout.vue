<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    cartItems: {
        type: Array,
        default: () => []
    },
    cartTotal: {
        type: Number,
        default: 0
    }
});

const breadcrumbs = [
    { label: 'Главная', href: '/' },
    { label: 'Оформление заказа' }
];

const form = useForm({
    first_name: '',
    last_name: '',
    phone: '',
    email: '',
    delivery_method: 'courier',
    payment_method: 'card',
    address: {
        city: 'Челябинск',
        street: '',
        house: '',
        apartment: '',
        entrance: '',
        comment: ''
    },
    additional_services: [],
    comment: '',
    agreement: false
});

const submitOrder = () => {
    form.post('/checkout', {
        onSuccess: () => {
            // Обработка успешного заказа
        }
    });
};
</script>

<template>
    <AppLayout title="Оформление заказа - СантехникаЧелябинск">
        <Breadcrumbs :items="breadcrumbs" />

        <!-- Checkout Steps -->
        <div class="bg-white border-b">
            <div class="container mx-auto px-4 py-6">
                <div class="flex items-center justify-center">
                    <div class="flex items-center space-x-8">
                        <div class="flex items-center">
                            <div class="bg-primary text-white w-8 h-8 rounded-full flex items-center justify-center font-semibold mr-3">1</div>
                            <span class="text-primary font-semibold">Корзина</span>
                        </div>
                        <div class="w-16 h-0.5 bg-primary"></div>
                        <div class="flex items-center">
                            <div class="bg-primary text-white w-8 h-8 rounded-full flex items-center justify-center font-semibold mr-3">2</div>
                            <span class="text-primary font-semibold">Оформление</span>
                        </div>
                        <div class="w-16 h-0.5 bg-gray-300"></div>
                        <div class="flex items-center">
                            <div class="bg-gray-300 text-gray-600 w-8 h-8 rounded-full flex items-center justify-center font-semibold mr-3">3</div>
                            <span class="text-gray-600">Подтверждение</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-8">
            <form @submit.prevent="submitOrder">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Order Form -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Contact Information -->
                        <div class="bg-white rounded-2xl shadow-lg p-8">
                            <h2 class="text-2xl font-semibold mb-6">Контактная информация</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Имя *</label>
                                    <input
                                        v-model="form.first_name"
                                        type="text"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        placeholder="Введите ваше имя"
                                    />
                                    <div v-if="form.errors.first_name" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.first_name }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Фамилия *</label>
                                    <input
                                        v-model="form.last_name"
                                        type="text"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        placeholder="Введите вашу фамилию"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Телефон *</label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        placeholder="+7 (___) ___-__-__"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        placeholder="example@email.com"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Method -->
                        <div class="bg-white rounded-2xl shadow-lg p-8">
                            <h2 class="text-2xl font-semibold mb-6">Способ получения</h2>
                            <div class="space-y-4">
                                <label class="flex items-start p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-primary hover:bg-blue-50 transition-colors">
                                    <input
                                        v-model="form.delivery_method"
                                        type="radio"
                                        value="courier"
                                        class="mt-1 text-primary focus:ring-primary"
                                    />
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="font-semibold text-gray-800">Курьерская доставка</div>
                                                <div class="text-sm text-gray-600 mt-1">Доставим завтра с 10:00 до 18:00</div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-semibold text-green-600">Бесплатно</div>
                                                <div class="text-sm text-gray-500">от 5 000 ₽</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <label class="flex items-start p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-primary hover:bg-blue-50 transition-colors">
                                    <input
                                        v-model="form.delivery_method"
                                        type="radio"
                                        value="pickup"
                                        class="mt-1 text-primary focus:ring-primary"
                                    />
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="font-semibold text-gray-800">Самовывоз из магазина</div>
                                                <div class="text-sm text-gray-600 mt-1">г. Челябинск, ул. Работниц, 89/1</div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-semibold text-green-600">Бесплатно</div>
                                                <div class="text-sm text-gray-500">сегодня</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Delivery Address (shown when courier delivery is selected) -->
                            <div v-show="form.delivery_method === 'courier'" class="mt-6 p-6 bg-gray-50 rounded-lg">
                                <h3 class="font-semibold mb-4">Адрес доставки</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Улица *</label>
                                        <input
                                            v-model="form.address.street"
                                            type="text"
                                            required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                            placeholder="Название улицы"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Дом *</label>
                                        <input
                                            v-model="form.address.house"
                                            type="text"
                                            required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                            placeholder="Номер дома"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="bg-white rounded-2xl shadow-lg p-8">
                            <h2 class="text-2xl font-semibold mb-6">Способ оплаты</h2>
                            <div class="space-y-4">
                                <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-primary hover:bg-blue-50 transition-colors">
                                    <input
                                        v-model="form.payment_method"
                                        type="radio"
                                        value="card"
                                        class="text-primary focus:ring-primary"
                                    />
                                    <div class="ml-4 flex items-center">
                                        <i class="fas fa-credit-card text-2xl text-gray-400 mr-4"></i>
                                        <div>
                                            <div class="font-semibold text-gray-800">Банковская карта</div>
                                            <div class="text-sm text-gray-600">Visa, MasterCard, МИР</div>
                                        </div>
                                    </div>
                                </label>

                                <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-primary hover:bg-blue-50 transition-colors">
                                    <input
                                        v-model="form.payment_method"
                                        type="radio"
                                        value="cash"
                                        class="text-primary focus:ring-primary"
                                    />
                                    <div class="ml-4 flex items-center">
                                        <i class="fas fa-money-bill-wave text-2xl text-gray-400 mr-4"></i>
                                        <div>
                                            <div class="font-semibold text-gray-800">Наличными</div>
                                            <div class="text-sm text-gray-600">Оплата курьеру при получении</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-lg p-8 sticky top-24">
                            <h2 class="text-2xl font-semibold mb-6">Ваш заказ</h2>

                            <!-- Cart Items -->
                            <div class="space-y-4 mb-6">
                                <div
                                    v-for="item in cartItems"
                                    :key="item.id"
                                    class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg"
                                >
                                    <div class="bg-gray-200 w-16 h-16 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-faucet text-2xl text-gray-400"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-sm">{{ item.name }}</h3>
                                        <p class="text-sm text-gray-600">Количество: {{ item.quantity }} шт.</p>
                                        <p class="font-semibold text-primary">{{ item.price }} ₽</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Total -->
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Товары ({{ cartItems.length }} шт.):</span>
                                    <span>{{ cartTotal }} ₽</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Доставка:</span>
                                    <span class="text-green-600">Бесплатно</span>
                                </div>
                                <div class="border-t pt-3">
                                    <div class="flex justify-between text-xl font-bold">
                                        <span>Итого:</span>
                                        <span class="text-primary">{{ cartTotal }} ₽</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Button -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-primary hover:bg-secondary text-white py-4 rounded-lg font-semibold text-lg transition-colors mb-4 disabled:opacity-50"
                            >
                                <span v-if="!form.processing">Оформить заказ</span>
                                <span v-else>Обработка...</span>
                            </button>

                            <!-- Agreement -->
                            <label class="flex items-start text-sm text-gray-600">
                                <input
                                    v-model="form.agreement"
                                    type="checkbox"
                                    required
                                    class="mt-1 mr-2 rounded text-primary focus:ring-primary"
                                />
                                <span>
                                    Я согласен с
                                    <a href="#" class="text-primary hover:underline">условиями обработки персональных данных</a>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

