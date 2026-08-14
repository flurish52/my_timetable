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
                placeholder="Search courses..."
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
                {{ scans?.data?.length ? 'No scans match your search.' : 'No scans yet. Upload a past question to get started.' }}
            </p>
        </div>

        <ul v-else class="space-y-2.5">
            <li v-for="item in filtered" :key="item.id" class="rounded-xl border border-primary/10 bg-white p-4 transition-shadow hover:shadow-sm">
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

        <Pagination :meta="scans" class="mt-5" />

        <ScanCTA
        title="Got a new paper?"
        />
    </div>
</template>

<script setup>
import StatusBadge from './StatusBadge.vue'
import Pagination from './Pagination.vue'
import { Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import ScanCTA from "@/Components/ScanCTA.vue";

const props = defineProps({ scans: Object })
const query = ref('')

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
    if (!q) return props.scans?.data ?? []
    return (props.scans?.data ?? []).filter(c => c.title.toLowerCase().includes(q))
})
</script>

<script>
export const ScanIcon = {
    template: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7V4h3M17 4h3v3M20 17v3h-3M7 20H4v-3"/></svg>`,
}
</script>
