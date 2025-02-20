import './bootstrap';
import '../css/app.css';

document.documentElement.classList.add('dark');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import Dialog from 'vue3-dialog';
import { createPinia } from 'pinia';

import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        const pinia = createPinia();
        pinia.use(piniaPluginPersistedstate);
        return app
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(Dialog)
            .use(pinia)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
