import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import VueStripe from '@vue-stripe/vue-stripe';
import NaiveUI from 'naive-ui';
import io from 'socket.io-client';
import { VueFlip } from 'vue-flip';
import 'vue3-toastify/dist/index.css';
import VueSocialSharing from 'vue-social-sharing';
import { i18nVue } from 'laravel-vue-i18n';

// import VueMeta from 'vue-meta'

// import 'vfonts/Lato.css'
// // Monospace Font
// import 'vfonts/FiraCode.css'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'ProPhotos';
// window.io = io('http://localhost:3000');
window.io = io('https://server.prophotos.ai');
createInertiaApp({
    title: (title) => `${appName} ${title}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            // .use(VueMeta)
            .use(ZiggyVue, Ziggy)
            .use(NaiveUI)
            .use(VueSocialSharing)
            .use(i18nVue, { 
                resolve: async lang => {
                    const langs = import.meta.glob('../../lang/*.json');
                    return await langs[`../../lang/${lang}.json`]();
                }
            })
            .component('VueFlip', VueFlip)
            .use(VueStripe)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});