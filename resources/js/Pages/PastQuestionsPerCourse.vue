<template>
    <Head :title="past_question ? `${past_question.title ?? 'Past Questions'} - Past questions` : 'Past questions'"/>
    <div class="min-h-screen flex items-start justify-center bg-[var(--color-bg,#f5f7fa)] font-inherit">
        <div
            class="w-full max-w-[680px] bg-[var(--color-surface,#fff)] border border-[var(--color-border,#e2e8f0)] rounded-2xl p-8 shadow-[0_2px_12px_rgba(0,0,0,0.06)]">
            <BackButton route="/pastquestions"/>

            <!-- Whole-page empty state: past_question itself is null/missing -->
            <div v-if="!past_question" class="flex flex-col items-center gap-3 py-16 px-4 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-1">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                         class="text-gray-400">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                <span class="font-medium text-gray-500">We couldn't find this paper</span>
                <span class="text-xs text-gray-400 max-w-[280px]">
                    It may still be processing, or the link may be out of date. Try going back and opening it again.
                </span>
            </div>

            <template v-else>
                <!-- Header -->
                <div class="mb-7">
                    <div class="flex items-center gap-2 mb-1">
                        <span
                            class="text-[0.7rem] font-semibold tracking-widest uppercase text-primary/60">Past Questions</span>
                    </div>
                    <h1 class="m-0 mb-1 text-[1.5rem] font-bold text-[var(--color-text,#1a202c)] leading-tight">
                        {{ past_question.code ?? 'Untitled course' }}
                        <span class="font-normal text-gray-400">·</span>
                        {{ past_question.title ?? 'Untitled paper' }}
                    </h1>
                    <p class="m-0 mt-1.5 text-sm text-[var(--color-muted,#718096)] leading-relaxed">
                        Practice with real exam papers. Understand patterns, boost confidence.
                    </p>
                </div>

                <!-- Search -->
                <div class="relative mb-6">
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
                        placeholder="Search by session or title..."
                        class="w-full box-border py-[0.65rem] pr-10 pl-[2.4rem] border-[1.5px] border-[var(--color-border,#e2e8f0)] rounded-[0.6rem] text-[0.925rem] bg-[var(--color-bg,#f8fafc)] text-[var(--color-text,#1a202c)] outline-none transition-[border-color,box-shadow] duration-200 font-inherit focus:border-primary focus:bg-[var(--color-surface,#fff)]"
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

                <!-- Empty (no papers match / none exist) -->
                <div v-if="filtered.length === 0"
                     class="flex flex-col items-center gap-3 py-14 px-4 text-[var(--color-muted,#a0aec0)] text-[0.9rem]">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-1">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                             class="text-gray-400">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                    <span class="font-medium text-gray-500">No past questions found</span>
                    <span class="text-xs text-gray-400">Try a different session or keyword</span>

                    <ScanCTA
                        title="Didn't get the year/session you're looking for?"
                        description="Got any year at hand?, Scan and start practicing in CBT mode"
                    />
                </div>

                <!-- Paper List -->
                <ul v-else class="list-none m-0 p-0 flex flex-col gap-3">
                    <li
                        v-for="(paper, i) in filtered"
                        :key="paper.id"
                        class="group relative rounded-xl border border-[var(--color-border,#e8edf2)] bg-white hover:border-primary/30 hover:shadow-[0_4px_16px_rgba(0,0,0,0.07)] transition-all duration-200 animate-[fadeUp_0.35s_ease_both] overflow-hidden"
                        :style="{ animationDelay: `${i * 50}ms` }"
                    >
                        <!-- Top accent bar -->
                        <div class="h-[3px] w-full bg-gradient-to-r from-primary/60 via-primary to-primary/40"></div>

                        <div class="flex items-start gap-4 p-4">
                            <!-- Icon -->
                            <div
                                class="shrink-0 w-11 h-11 bg-primary/8 text-primary rounded-xl flex items-center justify-center mt-0.5 group-hover:bg-primary/12 transition-colors duration-200">
                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" stroke="currentColor"
                                     stroke-width="1.8">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <path d="M8 13h6M8 17h4"/>
                                </svg>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">

                                <!-- Title row -->
                                <div class="flex items-start justify-between gap-2 mb-1">
                                        <span
                                            class="font-semibold text-[0.95rem] text-[var(--color-text,#1a202c)] leading-snug">
                                            {{ past_question.code ?? 'Course' }} · {{ paper.session ?? 'Session unspecified' }}
                                            <span v-if="paper.semester?.name" class="font-normal text-gray-400 text-[0.85rem]">
                                                — {{ paper.semester.name }} semester
                                            </span>
                                        </span>
                                </div>

                                <!-- Description -->
                                <p v-if="paper.description" class="m-0 mb-2.5 text-[0.82rem] text-[var(--color-muted,#718096)] leading-relaxed">
                                    {{ paper.description }}
                                </p>

                                <!-- Meta pills row -->
                                <div class="flex items-center gap-2 flex-wrap mb-3.5">
        <span
            class="inline-flex items-center gap-1 text-[0.7rem] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 border border-blue-100">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3"
                                                                                                                       y="4"
                                                                                                                       width="18"
                                                                                                                       height="18"
                                                                                                                       rx="2"/><path
                d="M16 2v4M8 2v4M3 10h18"/></svg>
            {{ paper.session ?? 'Unspecified' }}
        </span>
                                    <span v-if="paper.duration_minutes"
                                          class="inline-flex items-center gap-1 text-[0.7rem] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 border border-amber-100">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle
                cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            {{ paper.duration_minutes }} mins
        </span>
                                    <span v-if="paper.instructions"
                                          class="inline-flex items-center gap-1 text-[0.7rem] text-gray-400 truncate max-w-[220px]"
                                          :title="paper.instructions">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle
                cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
            {{ paper.instructions }}
        </span>

                                    <Link href="/contributors">
                                    <span
                                        class="inline-flex items-center gap-1 text-[0.7rem] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            Contributed by {{ paper.creator?.username ?? 'myUniAlly' }}
        </span>
                                    </Link>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-col md:flex-row items-center justify-between gap-2">
                                    <a
                                        v-if="paper.source_file"
                                        :href="paper.source_file"
                                        :download="paper.title ?? 'past-question'"
                                        class="inline-flex w-full border-2 border-primary items-center gap-1.5 px-4 py-2 border border-[var(--color-border,#e2e8f0)] text-[var(--color-muted,#4a5568)] bg-gray-50 rounded-lg text-[0.82rem] font-semibold no-underline transition-all duration-150 hover:border-primary/40 hover:text-primary hover:bg-primary/5 hover:-translate-y-px active:translate-y-0 font-inherit"
                                    >
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2.5">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                            <polyline points="7 10 12 15 17 10"/>
                                            <line x1="12" y1="15" x2="12" y2="3"/>
                                        </svg>
                                        Download
                                    </a>
                                    <span v-else
                                          class="inline-flex w-full items-center justify-center gap-1.5 px-4 py-2 border border-dashed border-[var(--color-border,#e2e8f0)] text-[var(--color-muted,#a0aec0)] rounded-lg text-[0.78rem] font-medium"
                                    >
                                        Preparing file...
                                    </span>
                                    <Link v-if="past_question.code"
                                          :href="`/pastquestions/${past_question.code}/${paper.id}`"
                                          class="inline-flex w-full items-center gap-1.5 px-4 py-2 bg-primary text-white rounded-lg text-[0.82rem] font-semibold no-underline transition-all duration-150 hover:opacity-90 hover:-translate-y-px active:translate-y-0 font-inherit shadow-[0_2px_8px_rgba(0,0,0,0.12)]"
                                    >
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2.5">
                                            <polygon points="5 3 19 12 5 21 5 3"/>
                                        </svg>
                                        Start Practice
                                    </Link>
                                    <!-- No course code yet (unmatched scan) — practice route needs a course slug, so fall back to id-based review -->
                                    <Link v-else
                                          :href="`/scan/review/${paper.id}`"
                                          class="inline-flex w-full items-center gap-1.5 px-4 py-2 bg-primary text-white rounded-lg text-[0.82rem] font-semibold no-underline transition-all duration-150 hover:opacity-90 hover:-translate-y-px active:translate-y-0 font-inherit shadow-[0_2px_8px_rgba(0,0,0,0.12)]"
                                    >
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2.5">
                                            <polygon points="5 3 19 12 5 21 5 3"/>
                                        </svg>
                                        Start Practice
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>

                <p v-if="(past_question.past_question ?? []).length"
                   class="mt-5 text-right text-[0.78rem] text-[var(--color-muted,#a0aec0)]">
                    {{ filtered.length }} of {{ past_question.past_question.length }}
                    paper{{ past_question.past_question.length !== 1 ? 's' : '' }}
                </p>

                <ScanCTA
                    v-if="filtered.length !== 0"
                    title="Didn't get the year/session you're looking for?"
                    description="Got any year at hand?, Scan and start practicing in CBT mode"
                />
            </template>
        </div>
    </div>
</template>

<script setup>
import {ref, computed, onMounted, onUnmounted} from 'vue'
import {Head, router, Link} from '@inertiajs/vue3'
import BackButton from "@/Components/BackButton.vue"
import ScanCTA from "@/Components/ScanCTA.vue";

const props = defineProps({
    past_question: {
        type: Object,
        // Nullable on purpose: a scan can resolve to a paper whose relations
        // (and in edge cases the paper lookup itself) aren't ready yet.
        default: null
    }
})

const query = ref('')

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase()

    const data = props.past_question?.past_question ?? []

    if (!q) return data

    return data.filter(p =>
        (p.title ?? '').toLowerCase().includes(q) ||
        (p.session ?? '').toLowerCase().includes(q) ||
        (p.description ?? '').toLowerCase().includes(q)
    )
})

let interval = null

const hasMissingFiles = () => {
    const data = props.past_question?.past_question ?? []
    return data.some(p => p.source_file === null)
}

const stopPolling = () => {
    if (interval) {
        clearInterval(interval)
        interval = null
    }
}

onMounted(() => {
    // Nothing to poll for if the paper itself never resolved.
    if (!props.past_question) return
    if (!hasMissingFiles()) return

    interval = setInterval(() => {
        if (!hasMissingFiles()) {
            stopPolling()
            router.reload({only: ['past_question']})
            return
        }

        router.reload({only: ['past_question']})
    }, 5000)
})

onUnmounted(() => {
    stopPolling()
})
</script>

<style>
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
