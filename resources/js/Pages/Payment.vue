<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { ref } from 'vue';

const breadcrumbs = [
    { label: 'Главная', href: '/' },
    { label: 'Оплата и доставка' }
];

const activeFaq = ref(null);

const toggleFaq = (number) => {
    activeFaq.value = activeFaq.value === number ? null : number;
};
</script>

<template>
    <AppLayout title="Оплата и доставка - АкватЭрия">
        <Breadcrumbs :items="breadcrumbs" />

        <!-- Hero Section -->
        <section class="bg-primary text-white py-10 md:py-16">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-2xl md:text-4xl lg:text-5xl font-bold mb-4 md:mb-6">Оплата и доставка</h1>
                <p class="text-sm md:text-lg lg:text-xl text-blue-100 max-w-3xl mx-auto">
                    Удобные способы оплаты и быстрая доставка по всей России.
                    Мы заботимся о том, чтобы получение вашего заказа было максимально комфортным.
                </p>
            </div>
        </section>

        <div class="container mx-auto px-4 py-10 md:py-16">
            <!-- Quick Info -->
            <section class="mb-12 md:mb-20">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
                    <div class="bg-white rounded-xl md:rounded-2xl shadow-lg p-6 md:p-8 text-center">
                        <div class="bg-green-100 w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                            <i class="fas fa-truck text-2xl md:text-3xl text-green-600"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-semibold mb-2 md:mb-3">Быстрая доставка</h3>
                        <p class="text-gray-600 mb-4">От 1 дня по Москве, 3-7 дней по России</p>
                        <div class="text-2xl font-bold text-primary">Бесплатно от 5 000 ₽</div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                        <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-credit-card text-3xl text-primary"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Удобная оплата</h3>
                        <p class="text-gray-600 mb-4">Наличными, картой, переводом</p>
                        <div class="text-2xl font-bold text-primary">100% безопасно</div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                        <div class="bg-yellow-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-shield-alt text-3xl text-yellow-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Гарантия качества</h3>
                        <p class="text-gray-600 mb-4">Проверяем каждый товар перед отправкой</p>
                        <div class="text-2xl font-bold text-primary">До 5 лет</div>
                    </div>
                </div>
            </section>

            <!-- Delivery Methods -->
            <section class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-800 mb-4">Способы доставки</h2>
                    <p class="text-xl text-gray-600">Выберите наиболее удобный для вас вариант</p>
                </div>

                <div class="space-y-8">
                    <!-- Courier Delivery -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-primary to-secondary text-white p-6">
                            <div class="flex items-center">
                                <i class="fas fa-truck text-3xl mr-4"></i>
                                <div>
                                    <h3 class="text-2xl font-semibold">Курьерская доставка</h3>
                                    <p class="text-blue-100">Доставим прямо к вашей двери</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <h4 class="text-xl font-semibold mb-4">По Москве и МО</h4>
                                    <ul class="space-y-3 text-gray-600">
                                        <li class="flex items-center">
                                            <i class="fas fa-check text-green-600 mr-3"></i>
                                            <span>Бесплатно при заказе от 5 000 ₽</span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-check text-green-600 mr-3"></i>
                                            <span>500 ₽ при заказе до 5 000 ₽</span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-check text-green-600 mr-3"></i>
                                            <span>Доставка на следующий день</span>
                                        </li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="text-xl font-semibold mb-4">По России</h4>
                                    <ul class="space-y-3 text-gray-600">
                                        <li class="flex items-center">
                                            <i class="fas fa-check text-green-600 mr-3"></i>
                                            <span>Стоимость от 800 ₽</span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-check text-green-600 mr-3"></i>
                                            <span>Срок доставки: 3-7 дней</span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-check text-green-600 mr-3"></i>
                                            <span>Отслеживание посылки</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Payment Methods -->
            <section class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-800 mb-4">Способы оплаты</h2>
                    <p class="text-xl text-gray-600">Выберите удобный для вас способ оплаты</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Card Payment -->
                    <div class="bg-white rounded-2xl shadow-lg p-8 hover:shadow-2xl transition-shadow">
                        <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-credit-card text-3xl text-primary"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-center mb-4">Банковские карты</h3>
                        <ul class="space-y-3 text-gray-600 mb-6">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-600 mr-3"></i>
                                <span>Visa, MasterCard, МИР</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-600 mr-3"></i>
                                <span>Безопасная оплата</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-600 mr-3"></i>
                                <span>SSL-шифрование</span>
                            </li>
                        </ul>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-primary">0% комиссии</div>
                        </div>
                    </div>

                    <!-- Cash Payment -->
                    <div class="bg-white rounded-2xl shadow-lg p-8 hover:shadow-2xl transition-shadow">
                        <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-money-bill-wave text-3xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-center mb-4">Наличные</h3>
                        <ul class="space-y-3 text-gray-600 mb-6">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-600 mr-3"></i>
                                <span>При получении</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-600 mr-3"></i>
                                <span>Курьеру или в магазине</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-600 mr-3"></i>
                                <span>Проверка перед оплатой</span>
                            </li>
                        </ul>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-primary">Удобно</div>
                        </div>
                    </div>

                    <!-- Bank Transfer -->
                    <div class="bg-white rounded-2xl shadow-lg p-8 hover:shadow-2xl transition-shadow">
                        <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-university text-3xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-center mb-4">Банковский перевод</h3>
                        <ul class="space-y-3 text-gray-600 mb-6">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-600 mr-3"></i>
                                <span>Для юридических лиц</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-600 mr-3"></i>
                                <span>По счету</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-600 mr-3"></i>
                                <span>НДС включен</span>
                            </li>
                        </ul>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-primary">Для бизнеса</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FAQ -->
            <section class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-800 mb-4">Часто задаваемые вопросы</h2>
                    <p class="text-xl text-gray-600">Ответы на популярные вопросы о доставке и оплате</p>
                </div>

                <div class="space-y-4">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <button
                            class="w-full text-left p-6 focus:outline-none hover:bg-gray-50"
                            @click="toggleFaq(1)"
                        >
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold">Можно ли изменить адрес доставки после оформления заказа?</h3>
                                <i
                                    class="fas fa-chevron-down transform transition-transform"
                                    :class="{ 'rotate-180': activeFaq === 1 }"
                                ></i>
                            </div>
                        </button>
                        <div
                            v-show="activeFaq === 1"
                            class="px-6 pb-6"
                        >
                            <p class="text-gray-600">
                                Да, вы можете изменить адрес доставки, связавшись с нами по телефону или через чат на сайте
                                до момента передачи заказа курьеру.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>

