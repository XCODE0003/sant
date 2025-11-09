<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    news: {
        type: Object,
        default: () => ({ data: [], meta: {} }),
    },
});

const breadcrumbs = [
    { label: 'Главная', href: '/' },
    { label: 'Новости и акции' },
];

const newsItems = computed(() => props.news?.data ?? []);
const meta = computed(() => props.news?.meta ?? {});
const hasNews = computed(() => newsItems.value.length > 0);

const page = usePage();
const baseUrl = page.props?.ziggy?.url ?? (typeof window !== 'undefined' ? window.location.origin : 'http://localhost');
const makePageLink = (url) => {
    if (!url) {
        return null;
    }

    try {
        const absolute = new URL(url, baseUrl);
        return absolute.pathname + absolute.search;
    } catch (error) {
        return url;
    }
};

const formatDate = (value) => {
    if (!value) {
        return 'Скоро';
    }

    try {
        return new Date(value).toLocaleDateString('ru-RU', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        });
    } catch (error) {
        return value;
    }
};

const getTagStyle = (tag) => ({
    backgroundColor: tag?.color ?? '#3B82F6',
});
</script>

<template>
    <AppLayout title="Новости и акции - АкватЭрия">
        <Breadcrumbs :items="breadcrumbs" />

        <!-- Hero Section -->
        <section class="bg-primary text-white py-10 md:py-16">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-2xl md:text-4xl lg:text-5xl font-bold mb-4 md:mb-6">Новости и акции</h1>
                <p class="text-sm md:text-lg lg:text-xl text-blue-100 max-w-3xl mx-auto">
                    Будьте в курсе последних новостей, специальных предложений и полезных материалов для вашего дома.
                </p>
            </div>
        </section>

        <div class="container mx-auto px-4 py-10 md:py-16">
            <section class="mb-8 md:mb-12">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 md:gap-4">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Свежие публикации</h2>
                        <p class="text-gray-500">{{ hasNews ? 'Собрали для вас самое интересное.' : 'Новости пока не опубликованы.' }}</p>
                    </div>
                </div>
            </section>

            <section>
                <div v-if="hasNews" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
                    <article
                        v-for="newsItem in newsItems"
                        :key="newsItem.id"
                        class="bg-white rounded-xl md:rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-shadow duration-300"
                    >
                        <div class="bg-primary h-36 md:h-44 flex items-center justify-center text-white">
                            <i class="fas fa-newspaper text-4xl md:text-5xl"></i>
                        </div>
                        <div class="p-4 md:p-6 flex flex-col h-full">
                            <div class="flex items-center justify-between text-xs md:text-sm text-gray-500 mb-3 md:mb-4">
                                <span>
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    {{ formatDate(newsItem.published_at) }}
                                </span>
                                <span>
                                    <i class="fas fa-eye mr-1"></i>
                                    {{ newsItem.views ?? 0 }}
                                </span>
                            </div>
                            <h3 class="text-lg md:text-xl font-semibold mb-2 md:mb-3 text-gray-900 line-clamp-2">
                                {{ newsItem.title }}
                            </h3>
                            <p class="text-gray-600 mb-4 md:mb-6 line-clamp-4 text-sm md:text-base">
                                {{ newsItem.excerpt || (newsItem.content ? newsItem.content.slice(0, 180) + '…' : 'Детали новости появятся скоро.') }}
                            </p>
                            <div class="">
                                <div v-if="newsItem.tags?.length" class="flex flex-wrap gap-2 mb-4">
                                    <span
                                        v-for="tag in newsItem.tags"
                                        :key="`${newsItem.id}-${tag.label}`"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold text-white"
                                        :style="getTagStyle(tag)"
                                    >
                                        {{ tag.label }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <Link
                                        :href="`/news/${newsItem.slug}`"
                                        class="text-primary hover:underline font-semibold"
                                    >
                                        Подробнее
                                        <i class="fas fa-arrow-right ml-2"></i>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
                <div v-else class="bg-white rounded-2xl shadow-sm p-12 text-center text-gray-500">
                    Публикации ещё не добавлены. Загляните позже или подпишитесь на наши обновления.
                </div>
            </section>

            <nav v-if="meta.last_page > 1" class="flex justify-center mt-12">
                <div class="inline-flex items-center gap-2">
                    <Link
                        v-if="meta.prev_page_url"
                        :href="makePageLink(meta.prev_page_url)"
                        preserve-scroll
                        preserve-state
                        class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100"
                    >
                        <i class="fas fa-chevron-left mr-2"></i>
                        Назад
                    </Link>
                    <span class="px-4 py-2 text-sm text-gray-500">
                        Страница {{ meta.current_page }} из {{ meta.last_page }}
                    </span>
                    <Link
                        v-if="meta.next_page_url"
                        :href="makePageLink(meta.next_page_url)"
                        preserve-scroll
                        preserve-state
                        class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100"
                    >
                        Вперёд
                        <i class="fas fa-chevron-right ml-2"></i>
                    </Link>
                </div>
            </nav>
        </div>
    </AppLayout>
</template>

