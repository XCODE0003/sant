<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import { useShopStore } from '@/stores/useShopStore';

defineProps({
    title: {
        type: String,
        default: 'АкватЭрия',
    },
});

const shopStore = useShopStore();
shopStore.initialize();

const page = usePage();
const footerCategories = computed(() => page.props.footerCategories ?? []);
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <Head>
            <title>{{ title }}</title>
            <link
                href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
                rel="stylesheet"
            />
        </Head>

        <Header :initial-search="page.props.searchQuery ?? ''" />

        <main>
            <slot />
        </main>

        <Footer :categories="footerCategories" />
    </div>
</template>

