<script setup>
defineProps({
    user: {
        type: Object,
        required: true,
    },
    lectureCount: {
        type: Number,
        default: 0,
    },
})

function getGreeting() {
    const h = new Date().getHours()
    if (h < 12) return 'Good morning'
    if (h < 17) return 'Good afternoon'
    return 'Good evening'
}
</script>

<template>
    <div class="flex items-start justify-between px-5 pt-6 pb-2">

        <!-- Greeting text -->
        <div>
            <h1 class="text-[1.65rem] font-extrabold text-gray-900 leading-tight tracking-tight">
                Hi, {{ getGreeting() }} {{ user.name }}
                <span class="inline-block animate-wave">👋</span>
            </h1>
            <p class="mt-1.5 text-sm text-gray-500 font-medium flex items-center gap-1">
                You have
                <span
                    class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-primary text-white text-[11px] font-bold"
                >
          {{ lectureCount }}
        </span>
                lectures today
            </p>
        </div>

        <!-- Bell + Avatar -->
<!--        <div class="flex items-center gap-2.5 mt-1">-->

<!--            <button-->
<!--                aria-label="Notifications"-->
<!--                class="relative w-10 h-10 rounded-full border border-gray-200 bg-white shadow-sm flex items-center justify-center hover:shadow-md transition-shadow"-->
<!--            >-->
<!--                <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"-->
<!--                     stroke-width="2">-->
<!--                    <path stroke-linecap="round" stroke-linejoin="round"-->
<!--                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"-->
<!--                    />-->
<!--                </svg>-->
<!--                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 border-2 border-white"/>-->
<!--            </button>-->

<!--            <button-->
<!--                aria-label="Profile"-->
<!--                class="w-10 h-10 rounded-full bg-secondary text-white font-bold text-base shadow-sm flex items-center justify-center hover:opacity-90 transition-opacity"-->
<!--            >-->
<!--                {{ user.avatar }}-->
<!--            </button>-->

<!--        </div>-->
    </div>
</template>

<style scoped>
@keyframes wave {
    0%, 100% {
        transform: rotate(0deg);
    }
    20% {
        transform: rotate(14deg);
    }
    40% {
        transform: rotate(-8deg);
    }
    60% {
        transform: rotate(14deg);
    }
    80% {
        transform: rotate(-4deg);
    }
}

.animate-wave {
    display: inline-block;
    animation: wave 2.2s ease-in-out infinite;
    transform-origin: 70% 70%;
}
</style>
