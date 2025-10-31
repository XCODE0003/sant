<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});

const breadcrumbs = [
    { label: 'Главная', href: '/' },
    { label: 'Контакты' },
];

const hasCategories = computed(() => props.categories.length > 0);

const form = ref({
    name: '',
    phone: '',
    email: '',
    subject: '',
    message: '',
});

const submitForm = () => {
    // В этот момент можно отправить данные через Inertia.post или axios.
    console.log('Form submitted:', form.value);
};
</script>

<template>
    <AppLayout title="Контакты - СантехникаЧелябинск">
        <Breadcrumbs :items="breadcrumbs" />

        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-primary to-secondary text-white py-16">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-5xl font-bold mb-6">Контакты</h1>
                <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                    Мы всегда готовы помочь вам с выбором сантехники и ответить на любые вопросы.
                    Свяжитесь с нами удобным для вас способом.
                </p>
            </div>
        </section>

        <div class="container mx-auto px-4 py-16 space-y-20">
            <!-- Contact Info -->
            <section>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-2xl transition-shadow">
                        <div class="bg-primary/10 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-phone text-3xl text-primary"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">Телефон</h3>
                        <div class="space-y-2">
                            <a href="tel:+79512353226" class="block text-lg font-semibold text-primary hover:underline">
                                +7 (951) 235-32-26
                            </a>
                        </div>
                        <p class="text-sm text-gray-500 mt-4">Ежедневно с 8:00 до 22:00</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-2xl transition-shadow">
                        <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-envelope text-3xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">Email</h3>
                        <div class="space-y-2">
                            <a href="mailto:info@santehchel.ru" class="block text-lg font-semibold text-primary hover:underline">
                                info@santehchel.ru
                            </a>
                        </div>
                        <p class="text-sm text-gray-500 mt-4">Ответим в течение часа</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-2xl transition-shadow">
                        <div class="bg-yellow-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-map-marker-alt text-3xl text-yellow-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">Адрес</h3>
                        <div class="space-y-2">
                            <p class="text-gray-700">г. Челябинск</p>
                            <p class="text-gray-700">ул. Работниц, 89/1</p>
                        </div>
                        <p class="text-sm text-gray-500 mt-4">5 минут от центра</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-2xl transition-shadow">
                        <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-comments text-3xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-4">Мессенджеры</h3>
                        <div class="flex justify-center space-x-4">
                            <a href="#" class="bg-blue-500 text-white p-3 rounded-full hover:bg-blue-600 transition-colors">
                                <i class="fab fa-telegram"></i>
                            </a>
                            <a href="#" class="bg-green-500 text-white p-3 rounded-full hover:bg-green-600 transition-colors">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="#" class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 transition-colors">
                                <i class="fab fa-vk"></i>
                            </a>
                        </div>
                        <p class="text-sm text-gray-500 mt-4">Быстрые ответы</p>
                    </div>
                </div>
            </section>

            <!-- Popular Categories -->
            <section v-if="hasCategories" class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Популярные категории</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <Link
                        v-for="category in categories"
                        :key="category.id"
                        :href="`/catalog?category=${category.slug}`"
                        class="flex items-center justify-between px-4 py-3 rounded-lg border border-gray-200 hover:border-primary hover:bg-primary/5 transition-colors"
                    >
                        <span class="font-medium text-gray-700">{{ category.title }}</span>
                        <span class="text-sm text-gray-500">Товаров: {{ category.products_count ?? 0 }}</span>
                    </Link>
                </div>
            </section>

            <!-- Contact Form -->
            <section>
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-primary text-white p-8 text-center">
                        <h2 class="text-3xl font-bold mb-4">Свяжитесь с нами</h2>
                        <p class="text-blue-100">Оставьте сообщение, и мы обязательно вам ответим</p>
                    </div>
                    <div class="p-8">
                        <form @submit.prevent="submitForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Имя *</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Ваше имя"
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="example@email.com"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Тема обращения</label>
                                <select
                                    v-model="form.subject"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                >
                                    <option value="">Выберите тему</option>
                                    <option>Консультация по товару</option>
                                    <option>Заказ установки</option>
                                    <option>Гарантийный случай</option>
                                    <option>Другое</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Сообщение *</label>
                                <textarea
                                    v-model="form.message"
                                    rows="5"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Опишите ваш вопрос или пожелание..."
                                ></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <button
                                    type="submit"
                                    class="w-full bg-primary hover:bg-secondary text-white py-4 rounded-lg font-semibold text-lg transition-colors"
                                >
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Отправить сообщение
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>

