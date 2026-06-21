<template>
    <div class="bg-white border-t border-gray-200 px-3.5 pt-2.5 pb-4 flex-shrink-0 z-20">

        <!-- Header -->
        <div class="flex items-center justify-between mb-2">
      <span class="flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-widest text-gray-500">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
          <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
        </svg>
        Questions
      </span>
            <span class="text-[11px] font-bold text-primary">
        {{ answeredCount }}/{{ total }} answered
      </span>
        </div>

        <!-- Grid -->
        <div class="flex flex-wrap gap-1.5">
            <button
                v-for="(_, i) in total"
                :key="i"
                @click="$emit('jump', i)"
                class="w-8 h-8 rounded-[8px] border-[1.5px] text-[11px] font-bold flex items-center justify-center cursor-pointer transition-all duration-100 flex-shrink-0"
                :class="gridBtnClass(i)"
            >
                {{ i + 1 }}
            </button>
        </div>

        <!-- Legend -->
        <div class="flex items-center gap-3.5 mt-2">
      <span class="flex items-center gap-1.5 text-[10px] text-gray-500">
        <span class="w-2.5 h-2.5 rounded-[3px] bg-primary border border-primary"/>Current
      </span>
            <span class="flex items-center gap-1.5 text-[10px] text-gray-500">
        <span class="w-2.5 h-2.5 rounded-[3px] bg-primary/5 border border-primary"/>Answered
      </span>
            <span class="flex items-center gap-1.5 text-[10px] text-gray-500">
        <span class="w-2.5 h-2.5 rounded-[3px] bg-gray-100 border border-gray-300"/>Skipped
      </span>
        </div>

    </div>
</template>

<script setup>
const props = defineProps({
    total: Number,
    currentIndex: Number,
    answeredCount: Number,
    isAnswered: Function, // (index) => boolean
})

defineEmits(['jump'])

function gridBtnClass(i) {
    if (props.currentIndex === i)
        return 'bg-primary border-primary text-white'
    if (props.isAnswered(i))
        return 'bg-primary/10 border-primary/50 text-primary'
    return 'bg-gray-100 border-gray-200 text-gray-500 hover:border-primary/50 hover:text-primary'
}
</script>
