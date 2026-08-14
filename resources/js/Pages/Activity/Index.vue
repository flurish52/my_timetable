<template>
    <Head title="Activities" />
    <div class="max-w-3xl mx-auto px-4 py-6 sm:px-6">
        <h1 class="text-xl font-semibold text-primary sm:text-2xl">My Activity</h1>
        <p class="mt-1 text-sm text-primary/70">Your scanned past questions and practice history.</p>

        <!-- Tabs -->
        <div class="mt-6 inline-flex w-full gap-1 rounded-xl bg-tertiary/5 p-1 sm:w-auto">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="changeTab(tab.key)"
                :class="[
          'relative flex flex-1 items-center justify-center gap-2 rounded-lg border-2 px-4 py-2 text-sm font-medium transition-all duration-200 sm:flex-initial',
          active === tab.key
            ? 'border-primary bg-primary text-white shadow-sm'
            : 'border-primary/30 text-primary hover:border-primary/60',
        ]"
            >
                {{ tab.label }}
                <span
                    v-if="tab.count"
                    :class="[
            'rounded-full px-1.5 py-0.5 text-xs font-semibold transition-colors',
            active === tab.key ? 'bg-white/20 text-white' : 'bg-secondary/15 text-secondary',
          ]"
                >{{ tab.count }}</span>
            </button>
        </div>

        <!-- Panels -->
        <div class="mt-5">
            <ScansTab v-if="active === 'scans'" :scans="scans" />
            <PracticeHistoryTab v-else :history="history" />
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

import ScansTab from '@/Components/Activity/ScansTab.vue'
import PracticeHistoryTab from '@/Components/Activity/PracticeHistoryTab.vue'

const props = defineProps({
    scans: Object,
    history: Object,
})

const active = ref('scans')

const tabs = computed(() => [
    { key: 'scans', label: 'My Scans', count: props.scans?.total },
    { key: 'history', label: 'Practice History', count: props.history?.total },
])

const changeTab = (tab) => {
    active.value = tab
    if (tab === 'history' && !props.history) {
        router.reload({ only: ['history'], preserveState: true, preserveScroll: true })
    }
}
</script>
