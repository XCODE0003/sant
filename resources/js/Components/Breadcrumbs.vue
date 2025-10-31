<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    items: {
        type: Array,
        required: true,
        // Example: [{ label: 'Главная', href: '/' }, { label: 'Каталог' }]
    }
});
</script>

<template>
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <template v-for="(item, index) in items" :key="index">
                    <Link
                        v-if="item.href && index < items.length - 1"
                        :href="item.href"
                        class="text-gray-500 hover:text-primary transition-colors"
                    >
                        {{ item.label }}
                    </Link>
                    <span
                        v-else
                        :class="index === items.length - 1 ? 'text-gray-800' : 'text-gray-500'"
                    >
                        {{ item.label }}
                    </span>
                    <i
                        v-if="index < items.length - 1"
                        class="fas fa-chevron-right text-gray-400 text-xs"
                    ></i>
                </template>
            </nav>
        </div>
    </div>
</template>

