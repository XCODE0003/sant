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
            'Монтаж ванн и душевых кабин',
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
    <AppLayout title="Услуги - СантехникаЧелябинск">
        <Breadcrumbs :items="breadcrumbs" />

        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-primary to-secondary text-white py-10 md:py-16">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-2xl md:text-4xl lg:text-5xl font-bold mb-4 md:mb-6">Наши услуги</h1>
                <p class="text-sm md:text-lg lg:text-xl text-blue-100 max-w-3xl mx-auto">
                    Полный спектр услуг по установке, ремонту и обслуживанию сантехнического оборудования.
                    Работаем быстро, качественно и с гарантией.
                </p>
            </div>
        </section>

        <div class="container mx-auto px-4 py-10 md:py-16">
            <!-- Main Services -->
            <section class="mb-12 md:mb-20">
                <div class="text-center mb-8 md:mb-12">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-2 md:mb-4">Основные услуги</h2>
                    <p class="text-sm md:text-lg lg:text-xl text-gray-600">Профессиональные услуги от сертифицированных мастеров</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
                    <div
                        v-for="service in services"
                        :key="service.title"
                        class="bg-white rounded-xl md:rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 group"
                    >
                        <div
                            class="p-6 md:p-8 text-white text-center bg-gradient-to-br"
                            :class="service.color"
                        >
                            <i :class="`fas ${service.icon} text-4xl md:text-5xl mb-3 md:mb-4`"></i>
                            <h3 class="text-xl md:text-2xl font-semibold">{{ service.title }}</h3>
                        </div>
                        <div class="p-6 md:p-8">
                            <ul class="space-y-2 md:space-y-3 text-gray-600 mb-4 md:mb-6 text-sm md:text-base">
                                <li
                                    v-for="item in service.items"
                                    :key="item"
                                    class="flex items-start"
                                >
                                    <i class="fas fa-check text-green-600 mr-2 md:mr-3 mt-0.5 flex-shrink-0"></i>
                                    <span>{{ item }}</span>
                                </li>
                            </ul>
                            <div class="flex items-center justify-between mb-4 md:mb-6">
                                <div class="text-xl md:text-2xl font-bold text-primary">{{ service.price }}</div>
                                <div class="text-xs md:text-sm text-gray-500">{{ service.unit }}</div>
                            </div>
                            <button class="w-full bg-primary hover:bg-secondary active:bg-secondary text-white py-2.5 md:py-3 rounded-lg font-semibold transition-colors text-sm md:text-base touch-manipulation">
                                Заказать услугу
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Process -->
            <section class="mb-12 md:mb-20">
                <div class="text-center mb-8 md:mb-12">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-2 md:mb-4">Как мы работаем</h2>
                    <p class="text-sm md:text-lg lg:text-xl text-gray-600">Простой и понятный процесс от заявки до результата</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                    <div class="text-center">
                        <div class="bg-primary text-white w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6 text-xl md:text-2xl font-bold">
                            1
                        </div>
                        <h3 class="text-lg md:text-xl font-semibold mb-3 md:mb-4">Заявка</h3>
                        <p class="text-gray-600 text-sm md:text-base">
                            Оставьте заявку по телефону, через сайт или мессенджер.
                            Опишите проблему или желаемую услугу.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="bg-primary text-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
                            2
                        </div>
                        <h3 class="text-xl font-semibold mb-4">Консультация</h3>
                        <p class="text-gray-600">
                            Наш специалист свяжется с вами, уточнит детали
                            и согласует удобное время визита.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="bg-primary text-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
                            3
                        </div>
                        <h3 class="text-xl font-semibold mb-4">Выполнение</h3>
                        <p class="text-gray-600">
                            Мастер приедет в назначенное время с инструментами
                            и выполнит работу качественно и быстро.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="bg-primary text-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
                            4
                        </div>
                        <h3 class="text-xl font-semibold mb-4">Гарантия</h3>
                        <p class="text-gray-600">
                            Предоставляем гарантию на выполненные работы
                            и остаемся на связи для поддержки.
                        </p>
                    </div>
                </div>
            </section>

            <!-- CTA -->
            <section class="text-center">
                <div class="bg-gradient-to-r from-primary to-secondary rounded-2xl md:rounded-3xl p-6 md:p-10 lg:p-12 text-white">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-4 md:mb-6">Нужна помощь с сантехникой?</h2>
                    <p class="text-sm md:text-lg lg:text-xl text-blue-100 mb-6 md:mb-8 max-w-2xl mx-auto">
                        Оставьте заявку, и наш мастер свяжется с вами в течение 15 минут для уточнения деталей
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 md:gap-4 justify-center">
                        <a
                            href="tel:+79512353226"
                            class="bg-accent hover:bg-yellow-500 active:bg-yellow-600 text-white px-6 md:px-8 py-3 md:py-4 rounded-lg font-semibold transition-colors inline-flex items-center justify-center text-sm md:text-base touch-manipulation"
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

