<script setup>
import NotificationPrompt from "@/Components/Notifications/NotificationPrompt.vue";
import InstallPWA from "@/Components/InstallPWA.vue";
import MobileNav from "@/Components/MobileNav.vue";
import {setupNotifications} from '../composables/useNotifications.js'
import {onMounted} from "vue";
import LoginNav from "@/Components/LoginNav.vue";

onMounted(() => {
    setupNotifications()
})

</script>

<template>

    <div
        class="sticky top-0 z-50 bg-primary text-lg text-center w-full font-semibold text-white tracking-wide py-1 shadow-sm">
        my<span class="text-secondary font-bold">Timetable</span>
    </div>
    <nav>
        <LoginNav/>
        <NotificationPrompt/>
    </nav>
    <slot/>

    <div class="mt-24">
        <MobileNav/>
    </div>
</template>
