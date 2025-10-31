<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    news: {
        type: Object,
        required: true,
    },
});

const breadcrumbs = computed(() => [
    { label: 'Главная', href: '/' },
    { label: 'Новости и акции', href: '/news' },
    { label: props.news.title },
]);

const formatDateTime = (value) => {
    if (!value) {
        return 'Дата уточняется';
    }

    try {
        const dt = new Date(value);
        return dt.toLocaleDateString('ru-RU', {
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
    <AppLayout :title="`${news.title} - Новости`">
        <Breadcrumbs :items="breadcrumbs" />

        <section class="bg-gradient-to-r from-primary to-secondary text-white py-16">
            <div class="container mx-auto px-4">
                <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-4">{{ news.title }}</h1>
                <div class="flex flex-wrap items-center gap-4 text-blue-100 text-sm">
                    <span>
                        <i class="fas fa-calendar-alt mr-2"></i>
                        {{ formatDateTime(news.published_at) }}
                    </span>
                    <span>
                        <i class="fas fa-eye mr-2"></i>
                        {{ news.views ?? 0 }}
                    </span>
                </div>
                <div v-if="news.tags?.length" class="mt-6 flex flex-wrap gap-2">
                    <span
                        v-for="tag in news.tags"
                        :key="tag.label"
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold text-white"
                        :style="getTagStyle(tag)"
                    >
                        {{ tag.label }}
                    </span>
                </div>
            </div>
        </section>

        <div class="container mx-auto px-4 py-12 max-w-4xl">
            <div v-if="news.image" class="mb-10">
                <img
                    :src="news.image"
                    :alt="news.title"
                    class="w-full h-96 object-cover rounded-3xl shadow-lg"
                />
            </div>

            <article class="prose max-w-none prose-lg prose-headings:text-gray-800 prose-p:text-gray-700">
                <div v-if="news.excerpt" class="bg-primary/10 border-l-4 border-primary px-6 py-4 rounded-2xl mb-8">
                    <p class="text-lg text-primary font-semibold">{{ news.excerpt }}</p>
                </div>
                <div v-html="news.content" class="space-y-4" />
            </article>

            <div class="mt-12">
                <Link
                    href="/news"
                    class="inline-flex items-center gap-2 text-primary hover:underline font-semibold"
                >
                    <i class="fas fa-arrow-left"></i>
                    Вернуться к списку новостей
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

