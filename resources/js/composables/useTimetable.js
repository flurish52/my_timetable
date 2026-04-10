/**
 * useTimetable.js
 *
 * Derives all UI-facing schedule state from the raw timetable JSON.
 * Components never touch the raw data directly — they consume what this
 * composable exposes.
 */

import { computed, ref, onMounted, onUnmounted } from 'vue'

const now = ref(Date.now())

let timer = null

function startClock() {
    timer = setInterval(() => {
        now.value = Date.now()
    }, 1000)
}

function stopClock() {
    if (timer) clearInterval(timer)
}

// ─── Colour palette cycling ───────────────────────────────────────────────────
// Maps each unique course name encountered to a rotating colour token.
// This way colours are consistent per course across the whole day.
const COLOUR_TOKENS = ['primary', 'secondary', 'tertiary']
const courseColourCache = {}
let colourIndex = 0

function colourForCourse(name) {
    if (!courseColourCache[name]) {
        courseColourCache[name] = COLOUR_TOKENS[colourIndex % COLOUR_TOKENS.length]
        colourIndex++
    }
    return courseColourCache[name]
}

// ─── Time helpers ─────────────────────────────────────────────────────────────

/** Convert "HH:MM" string → total minutes since midnight */
function toMinutes(timeStr) {
    const [h, m] = timeStr.split(':').map(Number)
    return h * 60 + m
}

/** Convert total minutes → "H:MM AM/PM" display string */
function toDisplayTime(timeStr) {
    const [h, m] = timeStr.split(':').map(Number)
    const period = h >= 12 ? 'PM' : 'AM'
    const hour   = h % 12 === 0 ? 12 : h % 12
    const min    = m.toString().padStart(2, '0')
    return `${hour}:${min} ${period}`
}

/** Current day key e.g. "monday" */
function todayKey() {
    return ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'][
        new Date().getDay()
        ]
}

/** Current time as total minutes since midnight */
function nowMinutes() {
    const n = new Date(now.value)
    return n.getHours() * 60 + n.getMinutes()
}

// ─── Progress calculation ─────────────────────────────────────────────────────

function lectureProgress(slot) {
    const start = toMinutes(slot.start)
    const end   = toMinutes(slot.end)
    const now   = nowMinutes()
    if (now <= start) return 0
    if (now >= end)   return 100
    return Math.round(((now - start) / (end - start)) * 100)
}

// ─── Status derivation ────────────────────────────────────────────────────────

function slotStatus(slot) {
    const start = toMinutes(slot.start)
    const end   = toMinutes(slot.end)
    const now   = nowMinutes()
    if (now >= start && now < end) return 'ongoing'
    if (now < start)               return 'upcoming'
    return 'done'
}

// ─── Main composable ──────────────────────────────────────────────────────────

export function useTimetable(rawData) {
    /** Today's raw slot array (courses + nulls), never undefined */
    const todaySlots = computed(() => {
        if (!rawData.value) return []
        return rawData.value.timetable[todayKey()] ?? []
    })

    /** Only slots that actually have a course (nulls are free periods) */
    const todayCourses = computed(() =>
        todaySlots.value.filter(s => s.course !== null)
    )

    /**
     * Enriched schedule items ready for the UI.
     * Each item includes: id, time, courseCode, courseName, venue, status, colour, progress (if ongoing)
     */
    const scheduleItems = computed(() =>
        todayCourses.value.map((slot, idx) => {
            const status = slotStatus(slot)
            return {
                id:         idx,
                start:      slot.start,
                end:        slot.end,
                time:       toDisplayTime(slot.start),
                courseName: slot.course,
                courseCode: slot.code   ?? slot.course,   // fallback to name if code missing
                venue:      slot.venue  ?? 'Venue TBA',   // fallback if venue missing
                status,
                colour:     colourForCourse(slot.course),
                progress:   status === 'ongoing' ? lectureProgress(slot) : null,
            }
        })
    )

    /** The currently ongoing lecture (or null if between lectures) */
    const ongoingLecture = computed(() =>
        scheduleItems.value.find(s => s.status === 'ongoing') ?? null
    )

    /** Next upcoming lecture after the ongoing one (or after now if nothing ongoing) */
    const nextLecture = computed(() => {
        const now = nowMinutes()
        return scheduleItems.value.find(s => toMinutes(s.start) > now) ?? null
    })

    /** Count of real lectures today (free periods excluded) */
    const lectureCount = computed(() => todayCourses.value.length)

    /** Only upcoming + ongoing items shown in the schedule list */
    const upcomingItems = computed(() =>
        scheduleItems.value.filter(s => s.status !== 'done')
    )

    onMounted(() => {
        startClock()
    })

    onUnmounted(() => {
        stopClock()
    })

    return {
        scheduleItems,
        upcomingItems,
        ongoingLecture,
        nextLecture,
        lectureCount,
        toDisplayTime,
    }
}
