<script setup>
import { ref, computed, watch, onBeforeUnmount } from 'vue'
import {Head, useForm, usePage, Link, router} from '@inertiajs/vue3'

const props = defineProps({
    course: {
        // Optional. Pass the Course model (id, code, title) when arriving from a
        // course's empty-state CTA. Null when arriving from the waitlist page —
        // in that case the scan has nowhere to attach yet and raw_course_label
        // carries the student's typed guess instead.
        type: Object,
        default: null,
    },
})

const page = usePage()

const MAX_FILES = 6
const MAX_FILE_BYTES = 8 * 1024 * 1024 // 8MB, mirrors ScanController::store() validation

const form = useForm({
    images: [],
    course_id: props.course?.id ?? null,
    raw_course_label: '',
})

// { file, url, id } — url is an object URL we revoke on removal/unmount
const previews = ref([])
let nextPreviewId = 0

const fileError = ref('')
const isDragging = ref(false)
const dragDepth = ref(0)

// Phase drives the loading copy. Upload has real progress; extraction doesn't
// (Gemini's a black box from the client's side), so it gets rotating messages
// instead of a fake progress bar.
const phase = ref('idle') // idle | uploading | extracting
const extractingMessages = [
    "Reading the paper...",
    'Working through each question...',
    'Matching options to answers...',
    'Almost done...',
]
const extractingMessageIndex = ref(0)
let extractingInterval = null

function startExtractingRotation() {
    extractingMessageIndex.value = 0
    extractingInterval = setInterval(() => {
        extractingMessageIndex.value = (extractingMessageIndex.value + 1) % extractingMessages.length
    }, 2600)
}
function stopExtractingRotation() {
    if (extractingInterval) clearInterval(extractingInterval)
    extractingInterval = null
}

const hasPdf = computed(() => previews.value.some((p) => p.isPdf))
const remainingSlots = computed(() => MAX_FILES - previews.value.length)
const canAddMore = computed(() => !hasPdf.value && remainingSlots.value > 0)
const canSubmit = computed(() => previews.value.length > 0 && phase.value === 'idle')

function addFiles(fileList) {
    fileError.value = ''
    const incoming = Array.from(fileList)

    if (!incoming.length) return

    const incomingPdf = incoming.find((f) => f.type === 'application/pdf')

    // A PDF replaces the whole batch — server enforces this too, but catching it
    // client-side avoids a wasted round trip.
    if (incomingPdf) {
        if (incoming.length > 1) {
            fileError.value = 'Upload either a single PDF or up to 6 photos — not both together.'
            return
        }
        if (previews.value.length > 0 && previews.value[0].file.type !== 'application/pdf') {
            fileError.value = 'Remove your photos first if you want to upload a PDF instead.'
            return
        }
        // A PDF's actual page count can only be verified server-side (needs to
        // open the file), so we just swap in the single PDF and let the backend
        // reject it after upload if it has too many pages.
        previews.value.forEach((p) => URL.revokeObjectURL(p.url))
        previews.value = [{ id: nextPreviewId++, file: incomingPdf, url: null, isPdf: true }]
        syncFormImages()
        return
    }

    if (previews.value.length === 1 && previews.value[0].isPdf) {
        fileError.value = 'Remove the PDF first if you want to upload photos instead.'
        return
    }

    const room = remainingSlots.value
    if (room <= 0) {
        fileError.value = `You've already added ${MAX_FILES} pages — that's the limit per scan.`
        return
    }

    const accepted = []
    const rejectedReasons = new Set()

    for (const file of incoming) {
        if (!file.type.startsWith('image/')) {
            rejectedReasons.add('Only image files (or a single PDF) are allowed.')
            continue
        }
        if (file.size > MAX_FILE_BYTES) {
            rejectedReasons.add('Each image must be under 8MB.')
            continue
        }
        accepted.push(file)
    }

    const overflow = accepted.length > room
    const toAdd = accepted.slice(0, room)

    for (const file of toAdd) {
        previews.value.push({
            id: nextPreviewId++,
            file,
            url: URL.createObjectURL(file),
            isPdf: false,
        })
    }

    if (overflow) {
        rejectedReasons.add(`Only added ${room} more — ${MAX_FILES} pages is the max per scan.`)
    }
    if (rejectedReasons.size) {
        fileError.value = Array.from(rejectedReasons).join(' ')
    }

    syncFormImages()
}

function removePreview(id) {
    const idx = previews.value.findIndex((p) => p.id === id)
    if (idx === -1) return
    if (previews.value[idx].url) URL.revokeObjectURL(previews.value[idx].url)
    previews.value.splice(idx, 1)
    fileError.value = ''
    syncFormImages()
}

function syncFormImages() {
    form.images = previews.value.map((p) => p.file)
}

function onFileInputChange(e) {
    addFiles(e.target.files)
    e.target.value = '' // allow re-selecting the same file after removal
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

function submit() {
    if (!canSubmit.value) return

    phase.value = 'uploading'

    form.post(route('scan.store'), {
        forceFormData: true,
        onProgress: (progress) => {
            // Once the upload itself finishes, Inertia progress stalls at 100
            // while the server is still calling Gemini — switch to the
            // extraction phase so the UI doesn't look frozen.
            if (progress?.percentage >= 100 && phase.value === 'uploading') {
                phase.value = 'extracting'
                startExtractingRotation()
            }
        },
        onStart: () => {
            if (phase.value !== 'extracting') phase.value = 'uploading'
        },
        onFinish: () => {
            // Covers both success (page will have navigated away) and failure
            // (we're still here and need to reset the UI).
            stopExtractingRotation()
            phase.value = 'idle'
        },
        onError: () => {
            stopExtractingRotation()
            phase.value = 'idle'
        },
    })
}

const uploadProgressPct = computed(() => form.progress?.percentage ?? 0)

const scanError = computed(() => page.props.flash?.scan_error ?? null)

onBeforeUnmount(() => {
    stopExtractingRotation()
    previews.value.forEach((p) => URL.revokeObjectURL(p.url))
})

const goBack = () => {
    window.history.back()
}
</script>

<template>
    <Head title="Scan a Past Question" />

    <div class="min-h-screen bg-slate-50">
        <div class="mx-auto max-w-2xl px-4 py-8 sm:py-12">

            <!-- Header -->
            <div class="mb-6">
                <button
                    @click="goBack"
                    class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-primary"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                    Back
                </button>

                <div class="flex items-start gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/10">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5.5 w-5.5 text-primary">
                            <path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <circle cx="12" cy="13" r="3.5" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-slate-900">Scan a past question</h1>
                        <p class="mt-0.5 text-sm text-slate-500">
                            <template v-if="course">
                                For {{ course.code }} — {{ course.title }}
                            </template>
                            <template v-else>
                                We'll match it to your school and course after reading the paper.
                            </template>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Server-side error (rejected paper, quota hit, extraction failure) -->
            <div
                v-if="scanError"
                class="mb-5 flex gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 shrink-0 text-amber-500">
                    <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
                <div class="text-sm text-amber-800">{{ scanError }}</div>
            </div>

            <!-- If arriving without a course, let the student tell us what it's for -->
            <div v-if="!course" class="mb-5">
                <label for="raw_course_label" class="mb-1.5 block text-sm font-medium text-slate-700">
                    What course is this for?
                </label>
                <input
                    id="raw_course_label"
                    v-model="form.raw_course_label"
                    type="text"
                    placeholder="e.g. GST 111, or the course title on the paper"
                    class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                />
                <p v-if="form.errors.raw_course_label" class="mt-1 text-xs text-red-600">
                    {{ form.errors.raw_course_label }}
                </p>
            </div>

            <!-- Dropzone -->
            <div
                class="relative rounded-2xl border-2 border-dashed p-6 text-center transition-colors"
                :class="isDragging ? 'border-primary bg-primary/5' : 'border-slate-300 bg-white'"
                @dragover.prevent
                @dragenter.prevent="onDragEnter"
                @dragleave.prevent="onDragLeave"
                @drop.prevent="onDrop"
            >
                <input
                    id="scan-file-input"
                    type="file"
                    accept="image/*,application/pdf"
                    multiple
                    capture="environment"
                    class="sr-only"
                    :disabled="!canAddMore"
                    @change="onFileInputChange"
                />

                <template v-if="previews.length === 0">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-slate-400">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-700">Drag pages here, or choose files</p>
                    <p class="mt-1 text-xs text-slate-400">Up to 6 photos, or a single PDF · JPG, PNG or PDF · 8MB each</p>
                    <label
                        for="scan-file-input"
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
                            class="group relative aspect-[3/4] overflow-hidden rounded-lg border border-slate-200 bg-slate-100"
                        >
                            <img v-if="!p.isPdf" :src="p.url" alt="" class="h-full w-full object-cover" />
                            <div v-else class="flex h-full w-full flex-col items-center justify-center gap-2 px-2 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-8 w-8 text-red-400">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                </svg>
                                <span class="line-clamp-2 text-[10px] font-medium text-slate-500">{{ p.file.name }}</span>
                                <span class="text-[10px] text-slate-400">Page count checked after upload</span>
                            </div>
                            <button
                                type="button"
                                class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-full bg-black/60 text-white opacity-90 hover:bg-black/80"
                                @click="removePreview(p.id)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="h-3.5 w-3.5">
                                    <path d="M18 6L6 18M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <label
                            v-if="canAddMore"
                            for="scan-file-input"
                            class="flex aspect-[3/4] cursor-pointer flex-col items-center justify-center gap-1 rounded-lg border-2 border-dashed border-slate-300 text-slate-400 hover:border-primary hover:text-primary"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-5 w-5">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            <span class="text-xs font-medium">Add</span>
                        </label>
                    </div>
                    <p class="mt-3 text-xs text-slate-400">
                        <template v-if="hasPdf">1 PDF added</template>
                        <template v-else>{{ previews.length }}/{{ MAX_FILES }} pages added</template>
                    </p>
                </template>
            </div>

            <p v-if="fileError" class="mt-2 text-xs text-red-600">{{ fileError }}</p>
            <p v-if="form.errors.images" class="mt-2 text-xs text-red-600">{{ form.errors.images }}</p>

            <!-- Submit -->
            <button
                type="button"
                class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-primary py-3 text-sm font-semibold text-white transition-opacity hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="!canSubmit"
                @click="submit"
            >
                <template v-if="phase === 'idle'">
                    Scan {{ previews.length > 0 ? `${previews.length} page${previews.length > 1 ? 's' : ''}` : '' }}
                </template>
                <template v-else-if="phase === 'uploading'">
                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                    Uploading — {{ uploadProgressPct }}%
                </template>
                <template v-else>
                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                    {{ extractingMessages[extractingMessageIndex] }}
                </template>
            </button>

            <p class="mt-3 text-center text-xs text-slate-400">
                Private by default — only you can see it. You can share it with everyone later from your scan.
            </p>
        </div>
    </div>
</template>
