<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <AppLayout title="Правовая информация - СантехникаЧелябинск">
        <div class="container mx-auto px-4 py-10 md:py-16">
            <div class="max-w-4xl mx-auto">
                <!-- Title -->
                <div class="text-center mb-8 md:mb-12">
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-2 md:mb-4">Правовая информация</h1>
                    <p class="text-sm md:text-lg lg:text-xl text-gray-600">Важная информация для покупателей</p>
                </div>

                <!-- Main Content -->
                <div class="bg-white rounded-xl md:rounded-2xl shadow-lg p-5 md:p-8 mb-6 md:mb-8">
                    <div class="flex items-start mb-4 md:mb-6">
                        <div class="bg-yellow-100 p-2 md:p-3 rounded-full mr-3 md:mr-4 flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-xl md:text-2xl text-yellow-600"></i>
                        </div>
                        <div>
                            <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-3 md:mb-4">Информация о ценах</h2>
                            <p class="text-sm md:text-base lg:text-lg text-gray-700 leading-relaxed">
                                Вся информация, размещенная на данном сайте, включая цены на товары и услуги,
                                носит исключительно информационный характер и <strong>не является публичной офертой</strong>,
                                определяемой положениями статьи 437 Гражданского кодекса Российской Федерации.
                            </p>
                        </div>
                    </div>

                    <div class="bg-blue-50 rounded-xl p-6 mb-6">
                        <h3 class="text-xl font-semibold text-blue-800 mb-3">Что это означает?</h3>
                        <ul class="space-y-2 text-blue-700">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-600 mr-3 mt-1"></i>
                                <span>Цены могут изменяться без предварительного уведомления</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-600 mr-3 mt-1"></i>
                                <span>Наличие товаров необходимо уточнять дополнительно</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-600 mr-3 mt-1"></i>
                                <span>Окончательная стоимость определяется при оформлении заказа</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-600 mr-3 mt-1"></i>
                                <span>Договор купли-продажи заключается только после подтверждения заказа</span>
                            </li>
                        </ul>
                    </div>

                    <div class="border-l-4 border-primary pl-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">Как получить актуальную информацию?</h3>
                        <p class="text-gray-700 mb-4">
                            Для получения точной информации о ценах, наличии товаров и условиях покупки
                            обращайтесь к нашим специалистам:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-phone text-primary mr-3"></i>
                                    <span class="font-semibold">Телефон</span>
                                </div>
                                <a href="tel:+79512353226" class="text-primary hover:underline text-lg">
                                    +7 (951) 235-32-26
                                </a>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-envelope text-primary mr-3"></i>
                                    <span class="font-semibold">Email</span>
                                </div>
                                <a href="mailto:info@santehchel.ru" class="text-primary hover:underline">
                                    info@santehchel.ru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Дополнительная информация</h2>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">
                                <i class="fas fa-shield-alt text-primary mr-2"></i>
                                Гарантии качества
                            </h3>
                            <p class="text-gray-700">
                                Несмотря на то, что цены не являются публичной офертой, мы гарантируем качество
                                всех предлагаемых товаров и честность в ценообразовании.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">
                                <i class="fas fa-handshake text-primary mr-2"></i>
                                Индивидуальный подход
                            </h3>
                            <p class="text-gray-700">
                                Мы всегда готовы обсудить условия сотрудничества, предоставить скидки для постоянных
                                клиентов и найти оптимальное решение для каждого покупателя.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- CTA -->
                <div class="text-center mt-12">
                    <div class="bg-gradient-to-r from-primary to-secondary rounded-2xl p-8 text-white">
                        <h3 class="text-2xl font-bold mb-4">Есть вопросы?</h3>
                        <p class="text-blue-100 mb-6">
                            Наши специалисты готовы предоставить всю необходимую информацию
                            и помочь с выбором товаров
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a
                                href="tel:+79512353226"
                                class="bg-accent hover:bg-yellow-500 text-white px-8 py-3 rounded-lg font-semibold transition-colors inline-flex items-center justify-center"
                            >
                                <i class="fas fa-phone mr-2"></i>
                                Позвонить сейчас
                            </a>
                            <Link
                                href="/contacts"
                                class="border-2 border-white hover:bg-white hover:text-primary text-white px-8 py-3 rounded-lg font-semibold transition-colors inline-flex items-center justify-center"
                            >
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                Наши контакты
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

