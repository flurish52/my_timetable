<template>
    <div
        v-if="showPrompt"
        class="fixed bottom-0 left-0 w-full bg-primary text-white p-4 text-center z-50 shadow-lg"
    >
        <p class="mb-3 text-sm md:text-base">
            Enable notifications for activity reminders?
        </p>

        <div class="flex justify-center gap-3">
            <button
                @click="enable"
                class="px-4 py-2 bg-primary/50 hover:bg-primary/10 rounded-md text-sm font-medium"
            >
                Yes, enable
            </button>

            <button
                @click="showPrompt = false"
                class="px-4 py-2 bg-gray-700 hover:bg-red-500 rounded-md text-sm font-medium"
            >
                No thanks
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { setupNotifications } from '@/composables/useNotifications'
    const showPrompt = ref(false)


onMounted(() => {
    if (Notification.permission === 'denied') return

    const lastShown = localStorage.getItem('notif_prompt_last_shown')
    const sevenDays = 7 * 24 * 60 * 60 * 1000

    if (!lastShown || (Date.now() - parseInt(lastShown)) > sevenDays) {
        setupNotifications()
        localStorage.setItem('notif_prompt_last_shown', Date.now().toString())
    }
})

async function enable() {
    await setupNotifications()
    showPrompt.value = false
}
</script>
