<script setup>
import { computed } from 'vue'
import GuestLayout from "@/Layouts/GuestLayout.vue";

const props = defineProps({
    timetable: {
        type: Array,
        required: true,
    }
})

// ── Config ────────────────────────────────────────────────────────────────────
const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']

const DAY_SHORT = {
    monday: 'Mon', tuesday: 'Tue', wednesday: 'Wed', thursday: 'Thu', friday: 'Fri', saturday: 'Sat'
}

// Hour slots 7 → 16
const HOUR_SLOTS = Array.from({ length: 11 }, (_, i) => {
    const h = i + 7
    const fmt = (n) => {
        const period = n >= 12 ? 'PM' : 'AM'
        const display = n > 12 ? n - 12 : n
        return `${display}:00 ${period}`
    }
    return {
        label: `${fmt(h)} – ${fmt(h + 1)}`,
        start: `${String(h).padStart(2, '0')}:00`,
        end:   `${String(h + 1).padStart(2, '0')}:00`,
        startH: h,
        endH:   h + 1,
    }
})

// ── Time helpers ──────────────────────────────────────────────────────────────
function toMinutes(t) {
    const [h, m] = t.split(':').map(Number)
    return h * 60 + m
}

function nowMinutes() {
    const n = new Date()
    return n.getHours() * 60 + n.getMinutes()
}

const todayKey = computed(() =>
    ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'][new Date().getDay()]
)

// ── Build lookup from DB records ──────────────────────────────────────────────
const timetableMap = computed(() => {
    const map = {}

    for (const day of DAYS) {
        map[day] = {}

        const dayRecords = props.timetable.filter(
            r => r.day_of_week.toLowerCase() === day
        )

        for (const hourSlot of HOUR_SLOTS) {
            const slotStartMin = toMinutes(hourSlot.start)
            const slotEndMin   = toMinutes(hourSlot.end)

            const match = dayRecords.find(r => {
                const cStart = toMinutes(r.start_time)
                const cEnd   = toMinutes(r.end_time)
                return cStart < slotEndMin && cEnd > slotStartMin
            })

            map[day][hourSlot.start] = match
                ? {
                    course:   match.course?.title  ?? `Course ${match.course_id}`,
                    code:     match.course?.code   ?? null,
                    lecturer: match.lecturer       ?? null,
                    venue:    match.venue          ?? null,
                    start:    match.start_time,
                    end:      match.end_time,
                    raw:      match,
                }
                : null
        }
    }

    return map
})

// ── Ongoing detection ─────────────────────────────────────────────────────────
function isOngoing(slot) {
    if (!slot) return false
    const now   = nowMinutes()
    const start = toMinutes(slot.start)
    const end   = toMinutes(slot.end)
    return now >= start && now < end
}

function isOngoingRow(hourSlot) {
    if (!DAYS.includes(todayKey.value)) return false
    const slot = timetableMap.value[todayKey.value]?.[hourSlot.start]
    return isOngoing(slot)
}
</script>

<template>
    <GuestLayout>
        <div class="flex px-4 md:px-12 flex-col h-full bg-white">

            <header class="flex justify-between items-center w-full bg-white border-b px-4 py-3 sticky top-0 z-40 shadow-sm">
                <div>
                    <h1 class="text-2xl font-display font-bold text-primary">Full Timetable</h1>
                    <p class="text-sm text-gray-500 font-medium">
                        View all lectures and free periods for the week
                    </p>
                </div>
            </header>

            <!-- Empty state -->
            <div v-if="!timetable.length" class="flex-1 flex items-center justify-center">
                <p class="text-gray-400 text-sm font-medium text-center">
                    No timetable data available for your department. <br>
                Please check back later. 🙏
                </p>
            </div>

            <!-- Table -->
            <div v-else class="flex-1 overflow-auto" id="tt-scroll">
                <table class="border-collapse table-fixed" style="min-width: 640px; width: 100%;">

                    <!-- ── HEADER ROW ──────────────────────────────────────────── -->
                    <thead>
                    <tr>
                        <th class="sticky top-0 left-0 z-30 bg-white border-b border-r border-gray-200 px-3 py-1 w-24 min-w-[96px]">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Time</span>
                        </th>

                        <th
                            v-for="day in DAYS"
                            :key="day"
                            :class="[
                                'sticky top-0 z-20 border-b border-r border-gray-200 last:border-r-0 px-2 py-3 text-center',
                                day === todayKey ? 'bg-secondary/5' : 'bg-white',
                            ]"
                        >
                            <div class="flex flex-col items-center gap-0.5">
                                <span :class="[
                                    'text-[11px] font-extrabold uppercase tracking-widest',
                                    day === todayKey ? 'text-secondary' : 'text-gray-500',
                                ]">
                                    {{ DAY_SHORT[day] }}
                                </span>
                                <span
                                    v-if="day === todayKey"
                                    class="text-[9px] font-bold bg-secondary text-white px-1.5 py-0.5 rounded-full leading-none"
                                >
                                    TODAY
                                </span>
                            </div>
                        </th>
                    </tr>
                    </thead>

                    <!-- ── BODY ROWS ───────────────────────────────────────────── -->
                    <tbody>
                    <tr
                        v-for="hourSlot in HOUR_SLOTS"
                        :key="hourSlot.start"
                        :class="[
                            'group',
                            isOngoingRow(hourSlot) ? 'bg-secondary/5' : 'hover:bg-gray-50/60',
                            'transition-colors duration-100',
                        ]"
                    >
                        <!-- Time label -->
                        <td :class="[
                            'sticky left-0 z-20 border-b border-r border-gray-200 px-0 py-0 w-24 min-w-[96px]',
                            isOngoingRow(hourSlot) ? 'bg-secondary/5' : 'bg-white group-hover:bg-gray-50/60',
                        ]">
                            <span :class="[
                                'text-[10px] font-semibold whitespace-nowrap',
                                isOngoingRow(hourSlot) ? 'text-secondary' : 'text-gray-500',
                            ]">
                                {{ hourSlot.label }}
                            </span>
                        </td>

                        <!-- Day cells -->
                        <td
                            v-for="day in DAYS"
                            :key="day"
                            class="border-b border-r border-gray-100 last:border-r-0 px-3 h-16 align-middle overflow-hidden"
                            style="vertical-align: middle; max-width: 0;"
                        >
                            <template v-if="timetableMap[day]?.[hourSlot.start]">
                                <div :class="[
                                    'flex flex-col gap-0 w-full min-w-0',
                                    isOngoing(timetableMap[day][hourSlot.start]) && day === todayKey ? 'opacity-100' : '',
                                ]">
                                    <!-- Live indicator -->
                                    <div
                                        v-if="isOngoing(timetableMap[day][hourSlot.start]) && day === todayKey"
                                        class="flex items-center gap-1 mb-1"
                                    >
                                        <span class="relative flex h-1.5 w-1.5">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-60"/>
                                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-secondary"/>
                                        </span>
                                        <span class="text-[9px] font-bold text-secondary uppercase tracking-widest">Live</span>
                                    </div>

                                    <!-- Course code -->
                                    <p class="text-[10px] font-bold text-gray-900 uppercase tracking-wide leading-none truncate">
                                        {{ timetableMap[day][hourSlot.start].code }}
                                    </p>

                                    <!-- Course title -->
                                    <p :class="[
                                        'text-[12px] leading-tight mt-0.5 truncate',
                                        isOngoing(timetableMap[day][hourSlot.start]) && day === todayKey
                                            ? 'text-secondary'
                                            : 'text-gray-500',
                                    ]"
                                       :title="timetableMap[day][hourSlot.start].course"
                                    >
                                        {{ timetableMap[day][hourSlot.start].course }}
                                    </p>

                                    <!-- Venue · Lecturer -->
                                    <p class="text-[11px] text-gray-400 font-medium leading-tight mt-0.5 truncate">
                                        {{ timetableMap[day][hourSlot.start].venue ?? 'TBA' }}
                                        <br />
                                        <span v-if="timetableMap[day][hourSlot.start].lecturer">
                                            · {{ timetableMap[day][hourSlot.start].lecturer }}
                                        </span>
                                    </p>
                                </div>
                            </template>

                            <template v-else>
                                <span class="text-gray-300 text-sm select-none">—</span>
                            </template>
                        </td>
                    </tr>
                    </tbody>

                </table>
            </div>

        </div>
    </GuestLayout>
</template>

<style scoped>
#tt-scroll {
    -webkit-overflow-scrolling: touch;
}
</style>
