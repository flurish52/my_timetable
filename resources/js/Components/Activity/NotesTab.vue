<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import ScanCTA from "@/Components/ScanCTA.vue";
import StatusBadge from "@/Components/Activity/StatusBadge.vue";

const props = defineProps({
    notes: { type: Object, default: null },
})

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


const items = computed(() => props.notes?.data ?? [])

const formatDate = (value) =>
    new Date(value).toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' })

function goTo(url) {
    if (!url) return
    const page = new URL(url).searchParams.get('page')
    router.get(
        route('activity.index'),
        { tab: 'notes', page },
        { preserveState: true, preserveScroll: true, only: ['notes', 'activeTab'] }
    )
}
</script>

<template>
    <!-- Loading (lazy prop not yet fetched) -->
    <div v-if="!notes" class="space-y-3">
        <div v-for="n in 3" :key="n" class="h-20 animate-pulse rounded-xl bg-slate-100" />
    </div>

    <!-- Empty state -->
    <div v-else-if="items.length === 0" class="rounded-2xl border border-dashed border-primary/30 bg-white p-8 text-center">
        <h2 class="text-base font-semibold text-slate-900">No notes quizzes yet</h2>
        <p class="mx-auto mt-1 max-w-xs text-sm text-slate-500">
            Snap a photo of your lecture notes and get a practice quiz you can take right away.
        </p>
        <Link
            :href="route('scan.notes.create')"
            class="mt-4 inline-flex items-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary/90"
        >
            Scan your notes
        </Link>
    </div>

    <!-- List -->
    <ul v-else class="space-y-2.5">
        <li v-for="item in items" :key="item.id" class="rounded-xl border border-primary/10 bg-white p-4 transition-shadow hover:shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-primary">{{ item.title }}</p>
                    <div class="mt-1.5 flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-primary/70">
              <span class="inline-flex items-center gap-1">
                <ScanIcon class="h-3.5 w-3.5" /> Scanned
              </span>
                        <span v-if="item.session && item.session !== 'Unspecified'">{{ item.session }}</span>
                        <span>{{ relativeDate(item.created_at) }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <StatusBadge :status="item?.visibility" />
                    <Link :href="`/scan/review/${item.id}`" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90">
                        Practice
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>
                        </svg>
                    </Link>
                </div>
            </div>
        </li>
    </ul>

    <ScanCTA
        title="Want to scan from your note?"
        description="Snap a photo of your lecture notes. Get exam-style questions, ready to practice."
    />
</template>
