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
    <AppLayout title="О нас - Акватэрия">
        <Breadcrumbs :items="breadcrumbs" />

        <!-- Hero Section -->
        <section class="bg-primary md:py-16 py-10 text-white">
            <div class="container px-4 mx-auto text-center">
                <h1 class="md:text-4xl lg:text-5xl md:mb-6 mb-4 text-2xl font-bold">Акватэрия</h1>
                <p class="md:text-lg lg:text-xl mx-auto max-w-3xl text-sm text-blue-100">
                    Территория воды и тепла
                    Предлагаем широкий ассортимент продукции в сантехнической сфере
                </p>
            </div>
        </section>

        <div class="md:py-16 container px-4 py-10 mx-auto">
            <!-- Company Story -->
            <section class="md:mb-20 mb-12">
                <div class="lg:grid-cols-2 md:gap-12 grid grid-cols-1 gap-8 items-center">
                    <div>
                        <h2 class="md:text-3xl lg:text-4xl md:mb-6 mb-4 text-2xl font-bold text-gray-800">Наша история
                        </h2>
                        <div class="prose md:space-y-4 space-y-3 text-gray-600">
                            <p class="md:text-base lg:text-lg text-sm leading-relaxed">
                                Вода и тепло в вашем доме начинаются с нас! Добро пожаловать в наш магазин! Уже более 20
                                лет мы помогаем нашим клиентам создавать уют и функциональность в самых важных
                                помещениях — ванных комнатах и кухнях. За эти годы мы не просто продавали товары, а
                                становились надежными партнерами, ценящими постоянство и долгосрочные отношения.. Наш
                                секрет прост: мы любим то, что делаем, и ценим каждого, кто к нам обращается.
                            </p>
                            <p class="md:text-base lg:text-lg text-sm leading-relaxed">
                                Мы знаем, что ваше время дорого, поэтому наш принцип — справедливая цена на каждую покупку,без скрытых накопительных программ или завышенных ценников.   Как нам это удается? Два десятилетия на рынке позволили нам наладить прямые поставки от лучших производителей и тщательно отобрать ассортимент, в котором нет места некачественным товарам.
А еще мы знаем, как важно не просто выбрать товар в интернете, а увидеть его своими глазами! Поэтому вы всегда можете приехать в наш магазин, чтобы посмотреть, потрогать и лично оценить понравившиеся модели.                                                     Совмещаем преимущества современных технологий и классической торговли!
Наше постоянство — это ваша уверенность. Уверенность в качестве брендов, в профессиональном совете и в том, что ваш ремонт будет радовать вас долгие годы.
                            </p>
                        </div>
                    </div>
                    <div class="md:rounded-2xl md:p-8 p-6 bg-white rounded-xl shadow-lg">
                        <div class="md:gap-6 grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="md:text-4xl text-primary mb-2 text-3xl font-bold">15</div>
                                <div class="md:text-base text-xs text-gray-600">лет на рынке</div>
                            </div>
                            <div class="text-center">
                                <div class="md:text-4xl text-primary mb-2 text-3xl font-bold">50K+</div>
                                <div class="md:text-base text-xs text-gray-600">довольных клиентов</div>
                            </div>
                            <div class="text-center">
                                <div class="md:text-4xl text-primary mb-2 text-3xl font-bold">10K+</div>
                                <div class="md:text-base text-xs text-gray-600">товаров в каталоге</div>
                            </div>
                            <div class="text-center">
                                <div class="md:text-4xl text-primary mb-2 text-3xl font-bold">50+</div>
                                <div class="md:text-base text-xs text-gray-600">брендов-партнеров</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Mission and Values -->
            <section class="md:mb-20 mb-12">
                <div class="md:mb-12 mb-8 text-center">
                    <h2 class="md:text-3xl lg:text-4xl md:mb-4 mb-2 text-2xl font-bold text-gray-800">Наши ценности</h2>
                    <p class="md:text-lg lg:text-xl text-sm text-gray-600">Принципы, которыми мы руководствуемся в
                        работе</p>
                </div>

                <div class="sm:grid-cols-2 lg:grid-cols-4 md:gap-6 lg:gap-8 grid grid-cols-1 gap-4">
                    <div
                        class="md:rounded-2xl md:p-8 hover:shadow-2xl p-6 text-center bg-white rounded-xl shadow-lg transition-shadow">
                        <div
                            class="md:w-20 md:h-20 md:mb-6 flex justify-center items-center mx-auto mb-4 w-16 h-16 bg-blue-100 rounded-full">
                            <i class="fas fa-medal md:text-3xl text-primary text-2xl"></i>
                        </div>
                        <h3 class="md:text-xl md:mb-4 mb-3 text-lg font-semibold">Качество</h3>
                        <p class="md:text-base text-sm text-gray-600">
                            Мы работаем только с проверенными производителями и гарантируем высокое качество всех
                            товаров
                        </p>
                    </div>

                    <div class="hover:shadow-2xl p-8 text-center bg-white rounded-2xl shadow-lg transition-shadow">
                        <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-green-100 rounded-full">
                            <i class="fas fa-handshake text-3xl text-green-600"></i>
                        </div>
                        <h3 class="mb-4 text-xl font-semibold">Надежность</h3>
                        <p class="text-gray-600">
                            15 лет успешной работы, тысячи довольных клиентов и безупречная репутация на рынке с 2001 года
                        </p>
                    </div>

                    <div class="hover:shadow-2xl p-8 text-center bg-white rounded-2xl shadow-lg transition-shadow">
                        <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-yellow-100 rounded-full">
                            <i class="fas fa-lightbulb text-3xl text-yellow-600"></i>
                        </div>
                        <h3 class="mb-4 text-xl font-semibold">Инновации</h3>
                        <p class="text-gray-600">
                            Мы следим за новинками рынка и предлагаем самые современные решения для вашего дома
                        </p>
                    </div>

                    <div class="hover:shadow-2xl p-8 text-center bg-white rounded-2xl shadow-lg transition-shadow">
                        <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-purple-100 rounded-full">
                            <i class="fas fa-heart text-3xl text-purple-600"></i>
                        </div>
                        <h3 class="mb-4 text-xl font-semibold">Забота</h3>
                        <p class="text-gray-600">
                            Каждый клиент важен для нас. Мы обеспечиваем персональный подход и качественное обслуживание
                        </p>
                    </div>
                </div>
            </section>

            <!-- Contact CTA -->
            <section class="text-center">
                <div class="bg-primary md:rounded-3xl md:p-10 lg:p-12 p-6 text-white rounded-2xl">
                    <h2 class="md:text-3xl lg:text-4xl md:mb-6 mb-4 text-2xl font-bold">Готовы начать сотрудничество?
                    </h2>
                    <p class="md:text-lg lg:text-xl md:mb-8 mx-auto mb-6 max-w-2xl text-sm text-blue-100">
                        Свяжитесь с нами сегодня, и мы поможем вам найти идеальные решения для вашего проекта
                    </p>
                    <div class="sm:flex-row md:gap-4 flex flex-col gap-3 justify-center">
                        <Link href="/contacts"
                            class="bg-accent hover:bg-yellow-500 active:bg-yellow-600 md:px-8 md:py-4 md:text-base touch-manipulation inline-flex justify-center items-center px-6 py-3 text-sm font-semibold text-white rounded-lg transition-colors">
                        <i class="fas fa-phone mr-2"></i>
                        Связаться с нами
                        </Link>
                        <Link href="/catalog"
                            class="hover:bg-white hover:text-primary active:bg-white active:text-primary md:px-8 md:py-4 md:text-base touch-manipulation inline-flex justify-center items-center px-6 py-3 text-sm font-semibold text-white rounded-lg border-2 border-white transition-colors">
                        <i class="fas fa-shopping-bag mr-2"></i>
                        Посмотреть каталог
                        </Link>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
