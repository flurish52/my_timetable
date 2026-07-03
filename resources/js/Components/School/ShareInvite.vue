<script setup>
import { ref } from 'vue'
import Sharebutton from "@/Components/CommunityButtons/Sharebutton.vue";

const props = defineProps({
    shareUrl: String,
    schoolName: String,
})

const copied = ref(false)

const shareApp = async () => {
    if (navigator.share) {
        await navigator.share({
            title: 'myUniAlly',
            text: 'Stop using paper timetables. Get your class timetable, daily lectures, and past questions in one place with myUniAlly.',
            url: props.shareUrl,
        })
    } else {
        await navigator.clipboard.writeText(props.shareUrl)
        copied.value = true
        setTimeout(() => {
            copied.value = false
        }, 2000)
    }

    isOpen.value = false
}
</script>

<template>
    <div class="border-t border-gray-100 pt-5">
        <p class="text-sm text-gray-600 mb-3">
            Invite classmates to move {{ schoolName }} up the list
        </p>
        <button
            @click="shareApp"
            class="w-full flex items-center justify-center gap-2 py-2.5 border border-primary text-primary text-sm font-medium rounded-lg transition-all duration-150 hover:bg-primary/5 active:scale-[0.99]"
        >
            <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                <rect x="5" y="5" width="7" height="7" rx="1.2" stroke="currentColor" stroke-width="1.4"/>
                <path d="M3 9.5V3.7C3 3.3 3.3 3 3.7 3h5.8" stroke="currentColor" stroke-width="1.4"/>
            </svg>
            {{ copied ? 'Link copied' : 'Share invite link' }}
        </button>
    </div>

    <Sharebutton />

</template>
