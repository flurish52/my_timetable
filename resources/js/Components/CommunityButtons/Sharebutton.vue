<script setup>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()

const currentUrl = computed(() => {
    return window.location.href
})

async function share() {
    async function share() {
        const shareData = {
            title: 'myUniAlly',
            text: 'Stop using paper timetables. Get your class timetable, daily lectures, and past questions in one place with myUniAllyApp.',
            url: currentUrl.value,
        }

        try {
            if (navigator.share) {
                await navigator.share(shareData)
            } else {
                await navigator.clipboard.writeText(
                    `${shareData.text}\n\n${shareData.url}`
                )

                alert('Share message copied to clipboard!')
            }
        } catch (error) {
            console.log(error)
        }
    }
}
</script>

<template>
    <button
        @click="share"
        class="absolute
        flex flex-col items-center px-3 py-1 rounded-lg text-sm font-medium
           text-primary bg-white/5 hover:bg-primary hover:text-white hover:border-primary/40
           transition-all duration-150"
        title="Share this page"
    >
        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-4 h-4 text-primary hover:text-white"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2"
             stroke-linecap="round"
             stroke-linejoin="round">
            <circle cx="18" cy="5" r="3"/>
            <circle cx="6" cy="12" r="3"/>
            <circle cx="18" cy="19" r="3"/>
            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
        </svg>

        <span>Share app</span>
    </button>
</template>
