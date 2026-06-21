<template>
    <div class="flex items-center gap-2.5">

        <!-- Prev -->
        <button
            @click="$emit('prev')"
            :disabled="isFirst"
            class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl border-[1.5px] text-[14px] font-bold cursor-pointer transition-all duration-150 flex-shrink-0"
            :class="isFirst
        ? 'border-gray-200 bg-white text-gray-800 cursor-not-allowed opacity-40'
        : 'border-gray-200 bg-white text-gray-800 hover:border-primary/40 hover:text-primary/60'"
        >
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Prev
        </button>

        <!-- Counter -->
        <span class="flex-1 text-center text-[13px] font-medium text-gray-400 tabular-nums">
      {{ currentIndex + 1 }} / {{ total }}
    </span>

        <!-- Next or Finish -->
        <button
            v-if="!isLast"
            @click="$emit('next')"
            class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl border-[1.5px] border-primary/60 bg-primary/60 hover:bg-primary/70 text-white text-[14px] font-bold cursor-pointer transition-all duration-150 active:scale-[.97] flex-shrink-0"
        >
            Next
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </button>

        <button
            v-else
            @click="$emit('finish')"
            class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl border-[1.5px] border-emerald-600 bg-emerald-600 hover:bg-emerald-700 text-white text-[14px] font-bold cursor-pointer transition-all duration-150 active:scale-[.97] flex-shrink-0"
        >
            Finish
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </button>

    </div>
</template>

<script setup>
defineProps({
    currentIndex: Number,
    total: Number,
    isFirst: Boolean,
    isLast: Boolean,
})

defineEmits(['prev', 'next', 'finish'])
</script>
