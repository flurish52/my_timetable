<script setup>
import {computed} from 'vue'
import {Link} from '@inertiajs/vue3'
import PublishToggle from "@/Components/PastQuestions/Publishtoggle.vue";

const props = defineProps({
    pastQuestion: {type: Object, required: true},
    justCreated: {type: Boolean, default: false},
})

const QUESTION_TYPE_LABELS = {
    objective: 'Objective',
    true_false: 'True / False',
    fill_blank: 'Fill in the blank',
    short_answer: 'Short answer',
    essay: 'Essay',
}

// Flatten every question (grouped + ungrouped) in position order, per section,
// and give the whole paper continuous numbering — Q1, Q2, ... — the way a
// printed exam would read.
const sectionsWithNumbering = computed(() => {
    let counter = 0
    return (props.pastQuestion.sections ?? []).map((section) => {
        // merge ungrouped questions and each group's questions into one ordered
        // list of "blocks" so passages render inline where they belong
        const blocks = [
            ...(section.questions ?? []).map((q) => ({type: 'question', data: q})),
            ...(section.groups ?? []).map((g) => ({type: 'group', data: g})),
        ]

        const numberedBlocks = blocks.map((block) => {
            if (block.type === 'question') {
                counter++
                return {type: 'question', data: block.data, number: counter}
            }
            const questions = (block.data.questions ?? []).map((q) => {
                counter++
                return {...q, number: counter}
            })
            return {type: 'group', data: block.data, questions}
        })

        return {...section, blocks: numberedBlocks}
    })
})

const totalQuestions = computed(() =>
    (props.pastQuestion.sections ?? []).reduce((sum, s) => {
        const grouped = (s.groups ?? []).reduce((gs, g) => gs + (g.questions?.length ?? 0), 0)
        return sum + (s.questions?.length ?? 0) + grouped
    }, 0)
)

const totalMarks = computed(() =>
    (props.pastQuestion.sections ?? []).reduce((sum, s) => {
        const own = (s.questions ?? []).reduce((qs, q) => qs + (Number(q.marks) || 0), 0)
        const grouped = (s.groups ?? []).reduce(
            (gs, g) => gs + (g.questions ?? []).reduce((qs, q) => qs + (Number(q.marks) || 0), 0),
            0
        )
        return sum + own + grouped
    }, 0)
)

function correctOptionText(question) {
    return question.options?.find((o) => o.is_correct)?.option_text
}
</script>

<template>
    <div class="min-h-screen bg-neutral-50">
        <!-- Success banner -->
        <div v-if="justCreated" class="bg-secondary/10 border-b border-secondary/30">
            <div class="max-w-3xl mx-auto px-4 py-3 flex items-center gap-2 text-sm text-secondary">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                          d="M16.7 5.3a1 1 0 010 1.4l-7.4 7.4a1 1 0 01-1.4 0L3.3 9.5a1 1 0 111.4-1.4l3.6 3.6 6.7-6.7a1 1 0 011.4 0z"
                          clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">Saved.</span> Here's how the paper looks.
            </div>
        </div>

        <!-- Header -->
        <div class="bg-white border-b border-neutral-200">

            <div class="max-w-3xl mx-auto px-4 py-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-primary mb-1">
                            {{ pastQuestion.course?.code }} · {{ pastQuestion.semester?.name }} ·
                            {{ pastQuestion.session }}
                        </p>
                        <h1 class="text-xl sm:text-2xl font-bold text-neutral-900">{{ pastQuestion.title }}</h1>
                        <p v-if="pastQuestion.course?.title" class="text-sm text-neutral-500 mt-0.5">
                            {{ pastQuestion.course.title }}
                        </p>
                    </div>
                    <PublishToggle :past-question="pastQuestion"/>
                    <Link :href="route('past-questions.build', pastQuestion.id)"
                          class="shrink-0 text-sm font-medium px-4 py-2 rounded-md border border-neutral-300 text-neutral-600 hover:border-primary hover:text-primary">
                        Edit questions
                    </Link>
                </div>

                <p v-if="pastQuestion.instructions"
                   class="mt-4 text-sm text-neutral-600 bg-neutral-50 border border-neutral-200 rounded-md px-3 py-2">
                    {{ pastQuestion.instructions }}
                </p>

                <div class="flex flex-wrap gap-x-5 gap-y-1 mt-4 text-sm text-neutral-500">
                    <span>{{ totalQuestions }} question{{ totalQuestions === 1 ? '' : 's' }}</span>
                    <span>{{ totalMarks }} marks</span>
                    <span v-if="pastQuestion.duration_minutes">{{ pastQuestion.duration_minutes }} minutes</span>
                    <a v-if="pastQuestion.source_file" :href="`/storage/${pastQuestion.source_file}`" target="_blank"
                       class="text-primary hover:underline">View original file</a>
                </div>
            </div>
        </div>

        <!-- Paper body -->
        <div class="max-w-3xl mx-auto px-4 py-8 space-y-8">
            <div v-if="!pastQuestion.sections?.length" class="text-center py-16 text-neutral-400">
                <p class="text-sm">No questions added yet.</p>
                <Link :href="route('past-questions.build', pastQuestion.id)"
                      class="text-primary text-sm font-medium hover:underline">
                    Add questions
                </Link>
            </div>

            <section v-for="section in sectionsWithNumbering" :key="section.id ?? section.title" class="space-y-4">
                <div class="border-b border-neutral-200 pb-2">
                    <h2 class="text-base font-bold text-neutral-900">{{ section.title }}</h2>
                    <p v-if="section.instructions" class="text-sm text-neutral-500 mt-0.5">{{
                            section.instructions
                        }}</p>
                </div>

                <template v-for="block in section.blocks"
                          :key="block.type === 'question' ? `q-${block.data.id}` : `g-${block.data.id}`">
                    <!-- Ungrouped question -->
                    <article v-if="block.type === 'question'" class="flex gap-3">
            <span
                class="shrink-0 w-7 h-7 rounded-full bg-neutral-100 text-neutral-600 text-xs font-semibold flex items-center justify-center mt-0.5">
              {{ block.number }}
            </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-neutral-800 leading-relaxed">{{ block.data.question_text }}</p>
                            <ul v-if="block.data.options?.length" class="mt-2 space-y-1">
                                <li v-for="o in block.data.options" :key="o.id"
                                    :class="['text-sm rounded-md px-3 py-1.5 border', o.is_correct ? 'border-secondary/40 bg-secondary/5 text-secondary font-medium' : 'border-neutral-200 text-neutral-600']">
                                    {{ o.option_text }}
                                </li>
                            </ul>
                            <p v-else-if="block.data.answers?.[0]?.answer_text" class="text-sm text-secondary mt-2">
                                <span class="font-medium">Answer:</span> {{ block.data.answers[0].answer_text }}
                            </p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-xs text-neutral-400">{{
                                        QUESTION_TYPE_LABELS[block.data.question_type]
                                    }}</span>
                                <span class="text-xs text-neutral-300">·</span>
                                <span class="text-xs text-neutral-400">{{
                                        block.data.marks
                                    }} mark{{ block.data.marks === 1 ? '' : 's' }}</span>
                            </div>
                        </div>
                    </article>

                    <!-- Passage group -->
                    <div v-else class="rounded-lg bg-secondary/5 border border-secondary/20 p-4 space-y-4">
                        <div v-if="block.data.title || block.data.content">
                            <p v-if="block.data.title" class="text-sm font-semibold text-neutral-800">
                                {{ block.data.title }}</p>
                            <p v-if="block.data.content"
                               class="text-sm text-neutral-600 mt-1 leading-relaxed whitespace-pre-line">
                                {{ block.data.content }}
                            </p>
                        </div>

                        <article v-for="q in block.questions" :key="q.id" class="flex gap-3">
              <span
                  class="shrink-0 w-7 h-7 rounded-full bg-white text-neutral-600 text-xs font-semibold flex items-center justify-center mt-0.5">
                {{ q.number }}
              </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-neutral-800 leading-relaxed">{{ q.question_text }}</p>
                                <ul v-if="q.options?.length" class="mt-2 space-y-1">
                                    <li v-for="o in q.options" :key="o.id"
                                        :class="['text-sm rounded-md px-3 py-1.5 border bg-white', o.is_correct ? 'border-secondary/40 text-secondary font-medium' : 'border-neutral-200 text-neutral-600']">
                                        {{ o.option_text }}
                                    </li>
                                </ul>
                                <p v-else-if="q.answers?.[0]?.answer_text" class="text-sm text-secondary mt-2">
                                    <span class="font-medium">Answer:</span> {{ q.answers[0].answer_text }}
                                </p>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span class="text-xs text-neutral-400">{{
                                            QUESTION_TYPE_LABELS[q.question_type]
                                        }}</span>
                                    <span class="text-xs text-neutral-300">·</span>
                                    <span class="text-xs text-neutral-400">{{ q.marks }} mark{{
                                            q.marks === 1 ? '' : 's'
                                        }}</span>
                                </div>
                            </div>
                        </article>
                    </div>
                </template>
            </section>
        </div>
    </div>
</template>
