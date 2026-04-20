<template>
    <div class="min-h-screen flex items-start justify-center bg-[var(--color-bg,#f5f7fa)] font-inherit">

        <div
            class="w-full max-w-[680px] bg-[var(--color-surface,#fff)] border border-[var(--color-border,#e2e8f0)] rounded-2xl p-8 shadow-[0_2px_12px_rgba(0,0,0,0.06)]">

            <!-- Header -->
            <div class="mb-6">
                <h1 class="m-0 mb-1 text-[1.4rem] font-bold text-[var(--color-text,#1a202c)]">Past Questions</h1>
                <p class="m-0 text-sm text-[var(--color-muted,#718096)]">Browse and download available past question
                    papers</p>
            </div>

            <!-- Search -->
            <div class="relative mb-5">
                <span
                    class="absolute left-[0.85rem] top-1/2 -translate-y-1/2 flex pointer-events-none text-[var(--color-muted,#a0aec0)]">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </span>
                <input
                    v-model="query"
                    type="text"
                    placeholder="Search files..."
                    class="w-full box-border py-[0.65rem] pr-10 pl-[2.4rem] border-[1.5px] border-[var(--color-border,#e2e8f0)] rounded-[0.6rem] text-[0.925rem] bg-[var(--color-bg,#f8fafc)] text-[var(--color-text,#1a202c)] outline-none transition-[border-color,box-shadow] duration-200 font-inherit focus:border-[var(--color-primary,#3b82f6)] focus:bg-[var(--color-surface,#fff)]"
                />
                <button
                    v-if="query"
                    @click="query = ''"
                    class="absolute right-[0.85rem] top-1/2 -translate-y-1/2 bg-transparent border-none cursor-pointer flex p-[2px] rounded-full text-[var(--color-muted,#a0aec0)] transition-colors duration-150 hover:text-[var(--color-text,#1a202c)]"
                >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5">
                        <path d="M18 6 6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Loading -->
            <div v-if="loading"
                 class="flex flex-col items-center gap-3 py-12 px-4 text-[var(--color-muted,#a0aec0)] text-[0.9rem]">
                <div
                    class="w-7 h-7 border-[3px] border-[var(--color-border,#e2e8f0)] border-t-[var(--color-primary,#3b82f6)] rounded-full animate-spin"></div>
                <span>Loading files…</span>
            </div>

            <!-- Error -->
            <div v-else-if="error"
                 class="flex flex-col items-center gap-3 py-12 px-4 text-[var(--color-danger,#e53e3e)] text-[0.9rem]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 8v4m0 4h.01"/>
                </svg>
                <span>{{ error }}</span>
            </div>

            <!-- Empty -->
            <div v-else-if="filtered.length === 0"
                 class="flex flex-col items-center gap-3 py-12 px-4 text-[var(--color-muted,#a0aec0)] text-[0.9rem]">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <span>No files found</span>
            </div>

            <!-- File List -->
            <ul v-else class="list-none m-0 p-0">
                <li
                    v-for="(file, i) in filtered"
                    :key="file.name"
                    class="flex items-center gap-4 py-[0.9rem] border-b border-[var(--color-border,#f0f4f8)] last:border-b-0 animate-[fadeUp_0.35s_ease_both] border-b-2 border-primary/20"
                    :style="{ animationDelay: `${i * 40}ms` }"
                >
                    <!-- File icon based on type -->
                    <span
                        class="shrink-0 w-12 h-12 p-0 rounded-lg flex items-center justify-center"
                        :class="[
                            ['docx','doc'].includes(ext(file.name))
                                ? 'bg-[color-mix(in_srgb,var(--color-secondary,#0ea5e9)_12%,transparent)] text-[var(--color-secondary,#0ea5e9)]'
                                : ['xlsx','xls'].includes(ext(file.name))
                                    ? 'bg-[color-mix(in_srgb,var(--color-tertiary,#10b981)_12%,transparent)] text-[var(--color-tertiary,#10b981)]'
                                    : 'bg-[color-mix(in_srgb,var(--color-primary,#3b82f6)_10%,transparent)] text-[var(--color-primary,#3b82f6)]'
                        ]"
                    >
                        <svg width="32" height="32" viewBox="0 0 22 22" fill="none" stroke="currentColor"
                             stroke-width="1.8">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline
                            points="14 2 14 8 20 8"/>
                        </svg>
                    </span>

                    <div :title="label(file.name)" class="flex-1 min-w-0 flex flex-col gap-1">
                        <span
                            class="font-semibold text-[0.95rem] text-[var(--color-text,#1a202c)] whitespace-nowrap overflow-hidden text-ellipsis">
                            {{ label(file.name) }}
                        </span>


                        <div class="flex justify-between">
                        <span class="flex items-center gap-2">
                            <span
                                class="text-[0.7rem] font-bold tracking-[0.04em] px-[0.45rem] py-[0.1rem] rounded-[0.3rem] bg-[color-mix(in_srgb,var(--color-primary,#3b82f6)_12%,transparent)] text-[var(--color-primary,#3b82f6)]">
                                {{ ext(file.name).toUpperCase() }}
                            </span>
                            <span v-if="file.size"
                                  class="text-[0.75rem] text-[var(--color-muted,#a0aec0)]">
                                {{ formatSize(file.size) }}
                            </span>
                        </span>
                            <a
                                class="shrink-0 inline-flex items-center gap-[0.45rem] px-4 py-2 bg-primary text-white rounded-lg text-[0.85rem] font-semibold no-underline transition-[opacity,transform] duration-150 hover:opacity-90 hover:-translate-y-px active:opacity-100 active:translate-y-0 font-inherit"
                                :href="`/past_questions/${file.name}`"
                                :download="file.name"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2.5">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                <span class="">
                        Download
                        </span>
                            </a>
                        </div>
                    </div>

                </li>
            </ul>

            <p v-if="!loading && !error && files.length"
               class="mt-4 text-right text-[0.78rem] text-[var(--color-muted,#a0aec0)]">
                {{ filtered.length }} of {{ files.length }} file{{ files.length !== 1 ? 's' : '' }}
            </p>
        </div>
    </div>
</template>

<script setup>
import {ref, computed, onMounted} from 'vue'

const files = ref([])
const query = ref('')
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
    try {
        const res = await fetch('/past_questions/files.json', {
            cache: "no-cache"
        })
        if (!res.ok) throw new Error(`Could not load file list (${res.status})`)
        const data = await res.json()
        files.value = Array.isArray(data) ? data : data.files ?? []
    } catch (e) {
        error.value = e.message
    } finally {
        loading.value = false
    }
})

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase()
    if (!q) return files.value
    return files.value.filter(f => f.name.toLowerCase().includes(q))
})

function label(name) {
    return name
        .replace(/\.[^.]+$/, '')
        .replace(/[_-]+/g, ' ')
        .replace(/\b\w/g, c => c.toUpperCase())
}

function ext(name) {
    return (name.match(/\.([^.]+)$/) ?? ['', 'file'])[1].toLowerCase()
}

function formatSize(bytes) {
    if (bytes < 1024) return `${bytes} B`
    if (bytes < 1048576) return `${(bytes / 1024).toFixed(1)} KB`
    return `${(bytes / 1048576).toFixed(1)} MB`
}
</script>

<style>
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
