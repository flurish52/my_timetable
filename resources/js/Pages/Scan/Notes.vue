<script setup>
import {ref, computed, onBeforeUnmount} from 'vue'
import {Head, useForm} from '@inertiajs/vue3'
import {useScanFiles} from '@/composables/useScanFiles'
import {Link} from "@inertiajs/vue3";

const props = defineProps({
    course: {type: Object, default: null},
    courses: {type: Array, default: () => []},
    maxImages: {type: Number, default: 6},
    scansRemaining: {type: Number, default: null},
    questionLimits: {type: Object, default: () => ({min: 10, max: 50, step: 10})},
    isGuest: {type: Boolean, default: false},
})

const {previews, files, fileError, isProcessing, hasPdf, canAddMore, addFiles, removePreview} =
    useScanFiles(props.maxImages)

const form = useForm({
    images: [],
    course_id: props.course?.id ?? null,
    question_count: 10,
    types: ['objective'],
    difficulty: 'medium',
})

const typeOptions = [
    {value: 'objective', label: 'Multiple choice'},
    {value: 'true_false', label: 'True / False'},
    {value: 'short_answer', label: 'Short answer'},
]
const difficultyOptions = [
    {value: 'easy', label: 'Easy'},
    {value: 'medium', label: 'Medium'},
    {value: 'hard', label: 'Hard'},
]

function toggleType(value) {
    const i = form.types.indexOf(value)
    if (i === -1) form.types.push(value)
    else if (form.types.length > 1) form.types.splice(i, 1) // keep at least one
}

const isDragging = ref(false)
const dragDepth = ref(0)
const phase = ref('idle') // idle | uploading | generating

const generatingMessages = [
    'Reading your notes...',
    'Picking out the key points...',
    'Writing your questions...',
    'Checking the answers...',
]
const messageIndex = ref(0)
let interval = null

const startRotation = () => {
    messageIndex.value = 0
    interval = setInterval(() => {
        messageIndex.value = (messageIndex.value + 1) % generatingMessages.length
    }, 2600)
}
const stopRotation = () => {
    if (interval) clearInterval(interval)
    interval = null
}

const limitReached = computed(() => !props.isGuest && props.scansRemaining <= 0)
const canSubmit = computed(
    () => !props.isGuest && previews.value.length > 0 && phase.value === 'idle' && !isProcessing.value && !limitReached.value
)
const uploadPct = computed(() => form.progress?.percentage ?? 0)

const imagesError = computed(() => {
    if (form.errors.images) return form.errors.images
    const key = Object.keys(form.errors).find((k) => k.startsWith('images.'))
    return key ? form.errors[key] : null
})
const otherError = computed(() => form.errors.types || form.errors.question_count || form.errors.difficulty || null)

function submit() {
    if (!canSubmit.value) return
    form.images = files.value
    phase.value = 'uploading'

    form.post(route('scan.notes.store'), {
        forceFormData: true,
        onProgress: (p) => {
            if (p?.percentage >= 100 && phase.value === 'uploading') {
                phase.value = 'generating'
                startRotation()
            }
        },
        onFinish: () => {
            stopRotation()
            phase.value = 'idle'
        },
    })
}

function onFileInputChange(e) {
    addFiles(e.target.files)
    e.target.value = ''
}

function onDrop(e) {
    isDragging.value = false
    dragDepth.value = 0
    addFiles(e.dataTransfer.files)
}

function onDragEnter() {
    dragDepth.value++
    isDragging.value = true
}

function onDragLeave() {
    dragDepth.value = Math.max(0, dragDepth.value - 1)
    if (dragDepth.value === 0) isDragging.value = false
}

onBeforeUnmount(stopRotation)
const goBack = () => window.history.back()
</script>

<template>
    <Head title="Quiz from Notes"/>

    <div class="min-h-screen bg-slate-50">
        <div class="mx-auto max-w-2xl px-4 py-8 sm:py-12">
            <!-- Header -->
            <div class="mb-6">
                <button
                    type="button"
                    class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-primary"
                    @click="goBack"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                    Back
                </button>

                <div class="flex items-start gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/10">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="h-5 w-5 text-primary">
                            <path
                                d="M4 19.5A2.5 2.5 0 016.5 17H20M4 19.5A2.5 2.5 0 006.5 22H20V2H6.5A2.5 2.5 0 004 4.5v15z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-slate-900">Quiz from your notes</h1>
                        <p class="mt-0.5 text-sm text-slate-500">
                            <template v-if="course">For {{ course.code }} — {{ course.title }}</template>
                            <template v-else>Scan your notes and we'll turn them into practice questions.</template>
                        </p>
                    </div>
                </div>
            </div>

            <div v-if="isGuest"
                 class="mb-5 rounded-xl border border-primary/20 bg-primary/5 p-4 text-sm text-slate-700">
                Log in to generate a quiz from your notes. Your scans and quizzes are saved to your account.
            </div>
            <!-- Limit reached -->
            <div v-if="limitReached"
                 class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                You've used all your scans for today. Come back tomorrow.
            </div>

            <!-- Server errors -->
            <div v-if="imagesError" class="mb-5 flex gap-3 rounded-xl border border-red-200 bg-red-50 p-4">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="h-5 w-5 shrink-0 text-red-500">
                    <path
                        d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <div class="text-sm text-red-800">{{ imagesError }}</div>
            </div>
            <p v-if="otherError" class="mb-4 text-xs text-red-600">{{ otherError }}</p>

            <!-- Dropzone -->
            <div
                class="relative rounded-2xl border-2 border-dashed p-6 text-center transition-colors"
                :class="[isDragging ? 'border-primary bg-primary/5' : 'border-slate-300 bg-white']"
                @dragover.prevent
                @dragenter.prevent="onDragEnter"
                @dragleave.prevent="onDragLeave"
                @drop.prevent="onDrop"
            >
                <input
                    id="notes-file-input"
                    type="file"
                    accept="image/*,application/pdf"
                    multiple
                    class="sr-only"
                    :disabled="!canAddMore"
                    @change="onFileInputChange"
                />

                <template v-if="isProcessing">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                        <svg class="h-6 w-6 animate-spin text-primary" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-700">Processing photo...</p>
                </template>

                <template v-else-if="previews.length === 0">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="h-6 w-6 text-slate-400">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-700">Drag your notes here, or choose files</p>
                    <p class="mt-1 text-xs text-slate-400">Up to {{ maxImages }} photos, or a single PDF · JPG, PNG or
                        PDF · 8MB each</p>
                    <label
                        for="notes-file-input"
                        class="mt-4 inline-flex cursor-pointer items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90"
                    >
                        Choose photos
                    </label>
                </template>

                <template v-else>
                    <div class="grid grid-cols-3 gap-2.5 sm:grid-cols-4">
                        <div
                            v-for="p in previews"
                            :key="p.id"
                            class="relative aspect-[3/4] overflow-hidden rounded-lg border border-slate-200 bg-slate-100"
                        >
                            <img v-if="!p.isPdf" :src="p.url" alt="" class="h-full w-full object-cover"/>
                            <div v-else
                                 class="flex h-full w-full flex-col items-center justify-center gap-2 px-2 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.5" class="h-8 w-8 text-red-400">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                </svg>
                                <span class="line-clamp-2 text-[10px] font-medium text-slate-500">{{
                                        p.file.name
                                    }}</span>
                                <span class="text-[10px] text-slate-400">Page count checked after upload</span>
                            </div>
                            <button
                                type="button"
                                class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-full bg-black/60 text-white hover:bg-black/80"
                                aria-label="Remove page"
                                @click="removePreview(p.id)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                     class="h-3.5 w-3.5">
                                    <path d="M18 6L6 18M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <label
                            v-if="canAddMore"
                            for="notes-file-input"
                            class="flex aspect-[3/4] cursor-pointer flex-col items-center justify-center gap-1 rounded-lg border-2 border-dashed border-slate-300 text-slate-400 hover:border-primary hover:text-primary"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-5 w-5">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            <span class="text-xs font-medium">Add</span>
                        </label>
                    </div>
                    <p class="mt-3 text-xs text-slate-400">
                        <template v-if="hasPdf">1 PDF added</template>
                        <template v-else>{{ previews.length }}/{{ maxImages }} pages added</template>
                    </p>
                </template>
            </div>
            <p v-if="fileError" class="mt-2 text-xs text-red-600">{{ fileError }}</p>

            <!-- Options -->
            <div class="mt-6 space-y-5 rounded-2xl border border-slate-200 bg-white p-5">
                <div v-if="!course && courses.length">
                    <label for="course_id" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Course <span class="font-normal text-slate-400">(optional)</span>
                    </label>
                    <select
                        id="course_id"
                        v-model="form.course_id"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                    >
                        <option :value="null">Not sure / general</option>
                        <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.code }} — {{ c.title }}</option>
                    </select>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <label for="question_count" class="text-sm font-medium text-slate-700">Number of questions</label>
                        <span class="text-sm font-semibold text-primary">{{ form.question_count }}</span>
                    </div>
                    <input
                        id="question_count"
                        v-model.number="form.question_count"
                        type="range"
                        :min="questionLimits.min"
                        :max="questionLimits.max"
                        :step="questionLimits.step"
                        class="w-full accent-primary"
                    />
                    <div class="mt-1 flex justify-between text-xs text-slate-400">
                        <span>{{ questionLimits.min }}</span>
                        <span>{{ questionLimits.max }}</span>
                    </div>
                    <p v-if="form.question_count > 30" class="mt-2 text-xs text-slate-500">
                        Bigger quizzes take longer to generate and  short notes may give you fewer questions than you ask for.
                    </p>
                </div>

                <div>
                    <span class="mb-1.5 block text-sm font-medium text-slate-700">Question types</span>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="t in typeOptions"
                            :key="t.value"
                            type="button"
                            class="rounded-lg border px-4 py-2 text-sm font-medium transition-colors"
                            :class="form.types.includes(t.value)
                                ? 'border-primary bg-primary/10 text-primary'
                                : 'border-slate-300 bg-white text-slate-700 hover:border-primary'"
                            @click="toggleType(t.value)"
                        >
                            {{ t.label }}
                        </button>
                    </div>
                </div>

                <div>
                    <span class="mb-1.5 block text-sm font-medium text-slate-700">Difficulty</span>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="d in difficultyOptions"
                            :key="d.value"
                            type="button"
                            class="rounded-lg border px-4 py-2 text-sm font-medium transition-colors"
                            :class="form.difficulty === d.value
                                ? 'border-primary bg-primary text-white'
                                : 'border-slate-300 bg-white text-slate-700 hover:border-primary'"
                            @click="form.difficulty = d.value"
                        >
                            {{ d.label }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Submit -->

            <Link
                v-if="isGuest"
                :href="route('login')"
                class="mt-5 flex w-full items-center justify-center rounded-xl bg-primary py-3 text-sm font-semibold text-white hover:bg-primary/90"
            >
                Log in to continue
            </Link>
            <button
                type="button"
                class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-primary py-3 text-sm font-semibold text-white transition-opacity hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="!canSubmit"
                @click="submit"
            >
                <template v-if="phase === 'idle'">Generate {{ form.question_count }} questions</template>
                <template v-else-if="phase === 'uploading'">
                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Uploading — {{ uploadPct }}%
                </template>
                <template v-else>
                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    {{ generatingMessages[messageIndex] }}
                </template>
            </button>

            <p class="mt-3 text-center text-xs text-slate-400">
                Private, only you can see your quiz. {{ scansRemaining }} scan{{ scansRemaining === 1 ? '' : 's' }} left
                today.
            </p>
        </div>
    </div>
</template>
