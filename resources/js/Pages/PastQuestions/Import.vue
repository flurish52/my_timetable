<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import ImportQuestionsStep from '@/Components/PastQuestions/ImportQuestionsStep.vue'

const props = defineProps({
    pastQuestion: { type: Object, required: true },
})

const METHODS = [
    {
        key: 'paste',
        label: 'Paste or type',
        description: 'Fastest if you already have questions in any text — just paste them in.',
        iconClasses: 'bg-primary/10 text-primary',
    },
    {
        key: 'excel',
        label: 'Excel spreadsheet',
        description: 'Best for transcribing a printed paper into a structured template.',
        iconClasses: 'bg-secondary/10 text-secondary',
    },
    {
        key: 'word',
        label: 'Word document',
        description: 'Upload a .docx following the same Q: / Type: / Answer: format.',
        iconClasses: 'bg-tertiary/10 text-tertiary',
    },
    {
        key: 'pdf',
        label: 'PDF file',
        description: 'Upload a PDF following the same format — text is extracted automatically.',
        iconClasses: 'bg-tertiary/10 text-tertiary',
    },
]

const activeMethod = ref(null)
const excelFileInput = ref(null)
const wordFileInput = ref(null)
const pdfFileInput = ref(null)
const uploading = ref(null)
const uploadError = ref(null)

function selectMethod(key) {
    activeMethod.value = key
    uploadError.value = null

    if (key === 'excel') excelFileInput.value?.click()
    if (key === 'word') wordFileInput.value?.click()
    if (key === 'pdf') pdfFileInput.value?.click()
}

function handleExcelFile(e) {
    const file = e.target.files[0]
    if (!file) return
    uploading.value = 'excel'
    router.post(route('past-questions.import-excel', props.pastQuestion.id), { file }, {
        forceFormData: true,
        onError: (errs) => { uploadError.value = Object.values(errs)[0] ?? 'Import failed.' },
        onFinish: () => { uploading.value = null },
    })
}

function handleDocumentFile(kind, e) {
    const file = e.target.files[0]
    if (!file) return
    uploading.value = kind
    router.post(route('past-questions.import-document', props.pastQuestion.id), { file }, {
        forceFormData: true,
        onError: (errs) => { uploadError.value = Object.values(errs)[0] ?? 'Import failed.' },
        onFinish: () => { uploading.value = null },
    })
}

function onImported(count) {
    router.visit(route('past-questions.build', props.pastQuestion.id))
}
</script>

<template>
    <div class="min-h-screen bg-neutral-50">
        <div class="bg-white border-b border-neutral-200">
            <div class="max-w-3xl mx-auto px-4 py-5 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-primary mb-0.5">Import questions</p>
                    <h1 class="text-lg font-bold text-neutral-900 truncate">{{ pastQuestion.title }}</h1>
                </div>
                <Link :href="route('past-questions.build', pastQuestion.id)"
                      class="shrink-0 text-sm text-neutral-500 hover:text-neutral-700">
                    Skip, add manually →
                </Link>
            </div>
        </div>

        <div class="max-w-3xl mx-auto px-4 py-8">
            <!-- Method picker -->
            <div v-if="activeMethod !== 'paste'">
                <p class="text-sm text-neutral-500 mb-4">Choose how you'd like to bring in your questions.</p>
                <p v-if="uploadError" class="mt-4 mt-1 text-sm text-red-600 bg-red-50 border border-red-200 rounded-md px-3 py-2">
                    {{ uploadError }}
                </p>

                <div class="grid sm:grid-cols-2 gap-3">
                    <button
                        v-for="m in METHODS" :key="m.key"
                        @click="selectMethod(m.key)"
                        :disabled="uploading === m.key"
                        class="text-left p-4 rounded-xl border border-neutral-200 bg-white hover:border-primary/40 hover:shadow-sm transition disabled:opacity-50"
                    >
                        <div :class="`w-9 h-9 rounded-lg flex items-center justify-center mb-3 ${m.iconClasses}`">
                            <svg v-if="m.key === 'paste'" class="w-4.5 h-4.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 3a1 1 0 011-1h6a1 1 0 011 1v1h1a2 2 0 012 2v11a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2h1V3zm2 0v1h4V3H8zM5 6v11h10V6H5zm2 3a1 1 0 100 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h4a1 1 0 100-2H7z" clip-rule="evenodd" />
                            </svg>
                            <svg v-else-if="m.key === 'excel'" class="w-4.5 h-4.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm7 9a1 1 0 10-2 0v.01a1 1 0 102 0V13zm-4-2a1 1 0 011 1v.01a1 1 0 11-2 0V12a1 1 0 011-1zm4-2a1 1 0 10-2 0v.01a1 1 0 102 0V9zm-4-2a1 1 0 011 1v.01a1 1 0 11-2 0V8a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            <svg v-else class="w-4.5 h-4.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm8 1.414L14.586 8H12V5.414zM6 10a1 1 0 100 2h8a1 1 0 100-2H6zm0 4a1 1 0 100 2h5a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-neutral-900">{{ m.label }}</p>
                        <p class="text-xs text-neutral-500 mt-1 leading-relaxed">{{ m.description }}</p>
                        <p v-if="uploading === m.key" class="text-xs text-primary mt-2 font-medium">Uploading…</p>
                    </button>
                </div>

                <input ref="excelFileInput" type="file" accept=".xlsx,.xls,.csv" class="hidden" @change="handleExcelFile" />
                <input ref="wordFileInput" type="file" accept=".doc,.docx" class="hidden" @change="(e) => handleDocumentFile('word', e)" />
                <input ref="pdfFileInput" type="file" accept=".pdf" class="hidden" @change="(e) => handleDocumentFile('pdf', e)" />

                <div class="mt-4 flex items-center gap-3 text-xs text-neutral-500">
                    <a :href="route('past-questions.import-template')" class="text-primary hover:underline">
                        Get the Excel template
                    </a>
                    <span class="text-neutral-300">·</span>
                    <span>Word and PDF files should follow the same Q: / Type: / Answer: format as pasting</span>
                </div>
            </div>

            <!-- Paste flow -->
            <div v-else class="bg-white border border-neutral-200 rounded-xl p-5">
                <ImportQuestionsStep
                    :paper-id="pastQuestion.id"
                    @imported="onImported"
                    @back="activeMethod = null"
                />
            </div>
        </div>
    </div>
</template>
