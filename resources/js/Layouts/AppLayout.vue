<script setup>
import NotificationPrompt from "@/Components/Notifications/NotificationPrompt.vue";
import {setupNotifications} from '../composables/useNotifications.js'
import {onMounted} from "vue";
import AuthHeader from "@/Components/AuthHeader.vue";
import {usePage} from '@inertiajs/vue3'
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import ShareButton from "@/Components/CommunityButtons/ShareButton.vue";
import ButtomNav from   "@/Components/Navigation/BottomNav.vue"
import LoginNav from "@/Components/LoginNav.vue";
import { useHeartbeat } from '@/composables/useHeartbeat'

const page = usePage()
useHeartbeat()

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
        <div class="w-8 h-8 inline-flex items-center justify-center rounded-2xl bg-primary/10 rounded-md">
            <ApplicationLogo class="w-8 h-8 rounded-md"/>
        </div>
        <nav>
            <div v-if="!$page.props.auth?.user">
                <LoginNav />
            </div>
            <div v-else>
                <AuthHeader/>
            </div>
            <NotificationPrompt/>
        </nav>

    </div>
    <!--     Share button: sticky directly below the nav -->
    <div class="relative">

        <slot/>
    </div>

    <!-- WhatsApp join button: floating above mobile nav -->
    <div class="flex items-center  justify-center">

        <div class="sticky top-12 z-50 flex justify-end px-4 ">
            <ShareButton/>
        </div>
        <div class="sticky top-12 z-50 flex justify-end px-4 ">
        </div>
    </div>

    <div
        class="mt-24">
        <ButtomNav />
    </div>
</template>
