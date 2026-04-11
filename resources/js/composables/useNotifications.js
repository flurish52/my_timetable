import { getFirebaseMessaging } from '../firebase.js'
import { getToken, onMessage } from 'firebase/messaging'

const VAPID_KEY = 'BEz-2KiGbsTkbU2c7Ar-YD0u1dFiKicOHxM9hHtVZThDP2jfM9kJRZxBIlteFVCOuYCwm2eUQ5eqpcGbkAQY8-4'

export async function setupNotifications() {

    try {
        const messaging = await getFirebaseMessaging()
        if (!messaging) {
            return
        }

        const permission = await Notification.requestPermission()
        if (permission !== 'granted') return

        const token = await getToken(messaging, {
            vapidKey: VAPID_KEY
        })

    // send token to backend later
    // await fetch('/save-token', {...})

    onMessage(messaging, (payload) => {
        alert(`${payload.notification.title}\n${payload.notification.body}`)
    })

    } catch (e) {
        console.error('ERROR:', e)
    }

}
