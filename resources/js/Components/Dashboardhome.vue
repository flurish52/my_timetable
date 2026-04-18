<script setup>
import {ref, onMounted} from 'vue'
import {useTimetable} from '../composables/useTimetable.js'

import GreetingHeader from './GreetingHeader.vue'
import OngoingLectureCard from './OngoingLectureCard.vue'
import ScheduleList from './ScheduleList.vue'

// ── Data fetching ─────────────────────────────────────────────────────────────
const rawData = ref(null)
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
    try {
        const res = await fetch('/data/timetable.json', {
            cache: 'no-cache'
        })
        if (!res.ok) throw new Error(`HTTP ${res.status} — could not load timetable.json`)
        rawData.value = await res.json()
    } catch (err) {
        error.value = err.message
    } finally {
        loading.value = false
    }
})

// ── Derived schedule state (all logic lives in composable) ────────────────────
const {
    upcomingItems,
    ongoingLecture,
    nextLecture,
    lectureCount,
} = useTimetable(rawData)
</script>

<template>
    <div class="relative min-h-screen bg-gray-50">

        <!-- Loading -->
        <div v-if="loading" class="flex flex-col items-center justify-center min-h-screen gap-3">
            <div class="w-10 h-10 rounded-full border-4 border-primary/20 border-t-primary animate-spin"/>
            <p class="text-sm text-gray-400 font-medium">Loading your schedule…</p>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="flex items-center justify-center min-h-screen px-6">
            <div class="text-center space-y-1">
                <p class="text-red-500 font-semibold text-sm">{{ error }}</p>
                <p class="text-gray-400 text-xs">
                    Ensure <code class="bg-gray-100 px-1 rounded">timetable</code> exists.
                </p>
            </div>
        </div>

        <!-- Main UI -->
        <div v-else-if="rawData" class="max-w-md mx-auto pb-10">
            <div class="sticky top-0 z-50 bg-primary text-lg text-center w-full font-semibold text-white tracking-wide py-1 shadow-sm">
                my<span class="text-secondary font-bold">Timetable</span>
            </div>
            <GreetingHeader
                :user="rawData.user"
                :lecture-count="lectureCount"
            />


            <OngoingLectureCard
                :ongoing="ongoingLecture"
                :next="nextLecture"
            />


            <ScheduleList :items="upcomingItems"/>

        </div>
    </div>
</template>
