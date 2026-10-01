<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

const props = defineProps({
    past_question: { type: Object, required: true },
})

const questions = computed(() => props.past_question.questions ?? [])
const lowCount = computed(() => questions.value.filter((q) => q.answer_confidence === 'low').length)

const typeLabels = {
    objective: 'Multiple choice',
    true_false: 'True / False',
    short_answer: 'Short answer',
}

const editingId = ref(null)
const draft = ref(null)
const saving = ref(false)
const errors = ref({})

const isCorrect = (o) => Number(o.is_correct) === 1

function startEdit(q) {
    errors.value = {}
    editingId.value = q.id
    draft.value = {
        question_text: q.question_text,
        explanation: q.explanation ?? '',
        options: (q.options ?? []).map((o) => ({ id: o.id, option_text: o.option_text })),
        correct_option_id: q.options?.find(isCorrect)?.id ?? null,
        answer_text: q.answers?.[0]?.answer_text ?? '',
    }
}

function cancelEdit() {
    editingId.value = null
    draft.value = null
    errors.value = {}
}

function save(q) {
    saving.value = true
    errors.value = {}

    router.patch(route('scan.notes.questions.update', q.id), draft.value, {
        preserveScroll: true,
        onSuccess: () => cancelEdit(),
        onError: (e) => (errors.value = e),
        onFinish: () => (saving.value = false),
    })
}

function remove(q) {
    if (!confirm('Delete this question?')) return
    errors.value = {}

    router.delete(route('scan.notes.questions.destroy', q.id), {
        preserveScroll: true,
        onError: (e) => (errors.value = e),
    })
}
</script>

<template>
    <Head :title="past_question.title" />

    <div class="min-h-screen bg-slate-50">
        <div class="mx-auto max-w-2xl px-4 py-8 sm:py-12">
            <!-- Header -->
            <div class="mb-6">
                <Link :href="route('scan.notes.create')" class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M15 18l-6-6 6-6" /></svg>
                    Scan more notes
                </Link>

                <h1 class="text-lg font-semibold text-slate-900">{{ past_question.title }}</h1>
                <p class="mt-0.5 text-sm text-slate-500">
                    {{ questions.length }} question{{ questions.length === 1 ? '' : 's' }} generated from your notes
                    <template v-if="past_question.course"> · {{ past_question.course.code }}</template>
                </p>
            </div>

            <!-- Accuracy notice -->
            <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                Answers are AI-generated from your notes. Check them before you practice, and edit or delete anything that looks wrong.
                <span v-if="lowCount" class="font-medium"> {{ lowCount }} {{ lowCount === 1 ? 'question is' : 'questions are' }} marked low confidence.</span>
            </div>

            <div v-if="errors.question" class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                {{ errors.question }}
            </div>

            <!-- Start practice -->
            <Link
                :href="route('view.practice', past_question.id)"
                class="mb-6 flex w-full items-center justify-center gap-2 rounded-xl bg-primary py-3 text-sm font-semibold text-white hover:bg-primary/90"
            >
                Start practice
            </Link>

            <!-- Questions -->
            <ol class="space-y-4">
                <li
                    v-for="(q, i) in questions"
                    :key="q.id"
                    class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5"
                >
                    <div class="mb-2 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-semibold text-slate-400">Q{{ i + 1 }}</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600">
                            {{ typeLabels[q.question_type] ?? q.question_type }}
                        </span>
                        <span v-if="q.topic_tag" class="rounded-full bg-primary/10 px-2 py-0.5 text-[11px] font-medium text-primary">
                            {{ q.topic_tag }}
                        </span>
                        <span
                            v-if="q.answer_confidence === 'low'"
                            class="rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-medium text-amber-800"
                        >
                            Check this answer
                        </span>
                    </div>

                    <!-- View mode -->
                    <template v-if="editingId !== q.id">
                        <p class="text-sm font-medium text-slate-900">{{ q.question_text }}</p>

                        <ul v-if="q.options?.length" class="mt-3 space-y-1.5">
                            <li
                                v-for="o in q.options"
                                :key="o.id"
                                class="rounded-lg border px-3 py-2 text-sm"
                                :class="isCorrect(o)
                                    ? 'border-emerald-300 bg-emerald-50 text-emerald-900'
                                    : 'border-slate-200 text-slate-700'"
                            >
                                {{ o.option_text }}
                                <span v-if="isCorrect(o)" class="ml-1 text-xs font-semibold">✓ correct</span>
                            </li>
                        </ul>

                        <div v-else-if="q.answers?.length" class="mt-3 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-sm text-emerald-900">
                            <span class="block text-xs font-semibold">Model answer</span>
                            {{ q.answers[0].answer_text }}
                        </div>

                        <p v-if="q.explanation" class="mt-3 text-sm text-slate-600">
                            <span class="font-medium text-slate-700">Why:</span> {{ q.explanation }}
                        </p>
                        <p v-if="q.source_excerpt" class="mt-2 border-l-2 border-slate-200 pl-3 text-xs italic text-slate-500">
                            From your notes: “{{ q.source_excerpt }}”
                        </p>

                        <div class="mt-4 flex gap-4 text-sm">
                            <button type="button" class="font-medium text-primary hover:underline" @click="startEdit(q)">Edit</button>
                            <button type="button" class="font-medium text-red-600 hover:underline" @click="remove(q)">Delete</button>
                        </div>
                    </template>

                    <!-- Edit mode -->
                    <template v-else>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Question</label>
                        <textarea
                            v-model="draft.question_text"
                            rows="3"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                        />
                        <p v-if="errors.question_text" class="mt-1 text-xs text-red-600">{{ errors.question_text }}</p>

                        <div v-if="draft.options.length" class="mt-3 space-y-2">
                            <span class="block text-xs font-medium text-slate-600">Options (select the correct one)</span>
                            <div v-for="o in draft.options" :key="o.id" class="flex items-center gap-2">
                                <input
                                    v-model="draft.correct_option_id"
                                    type="radio"
                                    :value="o.id"
                                    class="h-4 w-4 shrink-0 border-slate-300 text-primary focus:ring-primary"
                                />
                                <input
                                    v-model="o.option_text"
                                    type="text"
                                    :readonly="q.question_type === 'true_false'"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 read-only:bg-slate-50 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                />
                            </div>
                            <p v-if="errors.correct_option_id" class="text-xs text-red-600">{{ errors.correct_option_id }}</p>
                        </div>

                        <div v-else-if="q.question_type === 'short_answer'" class="mt-3">
                            <label class="mb-1 block text-xs font-medium text-slate-600">Model answer</label>
                            <textarea
                                v-model="draft.answer_text"
                                rows="3"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-medium text-slate-600">Explanation (optional)</label>
                            <textarea
                                v-model="draft.explanation"
                                rows="2"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            />
                        </div>

                        <div class="mt-4 flex gap-3">
                            <button
                                type="button"
                                class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90 disabled:opacity-50"
                                :disabled="saving"
                                @click="save(q)"
                            >
                                {{ saving ? 'Saving...' : 'Save' }}
                            </button>
                            <button
                                type="button"
                                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                @click="cancelEdit"
                            >
                                Cancel
                            </button>
                        </div>
                    </template>
                </li>
            </ol>

            <Link
                :href="route('view.practice', past_question.id)"
                class="mt-6 flex w-full items-center justify-center rounded-xl bg-primary py-3 text-sm font-semibold text-white hover:bg-primary/90"
            >
                Start practice
            </Link>
        </div>
    </div>
</template>
