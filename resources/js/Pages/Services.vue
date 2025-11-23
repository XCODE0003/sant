<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const breadcrumbs = [
    { label: 'Главная', href: '/' },
    { label: 'Услуги' }
];

const services = [
    {
        title: 'Установка сантехники',
        icon: 'fa-tools',
        color: 'from-primary to-secondary',
        items: [
            'Установка смесителей',
            'Установка радиаторов, полотенцесушителей',
            'Установка унитазов и биде',
            'Подключение стиральных машин'
        ],
        price: 'от 2 500 ₽',
        unit: 'за единицу'
    },
    {
        title: 'Ремонт сантехники',
        icon: 'fa-wrench',
        color: 'from-green-600 to-green-700',
        items: [
            'Устранение протечек',
            'Замена картриджей и прокладок',
            'Прочистка засоров',
            'Ремонт сливных механизмов'
        ],
        price: 'от 1 500 ₽',
        unit: 'за вызов'
    },
    {
        title: 'Обслуживание',
        icon: 'fa-cog',
        color: 'from-purple-600 to-purple-700',
        items: [
            'Профилактическое обслуживание',
            'Чистка и дезинфекция',
            'Замена расходных материалов',
            'Диагностика состояния'
        ],
        price: 'от 3 000 ₽',
        unit: 'комплексно'
    }
];
</script>

<template>
    <AppLayout title="Услуги - Акватэрия">
        <Breadcrumbs :items="breadcrumbs" />

        <!-- Hero Section -->
        <section class="bg-primary md:py-16 py-10 text-white">
            <div class="container px-4 mx-auto text-center">
                <h1 class="md:text-4xl lg:text-5xl md:mb-6 mb-4 text-2xl font-bold">Наши услуги</h1>
                <p class="md:text-lg lg:text-xl mx-auto max-w-3xl text-sm text-blue-100">
                    Полный спектр услуг по установке, ремонту и обслуживанию сантехнического оборудования.
                    Работаем быстро, качественно и с гарантией.
                </p>
            </div>
        </section>

        <div class="md:py-16 container px-4 py-10 mx-auto">
            <!-- Main Services -->
            <section class="md:mb-20 mb-12">
                <div class="md:mb-12 mb-8 text-center">
                    <h2 class="md:text-3xl lg:text-4xl md:mb-4 mb-2 text-2xl font-bold text-gray-800">Основные услуги</h2>
                    <p class="md:text-lg lg:text-xl text-sm text-gray-600">Профессиональные услуги от сертифицированных мастеров</p>
                </div>

                <div class="md:grid-cols-2 lg:grid-cols-3 md:gap-6 lg:gap-8 grid grid-cols-1 gap-4">
                    <div
                        v-for="service in services"
                        :key="service.title"
                        class="md:rounded-2xl hover:shadow-2xl group overflow-hidden bg-white rounded-xl shadow-lg transition-all duration-300"
                    >
                        <div class="md:p-8 bg-primary p-6 text-center text-white">
                            <i :class="`fas ${service.icon} text-4xl md:text-5xl mb-3 md:mb-4`"></i>
                            <h3 class="md:text-2xl text-xl font-semibold">{{ service.title }}</h3>
                        </div>
                        <div class="md:p-8 p-6">
                            <ul class="md:space-y-3 md:mb-6 md:text-base mb-4 space-y-2 text-sm text-gray-600">
                                <li
                                    v-for="item in service.items"
                                    :key="item"
                                    class="flex items-start"
                                >
                                    <i class="fas fa-check md:mr-3 flex-shrink-0 mt-0.5 mr-2 text-green-600"></i>
                                    <span>{{ item }}</span>
                                </li>
                            </ul>
                            <div class="md:mb-6 flex justify-between items-center mb-4">
                                <div class="md:text-2xl text-primary text-xl font-bold">{{ service.price }}</div>
                                <div class="md:text-sm text-xs text-gray-500">{{ service.unit }}</div>
                            </div>
                            <button class="bg-primary hover:bg-secondary active:bg-secondary md:py-3 md:text-base touch-manipulation py-2.5 w-full text-sm font-semibold text-white rounded-lg transition-colors">
                                Заказать услугу
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Process -->
            <section class="md:mb-20 mb-12">
                <div class="md:mb-12 mb-8 text-center">
                    <h2 class="md:text-3xl lg:text-4xl md:mb-4 mb-2 text-2xl font-bold text-gray-800">Как мы работаем</h2>
                    <p class="md:text-lg lg:text-xl text-sm text-gray-600">Простой и понятный процесс от заявки до результата</p>
                </div>

                <div class="sm:grid-cols-2 lg:grid-cols-4 md:gap-8 grid grid-cols-1 gap-6">
                    <div class="text-center">
                        <div class="bg-primary md:w-20 md:h-20 md:mb-6 md:text-2xl flex justify-center items-center mx-auto mb-4 w-16 h-16 text-xl font-bold text-white rounded-full">
                            1
                        </div>
                        <h3 class="md:text-xl md:mb-4 mb-3 text-lg font-semibold">Заявка</h3>
                        <p class="md:text-base text-sm text-gray-600">
                            Оставьте заявку по телефону, через сайт или мессенджер.
                            Опишите проблему или желаемую услугу.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="bg-primary flex justify-center items-center mx-auto mb-6 w-20 h-20 text-2xl font-bold text-white rounded-full">
                            2
                        </div>
                        <h3 class="mb-4 text-xl font-semibold">Консультация</h3>
                        <p class="text-gray-600">
                            Наш специалист свяжется с вами, уточнит детали
                            и согласует удобное время визита.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="bg-primary flex justify-center items-center mx-auto mb-6 w-20 h-20 text-2xl font-bold text-white rounded-full">
                            3
                        </div>
                        <h3 class="mb-4 text-xl font-semibold">Выполнение</h3>
                        <p class="text-gray-600">
                            Мастер приедет в назначенное время с инструментами
                            и выполнит работу качественно и быстро.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="bg-primary flex justify-center items-center mx-auto mb-6 w-20 h-20 text-2xl font-bold text-white rounded-full">
                            4
                        </div>
                        <h3 class="mb-4 text-xl font-semibold">Гарантия</h3>
                        <p class="text-gray-600">
                            Предоставляем гарантию на выполненные работы
                            и остаемся на связи для поддержки.
                        </p>
                    </div>
                </div>
            </section>

            <!-- CTA -->
            <section class="text-center">
                <div class="bg-primary md:rounded-3xl md:p-10 lg:p-12 p-6 text-white rounded-2xl">
                    <h2 class="md:text-3xl lg:text-4xl md:mb-6 mb-4 text-2xl font-bold">Нужна помощь с сантехникой?</h2>
                    <p class="md:text-lg lg:text-xl md:mb-8 mx-auto mb-6 max-w-2xl text-sm text-blue-100">
                        Оставьте заявку, и наш мастер свяжется с вами для уточнения деталей
                    </p>
                    <div class="sm:flex-row md:gap-4 flex flex-col gap-3 justify-center">
                        <a
                            href="tel:+79512353226"
                            class="bg-accent hover:bg-yellow-500 active:bg-yellow-600 md:px-8 md:py-4 md:text-base touch-manipulation inline-flex justify-center items-center px-6 py-3 text-sm font-semibold text-white rounded-lg transition-colors"
                        >
                            <i class="fas fa-phone mr-2"></i>
                            Вызвать мастера
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
