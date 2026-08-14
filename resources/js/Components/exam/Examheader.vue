<template>
    <header class="sticky top-0 z-30 flex items-center justify-between gap-3 px-4 py-2.5 bg-white border-b border-gray-200">
        <!-- Left: course info -->
        <div class="flex flex-col min-w-0 gap-0.5">
      <span class="text-[10px] font-extrabold text-primary uppercase tracking-wider">
        {{ courseCode || 'Course' }}
      </span>
            <span class="text-[13px] font-semibold text-gray-900 truncate max-w-[52vw] md:max-w-none">
        {{ courseTitle || 'Untitled paper' }}
      </span>
        </div>

        <!-- Right: timer + submit -->
        <div class="flex items-center gap-2 flex-shrink-0">
            <!-- Timer -->
            <div
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border-[1.5px] text-[13px] font-bold tabular-nums transition-all duration-300"
                :class="timerClasses"
            >
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                <span>{{ formattedTime }}</span>
            </div>

            <!-- Submit -->
            <button
                @click="$emit('submit')"
                class="px-3.5 py-1.5 rounded-lg bg-primary hover:bg-primary/50 active:scale-95 text-white text-[13px] font-bold border-none cursor-pointer transition-all duration-150"
            >
                Submit
            </button>
        </div>
    </header>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    courseCode: { type: String, default: '' },
    courseTitle: { type: String, default: '' },
    timeLeft: { type: Number, default: 0 }, // seconds
})

defineEmits(['submit'])

const safeTimeLeft = computed(() => {
    const n = Number(props.timeLeft)
    return Number.isFinite(n) && n > 0 ? n : 0
})

const formattedTime = computed(() => {
    const h = Math.floor(safeTimeLeft.value / 3600)
    const m = Math.floor((safeTimeLeft.value % 3600) / 60)
    const s = safeTimeLeft.value % 60
    if (h > 0) return `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
    return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
})

const timerClasses = computed(() => {
    if (safeTimeLeft.value <= 300)
        return 'bg-red-50 border-red-300 text-red-600 animate-pulse'
    if (safeTimeLeft.value <= 600)
        return 'bg-amber-50 border-amber-300 text-amber-600'
    return 'bg-gray-50 border-gray-200 text-gray-900'
})
</script>
