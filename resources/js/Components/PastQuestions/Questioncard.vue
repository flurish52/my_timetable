<script setup>
const question = defineModel({ required: true })

defineProps({
    number: { type: Number, required: true },
})

const emit = defineEmits(['remove'])

const QUESTION_TYPES = [
    { value: 'objective', label: 'Objective' },
    { value: 'true_false', label: 'True / False' },
    { value: 'fill_blank', label: 'Fill in the blank' },
    { value: 'short_answer', label: 'Short answer' },
    { value: 'essay', label: 'Essay' },
]

function needsOptions(type) {
    return type === 'objective' || type === 'true_false'
}

function onTypeChange() {
    if (question.value.question_type === 'true_false') {
        question.value.options = [
            { id: question.value.options?.[0]?.id ?? null, option_text: 'True', is_correct: false },
            { id: question.value.options?.[1]?.id ?? null, option_text: 'False', is_correct: false },
        ]
    } else if (question.value.question_type === 'objective' && question.value.options.length < 2) {
        question.value.options = [
            { id: null, option_text: '', is_correct: false },
            { id: null, option_text: '', is_correct: false },
        ]
    }
}

function addOption() {
    question.value.options.push({ id: null, option_text: '', is_correct: false })
}

function removeOption(index) {
    question.value.options.splice(index, 1)
}

function selectCorrect(index) {
    question.value.options.forEach((o, i) => { o.is_correct = i === index })
}

function preview(text) {
    return text.trim() ? text.trim().slice(0, 72) : 'Untitled question'
}
</script>

<template>
    <div class="rounded-lg border border-neutral-200 bg-white overflow-hidden">
        <!-- Collapsed header row -->
        <button
            type="button"
            @click="question.expanded = !question.expanded"
            class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-neutral-50"
        >
      <span class="shrink-0 w-7 h-7 rounded-full bg-neutral-100 text-neutral-600 text-xs font-semibold flex items-center justify-center">
        {{ number }}
      </span>
            <span class="flex-1 min-w-0 truncate text-sm text-neutral-800">
        {{ preview(question.question_text) }}
      </span>
            <span class="shrink-0 hidden sm:inline-flex text-xs px-2 py-0.5 rounded-full bg-neutral-100 text-neutral-500">
        {{ QUESTION_TYPES.find(t => t.value === question.question_type)?.label }}
      </span>
            <span class="shrink-0 text-xs text-neutral-400">{{ question.marks }} mk</span>
            <svg :class="['w-4 h-4 shrink-0 text-neutral-400 transition-transform', question.expanded && 'rotate-180']"
                 viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.65a.75.75 0 01-1.08 0l-4.25-4.65a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </button>

        <!-- Expanded editor -->
        <div v-if="question.expanded" class="px-4 pb-4 pt-1 border-t border-neutral-100 space-y-3">
            <div class="flex flex-col sm:flex-row gap-3">
                <select v-model="question.question_type" @change="onTypeChange"
                        class="border border-neutral-300 rounded-md px-3 py-2 text-sm sm:w-52 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
                    <option v-for="t in QUESTION_TYPES" :key="t.value" :value="t.value">{{ t.label }}</option>
                </select>

                <label class="flex items-center gap-2 text-sm text-neutral-500 sm:ml-auto">
                    Marks
                    <input v-model.number="question.marks" type="number" min="1"
                           class="w-16 border border-neutral-300 rounded-md px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary" />
                </label>
            </div>

            <textarea v-model="question.question_text" rows="2" placeholder="Type the question here"
                      class="w-full border border-neutral-300 rounded-md px-3 py-2 text-sm resize-y focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary" />

            <!-- Objective options -->
            <div v-if="question.question_type === 'objective'" class="space-y-2">
                <p class="text-xs font-medium text-neutral-500">Options — select the correct one</p>
                <div v-for="(o, oi) in question.options" :key="oi" class="flex items-center gap-2">
                    <button type="button" @click="selectCorrect(oi)"
                            :class="['shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors',
              o.is_correct ? 'border-secondary bg-secondary' : 'border-neutral-300']">
                        <svg v-if="o.is_correct" class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.4 7.4a1 1 0 01-1.4 0L3.3 9.5a1 1 0 111.4-1.4l3.6 3.6 6.7-6.7a1 1 0 011.4 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <input v-model="o.option_text" type="text" :placeholder="`Option ${oi + 1}`"
                           class="flex-1 border border-neutral-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary" />
                    <button v-if="question.options.length > 2" type="button" @click="removeOption(oi)"
                            class="shrink-0 text-neutral-400 hover:text-red-500 text-sm px-1">✕</button>
                </div>
                <button type="button" @click="addOption" class="text-xs font-medium text-primary hover:underline">
                    + Add option
                </button>
            </div>

            <!-- True / False -->
            <div v-else-if="question.question_type === 'true_false'" class="flex gap-2">
                <button v-for="(o, oi) in question.options" :key="oi" type="button" @click="selectCorrect(oi)"
                        :class="['flex-1 border rounded-md py-2 text-sm font-medium transition-colors',
            o.is_correct ? 'border-secondary bg-secondary/10 text-secondary' : 'border-neutral-300 text-neutral-600 hover:bg-neutral-50']">
                    {{ o.option_text }}
                </button>
            </div>

            <!-- Free text answer -->
            <textarea v-else v-model="question.answer_text" rows="2"
                      placeholder="Model answer (optional — helps with grading later)"
                      class="w-full border border-neutral-300 rounded-md px-3 py-2 text-sm resize-y focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary" />

            <div class="flex justify-end pt-1">
                <button type="button" @click="emit('remove')" class="text-xs text-neutral-400 hover:text-red-500">
                    Remove question
                </button>
            </div>
        </div>
    </div>
</template>
