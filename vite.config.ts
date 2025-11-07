import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    css: {
        lightningcss: {
            targets: {
                chrome: 80,
                firefox: 90,
                safari: 13,
                edge: 90,
            },
            cssModules: false,
            drafts: {
                customMedia: false,
            },
        },
    },
    build: {
        target: ['es2020', 'chrome80', 'firefox90', 'safari13', 'edge90'],
        cssTarget: ['chrome80', 'firefox90', 'safari13', 'edge90'],
    },
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
