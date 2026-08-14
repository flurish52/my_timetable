<template>
    <Head :title="past_question?.course?.code ? `${past_question.course.code} - Examination` : 'Examination'" />
    <div class="font-[DM_Sans,Segoe_UI,system-ui,sans-serif] bg-gray-50 min-h-dvh overflow-x-hidden">

        <!-- Whole-page empty state: past_question itself is null/missing -->
        <div v-if="!past_question" class="max-w-[500px] mx-auto px-4 pt-24 flex flex-col items-center gap-3 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-1">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-gray-400">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <span class="font-medium text-gray-500">We couldn't load this paper</span>
            <span class="text-xs text-gray-400 max-w-[280px]">
                    It may still be processing, or the link may be out of date. Try going back and opening it again.
                </span>
        </div>

        <template v-else>
            <!-- ── Exam Page ── -->
            <Transition name="page-slide-r">
                <ExamPage
                    v-if="!resultsMode"
                    ref="examPageRef"
                    :past_question="past_question"
                    @submitted="handleSubmitted"
                />
            </Transition>
            <!-- ── Results Page ── -->
            <Transition name="page-slide">
                <ResultsPage
                    v-if="resultsMode"
                    :questions="questions"
                    :sections="sections"
                    :answers="submittedAnswers"
                    :course-code="past_question.course?.code ?? ''"
                    :course-title="past_question.course?.title ?? 'Untitled course'"
                    :score-percent="scorePercent"
                    :correct-count="correctCount"
                    :wrong-count="wrongCount"
                    :skipped-count="skippedCount"
                    :total-marks-scored="totalMarksScored"
                    :total-marks="totalMarks"
                    @retake="handleRetake"
                />
            </Transition>
        </template>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import AppLayout  from '@/Layouts/AppLayout.vue'
import ExamPage   from '@/components/exam/ExamPage.vue'
import ResultsPage from '@/components/exam/ResultsPage.vue'
import {Head} from "@inertiajs/vue3";

const props = defineProps({
    past_question: {
        type: Object,
        // Nullable: an unresolved paper, or one whose relations (course,
        // questions, sections) may not all be present yet.
        default: null,
    },
})

/* ── Safe derived data ── */
// Every scoring computed below reads from these instead of props.past_question
// directly, so a null paper or a missing questions/sections array never throws.
const questions = computed(() => props.past_question?.questions ?? [])
const sections  = computed(() => props.past_question?.sections ?? [])

/* ── State ── */
const resultsMode      = ref(false)
const submittedAnswers = ref({})
const examPageRef      = ref(null)

/* ── Handlers ── */
function handleSubmitted(answers) {
    submittedAnswers.value = answers
    resultsMode.value = true
}

function handleRetake() {
    resultsMode.value = false
    submittedAnswers.value = {}
    // Let DOM settle then tell ExamPage to reset
    setTimeout(() => examPageRef.value?.retake(), 50)
}

/* ── Scoring (computed from submitted answers) ── */
const LETTERS = ['A', 'B', 'C', 'D', 'E']

function getQuestionType(q) {
    return (q?.type || q?.question_type || 'objective').toLowerCase()
}
function isMcq(q) {
    const t = getQuestionType(q)
    return t.includes('objective') || t.includes('mcq') || t.includes('multiple')
}
function isTrueFalse(q) {
    const t = getQuestionType(q)
    return t.includes('true') || t.includes('false') || t === 'tf'
}
function getOptions(q) {
    if (q?.options?.length) {
        if (typeof q.options[0] === 'object')
            return q.options.map(o => o.option_text ?? o.text ?? o.label ?? Object.values(o)[1])
        return q.options
    }
    return []
}
function getAnswerText(q) {
    if (q?.answers?.length) return q.answers[0]?.answer_text ?? null
    if (q?.answer) return q.answer
    return null
}
function getCorrectIndex(q) {
    if (!q?.options?.length) return -1

    return q.options.findIndex(
        option => Number(option.is_correct) === 1
    )
}

const correctCount = computed(() =>
    questions.value.reduce((acc, q, i) => {
        if (isCorrect(q, submittedAnswers.value[i])) {
            return acc + 1
        }
        return acc
    }, 0)
)

const wrongCount = computed(() =>
    questions.value.reduce((acc, q, i) => {
        const a = submittedAnswers.value[i]

        if (a === undefined || a === '') return acc

        if (!isCorrect(q, a)) {
            return acc + 1
        }

        return acc
    }, 0)
)

const skippedCount = computed(() =>
    questions.value.filter((_, i) => {
        const a = submittedAnswers.value[i]
        return a === undefined || a === ''
    }).length
)
const totalMarksScored = computed(() =>
    questions.value.reduce((acc, q, i) => {
        if (isCorrect(q, submittedAnswers.value[i])) {
            return acc + (q.marks ?? 1)
        }

        return acc
    }, 0)
)

const totalMarks = computed(() =>
    questions.value.reduce((s, q) => s + (q.marks ?? 1), 0)
)

const scorePercent = computed(() =>
    totalMarks.value > 0 ? Math.round((totalMarksScored.value / totalMarks.value) * 100) : 0
)

function isCorrect(q, userAnswer) {
    if (userAnswer === undefined || userAnswer === '') {
        return false
    }

    // MCQ
    if (isMcq(q)) {
        const ci = q?.options?.findIndex(
            o => Number(o.is_correct) === 1
        ) ?? -1

        return Number(userAnswer) === ci
    }

    // Essay / short answer
    if (q?.answers?.length) {
        return q.answers.some(a => {
            const correct = a.answer_text?.toString().trim().toLowerCase()
            const user = userAnswer?.toString().trim().toLowerCase()

            return correct === user
        })
    }

    return false
}
</script>

<style scoped>
/* Page slide transitions */
.page-slide-enter-active,
.page-slide-leave-active,
.page-slide-r-enter-active,
.page-slide-r-leave-active {
    transition: all .35s cubic-bezier(.4, 0, .2, 1);
    position: absolute;
    width: 100%;
}
.page-slide-enter-from  { opacity: 0; transform: translateX(-24px); }
.page-slide-leave-to    { opacity: 0; transform: translateX(24px); }
.page-slide-r-enter-from { opacity: 0; transform: translateX(24px); }
.page-slide-r-leave-to  { opacity: 0; transform: translateX(-24px); }
</style>
