import './bootstrap';
import { createApp } from 'vue'
import App from './App.vue'
// import App from './App.vue'
import router from './router'
import { registerSW } from 'virtual:pwa-register'


createApp(App).use(router).mount('#app')
registerSW({ immediate: true })
