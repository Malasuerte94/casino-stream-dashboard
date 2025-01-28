import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import Dialog from 'vue3-dialog';
import { createPinia } from 'pinia'; // Import Pinia

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        const pinia = createPinia(); // Create Pinia instance

        return app
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(Dialog)
            .use(pinia) // Use Pinia
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});