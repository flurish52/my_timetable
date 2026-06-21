<template>
    <div>

        <!-- ── MCQ ── -->
        <div v-if="isMcq" class="flex flex-col gap-2.5">
            <button
                v-for="(opt, i) in options"
                :key="i"
                @click="emit('answer', i)"
                class="flex items-center gap-3 w-full px-3.5 py-3 rounded-xl border-[1.5px] text-left cursor-pointer transition-all duration-150 active:scale-[.99]"
                :class="modelValue === i
          ? 'border-primary bg-primary/50 shadow-[0_0_0_3px_rgba(79,70,229,.1)]'
          : 'border-gray-200 bg-white hover:border-primary hover:bg-primary/30'"
            >
                <!-- Letter bubble -->
                <span
                    class="w-8 h-8 flex-shrink-0 rounded-[10px] border-[1.5px] flex items-center justify-center text-[11px] font-extrabold transition-all duration-150"
                    :class="modelValue === i
            ? 'bg-primary border-primary/90 text-white'
            : 'bg-gray-100 border-gray-300 text-gray-500'"
                >{{ LETTERS[i] }}</span>

                <!-- Option text (may contain HTML/math) -->
                <RichText :content="String(opt)" class="flex-1 text-[14px] leading-snug" :class="modelValue === i ? 'font-semibold text-gray-900' : 'text-gray-700'" />

                <!-- Checkmark -->
                <svg v-if="modelValue === i" class="flex-shrink-0 text-primary" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </button>
        </div>

        <!-- ── True / False ── -->
        <div v-else-if="isTrueFalse" class="flex gap-3">
            <button
                @click="emit('answer', 'true')"
                class="flex-1 flex items-center justify-center gap-2 py-4 rounded-xl border-[1.5px] font-bold text-[15px] cursor-pointer transition-all duration-150"
                :class="modelValue === 'true'
          ? 'border-emerald-500 bg-emerald-50 text-emerald-700 shadow-[0_0_0_3px_rgba(5,150,105,.12)]'
          : 'border-gray-200 bg-white text-gray-700 hover:border-emerald-400 hover:bg-emerald-50/40'"
            >
                <span class="text-lg">✓</span> True
            </button>
            <button
                @click="emit('answer', 'false')"
                class="flex-1 flex items-center justify-center gap-2 py-4 rounded-xl border-[1.5px] font-bold text-[15px] cursor-pointer transition-all duration-150"
                :class="modelValue === 'false'
          ? 'border-red-500 bg-red-50 text-red-700 shadow-[0_0_0_3px_rgba(220,38,38,.12)]'
          : 'border-gray-200 bg-white text-gray-700 hover:border-red-400 hover:bg-red-50/40'"
            >
                <span class="text-lg">✗</span> False
            </button>
        </div>

        <!-- ── Short Answer ── -->
        <div v-else-if="isShortAnswer" class="flex flex-col gap-1.5">
            <label class="text-[11px] font-extrabold uppercase tracking-wider text-gray-500">Your Answer</label>
            <input
                type="text"
                placeholder="Type your answer here…"
                :value="modelValue ?? ''"
                @input="e => emit('answer', e.target.value)"
                class="w-full px-3.5 py-3 rounded-xl border-[1.5px] border-gray-200 bg-white text-[14px] text-gray-900 outline-none transition-all duration-150 focus:border-primary focus:shadow-[0_0_0_3px_rgba(79,70,229,.12)]"
            />
            <span class="text-[11px] text-gray-400">Keep it concise — a word, phrase, or short sentence.</span>
        </div>

        <!-- ── Essay ── -->
        <div v-else-if="isEssay" class="flex flex-col gap-1.5">
            <label class="text-[11px] font-extrabold uppercase tracking-wider text-gray-500">Your Answer</label>
            <textarea
                placeholder="Write your full answer here…"
                :value="modelValue ?? ''"
                @input="e => emit('answer', e.target.value)"
                rows="7"
                class="w-full px-3.5 py-3 rounded-xl border-[1.5px] border-gray-200 bg-white text-[14px] text-gray-900 leading-relaxed outline-none resize-y transition-all duration-150 focus:border-primary"></textarea><span class="text-[11px] text-gray-400 text-right">{{ wordCount }} words</span>

        </div>

        <!-- ── Fallback ── -->
        <div v-else class="flex flex-col gap-1.5">
            <label class="text-[11px] font-extrabold uppercase tracking-wider text-gray-500">Your Answer</label>
            <textarea
                placeholder="Write your answer here…"
                :value="modelValue ?? ''"
                @input="e => emit('answer', e.target.value)"
                rows="5"
                class="w-full px-3.5 py-3 rounded-xl border-[1.5px] border-gray-200 bg-white text-[14px] text-gray-900 leading-relaxed outline-none resize-y transition-all duration-150 focus:border-primary focus:shadow-[0_0_0_3px_rgba(79,70,229,.12)]"
            />
        </div>

    </div>
</template>

<script setup>
import { computed } from 'vue'
import RichText from './RichText.vue'

const LETTERS = ['A', 'B', 'C', 'D', 'E']

const props = defineProps({
    questionType: String,
    options: { type: Array, default: () => [] },
    modelValue: { default: undefined },
})

const emit = defineEmits(['answer'])

const type = computed(() => (props.questionType || 'objective').toLowerCase())
const isMcq = computed(() => type.value.includes('objective') || type.value.includes('mcq') || type.value.includes('multiple'))
const isTrueFalse = computed(() => type.value.includes('true') || type.value.includes('false') || type.value === 'tf')
const isShortAnswer = computed(() => type.value.includes('short') || type.value.includes('fill'))
const isEssay = computed(() => type.value.includes('essay') || type.value.includes('long'))

const wordCount = computed(() => {
    const v = props.modelValue
    if (!v) return 0
    return String(v).trim().split(/\s+/).filter(Boolean).length
})
</script>
