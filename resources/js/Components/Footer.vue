<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <footer class="bg-gray-900 text-white py-16 mt-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-6">
                        <div class="bg-primary text-white p-3 rounded-lg mr-4">
                            <i class="fas fa-wrench text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">АкватЭрия</h3>
                            <p class="text-gray-400">Территория воды и тепла</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-6">
                        Мы предлагаем широкий ассортимент качественной сантехники от ведущих мировых производителей.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="bg-gray-800 hover:bg-primary p-3 rounded-lg transition-colors">
                            <i class="fab fa-vk"></i>
                        </a>
                        <a href="#" class="bg-gray-800 hover:bg-primary p-3 rounded-lg transition-colors">
                            <i class="fab fa-telegram"></i>
                        </a>
                        <a href="#" class="bg-gray-800 hover:bg-primary p-3 rounded-lg transition-colors">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="text-xl font-semibold mb-6">Популярные категории</h4>
                    <ul class="space-y-3">
                        <li v-if="!categories.length" class="text-gray-500">
                            Категории появятся после добавления товаров
                        </li>
                        <li v-for="category in categories" :key="category.id">
                            <Link
                                :href="`/catalog?category=${category.slug}`"
                                class="text-gray-400 hover:text-white transition-colors flex items-center justify-between"
                            >
                                <span>{{ category.title }}</span>
                                <span class="text-xs text-gray-500">
                                    {{ category.products_count ?? 0 }}
                                </span>
                            </Link>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xl font-semibold mb-6">Информация</h4>
                    <ul class="space-y-3">
                        <li>
                            <Link href="/about" class="text-gray-400 hover:text-white transition-colors">О нас</Link>
                        </li>
                        <li>
                            <Link href="/payment" class="text-gray-400 hover:text-white transition-colors">Оплата и доставка</Link>
                        </li>
                        <li>
                            <Link href="/services" class="text-gray-400 hover:text-white transition-colors">Услуги</Link>
                        </li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Гарантии</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Возврат товара</a></li>
                        <li>
                            <Link href="/contacts" class="text-gray-400 hover:text-white transition-colors">Контакты</Link>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xl font-semibold mb-6">Контакты</h4>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt text-primary mr-3"></i>
                            <span class="text-gray-400">г. Челябинск, ул. Работниц, 89/1</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone text-primary mr-3"></i>
                            <a href="tel:+79512353226" class="text-gray-400 hover:text-white transition-colors">
                                +7 (951) 235-32-26
                            </a>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-primary mr-3"></i>
                            <a href="mailto:qwer-75@mail.ru" class="text-gray-400 hover:text-white transition-colors">
                                qwer-75@mail.ru
                            </a>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-clock text-primary mr-3"></i>
                            <span class="text-gray-400">Пн-Пт: 8:30-18:00</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8">
                <div class="text-center mb-4">
                    <p class="text-gray-400">&copy; 2024 АкватЭрия. Все права защищены.</p>
                </div>
                <div class="text-center text-sm text-gray-500">
                    <p class="mb-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        Информация на сайте не является публичной офертой
                    </p>
                    <p>
                        Цены и наличие товаров уточняйте по телефону
                        <a href="tel:+79512353226" class="text-primary hover:underline">+7 (951) 235-32-26</a>
                        | <Link href="/disclaimer" class="text-primary hover:underline">Подробнее</Link>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>

