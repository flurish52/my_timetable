<template>
    <div>
        <div class="relative mb-5">
      <span class="absolute left-[0.85rem] top-1/2 -translate-y-1/2 flex pointer-events-none text-primary/50">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
      </span>
            <input
                v-model="query"
                type="text"
                placeholder="Search attempts..."
                class="w-full box-border py-[0.65rem] pr-10 pl-[2.4rem] border-[1.5px] border-primary/20 rounded-[0.6rem] text-[0.925rem] bg-tertiary/5 text-primary outline-none transition focus:border-primary focus:bg-white"
            />
            <button v-if="query" @click="query = ''" class="absolute right-[0.85rem] top-1/2 -translate-y-1/2 bg-transparent border-none cursor-pointer flex p-[2px] rounded-full text-primary/50 hover:text-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div v-if="filtered?.length === 0" class="rounded-xl border border-dashed border-primary/20 py-14 text-center">
            <p class="text-sm text-primary/70">
                {{ history?.data?.length ? 'No attempts match your search.' : 'No practice attempts yet.' }}
            </p>
                <button
                    @click="startPractice($page.props.auth.user.roles)"
                    class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-primary/25 transition hover:opacity-90 active:scale-[0.98]"
                >
                    Start practice
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M13 6l6 6-6 6" />
                    </svg>
                </button>
        </div>

        <ul v-else class="space-y-2.5">
            <li v-for="attempt in filtered" :key="attempt.id" class="flex items-center gap-4 rounded-xl border border-primary/10 bg-white p-4 transition-shadow hover:shadow-sm">
                <div class="relative flex h-12 w-12 shrink-0 items-center justify-center">
                    <svg viewBox="0 0 40 40" class="h-12 w-12 -rotate-90">
                        <circle cx="20" cy="20" r="16" fill="none" stroke="currentColor" stroke-width="4" class="text-primary/15" />
                        <circle
                            v-if="attempt.total_attempted"
                            cx="20" cy="20" r="16" fill="none" stroke="currentColor" stroke-width="4"
                            stroke-linecap="round" class="text-secondary"
                            :stroke-dasharray="100.5"
                            :stroke-dashoffset="100.5 - (100.5 * accuracy(attempt) / 100)"
                        />
                    </svg>
                    <span class="absolute text-xs font-bold text-primary">
            {{ attempt.total_attempted ? `${accuracy(attempt)}%` : '—' }}
          </span>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-primary">{{ attempt.past_question?.title ?? 'Untitled' }}</p>
                    <div class="mt-1.5 flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-primary/70">
            <span v-if="attempt.total_attempted" class="text-secondary font-medium">
              {{ attempt.correct_answers }}/{{ attempt.total_attempted }} correct
            </span>
                        <span v-else class="text-primary/50">Incomplete attempt</span>
                        <span>{{ relativeDate(attempt.submitted_at) }}</span>
                    </div>
                </div>

                <Link :href="`/scan/review/${attempt.past_question?.id}`" class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90">
                    Retry
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>
                    </svg>
                </Link>
            </li>
        </ul>

        <Pagination :meta="history" class="mt-5" />
    </div>
</template>

<script setup>
import Pagination from './Pagination.vue'
import {Link, router} from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({ history: Object })
const query = ref('')

function accuracy(attempt) {
    if (!attempt.total_attempted) return 0
    return Math.round((attempt.correct_answers / attempt.total_attempted) * 100)
}

function relativeDate(dateStr) {
    if (!dateStr) return ''
    const diffMs = Date.now() - new Date(dateStr).getTime()
    const mins = Math.floor(diffMs / 60000)
    if (mins < 1) return 'just now'
    if (mins < 60) return `${mins}m ago`
    const hrs = Math.floor(mins / 60)
    if (hrs < 24) return `${hrs}h ago`
    const days = Math.floor(hrs / 24)
    if (days < 7) return `${days}d ago`
    return new Date(dateStr).toLocaleDateString('en-NG', { day: 'numeric', month: 'short', year: 'numeric' })
}

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase()
    if (!q) return props.history?.data ?? []
    return (props.history?.data ?? []).filter(a => a.past_question?.title?.toLowerCase().includes(q))
})

function startPractice(roles) {
    const isIndependent = roles?.some(r => r.name === 'independent')

    if (isIndependent) {
        router.visit('/activity?tab=scans')
    } else {
        router.visit('/pastquestions')
    }
}
</script>
