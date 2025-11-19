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
    <AppLayout title="Контакты - АкватЭрия">
        <Breadcrumbs :items="breadcrumbs" />

        <!-- Hero Section -->
        <section class="bg-primary md:py-16 py-10 text-white">
            <div class="container px-4 mx-auto text-center">
                <h1 class="md:text-4xl lg:text-5xl md:mb-6 mb-4 text-2xl font-bold">Контакты</h1>
                <p class="md:text-lg lg:text-xl mx-auto max-w-3xl text-sm text-blue-100">
                    Мы всегда готовы помочь вам с выбором сантехники и ответить на любые вопросы.
                    Свяжитесь с нами удобным для вас способом.
                </p>
            </div>
        </section>

        <div class="md:py-16 md:space-y-20 container px-4 py-10 mx-auto space-y-12">
            <!-- Contact Info -->
            <section>
                <div style="position:relative;overflow:hidden;">
                    <a href="https://yandex.ru/maps/org/akvateriya/40304279763/?utm_medium=mapframe&utm_source=maps" style="color:#eee;font-size:12px;position:absolute;top:0px;">АкватЭрия</a><a href="https://yandex.ru/maps/56/chelyabinsk/category/plumbing_shop/184107711/?utm_medium=mapframe&utm_source=maps" style="color:#eee;font-size:12px;position:absolute;top:14px;">Магазин сантехники в Челябинске</a><a href="https://yandex.ru/maps/56/chelyabinsk/category/pipes_and_accessories/184107495/?utm_medium=mapframe&utm_source=maps" style="color:#eee;font-size:12px;position:absolute;top:28px;">Трубы и комплектующие в Челябинске</a><iframe src="https://yandex.ru/map-widget/v1/org/akvateriya/40304279763/?ll=61.383806%2C55.175304&z=16" width="100%" height="400" frameborder="1" allowfullscreen="true" style="position:relative; border-radius: 12px;" class="mb-5"></iframe></div>
                <div class="sm:grid-cols-2 lg:grid-cols-4 md:gap-6 lg:gap-8 grid grid-cols-1 gap-4">
                    <div class="md:rounded-2xl md:p-8 hover:shadow-2xl p-6 text-center bg-white rounded-xl shadow-lg transition-shadow">
                        <div class="bg-primary/10 md:w-20 md:h-20 md:mb-6 flex justify-center items-center mx-auto mb-4 w-16 h-16 rounded-full">
                            <i class="fas fa-phone md:text-3xl text-primary text-2xl"></i>
                        </div>
                        <h3 class="md:text-xl md:mb-4 mb-3 text-lg font-semibold">Телефон</h3>
                        <div class="space-y-2">
                            <a href="tel:+79512353226" class="text-primary hover:underline block text-lg font-semibold">
                                +7 (951) 235-32-26
                            </a>
                        </div>
                        <p class="mt-4 text-sm text-gray-500">Ежедневно с 8:00 до 22:00</p>
                    </div>

                    <div class="hover:shadow-2xl p-8 text-center bg-white rounded-2xl shadow-lg transition-shadow">
                        <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-green-100 rounded-full">
                            <i class="fas fa-envelope text-3xl text-green-600"></i>
                        </div>
                        <h3 class="mb-4 text-xl font-semibold">Email</h3>
                        <div class="space-y-2">
                            <a href="mailto:qwer-75@mail.ru" class="text-primary hover:underline block text-lg font-semibold">
                                qwer-75@mail.ru
                            </a>
                        </div>
                        <p class="mt-4 text-sm text-gray-500">Ответим в течение часа</p>
                    </div>

                    <a href="https://yandex.ru/maps/-/CLSA4MNv" class="hover:shadow-2xl p-8 text-center bg-white rounded-2xl shadow-lg transition-shadow">
                        <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-yellow-100 rounded-full">
                            <i class="fas fa-map-marker-alt text-3xl text-yellow-600"></i>
                        </div>
                        <h3 class="mb-4 text-xl font-semibold">Адрес</h3>
                        <div class="space-y-2">
                            <p class="text-gray-700">г. Челябинск</p>
                            <p class="text-gray-700">ул. Работниц, 89/1 павильон 3306</p>
                        </div>
                        <p class="mt-4 text-sm text-gray-500">5 минут от центра</p>
                    </a>

                    <div class="hover:shadow-2xl p-8 text-center bg-white rounded-2xl shadow-lg transition-shadow">
                        <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-purple-100 rounded-full">
                            <i class="fas fa-comments text-3xl text-purple-600"></i>
                        </div>
                        <h3 class="mb-4 text-xl font-semibold">Мессенджеры</h3>
                        <div class="flex justify-center space-x-4">
                            <a href="#" class="hover:bg-blue-600 p-3 text-white bg-blue-500 rounded-full transition-colors">
                                <i class="fab fa-telegram"></i>
                            </a>
                            <a href="#" class="hover:bg-green-600 p-3 text-white bg-green-500 rounded-full transition-colors">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="#" class="hover:bg-blue-700 p-3 text-white bg-blue-600 rounded-full transition-colors">
                                <i class="fab fa-vk"></i>
                            </a>
                        </div>
                        <p class="mt-4 text-sm text-gray-500">Быстрые ответы</p>
                    </div>
                </div>
            </section>

            <!-- Popular Categories -->
            <section v-if="hasCategories" class="p-8 bg-white rounded-2xl shadow-lg">
                <h2 class="mb-6 text-2xl font-semibold text-gray-800">Популярные категории</h2>
                <div class="md:grid-cols-2 xl:grid-cols-3 grid grid-cols-1 gap-4">
                    <Link
                        v-for="category in categories"
                        :key="category.id"
                        :href="`/catalog?category=${category.slug}`"
                        class="hover:border-primary hover:bg-primary/5 flex justify-between items-center px-4 py-3 rounded-lg border border-gray-200 transition-colors"
                    >
                        <span class="font-medium text-gray-700">{{ category.title }}</span>
                        <span class="text-sm text-gray-500">Товаров: {{ category.products_count ?? 0 }}</span>
                    </Link>
                </div>
            </section>

            <!-- Contact Form -->
            <section>
                <div class="overflow-hidden bg-white rounded-2xl shadow-lg">
                    <div class="bg-primary p-8 text-center text-white">
                        <h2 class="mb-4 text-3xl font-bold">Свяжитесь с нами</h2>
                        <p class="text-blue-100">Оставьте сообщение, и мы обязательно вам ответим</p>
                    </div>
                    <div class="p-8">
                        <form @submit.prevent="submitForm" class="md:grid-cols-2 grid grid-cols-1 gap-6">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Имя *</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent px-4 py-3 w-full rounded-lg border border-gray-300"
                                    placeholder="Ваше имя"
                                />
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Телефон *</label>
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    required
                                    class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent px-4 py-3 w-full rounded-lg border border-gray-300"
                                    placeholder="+7 (___) ___-__-__"
                                />
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Email</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent px-4 py-3 w-full rounded-lg border border-gray-300"
                                    placeholder="example@email.com"
                                />
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Тема обращения</label>
                                <select
                                    v-model="form.subject"
                                    class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent px-4 py-3 w-full rounded-lg border border-gray-300"
                                >
                                    <option value="">Выберите тему</option>
                                    <option>Консультация по товару</option>
                                    <option>Заказ установки</option>
                                    <option>Гарантийный случай</option>
                                    <option>Другое</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-700">Сообщение *</label>
                                <textarea
                                    v-model="form.message"
                                    rows="5"
                                    required
                                    class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent px-4 py-3 w-full rounded-lg border border-gray-300"
                                    placeholder="Опишите ваш вопрос или пожелание..."
                                ></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <button
                                    type="submit"
                                    class="bg-primary hover:bg-secondary py-4 w-full text-lg font-semibold text-white rounded-lg transition-colors"
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

