importScripts('https://www.gstatic.com/firebasejs/10.0.0/firebase-app-compat.js')
importScripts('https://www.gstatic.com/firebasejs/10.0.0/firebase-messaging-compat.js')

firebase.initializeApp({
    apiKey: "AIzaSyBJI627pYksfTDGZHKEsVwvF_xvJwfO7m8",
    authDomain: "mytimetable-9beae.firebaseapp.com",
    projectId: "mytimetable-9beae",
    storageBucket: "mytimetable-9beae.firebasestorage.app",
    messagingSenderId: "671015299600",
    appId: "1:671015299600:web:dec98f039058f55c8c7895"
})

const messaging = firebase.messaging()

messaging.onBackgroundMessage(async function (payload) {
    // Our Laravel command sends DATA-ONLY payloads (no top-level
    // "notification" key), so read from payload.data, not payload.notification.
    const title = payload.data?.title || 'Notification';
    const body = payload.data?.body || 'New notification!';
    const url = payload.data?.url || '/'; // FIX: was payload.url, always undefined on data-only payloads

    // Known FCM web quirk: if a tab with this app is open and focused,
    // that tab's own onMessage() handler (in app.js) will already show a
    // notification for this same payload. If we ALSO show one here, the
    // user sees it twice. So: only show it from the service worker if no
    // focused/visible client is currently handling it.
    const allClients = await self.clients.matchAll({
        type: 'window',
        includeUncontrolled: true,
    });

    const hasFocusedClient = allClients.some((client) => client.focused);

    if (hasFocusedClient) {
        return;
    }

    self.registration.showNotification(title, {
        body,
        icon: '/icons/pwa-192x192.png',
        data: { url },
    });
});

// NEW: handles the actual tap. Without this, the notification shows fine
// but tapping it does nothing — the OS just dismisses it.
self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const url = event.notification.data?.url || '/';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
            // If a tab is already open on this exact path, just focus it
            for (const client of windowClients) {
                if (client.url.includes(url) && 'focus' in client) {
                    return client.focus();
                }
            }
            // Otherwise open a new tab/window at the target URL
            if (self.clients.openWindow) {
                return self.clients.openWindow(url);
            }
        })
    );
});
