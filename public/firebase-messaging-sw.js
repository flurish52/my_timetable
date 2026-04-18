importScripts('https://www.gstatic.com/firebasejs/10.0.0/firebase-app-compat.js')
importScripts('https://www.gstatic.com/firebasejs/10.0.0/firebase-messaging-compat.js')


console.log('SW LOADED')
firebase.initializeApp({
    apiKey: "AIzaSyBJI627pYksfTDGZHKEsVwvF_xvJwfO7m8",
    authDomain: "mytimetable-9beae.firebaseapp.com",
    projectId: "mytimetable-9beae",
    storageBucket: "mytimetable-9beae.firebasestorage.app",
    messagingSenderId: "671015299600",
    appId: "1:671015299600:web:dec98f039058f55c8c7895"
})

const messaging = firebase.messaging()
messaging.onBackgroundMessage(function(payload) {
    self.registration.showNotification(
        payload.notification?.title || 'Notification',
        {
            body: payload.notification?.body || 'New notification!',
            icon: '/icons/pwa-192x192.png'
        }
    )
})


