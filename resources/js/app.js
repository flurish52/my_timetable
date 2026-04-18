import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';

// Mount the app first — always
createApp(App).use(router).mount('#app');

// Only initialise Firebase Messaging in supported browsers
const isBrowserSupported =
    typeof window !== 'undefined' &&
    'serviceWorker' in navigator &&
    'PushManager' in window &&
    'Notification' in window;

if (isBrowserSupported) {
    import('firebase/messaging').then(({ getMessaging, onMessage }) => {
        try {
            const messaging = getMessaging();

            onMessage(messaging, (payload) => {
                const title = payload.notification?.title || payload.data?.title || 'Notification';
                const body  = payload.notification?.body  || payload.data?.body  || 'New Notification!';

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
