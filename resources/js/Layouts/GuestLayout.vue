<script setup>
import NotificationPrompt from "@/Components/Notifications/NotificationPrompt.vue";
import MobileNav from "@/Components/MobileNav.vue";
import {setupNotifications} from '../composables/useNotifications.js'
import {onMounted} from "vue";
import LoginNav from "@/Components/LoginNav.vue";
import AuthHeader from "@/Components/AuthHeader.vue";
import { usePage } from '@inertiajs/vue3'
const page = usePage()


onMounted(() => {

    const user = page.props.auth?.user

    if (!user) return
    if (!user.programme_id) return

    setupNotifications()
})

</script>

<template>

    <div
        class="sticky top-0 z-50 px-8 bg-primary text-lg flex justify-between items-center text-center w-full font-semibold text-white tracking-wide py-1 shadow-sm">
        <div>
            my<span class="text-secondary font-bold">UniAlly</span>
        </div>
        <nav>
            <div v-if="!$page.props.auth?.user">
                <LoginNav/>
            </div>
            <div v-else>
                <AuthHeader/>
            </div>
            <NotificationPrompt/>
        </nav>
    </div>

    <slot/>

    <div
        v-if="$page.props?.auth?.user"
        class="mt-24">
        <MobileNav/>
    </div>
</template>
