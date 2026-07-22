<script setup>
import { reactive, ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import QuestionCard from '@/Components/PastQuestions/QuestionCard.vue'

const props = defineProps({
    pastQuestion: { type: Object, required: true },
})

let tempId = -1
const nextTempId = () => tempId--

function blankOption() {
    return { id: null, option_text: '', is_correct: false }
}
function blankQuestion() {
    return {
        _key: nextTempId(),
        id: null,
        question_type: 'objective',
        question_text: '',
        marks: 1,
        options: [blankOption(), blankOption()],
        answer_text: '',
        expanded: true,
    }
}
function blankGroup() {
    return { _key: nextTempId(), id: null, title: '', content: '', questions: [blankQuestion()] }
}
function blankSection() {
    return {
        _key: nextTempId(),
        id: null,
        title: '',
        instructions: '',
        questions: [],
        groups: [],
        expanded: true,
    }
}

function hydrateQuestion(q) {
    return {
        _key: nextTempId(),
        id: q.id,
        question_type: q.question_type,
        question_text: q.question_text,
        marks: q.marks,
        options: (q.options ?? []).map((o) => ({ id: o.id, option_text: o.option_text, is_correct: !!o.is_correct })),
        answer_text: q.answers?.[0]?.answer_text ?? '',
        expanded: false,
    }
}

function hydrate() {
    const src = props.pastQuestion.sections ?? []
    if (src.length === 0) return [blankSection()]
    return src.map((s, i) => ({
        _key: nextTempId(),
        id: s.id,
        title: s.title,
        instructions: s.instructions ?? '',
        questions: (s.questions ?? []).map(hydrateQuestion),
        groups: (s.groups ?? []).map((g) => ({
            _key: nextTempId(),
            id: g.id,
            title: g.title ?? '',
            content: g.content ?? '',
            questions: (g.questions ?? []).map(hydrateQuestion),
        })),
        expanded: i === 0,
    }))
}

const sections = reactive(hydrate())
const deletedSectionIds = ref([])
const deletedGroupIds = ref([])
const deletedQuestionIds = ref([])
const errors = ref({})
const saving = ref(false)

// ---- derived summary info, used in the nav rail + sticky bar ----
const sectionStats = computed(() =>
    sections.map((s) => {
        const grouped = s.groups.flatMap((g) => g.questions)
        const all = [...s.questions, ...grouped]
        return { count: all.length, marks: all.reduce((sum, q) => sum + (Number(q.marks) || 0), 0) }
    })
)
const totalQuestions = computed(() => sectionStats.value.reduce((sum, s) => sum + s.count, 0))
const totalMarks = computed(() => sectionStats.value.reduce((sum, s) => sum + s.marks, 0))

function scrollToSection(key) {
    document.getElementById(`section-${key}`)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

// ---- mutations ----
function addSection() {
    sections.forEach((s) => (s.expanded = false))
    sections.push({ ...blankSection(), expanded: true })
}

function removeSection(index) {
    const s = sections[index]
    if (s.id) deletedSectionIds.value.push(s.id)
    s.groups.forEach((g) => {
        if (g.id) deletedGroupIds.value.push(g.id)
        g.questions.forEach((q) => q.id && deletedQuestionIds.value.push(q.id))
    })
    s.questions.forEach((q) => q.id && deletedQuestionIds.value.push(q.id))
    sections.splice(index, 1)
}

function addQuestion(section) {
    section.questions.push(blankQuestion())
}
function addGroup(section) {
    section.groups.push(blankGroup())
}
function removeQuestionFrom(list, index) {
    const q = list[index]
    if (q.id) deletedQuestionIds.value.push(q.id)
    list.splice(index, 1)
}
function removeGroup(section, index) {
    const g = section.groups[index]
    if (g.id) deletedGroupIds.value.push(g.id)
    g.questions.forEach((q) => q.id && deletedQuestionIds.value.push(q.id))
    section.groups.splice(index, 1)
}

function needsOptions(type) {
    return type === 'objective' || type === 'true_false'
}

// ---- serialize + save ----
function serializeQuestion(q, qi) {
    return {
        id: q.id,
        question_type: q.question_type,
        question_text: q.question_text,
        marks: Number(q.marks) || 1,
        position: qi + 1,
        options: needsOptions(q.question_type)
            ? q.options.filter((o) => o.option_text.trim() !== '').map((o) => ({ id: o.id, option_text: o.option_text, is_correct: !!o.is_correct }))
            : [],
        answer_text: !needsOptions(q.question_type) ? (q.answer_text || null) : null,
    }
}

function serializeSections() {
    return sections.map((s, si) => ({
        id: s.id,
        title: s.title,
        instructions: s.instructions || null,
        position: si + 1,
        questions: s.questions.map(serializeQuestion),
        groups: s.groups.map((g, gi) => ({
            id: g.id,
            title: g.title || null,
            content: g.content || null,
            position: gi + 1,
            questions: g.questions.map(serializeQuestion),
        })),
    }))
}

function save() {
    saving.value = true
    errors.value = {}
    router.post(
        route('past-questions.questions.store', props.pastQuestion.id),
        {
            sections: serializeSections(),
            deletedSectionIds: deletedSectionIds.value,
            deletedGroupIds: deletedGroupIds.value,
            deletedQuestionIds: deletedQuestionIds.value,
        },
        {
            onError: (e) => { errors.value = e },
            onFinish: () => { saving.value = false },
        }
    )
}
</script>

<template>
    <div class="min-h-screen bg-neutral-50">
        <!-- Top bar -->
        <div class="sticky top-0 z-20 bg-white border-b border-neutral-200">
            <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-primary mb-0.5">Step 2 of 2 · Questions</p>
                    <h1 class="text-lg font-bold text-neutral-900 truncate">{{ pastQuestion.title }}</h1>
                </div>
                <div class="hidden sm:flex items-center gap-4 text-sm text-neutral-500 shrink-0">
                    <span>{{ totalQuestions }} question{{ totalQuestions === 1 ? '' : 's' }}</span>
                    <span class="w-1 h-1 rounded-full bg-neutral-300" />
                    <span>{{ totalMarks }} marks</span>
                </div>
            </div>

            <!-- Section pill nav — horizontal scroll on mobile, wraps on desktop -->
            <div class="max-w-5xl mx-auto px-4 pb-3 flex gap-2 overflow-x-auto">
                <button
                    v-for="(s, i) in sections" :key="s._key" @click="scrollToSection(s._key)"
                    class="shrink-0 flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full border border-neutral-200 text-neutral-600 hover:border-primary hover:text-primary whitespace-nowrap"
                >
                    {{ s.title || `Section ${i + 1}` }}
                    <span class="text-neutral-400">· {{ sectionStats[i]?.count ?? 0 }}</span>
                </button>
                <button @click="addSection"
                        class="shrink-0 text-xs font-medium px-3 py-1.5 rounded-full border border-dashed border-neutral-300 text-neutral-500 hover:border-primary hover:text-primary whitespace-nowrap">
                    + Add section
                </button>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 py-6 pb-28">
            <!-- Import bar -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6 p-3 rounded-lg bg-tertiary/10 border border-tertiary/30">
                <p class="text-sm text-neutral-600 flex-1">
                    Have questions elsewhere? Paste them in, or upload an Excel, Word, or PDF file.
                </p>
                <Link :href="route('past-questions.import', pastQuestion.id)"
                      class="shrink-0 text-sm px-4 py-1.5 rounded-md bg-tertiary text-white font-medium text-center">
                    Import questions
                </Link>
            </div>

            <!-- Validation summary -->
            <div v-if="Object.keys(errors).length" class="mb-6 p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                <p class="font-medium mb-1">Fix the following before saving:</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    <li v-for="(msg, key) in errors" :key="key">{{ msg }}</li>
                </ul>
            </div>

            <!-- Sections -->
            <div v-for="(section, si) in sections" :id="`section-${section._key}`" :key="section._key"
                 class="scroll-mt-32 rounded-xl border border-neutral-200 bg-white mb-5 overflow-hidden">
                <div class="flex items-start gap-3 px-4 py-4 sm:px-5">
                    <button type="button" @click="section.expanded = !section.expanded"
                            class="mt-1.5 shrink-0 text-neutral-400 hover:text-neutral-600">
                        <svg :class="['w-4 h-4 transition-transform', section.expanded && 'rotate-180']" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.65a.75.75 0 01-1.08 0l-4.25-4.65a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="flex-1 min-w-0 space-y-2">
                        <input v-model="section.title" type="text" placeholder="Section title — e.g. Section A"
                               class="w-full text-base font-semibold text-neutral-900 placeholder:font-normal placeholder:text-neutral-400 outline-none" />
                        <input v-model="section.instructions" type="text" placeholder="Instructions for this section (optional)"
                               class="w-full text-sm text-neutral-500 outline-none" />
                    </div>
                    <span class="shrink-0 hidden sm:inline text-xs text-neutral-400 mt-1.5">
            {{ sectionStats[si]?.count ?? 0 }} q · {{ sectionStats[si]?.marks ?? 0 }} mk
          </span>
                    <button @click="removeSection(si)" class="shrink-0 text-neutral-400 hover:text-red-500 text-xs mt-1.5">
                        Remove
                    </button>
                </div>

                <div v-if="section.expanded" class="px-4 sm:px-5 pb-5 space-y-3 border-t border-neutral-100 pt-4">
                    <QuestionCard
                        v-for="(q, qi) in section.questions" :key="q._key"
                        v-model="section.questions[qi]" :number="qi + 1"
                        @remove="removeQuestionFrom(section.questions, qi)"
                    />

                    <button @click="addQuestion(section)"
                            class="text-sm font-medium text-primary hover:underline">
                        + Add question
                    </button>

                    <!-- Passage groups -->
                    <div v-for="(group, gi) in section.groups" :key="group._key"
                         class="rounded-lg bg-secondary/5 border border-secondary/20 p-4 space-y-3">
                        <div class="flex items-start gap-3">
              <span class="shrink-0 text-xs font-medium text-secondary bg-secondary/10 rounded-full px-2 py-1 mt-0.5">
                Shared passage
              </span>
                            <div class="flex-1 min-w-0 space-y-2">
                                <input v-model="group.title" type="text" placeholder="Passage title (optional)"
                                       class="w-full text-sm font-medium text-neutral-800 outline-none bg-transparent" />
                                <textarea v-model="group.content" rows="2" placeholder="Paste the shared passage or content here"
                                          class="w-full text-sm border border-neutral-200 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-secondary/40" />
                            </div>
                            <button @click="removeGroup(section, gi)" class="shrink-0 text-neutral-400 hover:text-red-500 text-xs mt-1">
                                Remove
                            </button>
                        </div>

                        <QuestionCard
                            v-for="(q, qi) in group.questions" :key="q._key"
                            v-model="group.questions[qi]" :number="qi + 1"
                            @remove="removeQuestionFrom(group.questions, qi)"
                        />

                        <button @click="group.questions.push(blankQuestion())" class="text-xs font-medium text-secondary hover:underline">
                            + Add question to this passage
                        </button>
                    </div>

                    <button @click="addGroup(section)"
                            class="text-sm text-neutral-500 hover:text-secondary">
                        + Add a shared passage / comprehension group
                    </button>
                </div>
            </div>

            <button @click="addSection"
                    class="w-full py-3 rounded-xl border-2 border-dashed border-neutral-300 text-sm font-medium text-neutral-500 hover:border-primary hover:text-primary">
                + Add another section
            </button>
        </div>
        <!-- Sticky save bar -->
        <div class="fixed inset-x-0 bottom-24 z-20 bg-white/95 border-t border-primary">
            <div class="max-w-2xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
        <span class="text-sm text-neutral-500">
          {{ totalQuestions }} question{{ totalQuestions === 1 ? '' : 's' }} · {{ totalMarks }} marks total
        </span>
                <button @click="save" :disabled="saving"
                        class="bg-primary text-white px-6 py-2.5 rounded-md font-medium text-sm disabled:opacity-50 hover:opacity-90">
                    {{ saving ? 'Saving…' : 'Save questions' }}
                </button>
            </div>
        </div>
    </div>
</template>
