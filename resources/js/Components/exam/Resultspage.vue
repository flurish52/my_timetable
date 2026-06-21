<template>
    <div class="min-h-dvh flex flex-col bg-gray-50">
        <!-- ── Hero Score Band ── -->
        <div class="flex justify-center px-5 py-10" :class="heroBg">
            <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-8 w-full max-w-xl">

                <!-- SVG Ring -->
                <div class="relative w-[120px] h-[120px] flex-shrink-0">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="rgba(255,255,255,.18)" stroke-width="8"/>
                        <circle
                            cx="60" cy="60" r="52" fill="none"
                            stroke="white" stroke-width="8"
                            stroke-linecap="round"
                            :stroke-dasharray="`${scorePercent * 3.267} 326.7`"
                            stroke-dashoffset="81.68"
                            style="transition: stroke-dasharray 1.2s cubic-bezier(.4,0,.2,1)"
                        />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-white">
                        <span class="text-[26px] font-black leading-none">{{ scorePercent }}%</span>
                        <span class="text-[10px] font-bold opacity-80 uppercase tracking-wide">{{ grade.label }}</span>
                    </div>
                </div>

                <!-- Meta -->
                <div class="text-white text-center sm:text-left">
                    <h1 class="text-[22px] font-black m-0 mb-1 drop-shadow-md">
                        {{ grade.title }}
                    </h1>

                    <p class="text-[12px] m-0 drop-shadow-md">
                        {{ grade.message }}
                    </p>

                    <p class="text-[12px] m-0 drop-shadow-md font-semibold">
                        {{ grade.action }}
                    </p>
                    <p class="text-[13px] opacity-85 m-0 mb-3.5">{{ courseCode }} · {{ courseTitle }}</p>
                    <div class="flex flex-wrap gap-2 justify-center sm:justify-start">
            <span class="px-3 py-1 rounded-full text-[12px] font-bold bg-white/30 text-white">
              <b>{{ correctCount }}</b> Correct
            </span>
                        <span class="px-3 py-1 rounded-full text-[12px] font-bold bg-white/15 text-white/90">
              <b>{{ wrongCount }}</b> Wrong
            </span>
                        <span class="px-3 py-1 rounded-full text-[12px] font-bold bg-white/15 text-white/90">
              <b>{{ skippedCount }}</b> Skipped
            </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Stats Row ── -->
        <div class="flex bg-white border-b border-gray-200">
            <div v-for="stat in stats" :key="stat.label" class="flex-1 flex flex-col items-center py-4 gap-0.5 border-r border-gray-200 last:border-r-0">
                <span class="text-[22px] font-black leading-none" :class="stat.color">{{ stat.value }}</span>
                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">{{ stat.label }}</span>
            </div>
        </div>
        <div class="mx-auto my-6 max-w-2xl rounded-2xl border border-primary/20 bg-primary/10 p-5 mx-3 ">
            <div class="flex gap-3">
                <div>
                    <p class="font-semibold text-primary/90">
                        Essay Marking Notice
                    </p>

                    <p class="mt-2 text-sm text-gray-500 leading-6">
                        Your score currently reflects objective questions only. Essay
                        grading is still under development and may not be fully
                        accurate. We are working on improving essay assessments in
                        future updates.
                    </p>
                </div>
            </div>
        </div>

        <!-- ── Corrections ── -->
        <div class="flex-1 max-w-3xl w-full mx-auto px-4 sm:px-6 py-6 box-border">
            <h2 class="text-[15px] font-extrabold text-gray-900 mb-4 m-0">Review &amp; Corrections</h2>

            <div class="flex flex-col gap-3.5">
                <div
                    v-for="(q, i) in questions"
                    :key="q.id ?? i"
                    class="bg-white rounded-2xl border-[1.5px] p-4 sm:p-5 shadow-sm"
                    :class="cardBorder(i)"
                >

                    <!-- Card header -->
                    <div class="flex items-center flex-wrap gap-2 mb-3">
            <span class="text-[10px] font-extrabold bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">
              Q{{ i + 1 }}
            </span>
                        <span class="text-[10px] text-gray-400 flex-1">{{ getSectionTitle(q) }}</span>
                        <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full" :class="statusBadgeClass(i)">
              <template v-if="resultClass(i) === 'correct'">✓ Correct</template>
              <template v-else-if="resultClass(i) === 'wrong'">✗ Wrong</template>
              <template v-else-if="resultClass(i) === 'written'">✍ Written</template>
              <template v-else>— Skipped</template>
            </span>
                        <span class="text-[10px] bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full">
              {{ q.marks ?? 1 }} mk
            </span>
                    </div>

                    <!-- Question text -->
                    <RichText :content="q.question_text" class="text-[14px] font-medium text-gray-900 leading-relaxed block mb-3" />

                    <!-- MCQ options -->
                    <template v-if="isMcq(q)">
                        <div class="flex flex-col gap-2">
                            <div
                                v-for="(opt, oi) in getOptions(q)"
                                :key="oi"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-[10px] border-[1.5px] text-[13px]"
                                :class="optionClass(q, i, oi)"
                            >
                <span
                    class="w-6 h-6 rounded-[7px] flex items-center justify-center text-[10px] font-extrabold flex-shrink-0"
                    :class="optionLetterClass(q, i, oi)"
                >{{ LETTERS[oi] }}</span>
                                <RichText :content="String(opt)" class="flex-1" />
                                <span v-if="oi === correctIndex(q)" class="text-emerald-600 font-bold text-sm">✓</span>
                                <span v-else-if="answers[i] === oi" class="text-red-500 font-bold text-sm">✗</span>
                            </div>
                        </div>
                    </template>

                    <!-- Essay / written answer -->
                    <template v-else>
                        <div
                            v-if="answers[i] !== undefined && answers[i] !== ''"
                            class="bg-gray-50 border-[1.5px] border-gray-200 rounded-xl p-3 mb-1"
                        >
                            <span class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-wider mb-1.5">Your answer</span>
                            <RichText :content="String(answers[i])" class="text-[13px] text-gray-800 leading-relaxed" />
                        </div>
                        <div v-else class="text-[13px] text-gray-400 italic py-1">No answer provided</div>
                    </template>

                    <!-- Tip / Explanation -->
                    <div v-if="getAnswerText(q)" class="flex gap-2.5 mt-3 bg-amber-50 border-[1.5px] border-amber-200 rounded-xl p-3 items-start">
                        <span class="text-base flex-shrink-0">💡</span>
                        <div>
                            <span class="block text-[10px] font-extrabold text-amber-800 uppercase tracking-wider mb-1">Answer / Explanation</span>
                            <RichText :content="getAnswerText(q)" class="text-[12px] text-amber-900 leading-relaxed" />
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ── Footer CTA ── -->
        <div class="max-w-3xl w-full mx-auto px-4 sm:px-6 pb-8 pt-2 box-border flex flex-col sm:flex-row gap-3">
            <button
                @click="$emit('retake')"
                class="flex-1 py-3.5 rounded-xl border-[1.5px] border-gray-200 bg-white text-[14px] font-bold text-gray-800 cursor-pointer hover:opacity-75 transition-opacity"
            >
                Retake Exam
            </button>
            <a
                href="/dashboard"
                class="flex-[1.4] py-3.5 rounded-xl bg-primary hover:bg-primary/50 text-white text-[14px] font-extrabold text-center flex items-center justify-center no-underline transition-colors"
            >
                Back to Dashboard
            </a>
        </div>

    </div>
</template>

<script setup>
import { computed } from 'vue'
import RichText from './RichText.vue'

const LETTERS = ['A', 'B', 'C', 'D', 'E']

const props = defineProps({
    questions: Array,
    sections: { type: Array, default: () => [] },
    answers: Object,
    courseCode: String,
    courseTitle: String,
    scorePercent: Number,
    correctCount: Number,
    wrongCount: Number,
    skippedCount: Number,
    totalMarksScored: Number,
    totalMarks: Number,
})

defineEmits(['retake'])

/* ── Grade ── */
const grade = computed(() => {
    const p = props.scorePercent

    if (p >= 90) {
        return {
            label: 'Top Performer',
            title: 'Excellent work',
            message: 'You performed better than 90% of your course mates',
            action: 'You are close to mastery. Retake to stay sharp and maintain your level.'
        }
    }

    if (p >= 80) {
        return {
            label: 'High Performer',
            title: 'Great job',
            message: 'You performed better than 80% of your course mates',
            action: 'Push higher. Retake and aim for top 10%.'
        }
    }

    if (p >= 70) {
        return {
            label: 'Above Average',
            title: 'Well done',
            message: 'You performed better than 70% of your course mates',
            action: 'Keep going. One more attempt can move you into top performance.'
        }
    }

    if (p >= 60) {
        return {
            label: 'Average+',
            title: 'Good effort',
            message: 'You performed better than 60% of your course mates',
            action: 'You are improving. Retake to strengthen weak areas.'
        }
    }

    if (p >= 50) {
        return {
            label: 'Average',
            title: 'Almost there',
            message: 'You performed better than 50% of your course mates',
            action: 'Don’t stop now. Another try will move you ahead of many.'
        }
    }

    return {
        label: 'Needs Improvement',
        title: 'Keep practising',
        message: 'You are behind most course mates',
        action: 'This is where growth starts. Retake until you break through.'
    }
})

const heroBg = computed(() => {
    const p = props.scorePercent

    if (p >= 70)
        return 'bg-gradient-to-br from-emerald-600 to-emerald-400'

    if (p >= 50)
        return 'bg-gradient-to-br from-amber-500 to-amber-400'

    return 'bg-gradient-to-br from-red-600 to-red-400'
})

const stats = computed(() => [
    {
        value: props.correctCount,
        label: 'Correct',
        color: 'text-emerald-600'
    },
    {
        value: props.wrongCount,
        label: 'Wrong',
        color: 'text-red-500'
    },
    {
        value: props.skippedCount,
        label: 'Skipped',
        color: 'text-gray-400'
    },
    {
        value: `${props.totalMarksScored}/${props.totalMarks}`,
        label: 'Marks',
        color: 'text-primary'
    }
])

/* ── Helpers ── */

function getQuestionType(q) {
    return (q.type || q.question_type || 'objective').toLowerCase()
}

function isMcq(q) {
    const t = getQuestionType(q)

    return (
        t.includes('objective') ||
        t.includes('mcq') ||
        t.includes('multiple')
    )
}

function isTrueFalse(q) {
    const t = getQuestionType(q)

    return (
        t.includes('true') ||
        t.includes('false') ||
        t === 'tf'
    )
}

function getOptions(q) {
    if (q.options?.length) {
        if (typeof q.options[0] === 'object') {
            return q.options.map(
                o =>
                    o.option_text ??
                    o.text ??
                    o.label ??
                    Object.values(o)[1]
            )
        }

        return q.options
    }

    if (isMcq(q)) {
        return [
            'Option A',
            'Option B',
            'Option C',
            'Option D'
        ]
    }

    return []
}

function getAnswerText(q) {
    if (q.answers?.length) {
        return q.answers[0]?.answer_text || null
    }

    if (q.answer) {
        return q.answers.answer_text
    }

    return null
}

function correctIndex(q) {
    if (!q.options?.length) {
        return -1
    }

    return q.options.findIndex(
        option => Number(option.is_correct) === 1
    )
}

function getSectionTitle(q) {
    const s = props.sections?.find(
        s => s.id === q.question_section_id
    )

    return s?.title || ''
}

function resultClass(i) {
    const q = props.questions[i]
    const userAnswer = props.answers[i]

    if (userAnswer === undefined || userAnswer === '') {
        return 'skipped'
    }

    if (isMcq(q)) {
        const ci = correctIndex(q)

        return userAnswer === ci
            ? 'correct'
            : 'wrong'
    }

    if (isTrueFalse(q)) {
        const answer = getAnswerText(q)

        if (!answer) {
            return 'written'
        }

        return userAnswer.toString().trim().toLowerCase() ===
        answer.toString().trim().toLowerCase()
            ? 'correct'
            : 'wrong'
    }

    return 'written'
}

/* ── Classes ── */

function cardBorder(i) {
    const r = resultClass(i)

    if (r === 'correct')
        return 'border-emerald-300'

    if (r === 'wrong')
        return 'border-red-300'

    if (r === 'written')
        return 'border-amber-200'

    return 'border-gray-200 opacity-75'
}

function statusBadgeClass(i) {
    const r = resultClass(i)

    if (r === 'correct')
        return 'bg-emerald-100 text-emerald-700'

    if (r === 'wrong')
        return 'bg-red-100 text-red-700'

    if (r === 'written')
        return 'bg-amber-100 text-amber-700'

    return 'bg-gray-100 text-gray-500'
}

function optionClass(q, i, oi) {
    const ci = correctIndex(q)

    if (oi === ci) {
        return 'bg-emerald-50 border-emerald-300'
    }

    if (props.answers[i] === oi && oi !== ci) {
        return 'bg-red-50 border-red-300'
    }

    return 'bg-gray-50 border-gray-100'
}

function optionLetterClass(q, i, oi) {
    const ci = correctIndex(q)

    if (oi === ci) {
        return 'bg-emerald-200 text-emerald-800'
    }

    if (props.answers[i] === oi && oi !== ci) {
        return 'bg-red-200 text-red-800'
    }

    return 'bg-gray-200 text-gray-500'
}


</script>
