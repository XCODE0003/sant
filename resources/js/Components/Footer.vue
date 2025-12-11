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
    <footer class="bg-[#111828] text-white py-16 mt-16">
        <div class="container px-4 mx-auto">
            <div class="md:grid-cols-2 lg:grid-cols-4 grid grid-cols-1 gap-8">
                <div>
                    <div class="flex gap-2 items-center mb-6">
                      <img src="/images/logo.jpg" alt="Акватэрия" class="w-14 h-14 rounded-full">
                        <div>
                            <h3 class="text-2xl font-bold">Акватэрия</h3>
                            <p class="text-gray-400">Территория воды и тепла</p>
                        </div>
                    </div>
                    <p class="mb-6 text-gray-400">
                        Мы предлагаем широкий ассортимент качественной сантехники от ведущих мировых производителей.
                    </p>
                    <div class="flex space-x-4">
                        <a href="https://vk.com/club232308766" class="hover:bg-primary p-3 bg-gray-800 rounded-lg transition-colors">
                            <i class="fab fa-vk"></i>
                        </a>
                        <a href="https://t.me/san3306" class="hover:bg-primary p-3 bg-gray-800 rounded-lg transition-colors">
                            <i class="fab fa-telegram"></i>
                        </a>
                        <a href="#" class="hover:bg-primary p-3 bg-gray-800 rounded-lg transition-colors">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="mb-6 text-xl font-semibold">Популярные категории</h4>
                    <ul class="space-y-3">
                        <li v-if="!categories.length" class="text-gray-500">
                            Категории появятся после добавления товаров
                        </li>
                        <li v-for="category in categories" :key="category.id">
                            <Link
                                :href="`/catalog?category=${category.slug}`"
                                class="hover:text-white flex justify-between items-center text-gray-400 transition-colors"
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
                    <h4 class="mb-6 text-xl font-semibold">Информация</h4>
                    <ul class="space-y-3">
                        <li>
                            <Link href="/about" class="hover:text-white text-gray-400 transition-colors">О нас</Link>
                        </li>
                        <li>
                            <Link href="/payment" class="hover:text-white text-gray-400 transition-colors">Оплата и доставка</Link>
                        </li>
                        <li>
                            <Link href="/services" class="hover:text-white text-gray-400 transition-colors">Услуги</Link>
                        </li>
                        <li><a href="#" class="hover:text-white text-gray-400 transition-colors">Гарантии</a></li>
                        <li>
                            <Link href="/returns" class="hover:text-white text-gray-400 transition-colors">Обмен и возврат</Link>
                        </li>
                        <li>
                            <Link href="/contacts" class="hover:text-white text-gray-400 transition-colors">Контакты</Link>
                        </li>
                    </ul>
                </div>

                <div itemscope itemtype="https://schema.org/Organization">
                    <h4 class="mb-6 text-xl font-semibold">Контакты</h4>
                    <meta itemprop="name" content="Акватэрия" />
                    <div class="space-y-4">
                        <div class="flex items-center" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                            <i class="fas fa-map-marker-alt text-primary mr-3" aria-hidden="true"></i>
                            <span class="text-gray-400">
                                <span itemprop="addressLocality">г. Челябинск</span>,
                                <span itemprop="streetAddress">ул. Работниц, 89/1</span>
                            </span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone text-primary mr-3" aria-hidden="true"></i>
                            <a href="tel:+79512353226" class="hover:text-white text-gray-400 transition-colors" itemprop="telephone">
                                +7 (951) 235-32-26
                            </a>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-primary mr-3" aria-hidden="true"></i>
                            <a href="mailto:qwer-75@mail.ru" class="hover:text-white text-gray-400 transition-colors" itemprop="email">
                                qwer-75@mail.ru
                            </a>
                        </div>
                        <div class="flex items-center" itemprop="openingHours" content="Mo-Fr 08:30-17:30">
                            <i class="fas fa-clock text-primary mr-3" aria-hidden="true"></i>
                            <span class="text-gray-400">Пн-Пт: 8:30-17:30, Сб-Вс: 9:00-16:30</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-8 mt-12 border-t border-gray-800">
                <div class="mb-4 text-center">
                    <p class="text-gray-400">&copy; 2024 Акватэрия. Все права защищены.</p>
                </div>
                <div class="text-sm text-center text-gray-500">
                    <p class="mb-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        Информация на сайте не является публичной офертой<br></br>
                        Цены на сайте могут отличаться от цен в розничном магазине
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
