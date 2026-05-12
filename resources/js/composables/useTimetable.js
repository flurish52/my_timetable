/**
 * useTimetable.js
 *
 * Derives all UI-facing schedule state from the new API timetable array.
 */

import { computed, ref, onMounted, onUnmounted } from 'vue'

const now = ref(Date.now())
let timer = null

function startClock() {
    timer = setInterval(() => { now.value = Date.now() }, 1000)
}
function stopClock() {
    if (timer) clearInterval(timer)
}

// ─── Colour palette ───────────────────────────────────────────────────────────
const COLOUR_TOKENS = ['primary', 'secondary', 'tertiary']
const courseColourCache = {}
let colourIndex = 0

function colourForCourse(code) {
    if (!courseColourCache[code]) {
        courseColourCache[code] = COLOUR_TOKENS[colourIndex % COLOUR_TOKENS.length]
        colourIndex++
    }
    return courseColourCache[code]
}

// ─── Time helpers ─────────────────────────────────────────────────────────────

/** "HH:MM:SS" or "HH:MM" → total minutes since midnight */
function toMinutes(timeStr) {
    const [h, m] = timeStr.split(':').map(Number)
    return h * 60 + m
}

/** "HH:MM:SS" → "H:MM AM/PM" */
function toDisplayTime(timeStr) {
    const [h, m] = timeStr.split(':').map(Number)
    const period = h >= 12 ? 'PM' : 'AM'
    const hour   = h % 12 === 0 ? 12 : h % 12
    return `${hour}:${m.toString().padStart(2, '0')} ${period}`
}

/** Current day as capitalized full name e.g. "Monday" */
function todayKey() {
    return ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][
        new Date().getDay()
        ]
}

/** Current time as total minutes since midnight */
function nowMinutes() {
    const n = new Date(now.value)
    return n.getHours() * 60 + n.getMinutes()
}

// ─── Status & progress ────────────────────────────────────────────────────────

function slotStatus(slot) {
    const start = toMinutes(slot.start_time)
    const end   = toMinutes(slot.end_time)
    const cur   = nowMinutes()
    if (cur >= start && cur < end) return 'ongoing'
    if (cur < start)               return 'upcoming'
    return 'done'
}

function lectureProgress(slot) {
    const start = toMinutes(slot.start_time)
    const end   = toMinutes(slot.end_time)
    const cur   = nowMinutes()
    if (cur <= start) return 0
    if (cur >= end)   return 100
    return Math.round(((cur - start) / (end - start)) * 100)
}

// ─── Main composable ──────────────────────────────────────────────────────────

export function useTimetable(timetableRef) {
    /** Only today's slots */
    const todaySlots = computed(() => {
        if (!timetableRef.value?.length) return []
        return timetableRef.value.filter(s => s.day_of_week.toLowerCase() === todayKey().toLowerCase())
    })

    /** Enriched items for the UI */
    const scheduleItems = computed(() =>
        todaySlots.value.map(slot => {
            const status = slotStatus(slot)
            return {
                id:         slot.id,
                start:      slot.start_time,
                end:        slot.end_time,
                time:       toDisplayTime(slot.start_time),
                endTime:    toDisplayTime(slot.end_time),
                courseCode: slot.course?.code  ?? '—',
                courseName: slot.course?.title ?? 'Unknown Course',
                creditUnit: slot.course?.credit_unit ?? null,
                venue:      slot.venue    ?? 'Venue TBA',
                lecturer:   slot.lecturer ?? 'TBA',
                isElective: !!slot.is_elective_slot,
                status,
                colour:     colourForCourse(slot.course?.code ?? slot.id),
                progress:   status === 'ongoing' ? lectureProgress(slot) : null,
            }
        })
    )

    const ongoingLecture = computed(() =>
        scheduleItems.value.find(s => s.status === 'ongoing') ?? null
    )

    const nextLecture = computed(() => {
        const cur = nowMinutes()
        return scheduleItems.value.find(s => toMinutes(s.start) > cur) ?? null
    })

    const lectureCount = computed(() => todaySlots.value.length)

    const upcomingItems = computed(() =>
        scheduleItems.value.filter(s => s.status !== 'done')
    )

    onMounted(startClock)
    onUnmounted(stopClock)

    return {
        scheduleItems,
        upcomingItems,
        ongoingLecture,
        nextLecture,
        lectureCount,
        toDisplayTime,
    }
}
