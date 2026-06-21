<template>
    <div class="fixed inset-0 z-[60] flex flex-col bg-gray-50">

        <!-- Header lives outside the scroll area — structurally it can never scroll -->
        <ExamHeader
            :course-code="past_question.course.code"
            :course-title="past_question.course.title"
            :time-left="timeLeft"
            @submit="openSubmitModal"
        />

        <!-- Everything else scrolls together as one unit -->
        <div ref="mainScroll" class="flex-1 overflow-y-auto overscroll-contain">

            <ExamProgress :percent="progressPercent" />

            <div class="flex">
                <div class="flex-1 min-w-0">
                    <div class="max-w-[680px] mx-auto px-4 sm:px-8 py-5 flex flex-col gap-4 pb-6">

                        <QuestionCard
                            :index="currentIndex"
                            :total="total"
                            :marks="currentQuestion.marks ?? 1"
                            :question-text="currentQuestion.question_text"
                            :question-type="getQuestionType(currentQuestion)"
                            :section="currentSection"
                        />

                        <AnswerInput
                            :question-type="getQuestionType(currentQuestion)"
                            :options="getOptions(currentQuestion)"
                            :model-value="answers[currentIndex]"
                            @answer="selectAnswer"
                        />

                        <NavRow
                            :current-index="currentIndex"
                            :total="total"
                            :is-first="currentIndex === 0"
                            :is-last="currentIndex === total - 1"
                            @prev="prev"
                            @next="next"
                            @finish="openSubmitModal"
                        />

                    </div>
                </div>

            </div>

            <QuestionGrid
                :total="total"
                :current-index="currentIndex"
                :answered-count="answeredCount"
                :is-answered="isAnswered"
                @jump="jumpTo"
            />

        </div>

        <SubmitModal
            :open="showModal"
            :answered-count="answeredCount"
            :total="total"
            :unanswered-count="unansweredCount"
            :submitting="submitting"
            :timeLeft="timeLeft"
            @close="closeModal"
            @confirm="submitExam"
        />

    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import ExamHeader   from './ExamHeader.vue'
import ExamProgress from './ExamProgress.vue'
import QuestionCard from './QuestionCard.vue'
import AnswerInput  from './AnswerInput.vue'
import NavRow       from './NavRow.vue'
import QuestionGrid from './QuestionGrid.vue'
import SubmitModal  from './SubmitModal.vue'

const props = defineProps({
    past_question: Object,
})

const emit = defineEmits(['submitted'])

/* ── State ── */
const currentIndex = ref(0)
const answers      = ref({})
const showModal    = ref(false)
const submitting   = ref(false)
const isMobile     = ref(window.innerWidth < 1024)
const mainScroll   = ref(null)
const timeLeft     = ref((props.past_question.duration_minutes ?? 120) * 60)

let timer = null

/* ── Computed ── */
const total            = computed(() => props.past_question.questions.length)
const currentQuestion  = computed(() => props.past_question.questions[currentIndex.value])
const answeredCount    = computed(() => Object.keys(answers.value).length)
const unansweredCount  = computed(() => total.value - answeredCount.value)
const progressPercent  = computed(() => Math.round((answeredCount.value / total.value) * 100))

const currentSection = computed(() => {
    const sid = currentQuestion.value?.question_section_id
    return props.past_question.sections?.find(s => s.id === sid) ?? null
})

const currentTip = computed(() => getAnswerText(currentQuestion.value))

/* ── Question helpers ── */
function getQuestionType(q) {
    return (q.type || q.question_type || 'objective').toLowerCase()
}

function getOptions(q) {
    if (q.options?.length) {
        if (typeof q.options[0] === 'object')
            return q.options.map(o => o.option_text ?? o.text ?? o.label ?? Object.values(o)[1])
        return q.options
    }
    const t = getQuestionType(q)
    if (t.includes('objective') || t.includes('mcq') || t.includes('multiple'))
        return ['Option A', 'Option B', 'Option C', 'Option D']
    return []
}

function getAnswerText(q) {
    if (q?.answers?.length) return q.answers[0]?.answer_text ?? null
    if (q?.answer) return q.answer
    return null
}

/* ── Navigation ── */
function selectAnswer(val) {
    answers.value = { ...answers.value, [currentIndex.value]: val }
}
function isAnswered(i) {
    const v = answers.value[i]
    return v !== undefined && v !== ''
}
function next() {
    if (currentIndex.value < total.value - 1) {
        currentIndex.value++
        mainScroll.value?.scrollTo({ top: 0, behavior: 'smooth' })
    }
}
function prev() {
    if (currentIndex.value > 0) {
        currentIndex.value--
        mainScroll.value?.scrollTo({ top: 0, behavior: 'smooth' })
    }
}
function jumpTo(i) {
    currentIndex.value = i
    mainScroll.value?.scrollTo({ top: 0, behavior: 'smooth' })
}

/* ── Modal ── */
function openSubmitModal() { showModal.value = true }
function closeModal()      { showModal.value = false }

/* ── Submit ── */
/* ── Submit ── */
async function submitExam() {
    submitting.value = true
    clearInterval(timer)

    const questions = props.past_question.questions
    const timeTaken = (props.past_question.duration_minutes ?? 120) * 60 - timeLeft.value

    // Build per-answer payload for question_attempt_answers
    const answerPayload = Object.entries(answers.value).map(([indexStr, selectedValue]) => {
        const index    = Number(indexStr)
        const question = questions[index]
        if (!question) return null

        const questionType = getQuestionType(question)
        const isObjective  = questionType.includes('objective') || questionType.includes('mcq') || questionType.includes('multiple')

        // Try to resolve option id if options are objects with an id
        let questionOptionId = null
        let answerText       = null

        if (isObjective) {
            const rawOptions = question.options ?? []
            if (rawOptions.length && typeof rawOptions[0] === 'object') {
                // options are objects — match by resolved text or by index
                const resolvedOptions = getOptions(question) // array of strings
                const matchIndex      = resolvedOptions.indexOf(selectedValue)
                const matched         = matchIndex !== -1 ? rawOptions[matchIndex] : null
                questionOptionId      = matched?.id ?? null
            }
            answerText = String(selectedValue)
        } else {
            // theory / essay
            answerText = String(selectedValue ?? '')
        }

        // Basic correctness check (only works if answer data is available)
        const correctAnswer = getAnswerText(question)
        const isCorrect     = correctAnswer !== null
            ? answerText.trim().toLowerCase() === String(correctAnswer).trim().toLowerCase()
            : null

        return {
            question_id:        question.id ?? null,
            question_option_id: questionOptionId,
            answer_text:        answerText,
            is_correct:         isCorrect,
        }
    }).filter(Boolean)

    // Score computation (marks-aware)
    const correct = answerPayload.filter(a => a.is_correct === true)
    const score   = correct.reduce((sum, a, _, arr) => {
        // find marks for this question if available
        const idx = questions.findIndex(q => q.id === a.question_id)
        return sum + (questions[idx]?.marks ?? 1)
    }, 0)

    try {
        await fetch('/practice/submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
            body: JSON.stringify({
                // question_attempts fields
                past_question_id: props.past_question.id,
                score,
                total_attempted:  answerPayload.length,
                correct_answers:  correct.length,
                time_taken:       timeTaken,   // seconds — backend derives submitted_at

                // question_attempt_answers rows (send even if you won't seed yet)
                answers: answerPayload,
            }),
        })
    } catch { /* show results locally even on network error */ }

    showModal.value  = false
    submitting.value = false
    emit('submitted', answers.value)
}

/* ── Timer ── */
function startTimer() {
    clearInterval(timer)
    timer = setInterval(() => {
        if (timeLeft.value > 0) timeLeft.value--
        else { clearInterval(timer); submitExam() }
    }, 1000)
}

/* ── Lifecycle ── */
onMounted(() => {
    startTimer()
    const onResize = () => { isMobile.value = window.innerWidth < 1024 }
    window.addEventListener('resize', onResize)
    onUnmounted(() => {
        clearInterval(timer)
        window.removeEventListener('resize', onResize)
    })
})

/* Expose retake helper to parent */
defineExpose({
    retake() {
        answers.value     = {}
        currentIndex.value = 0
        timeLeft.value    = (props.past_question.duration_minutes ?? 120) * 60
        startTimer()
    },
})
</script>
