import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { createPinia } from 'pinia';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title}` : 'СантехникаЧелябинск - ВОДА и ТЕПЛО в вашем доме'),
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .mount(el);
    },
    progress: {
        color: '#3B82F6',
    },
});

const initializeTheme = () => {
    if (typeof window === 'undefined') {
        return;
    }

    try {
        const root = document.documentElement;
        const storedTheme = window.localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = storedTheme || (prefersDark ? 'dark' : 'light');

        root.classList.remove('light', 'dark');
        root.classList.add(theme);
    } catch (error) {
        console.warn('THEME::INIT_FAILED', error);
    }
};

// This will set light / dark mode on page load...
initializeTheme();