import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import vClickOutside from 'v-click-outside';
// Import Ziggy
import { ZiggyVue } from 'ziggy-js/dist/vue.m';
import { Ziggy } from './ziggy';

// Make Ziggy available globally for use in components
if (typeof window !== 'undefined') {
    window.Ziggy = Ziggy;
}

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Restaurant POS';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(
        `./Pages/${name}.vue`,
        import.meta.glob('./Pages/**/*.vue')
    ),
    setup({ el, App, props, plugin }) {
        try {
            const app = createApp({ render: () => h(App, props) });
            
            // Register global directives
            app.directive('click-outside', vClickOutside.directive);
            
            // Merge server-side ziggy data if available
            if (props.initialPage?.props?.ziggy) {
                Object.assign(Ziggy, props.initialPage.props.ziggy);
            }
            
            // Ensure ziggy has location if not present
            if (!Ziggy.location && typeof window !== 'undefined') {
                Ziggy.location = window.location.href;
            }
            
            return app
                .use(plugin)
                .use(ZiggyVue, Ziggy)
                .mount(el);
        } catch (error) {
            console.error('Error initializing Inertia app:', error);
            throw error;
        }
    },
    progress: {
        color: '#4B5563',
    },
});
