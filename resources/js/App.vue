<template>
    <nav>
        <InstallPWA />   <!-- drop it right here -->
        <NotificationPrompt />
    </nav>
    <router-view />
</template>
<script setup>
import InstallPWA from "./Components/InstallPWA.vue";
import NotificationPrompt from "./Components/Notifications/NotificationPrompt.vue";
import { setupNotifications } from './composables/useNotifications.js'
import {onMounted} from "vue";

onMounted(() => {
    setupNotifications()
})
</script>
