import { initializeApp } from 'firebase/app'
import { getMessaging, isSupported } from 'firebase/messaging'

const firebaseConfig = {
    apiKey: "AIzaSyBJI627pYksfTDGZHKEsVwvF_xvJwfO7m8",
    authDomain: "mytimetable-9beae.firebaseapp.com",
    projectId: "mytimetable-9beae",
    storageBucket: "mytimetable-9beae.firebasestorage.app",
    messagingSenderId: "671015299600",
    appId: "1:671015299600:web:dec98f039058f55c8c7895"
};


export const app = initializeApp(firebaseConfig)
export async function getFirebaseMessaging() {
    const supported = await isSupported()
    if (!supported) return null
    return getMessaging(app)
}
