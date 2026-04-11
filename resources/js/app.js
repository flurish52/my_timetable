import './bootstrap';
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { registerSW } from 'virtual:pwa-register'

const updateSW = registerSW({
    onNeedRefresh() {
        // Show a prompt to the user to refresh
        if (confirm('New content available. Reload?')) {
            updateSW(true)
        }
    },
    onOfflineReady() {
        console.log('App is ready to work offline!')
    },
})
createApp(App).use(router).mount('#app')
