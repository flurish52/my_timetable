<template>
    <GuestLayout>
    <div class="min-h-screen flex items-start justify-center bg-[var(--color-bg,#f5f7fa)] font-inherit">
        <div
            class="w-full max-w-[680px] bg-[var(--color-surface,#fff)] border border-[var(--color-border,#e2e8f0)] rounded-2xl p-8 shadow-[0_2px_12px_rgba(0,0,0,0.06)]">

            <!-- Header -->
            <div class="mb-6 pt-2 bg-primary/5 rounded-md px-6">
                <div class="flex items-start gap-5">

<!--                    &lt;!&ndash; Icon block &ndash;&gt;-->
<!--                    <div class="w-13 h-13 shrink-0 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center text-2xl">-->
<!--                        🎯-->
<!--                    </div>-->

                    <!-- Text content -->
                    <div class="flex-1 min-w-0">

                        <!-- Title + badge -->
                        <div class="flex items-center gap-2.5 flex-wrap mb-1.5">
                            <h1 class="m-0 text-[1.35rem] font-semibold text-gray-900 leading-snug">
                                Your exam edge starts here
                            </h1>
                            <span class="text-xs font-medium px-2.5 py-0.5 rounded-md bg-green-50 text-green-700">
          Answers included
        </span>
                        </div>

                        <!-- Description -->
                        <p class="m-0 mb-4 text-[0.95rem] text-gray-500 leading-relaxed">
                            Browse real past question papers from
                            <span class="font-medium text-gray-800">Federal College of Obudu</span>
                            with answers and walk into every exam knowing exactly what to expect,
                            not just hoping for the best.
                        </p>

                        <!-- Meta pills -->
                        <div class="flex items-center gap-6 flex-wrap">
                            <div class="flex items-center gap-1.5">
                                <i class="ti ti-books text-base text-gray-400"></i>
                                <span class="text-xs text-gray-400">Multiple courses</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <i class="ti ti-circle-check text-base text-green-500"></i>
                                <span class="text-xs text-gray-400">Answers included</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <i class="ti ti-download text-base text-gray-400"></i>
                                <span class="text-xs text-gray-400">Download instantly</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <i class="ti ti-calendar text-base text-gray-400"></i>
                                <span class="text-xs text-gray-400">Past sessions included</span>
                            </div>
                        </div>

                    </div>
                </div>

                <hr class="mt-5 border-gray-100" />
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
                    v-for="(course, i) in filtered"
                    :key="course.course_title"
                    class="flex items-start gap-4 py-[0.9rem] border-b border-[var(--color-border,#f0f4f8)] last:border-b-0 animate-[fadeUp_0.35s_ease_both] border-b-2 border-primary/20"
                    :style="{ animationDelay: `${i * 40}ms` }"
                >
                    <!-- Course icon based on type -->
                    <div
                        class="shrink-0 w-18 h-18 p-0 bg-primary/10 rounded-lg flex items-center justify-center">
                            <svg width="44" height="44" viewBox="0 0 21 21" fill="none" stroke="currentColor" stroke-width="1.8"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6a3 3 0 0 1 3-3h12v18H6a3 3 0 0 1-3-3z"/>
                                <path d="M18 3a3 3 0 0 1 3 3v12a3 3 0 0 0-3-3H6"/>
                                <path d="M8 8h6"/>
                                <path d="M8 12h6"/>
                            </svg>
                    </div>

                    <div :title="course.course_title" class="flex-1 min-w-0 flex flex-col gap-1">
                        <span
                            class="font-semibold text-[0.95rem] text-[var(--color-text,#1a202c)] whitespace-nowrap overflow-hidden text-ellipsis">
                            {{ label(course.course_title) }} Past questions
                        </span>


                        <div class="flex justify-between">
                        <span class="flex items-center gap-2">
                            <span v-if="course?.files"
                                  class="text-[0.75rem] text-[var(--color-muted,#a0aec0)]">
                                {{ course.files.length }} files
                            </span>
                        </span>
                            <a
                                v-if="course.files.length !== 0"
                                class="shrink-0 inline-flex items-center gap-[0.45rem] px-4 py-2 bg-primary text-white rounded-lg text-[0.85rem] font-semibold no-underline transition-[opacity,transform] duration-150 hover:opacity-90 hover:-translate-y-px active:opacity-100 active:translate-y-0 font-inherit"
                                :href="`/pastquestions/${course.course_title}`">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2.5">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/>
                                </svg>
                                <span>View</span>
                            </a>
                            <button
                                v-else
                                title="No Files"
                                class="shrink-0 inline-flex items-center gap-[0.45rem] px-4 py-2 bg-gray-500 text-white rounded-lg text-[0.85rem] font-semibold no-underline transition-[opacity,transform] duration-150 hover:opacity-90 hover:-translate-y-px cursor-not-allowed">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2.5">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/>
                                </svg>
                                <span>View</span>
                            </button>
                        </div>
                    </div>

                </li>
            </ul>

            <p v-if="!loading && !error && courses.length"
               class="mt-4 text-right text-[0.78rem] text-[var(--color-muted,#a0aec0)]">
                {{ filtered.length }} of {{ courses.length }} courses{{ courses.length !== 1 ? 's' : '' }}
            </p>
        </div>
    </div>
    </GuestLayout>
</template>

<script setup>
import {ref, computed, onMounted} from 'vue'
import GuestLayout from "@/Layouts/GuestLayout.vue";

const courses = ref([])
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
        courses.value = Array.isArray(data) ? data : data.files ?? []
    } catch (e) {
        error.value = e.message
    } finally {
        loading.value = false
    }
})

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase()
    if (!q) return courses.value
    return courses.value.filter(c => c.course_title.toLowerCase().includes(q))
    console.log(courses)

})

//
function label(course_title) {
    return course_title
        .replace(/\.[^.]+$/, '')
        .replace(/[_-]+/g, ' ')
        .replace(/\b\w/g, c => c.toUpperCase())
}

// function ext(name) {
//     return (name.match(/\.([^.]+)$/) ?? ['', 'file'])[1].toLowerCase()
// }
//
// function formatSize(bytes) {
//     if (bytes < 1024) return `${bytes} B`
//     if (bytes < 1048576) return `${(bytes / 1024).toFixed(1)} KB`
//     return `${(bytes / 1048576).toFixed(1)} MB`
// }
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
