import '../css/app.css';
import './bootstrap';

import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {createApp, h} from 'vue';
import {ZiggyVue} from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'myUniAlly';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({el, App, props, plugin}) {
        return createApp({render: () => h(App, props)})

            .use(plugin)
            .use(ZiggyVue)
            .directive('click-outside', {
                mounted(el, binding) {
                    el._clickOutside = (e) => {
                        if (!el.contains(e.target)) binding.value()
                    }
                    document.addEventListener('mousedown', el._clickOutside)
                },
                unmounted(el) {
                    document.removeEventListener('mousedown', el._clickOutside)
                },
            })
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// Only initialise Firebase Messaging in supported browsers
const isBrowserSupported =
    typeof window !== 'undefined' &&
    'serviceWorker' in navigator &&
    'PushManager' in window &&
    'Notification' in window;

if (isBrowserSupported) {
    import('firebase/messaging').then(({getMessaging, onMessage}) => {
        try {
            const messaging = getMessaging();

            onMessage(messaging, (payload) => {
                const title = payload.notification?.title || payload.data?.title || 'Notification';
                const body = payload.notification?.body || payload.data?.body || 'New Notification!';

                if (Notification.permission !== 'granted') return;

                const n = new Notification(title, {
                    body,
                    icon: '/icons/pwa-192x192.png',
                });

                n.onclick = () => window.focus();
            });
        } catch (err) {
            console.warn('Firebase Messaging init failed:', err);
        }
    });
}
