<template>
    <div class="flex flex-col gap-3">

        <!-- Section badge -->
        <div v-if="section" class="flex flex-col gap-1">
      <span class="self-start text-[10px] font-extrabold uppercase tracking-widest text-primary bg-primary/10 px-2.5 py-1 rounded-md">
        {{ section.title }}
      </span>
            <p v-if="section.instructions" class="text-[12px] text-gray-500 leading-relaxed m-0">
                {{ section.instructions }}
            </p>
        </div>

        <!-- Card -->
        <div class="bg-white border-[1.5px] border-gray-200 rounded-2xl p-5 shadow-sm">

            <!-- Meta row -->
            <div class="flex items-center flex-wrap gap-2 mb-3">
        <span class="text-[11px] font-bold bg-gray-100 text-gray-700 px-2.5 py-1 rounded-full">
          Question {{ index + 1 }}
          <span class="font-normal text-gray-400">of {{ total }}</span>
        </span>
                <span class="text-[11px] bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full">
          {{ marks }} mark{{ marks !== 1 ? 's' : '' }}
        </span>
                <span class="text-[10px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-full" :class="typeBadgeClass">
          {{ typeName }}
        </span>
            </div>

            <!-- Question text (rich HTML + math) -->
            <RichText
                :content="questionText"
                class="text-[15px] font-medium text-gray-900 leading-relaxed"
                block
            />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import RichText from './RichText.vue'

const props = defineProps({
    index: Number,
    total: Number,
    marks: { type: Number, default: 1 },
    questionText: String,
    questionType: String,
    section: Object,
})

const typeName = computed(() => {
    const t = (props.questionType || '').toLowerCase()
    if (t.includes('essay') || t.includes('long')) return 'Essay'
    if (t.includes('short') || t.includes('fill')) return 'Short Answer'
    if (t.includes('true') || t.includes('false') || t === 'tf') return 'True / False'
    return 'Multiple Choice'
})

const typeBadgeClass = computed(() => {
    const t = (props.questionType || '').toLowerCase()
    if (t.includes('essay') || t.includes('long')) return 'bg-amber-100 text-amber-800'
    if (t.includes('short') || t.includes('fill')) return 'bg-emerald-100 text-emerald-800'
    if (t.includes('true') || t.includes('false') || t === 'tf') return 'bg-pink-100 text-pink-800'
    return 'bg-violet-100 text-violet-700'
})
</script>
