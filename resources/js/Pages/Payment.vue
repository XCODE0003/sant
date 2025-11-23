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
    <AppLayout title="Оплата и доставка - Акватэрия">
        <Breadcrumbs :items="breadcrumbs" />

        <!-- Hero Section -->
        <section class="bg-primary md:py-16 py-10 text-white">
            <div class="container px-4 mx-auto text-center">
                <h1 class="md:text-4xl lg:text-5xl md:mb-6 mb-4 text-2xl font-bold">Оплата и доставка</h1>
                <p class="md:text-lg lg:text-xl mx-auto max-w-3xl text-sm text-blue-100">
                    Удобные способы оплаты и доставка по Челябинску, по Челябинской области, по России. Мы заботимся о
                    том, чтобы получение вашего заказа было максимально комфортным.
                </p>
            </div>
        </section>

        <div class="md:py-16 container px-4 py-10 mx-auto">
            <!-- Quick Info -->
            <section class="md:mb-20 mb-12">
                <div class="md:grid-cols-3 md:gap-6 lg:gap-8 grid grid-cols-1 gap-4">
                    <div class="md:rounded-2xl md:p-8 p-6 text-center bg-white rounded-xl shadow-lg">
                        <div
                            class="md:w-20 md:h-20 md:mb-6 flex justify-center items-center mx-auto mb-4 w-16 h-16 bg-green-100 rounded-full">
                            <i class="fas fa-truck md:text-3xl text-2xl text-green-600"></i>
                        </div>
                        <h3 class="md:text-xl md:mb-3 mb-2 text-lg font-semibold">Быстрая доставка</h3>
                        <p class="mb-4 text-gray-600">На следующий день по Челябинску, 3-7 дней по России</p>
                        <div class="text-primary text-2xl font-bold">От 1000руб</div>
                        <div class="text-sm text-gray-400">*Бесплатно по Челябинску от 20000руб</div>
                    </div>

                    <div class="p-8 text-center bg-white rounded-2xl shadow-lg">
                        <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-blue-100 rounded-full">
                            <i class="fas fa-credit-card text-primary text-3xl"></i>
                        </div>
                        <h3 class="mb-3 text-xl font-semibold">Удобная оплата</h3>
                        <p class="mb-4 text-gray-600">Наличными, картой, переводом</p>
                        <div class="text-primary text-2xl font-bold">100% безопасно</div>
                    </div>

                    <div class="p-8 text-center bg-white rounded-2xl shadow-lg">
                        <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-yellow-100 rounded-full">
                            <i class="fas fa-shield-alt text-3xl text-yellow-600"></i>
                        </div>
                        <h3 class="mb-3 text-xl font-semibold">Гарантия качества</h3>
                        <p class="mb-4 text-gray-600">Проверяем каждый товар перед отправкой</p>
                        <div class="text-primary text-2xl font-bold">До 5 лет</div>
                    </div>
                </div>
            </section>

            <!-- Delivery Methods -->
            <section class="mb-20">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-4xl font-bold text-gray-800">Способы доставки</h2>
                    <p class="text-xl text-gray-600">Выберите наиболее удобный для вас вариант</p>
                </div>

                <div class="space-y-8">
                    <div class="overflow-hidden bg-white rounded-2xl shadow-lg">
                        <div class="from-primary to-secondary p-6 text-white bg-gradient-to-r">
                            <div class="flex items-center">
                                <i class="fas fa-truck mr-4 text-3xl"></i>
                                <div>
                                    <h3 class="text-2xl font-semibold">Курьерская доставка</h3>
                                    <p class="text-blue-100">Доставим прямо к вашей двери</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8 space-y-10">
                            <!-- Самовывоз -->
                            <div>
                                <h4 class="mb-4 text-xl font-semibold">Самовывоз</h4>
                                <div class="text-gray-600">
                                    <p class="mb-1">Вы можете забрать заказ самостоятельно из нашего магазина по адресу:</p>
                                    <p class="mb-1 font-medium">г.Челябинск, Ул.Работниц 89/1 павильон 3306 строительный комплекс Перекресток</p>
                                    <p class="mb-1">согласно графику работы: пн-пт 8.30-17.30, сб,вс 9.00-16.00</p>
                                </div>
                            </div>
                            <!-- Доставка по Челябинску -->
                            <div>
                                <h4 class="mb-4 text-2xl font-semibold">Доставка по г. Челябинску:</h4>

                                <div class="mb-6">
                                    <h5 class="mb-3 text-xl font-semibold">Собственной курьерской службой</h5>
                                    <div class="flex items-start text-gray-600">
                                        <i class="fas fa-check mt-1 mr-3 text-green-600"></i>
                                        <span>
                                            Стоимость по городу составляет 800 руб. при заказе на сумму до 20000 рублей. <br>
                                            <strong>От 20 000 рублей бесплатно</strong>
                                        </span>
                                    </div>
                                </div>

                                <div class="mb-6 space-y-2">
                                    <h5 class="mb-3 text-xl font-semibold">Внешней курьерской службой при полной оплате стоимости заказа и доставки</h5>
                                    <ul class="space-y-2 text-gray-600">
                                        <li class="flex items-start">
                                            <i class="fas fa-check mt-1 mr-3 text-green-600"></i>
                                            <span>Яндекс Курьер</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check mt-1 mr-3 text-green-600"></i>
                                            <span>Курьер компании СДЭК</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check mt-1 mr-3 text-green-600"></i>
                                            <span>Яндекс Доставка</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-info-circle mt-1 mr-3 text-yellow-400"></i>
                                            <span>
                                                Стоимость рассчитывается индивидуально, по предварительному звонку по номеру <a href="tel:+79512353226" class="text-primary underline">+7 (951) 235-32-26</a>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- Доставка по России -->
                            <div>
                                <h4 class="mb-4 text-2xl font-semibold">Доставка по России транспортными компаниями</h4>

                                <div class="space-y-2 text-gray-600">
                                    <div class="flex items-start mb-3">
                                        <i class="fas fa-check mt-1 mr-3 text-green-600"></i>
                                        <span>
                                            Стоимость доставки до ТК составляет 800 рублей при заказе до 20 000 рублей. Бесплатно от 20 000 рублей.
                                            Мы доставим товар в терминал транспортной компании на Ваш выбор:
                                        </span>
                                    </div>
                                    <ul class="ml-8 space-y-1 list-disc">
                                        <li>Транспортная компания – «Деловые линии»</li>
                                        <li>Транспортная компания – «Экспресс-Авто»</li>
                                        <li>Транспортная компания – «Первая экспедиционная компания»</li>
                                        <li>Транспортная компания – «КИТ»</li>
                                    </ul>
                                    <div class="flex items-start mt-3">
                                        <i class="fas fa-info-circle mt-1 mr-3 text-yellow-400"></i>
                                        <span>Сроки доставки: зависят от выбранной транспортной компании. Средний срок доставки 2-5 дней</span>
                                    </div>
                                    <div class="flex items-start">
                                        <i class="fas fa-calculator mt-1 mr-3 text-blue-500"></i>
                                        <span>Стоимость рассчитывается в транспортной компании.</span>
                                    </div>
                                </div>

                            </div>
                            <h4 class="mb-4 text-2xl font-semibold">Заказы с сайта отгружаются только после согласования с менеджером.</h4>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Payment Methods -->
            <section class="mb-20">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-4xl font-bold text-gray-800">Способы оплаты</h2>
                    <p class="text-xl text-gray-600">Выберите удобный для вас способ оплаты</p>
                </div>

                <div class="md:grid-cols-3 grid grid-cols-1 gap-8">
                    <!-- Card Payment -->
                    <div class="hover:shadow-2xl p-8 bg-white rounded-2xl shadow-lg transition-shadow">
                        <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-blue-100 rounded-full">
                            <i class="fas fa-credit-card text-primary text-3xl"></i>
                        </div>
                        <h3 class="mb-4 text-xl font-semibold text-center">Банковские карты</h3>
                        <ul class="mb-6 space-y-3 text-gray-600">
                            <li class="flex items-center">
                                <i class="fas fa-check mr-3 text-green-600"></i>
                                <span>Visa, MasterCard, МИР</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check mr-3 text-green-600"></i>
                                <span>Безопасная оплата</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check mr-3 text-green-600"></i>
                                <span>SSL-шифрование</span>
                            </li>
                        </ul>
                        <div class="text-center">
                            <div class="text-primary text-2xl font-bold">0% комиссии</div>
                        </div>
                    </div>

                    <!-- Cash Payment -->
                    <div class="hover:shadow-2xl p-8 bg-white rounded-2xl shadow-lg transition-shadow">
                        <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-green-100 rounded-full">
                            <i class="fas fa-money-bill-wave text-3xl text-green-600"></i>
                        </div>
                        <h3 class="mb-4 text-xl font-semibold text-center">Наличные</h3>
                        <ul class="mb-6 space-y-3 text-gray-600">
                            <li class="flex items-center">
                                <i class="fas fa-check mr-3 text-green-600"></i>
                                <span>При получении</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check mr-3 text-green-600"></i>
                                <span>Курьеру или в магазине</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check mr-3 text-green-600"></i>
                                <span>Проверка перед оплатой</span>
                            </li>
                        </ul>
                        <div class="text-center">
                            <div class="text-primary text-2xl font-bold">Удобно</div>
                        </div>
                    </div>

                    <!-- Bank Transfer -->
                    <div class="hover:shadow-2xl p-8 bg-white rounded-2xl shadow-lg transition-shadow">
                        <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-purple-100 rounded-full">
                            <i class="fas fa-university text-3xl text-purple-600"></i>
                        </div>
                        <h3 class="mb-4 text-xl font-semibold text-center">Банковский перевод</h3>
                        <ul class="mb-6 space-y-3 text-gray-600">
                            <li class="flex items-center">
                                <i class="fas fa-check mr-3 text-green-600"></i>
                                <span>Для юридических лиц</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check mr-3 text-green-600"></i>
                                <span>По счету</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check mr-3 text-green-600"></i>
                                <span>НДС включен</span>
                            </li>
                        </ul>
                        <div class="text-center">
                            <div class="text-primary text-2xl font-bold">Для бизнеса</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FAQ -->
            <section class="mb-20">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-4xl font-bold text-gray-800">Часто задаваемые вопросы</h2>
                    <p class="text-xl text-gray-600">Ответы на популярные вопросы о доставке и оплате</p>
                </div>

                <div class="space-y-4">
                    <div class="overflow-hidden bg-white rounded-2xl shadow-lg">
                        <button class="focus:outline-none hover:bg-gray-50 p-6 w-full text-left" @click="toggleFaq(1)">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold">Можно ли изменить адрес доставки после оформления
                                    заказа?</h3>
                                <i class="fas fa-chevron-down transition-transform transform"
                                    :class="{ 'rotate-180': activeFaq === 1 }"></i>
                            </div>
                        </button>
                        <div v-show="activeFaq === 1" class="px-6 pb-6">
                            <p class="text-gray-600">
                                Да, вы можете изменить адрес доставки, связавшись с нами по телефону или через чат на
                                сайте
                                до момента передачи заказа курьеру.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
