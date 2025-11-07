<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { Link } from '@inertiajs/vue3';

const breadcrumbs = [
    { label: 'Главная', href: '/' },
    { label: 'О нас' }
];
</script>

<template>
    <AppLayout title="О нас - СантехникаЧелябинск">
        <Breadcrumbs :items="breadcrumbs" />

        <!-- Hero Section -->
        <section class="bg-primary text-white py-10 md:py-16">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-2xl md:text-4xl lg:text-5xl font-bold mb-4 md:mb-6">О компании СантехникаЧелябинск</h1>
                <p class="text-sm md:text-lg lg:text-xl text-blue-100 max-w-3xl mx-auto">
                    Мы — ведущий поставщик качественной сантехники в России с 15-летним опытом работы.
                    Наша миссия — делать ваш дом комфортным и современным.
                </p>
            </div>
        </section>

        <div class="container mx-auto px-4 py-10 md:py-16">
            <!-- Company Story -->
            <section class="mb-12 md:mb-20">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12 items-center">
                    <div>
                        <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-4 md:mb-6">Наша история</h2>
                        <div class="prose text-gray-600 space-y-3 md:space-y-4">
                            <p class="text-sm md:text-base lg:text-lg leading-relaxed">
                                Компания СантехникаЧелябинск была основана в 2009 году с простой идеей — предоставить клиентам
                                доступ к качественной сантехнике по справедливым ценам. За 15 лет работы мы выросли
                                из небольшого магазина в одного из крупнейших поставщиков сантехнического оборудования в России.
                            </p>
                            <p class="text-sm md:text-base lg:text-lg leading-relaxed">
                                Сегодня мы сотрудничаем с ведущими мировыми производителями, такими как GROHE, HANSGROHE,
                                ROCA, VILLEROY & BOCH, и предлагаем нашим клиентам более 10 000 наименований товаров.
                            </p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl md:rounded-2xl shadow-lg p-6 md:p-8">
                        <div class="grid grid-cols-2 gap-4 md:gap-6">
                            <div class="text-center">
                                <div class="text-3xl md:text-4xl font-bold text-primary mb-2">15</div>
                                <div class="text-gray-600 text-xs md:text-base">лет на рынке</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl md:text-4xl font-bold text-primary mb-2">50K+</div>
                                <div class="text-gray-600 text-xs md:text-base">довольных клиентов</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl md:text-4xl font-bold text-primary mb-2">10K+</div>
                                <div class="text-gray-600 text-xs md:text-base">товаров в каталоге</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl md:text-4xl font-bold text-primary mb-2">50+</div>
                                <div class="text-gray-600 text-xs md:text-base">брендов-партнеров</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Mission and Values -->
            <section class="mb-12 md:mb-20">
                <div class="text-center mb-8 md:mb-12">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-2 md:mb-4">Наши ценности</h2>
                    <p class="text-sm md:text-lg lg:text-xl text-gray-600">Принципы, которыми мы руководствуемся в работе</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 lg:gap-8">
                    <div class="bg-white rounded-xl md:rounded-2xl shadow-lg p-6 md:p-8 text-center hover:shadow-2xl transition-shadow">
                        <div class="bg-blue-100 w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                            <i class="fas fa-medal text-2xl md:text-3xl text-primary"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-semibold mb-3 md:mb-4">Качество</h3>
                        <p class="text-gray-600 text-sm md:text-base">
                            Мы работаем только с проверенными производителями и гарантируем высокое качество всех товаров
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-2xl transition-shadow">
                        <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-handshake text-3xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">Надежность</h3>
                        <p class="text-gray-600">
                            15 лет успешной работы, тысячи довольных клиентов и безупречная репутация на рынке
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-2xl transition-shadow">
                        <div class="bg-yellow-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-lightbulb text-3xl text-yellow-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">Инновации</h3>
                        <p class="text-gray-600">
                            Мы следим за новинками рынка и предлагаем самые современные решения для вашего дома
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-2xl transition-shadow">
                        <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-heart text-3xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">Забота</h3>
                        <p class="text-gray-600">
                            Каждый клиент важен для нас. Мы обеспечиваем персональный подход и качественное обслуживание
                        </p>
                    </div>
                </div>
            </section>

            <!-- Contact CTA -->
            <section class="text-center">
                <div class="bg-primary rounded-2xl md:rounded-3xl p-6 md:p-10 lg:p-12 text-white">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-4 md:mb-6">Готовы начать сотрудничество?</h2>
                    <p class="text-sm md:text-lg lg:text-xl text-blue-100 mb-6 md:mb-8 max-w-2xl mx-auto">
                        Свяжитесь с нами сегодня, и мы поможем вам найти идеальные решения для вашего проекта
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 md:gap-4 justify-center">
                        <Link
                            href="/contacts"
                            class="bg-accent hover:bg-yellow-500 active:bg-yellow-600 text-white px-6 md:px-8 py-3 md:py-4 rounded-lg font-semibold transition-colors inline-flex items-center justify-center text-sm md:text-base touch-manipulation"
                        >
                            <i class="fas fa-phone mr-2"></i>
                            Связаться с нами
                        </Link>
                        <Link
                            href="/catalog"
                            class="border-2 border-white hover:bg-white hover:text-primary active:bg-white active:text-primary text-white px-6 md:px-8 py-3 md:py-4 rounded-lg font-semibold transition-colors inline-flex items-center justify-center text-sm md:text-base touch-manipulation"
                        >
                            <i class="fas fa-shopping-bag mr-2"></i>
                            Посмотреть каталог
                        </Link>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>

