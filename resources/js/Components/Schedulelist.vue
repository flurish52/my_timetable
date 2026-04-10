<script setup>
import ScheduleItem from './ScheduleItem.vue'

defineProps({
    /** Enriched schedule items from useTimetable (upcoming + ongoing only) */
    items: {
        type: Array,
        required: true,
    },
})
</script>

<template>
    <div class="mx-5 mt-4 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <p class="text-sm text-amber-700 bg-amber-50 border-l-4 border-amber-500 px-3 py-2">
        Please note that this timetable is for Edu Biology students ONLY! </p>

        <!-- Header -->
        <div class="flex items-center justify-between px-4 pt-4 pb-1">
            <h3 class="text-base font-extrabold text-gray-900 tracking-tight">Today's Schedule</h3>
            <button class="flex items-center gap-0.5 text-sm font-semibold text-secondary hover:opacity-75 transition-opacity">
                See all
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <div class="mx-4 mt-2 border-t border-gray-100" />

        <!-- Items -->
        <div class="px-4 divide-y divide-gray-50">
            <ScheduleItem
                v-for="item in items"
                :key="item.id"
                :item="item"
            />

            <!-- Empty state: no remaining lectures -->
            <div v-if="items.length === 0" class="py-8 text-center">
                <p class="text-gray-400 text-sm font-medium">No upcoming lectures for today.</p>
            </div>
        </div>

        <!-- View Full Timetable CTA -->
        <div class="p-4 pt-3">
            <button
                class="w-full flex items-center justify-between bg-secondary hover:bg-secondary/90 active:scale-[0.99] transition-all duration-200 text-white font-bold text-base rounded-2xl px-5 py-4 shadow-md shadow-secondary/25"
            >
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 opacity-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                    </svg>
                    View Full Timetable
                </div>
                <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

    </div>
</template>
