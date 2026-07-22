<script setup lang="ts">
/**
 * ImportQuestionsStep.vue
 *
 * Drop-in step for the existing "contribute" wizard. Lets a contributor
 * paste multiple questions in a simple block format and see a live,
 * parsed preview before submitting. Submitted questions are created as
 * DRAFTS (same rule you already have: publishing requires ≥1 question).
 *
 * Expected paste format (blank line or "---" separates questions):
 *
 *   Q: What is the powerhouse of the cell?
 *   Type: mcq
 *   A) Nucleus
 *   B) Mitochondria
 *   C) Ribosome
 *   D) Golgi apparatus
 *   Answer: B
 *   Tip: Mitochondria produce ATP through cellular respiration.
 *
 *   ---
 *
 *   Q: Explain Newton's second law.
 *   Type: essay
 *   Tip: Look for F = ma and a discussion of net force.
 *
 * Supported Type values: mcq, short, essay, tf (true/false)
 */
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
    paperId: number
}>()

const emit = defineEmits<{
    imported: [count: number]
    back: []
}>()

type QuestionType = 'mcq' | 'short' | 'essay' | 'tf'

interface ParsedQuestion {
    raw: string
    question: string
    type: QuestionType
    options: string[]
    answer: string | null
    tip: string | null
    errors: string[]
}

const rawText = ref('')
const submitting = ref(false)
const submitError = ref<string | null>(null)

const TYPE_LABELS: Record<QuestionType, string> = {
    mcq: 'Multiple Choice',
    short: 'Short Answer',
    essay: 'Essay',
    tf: 'True / False',
}

function parseBlock(block: string): ParsedQuestion | null {
    const lines = block.split('\n').map((l) => l.trim()).filter((l) => l.length)
    if (!lines.length) return null

    const result: ParsedQuestion = {
        raw: block,
        question: '',
        type: 'short',
        options: [],
        answer: null,
        tip: null,
        errors: [],
    }

    for (const line of lines) {
        const qMatch = line.match(/^Q:\s*(.+)$/i)
        const typeMatch = line.match(/^Type:\s*(mcq|short|essay|tf)\s*$/i)
        const optMatch = line.match(/^([A-D])\)\s*(.+)$/i)
        const ansMatch = line.match(/^Answer:\s*(.+)$/i)
        const tipMatch = line.match(/^Tip:\s*(.+)$/i)

        if (qMatch) result.question = qMatch[1].trim()
        else if (typeMatch) result.type = typeMatch[1].toLowerCase() as QuestionType
        else if (optMatch) result.options.push(optMatch[2].trim())
        else if (ansMatch) result.answer = ansMatch[1].trim()
        else if (tipMatch) result.tip = tipMatch[1].trim()
        else if (!result.question) result.question = line // tolerate a bare first line
    }

    // Validation
    if (!result.question) result.errors.push('Missing question text (add a "Q:" line).')
    if (result.type === 'mcq') {
        if (result.options.length < 2) result.errors.push('MCQ needs at least 2 options (A), B) ...).')
        if (!result.answer) result.errors.push('MCQ needs an "Answer:" line.')
    }
    if (result.type === 'tf' && result.answer && !/^(true|false)$/i.test(result.answer)) {
        result.errors.push('True/False answer must be "True" or "False".')
    }

    return result
}

const parsedQuestions = computed<ParsedQuestion[]>(() => {
    if (!rawText.value.trim()) return []
    return rawText.value
        .split(/\n\s*(?:---)?\s*\n/)
        .map((block) => block.trim())
        .filter(Boolean)
        .map(parseBlock)
        .filter((q): q is ParsedQuestion => q !== null)
})

const validQuestions = computed(() => parsedQuestions.value.filter((q) => q.errors.length === 0))
const hasErrors = computed(() => parsedQuestions.value.some((q) => q.errors.length > 0))

function submit() {
    if (!validQuestions.value.length || submitting.value) return
    submitting.value = true
    submitError.value = null

    router.post(
        route('pastQuestion.questions.import', { pastQuestion: props.paperId }),
        {
            questions: validQuestions.value.map((q) => ({
                question: q.question,
                type: q.type,
                options: q.options,
                answer: q.answer,
                tip: q.tip,
            })),
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                emit('imported', validQuestions.value.length)
                rawText.value = ''
            },
            onError: (errors) => {
                submitError.value = Object.values(errors)[0] as string ?? 'Import failed. Check the format and try again.'
            },
            onFinish: () => {
                submitting.value = false
            },
        }
    )
}
</script>

<template>
    <div class="space-y-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Import questions</h2>
            <p class="mt-1 text-sm text-gray-500">
                Paste multiple questions below. Separate each question with a blank line or
                <code class="rounded bg-gray-100 px-1 py-0.5 text-xs">---</code>.
            </p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Input -->
            <div class="space-y-2">
                <label for="import-textarea" class="text-sm font-medium text-gray-700">Paste questions</label>
                <textarea
                    id="import-textarea"
                    v-model="rawText"
                    rows="16"
                    placeholder="Q: What is the powerhouse of the cell?
Type: mcq
A) Nucleus
B) Mitochondria
C) Ribosome
D) Golgi apparatus
Answer: B
Tip: Mitochondria produce ATP through cellular respiration."
                    class="w-full resize-y rounded-lg border border-gray-300 p-3 font-mono text-sm text-gray-800 focus:border-[#01629c] focus:outline-none focus:ring-1 focus:ring-[#01629c]"
                />
                <details class="text-xs text-gray-500">
                    <summary class="cursor-pointer select-none text-[#01629c]">Format guide</summary>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        <li><code>Q:</code> the question text (required)</li>
                        <li><code>Type:</code> mcq, short, essay, or tf (defaults to short)</li>
                        <li><code>A) B) C) D)</code> options, mcq only</li>
                        <li><code>Answer:</code> required for mcq and tf</li>
                        <li><code>Tip:</code> optional explanation shown after the exam</li>
                    </ul>
                </details>
            </div>

            <!-- Live preview -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Preview</span>
                    <span v-if="parsedQuestions.length" class="text-xs text-gray-500">
            {{ validQuestions.length }} / {{ parsedQuestions.length }} ready
          </span>
                </div>

                <div v-if="!parsedQuestions.length" class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-400">
                    Parsed questions will appear here as you type.
                </div>

                <div v-else class="max-h-[26rem] space-y-3 overflow-y-auto pr-1">
                    <div
                        v-for="(q, i) in parsedQuestions"
                        :key="i"
                        class="rounded-lg border p-3 text-sm"
                        :class="q.errors.length ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-white'"
                    >
                        <div class="mb-1 flex items-center justify-between">
              <span class="rounded-full bg-[#01629c]/10 px-2 py-0.5 text-xs font-medium text-[#01629c]">
                {{ TYPE_LABELS[q.type] }}
              </span>
                            <span v-if="q.errors.length" class="text-xs font-medium text-red-600">Needs fixing</span>
                            <span v-else class="text-xs font-medium text-[#10B981]">Ready</span>
                        </div>

                        <p class="font-medium text-gray-900">{{ q.question || '(no question text)' }}</p>

                        <ul v-if="q.options.length" class="mt-2 space-y-0.5 text-gray-600">
                            <li v-for="(opt, oi) in q.options" :key="oi">
                                {{ String.fromCharCode(65 + oi) }}) {{ opt }}
                            </li>
                        </ul>

                        <p v-if="q.answer" class="mt-1 text-xs text-gray-500">Answer: {{ q.answer }}</p>
                        <p v-if="q.tip" class="mt-1 text-xs italic text-gray-500">Tip: {{ q.tip }}</p>

                        <ul v-if="q.errors.length" class="mt-2 space-y-0.5 text-xs text-red-600">
                            <li v-for="(err, ei) in q.errors" :key="ei">• {{ err }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <p v-if="hasErrors" class="text-sm text-[#F56E0B]">
            Some questions have errors and will be skipped on import. Fix them above or continue with the rest.
        </p>
        <p v-if="submitError" class="text-sm text-red-600">{{ submitError }}</p>

        <div class="flex items-center justify-between border-t pt-4">
            <button
                type="button"
                class="text-sm font-medium text-gray-500 hover:text-gray-700"
                @click="emit('back')"
            >
                Back
            </button>
            <button
                type="button"
                :disabled="!validQuestions.length || submitting"
                class="rounded-lg mb-2 bg-primary px-5 py-2 text-font-medium text-white transition hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50"
                @click="submit"
            >
                {{ submitting ? 'Importing…' : `Import ${validQuestions.length || ''} question${validQuestions.length === 1 ? '' : 's'}` }}
            </button>
            <button
                type="button"
                class="bg-none"
                @click="submit"
            >
            </button>
        </div>
    </div>
</template>
