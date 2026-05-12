import { getFirebaseMessaging } from '../firebase.js'
import { getToken } from 'firebase/messaging'

const VAPID_KEY = 'BEz-2KiGbsTkbU2c7Ar-YD0u1dFiKicOHxM9hHtVZThDP2jfM9kJRZxBIlteFVCOuYCwm2eUQ5eqpcGbkAQY8-4'

export async function setupNotifications() {
    const messaging = await getFirebaseMessaging()
    if (!messaging) return

    const permission = await Notification.requestPermission()
    if (permission !== 'granted') return

    const token = await getToken(messaging, {
        vapidKey: VAPID_KEY
    })
    if (!token) return


    let deviceId = localStorage.getItem('device_id')

    if (!deviceId) {
        deviceId = crypto.randomUUID()
        localStorage.setItem('device_id', deviceId)
    }

    const csrf = document.querySelector('meta[name="csrf-token"]').content

    await axios.post('/store-token', {
        token: token,
        platform: 'web',
        device_id: deviceId
    }, {
        headers: {
            'X-CSRF-TOKEN': csrf
        }
    })
}
