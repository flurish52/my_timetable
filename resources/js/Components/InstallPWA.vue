<template>
    <div v-if="!isInstalled && canInstall" :class="[`flex items-center justify-between p-4 bg-white shadow`,
            isInstalled? 'hidden' : ''
]">
        <span class="font-bold text-blue-600">MyTimetable</span>
        <div>
            <button
                v-if="canInstall"
                @click="installApp"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/>
                </svg>
                Install App
            </button>
        </div>
    </div>
</template>

<script setup>
import {ref, onMounted, onBeforeUnmount} from 'vue'

const canInstall = ref(false)
const isInstalled = ref(false)
let deferredPrompt = null

function handleBeforeInstall(e) {
    e.preventDefault()                // stop browser's default mini-bar
    deferredPrompt = e                // save the event
    canInstall.value = true           // show our button
}

function handleAppInstalled() {
    canInstall.value = false
    isInstalled.value = true
    deferredPrompt = null
}

async function installApp() {
    if (!deferredPrompt) return

    deferredPrompt.prompt()           // show the install dialog

    const {outcome} = await deferredPrompt.userChoice

    if (outcome === 'accepted') {
        canInstall.value = false
        isInstalled.value = true
    }

    deferredPrompt = null
}

onMounted(() => {
    // Check if already installed (standalone mode)
    if (window.matchMedia('(display-mode: standalone)').matches) {
        isInstalled.value = true
        return
    }

    window.addEventListener('beforeinstallprompt', handleBeforeInstall)
    window.addEventListener('appinstalled', handleAppInstalled)
})

onBeforeUnmount(() => {
    window.removeEventListener('beforeinstallprompt', handleBeforeInstall)
    window.removeEventListener('appinstalled', handleAppInstalled)
})
</script>
