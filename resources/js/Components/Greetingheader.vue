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
            <h1 class="text-[1.65rem]  text-gray-900 leading-tight tracking-tight">
                Hi, {{ getGreeting() }} <span v-if="user" class="font-extrabold">{{ user.username }}</span>
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
