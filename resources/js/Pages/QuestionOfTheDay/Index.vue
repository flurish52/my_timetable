<script setup>
import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import RichText from '@/Components/exam/RichText.vue'

const props = defineProps({
    question: Object,
    attempt: Object,
    streak: Object,
})

const isMcq = computed(() =>
    ['objective', 'true_false'].includes(props.question?.question?.question_type)
)
const hasAttempted = computed(() => !!props.attempt)

const courseCode = computed(() => props.question?.question?.past_question?.course?.code ?? null)

const form = useForm({ answer_text: '', selected_option_id: null })

// Submit stays disabled until there's actually something to grade —
// an MCQ needs a selected option, free text needs non-whitespace content.
const canSubmit = computed(() => {
    if (isMcq.value) {
        return form.selected_option_id !== null
    }
    return form.answer_text.trim().length > 0
})

const pasteWarning = ref(false)
let pasteWarningTimeout = null

function blockPaste() {
    pasteWarning.value = true
    clearTimeout(pasteWarningTimeout)
    pasteWarningTimeout = setTimeout(() => (pasteWarning.value = false), 2500)
}

function submit() {
    if (!canSubmit.value) return
    form.post(route('qotd.attempt', props.question.id), { preserveScroll: true })
}

function share(withAnswer) {
    const q = props.question.question
    const pq = q.past_question
    const schoolName = pq.school?.name ?? 'a Nigerian university'
    const session = pq.session
    const semesterName = pq.semester?.name ?? ''

    let text = `📚 Question of the Day — from ${schoolName}'s ${session} ${semesterName} exam\n\n`
    text += `Q: ${q.question_text}\n`
    if (withAnswer && props.attempt?.answer_text) {
        text += `My Answer: ${props.attempt.answer_text}\n`
    }
    text += `\nWant to try it out? 🧠\nWarm up your brain daily → ${route('qotd.index')}`

    fetch(route('qotd.shared', props.question.id), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
        body: JSON.stringify({ with_answer: withAnswer }),
    })

    if (navigator.share) {
        navigator.share({ text })
    } else {
        window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank')
    }
}
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <div class="max-w-xl mx-auto px-4 py-8">

            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-xs font-semibold tracking-wide text-primary uppercase">
                        Daily Challenge
                    </p>
                    <h1 class="text-2xl font-bold text-slate-900">
                        Question of the Day
                    </h1>
                </div>

                <div class="flex items-center gap-1.5 bg-white border border-orange-200 rounded-full pl-2.5 pr-3 py-1.5 shadow-sm">
                    <span class="text-lg leading-none">🔥</span>
                    <span class="text-sm font-bold text-slate-800">{{ streak.current }}</span>
                    <span class="text-xs text-slate-400">days</span>
                </div>
            </div>

            <!-- Empty state -->
            <div
                v-if="!question"
                class="bg-white border border-slate-200 rounded-2xl p-10 text-center"
            >
                <div class="text-4xl mb-3">🌙</div>
                <p class="text-slate-500 font-medium">No question available today</p>
                <p class="text-sm text-slate-400 mt-1">Check back soon — new questions drop daily.</p>
            </div>

            <!-- Question card -->
            <div v-else class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                <!-- Course ribbon -->
                <div class="bg-primary/10 border-b border-primary/20 px-5 py-2.5 flex items-center justify-between">
                    <span class="text-xs font-bold text-primary tracking-wide uppercase">
                        {{ courseCode ?? 'General Knowledge' }}
                    </span>
                    <span v-if="!hasAttempted" class="text-[11px] text-slate-400 font-medium">
                        1 attempt / day
                    </span>
                </div>

                <div class="p-5 space-y-5">
                    <!-- Question body -->
                    <div class="text-slate-800 leading-relaxed">
                        <RichText :content="question.question.question_text" />
                    </div>

                    <!-- Unanswered: form -->
                    <form v-if="!hasAttempted" @submit.prevent="submit" class="space-y-4">

                        <!-- Rules -->
                        <div class="flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2.5">
                            <span class="text-amber-500 text-sm leading-none mt-0.5">⚠️</span>
                            <p class="text-xs text-amber-800 leading-relaxed">
                                <span class="font-semibold">No copy-pasting answers.</span>
                                Type your own response — that's the only rule.
                            </p>
                        </div>

                        <!-- MCQ / True-False -->
                        <div v-if="isMcq" class="space-y-2.5">
                            <label
                                v-for="option in question.question.options"
                                :key="option.id"
                                class="group flex items-center gap-3 border rounded-xl px-4 py-3 cursor-pointer transition-all"
                                :class="form.selected_option_id === option.id
                                    ? 'border-primary bg-primary/5 ring-1 ring-primary'
                                    : 'border-slate-200 hover:border-primary/40 hover:bg-slate-50'"
                            >
                                <span
                                    class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 transition-colors"
                                    :class="form.selected_option_id === option.id
                                        ? 'border-primary'
                                        : 'border-slate-300 group-hover:border-primary/50'"
                                >
                                    <span
                                        v-if="form.selected_option_id === option.id"
                                        class="h-2.5 w-2.5 rounded-full bg-primary"
                                    />
                                </span>
                                <input
                                    type="radio"
                                    :value="option.id"
                                    v-model="form.selected_option_id"
                                    class="sr-only"
                                />
                                <span class="text-sm text-slate-700">{{ option.option_text }}</span>
                            </label>
                        </div>

                        <!-- Free text -->
                        <div v-else class="space-y-1.5">
                            <textarea
                                v-model="form.answer_text"
                                @paste.prevent="blockPaste"
                                @drop.prevent="blockPaste"
                                rows="4"
                                class="w-full border border-slate-200 rounded-xl p-3.5 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary resize-none"
                                placeholder="Type your answer here..."
                            />
                            <p
                                v-if="pasteWarning"
                                class="text-xs text-rose-500 font-medium"
                            >
                                Pasting isn't allowed — please type your answer.
                            </p>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing || !canSubmit"
                            class="w-full bg-primary hover:bg-primary/90 disabled:opacity-40 disabled:cursor-not-allowed text-white font-semibold text-sm rounded-xl py-3 transition-colors"
                        >
                            {{ form.processing ? 'Submitting...' : 'Submit Answer' }}
                        </button>

                        <p v-if="form.errors.answer" class="text-xs text-red-500 text-center">
                            {{ form.errors.answer }}
                        </p>
                    </form>

                    <!-- Answered: result -->
                    <div v-else class="rounded-xl bg-slate-50 border border-slate-200 p-4 space-y-2">
                        <p v-if="isMcq" class="text-sm text-slate-500">
                            You selected
                            <span class="font-semibold text-slate-800">
                                {{ question.question.options.find(o => o.id === attempt.selected_option_id)?.option_text }}
                            </span>
                        </p>
                        <p v-else class="text-sm text-slate-500">
                            You answered
                            <span class="font-semibold text-slate-800">{{ attempt.answer_text }}</span>
                        </p>

                        <div
                            v-if="attempt.is_correct !== null"
                            class="inline-flex items-center gap-1.5 text-sm font-semibold rounded-full px-3 py-1"
                            :class="attempt.is_correct
                                ? 'bg-emerald-100 text-emerald-700'
                                : 'bg-rose-100 text-rose-700'"
                        >
                            <span>{{ attempt.is_correct ? '✓' : '✕' }}</span>
                            {{ attempt.is_correct ? 'Correct!' : 'Not quite' }}
                        </div>
                        <p v-else-if="question.question.answers?.[0]" class="text-sm text-slate-500 pt-1">
                            <span class="font-medium text-slate-600">Sample answer:</span>
                            {{ question.question.answers[0].answer_text }}
                        </p>
                    </div>

                    <!-- Share — always available, whether or not they've answered.
                         Sharing your own answer only makes sense once you have one. -->
                    <div class="border-t border-slate-100 pt-4">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2.5">
                            {{ hasAttempted ? 'Challenge your friends' : 'Know someone who can solve this?' }}
                        </p>
                        <div class="flex gap-2">
                            <button
                                v-if="hasAttempted"
                                @click="share(true)"
                                class="flex-1 bg-secondary hover:bg-secondary/90 text-white text-sm font-semibold rounded-xl py-2.5 transition-colors"
                            >
                                Share with my answer
                            </button>
                            <button
                                @click="share(false)"
                                class="flex-1 text-sm font-semibold rounded-xl py-2.5 transition-colors"
                                :class="hasAttempted
                                    ? 'border border-tertiary text-tertiary hover:bg-tertiary/10'
                                    : 'bg-secondary hover:bg-secondary/90 text-white'"
                            >
                                Share question only
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
