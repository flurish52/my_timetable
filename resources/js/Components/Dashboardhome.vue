<script setup>
import {computed} from 'vue'
import {useTimetable} from '../composables/useTimetable.js'

import GreetingHeader from './GreetingHeader.vue'
import OngoingLectureCard from './OngoingLectureCard.vue'
import ScheduleList from './ScheduleList.vue'

const props = defineProps({
    timetable: {
        type: Array,
        required: true,
    },
    programme: {
        type: Object,
        required: true,
    },
    user: {
        type: Object,
        default: null,
    },
})

// Wrap the prop in a computed ref so the composable stays reactive
const timetableRef = computed(() => props.timetable)
const {
    upcomingItems,
    ongoingLecture,
    nextLecture,
    lectureCount,
} = useTimetable(timetableRef)
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-md mx-auto pb-10">
            <GreetingHeader
                :user="user"
                :lecture-count="lectureCount"
            />
            <OngoingLectureCard
                :ongoing="ongoingLecture"
                :next="nextLecture"
            />
            <ScheduleList
                :items="upcomingItems"
                :programme="programme"
            />
        </div>
    </div>
</template>
