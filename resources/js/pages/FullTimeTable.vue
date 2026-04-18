<script setup>
import {ref, computed, onMounted} from 'vue'
import { RouterLink } from 'vue-router'

// ── Data ──────────────────────────────────────────────────────────────────────
const rawData = ref(null)
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
    try {
        const res = await fetch('/data/timetable.json', {
            cache: 'no-cache'
        })
        if (!res.ok) throw new Error(`HTTP ${res.status}`)
        rawData.value = await res.json()
    } catch (err) {
        error.value = err.message
    } finally {
        loading.value = false
    }
})

// ── Config ────────────────────────────────────────────────────────────────────
const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']

const DAY_SHORT = {
    monday: 'Mon', tuesday: 'Tue', wednesday: 'Wed', thursday: 'Thu', friday: 'Fri',
}

// Hour slots 7 → 16 (each row = one hour block e.g. 7:00–8:00)
const HOUR_SLOTS = Array.from({length: 10}, (_, i) => {
    const h = i + 7
    const fmt = (n) => {
        const period = n >= 12 ? 'PM' : 'AM'
        const display = n > 12 ? n - 12 : n
        return `${display}:00 ${period}`
    }
    return {
        label: `${fmt(h)} – ${fmt(h + 1)}`,
        start: `${String(h).padStart(2, '0')}:00`,
        end: `${String(h + 1).padStart(2, '0')}:00`,
        startH: h,
        endH: h + 1,
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

// ── Build a lookup: day → Map<slotStart, slot> ────────────────────────────────
// For each hour row we find which course (if any) covers that hour window.
// A course covers a slot if its time range overlaps the slot.
const timetableMap = computed(() => {
    if (!rawData.value) return {}
    const map = {}
    for (const day of DAYS) {
        map[day] = {}
        const slots = rawData.value.timetable[day] ?? []
        for (const hourSlot of HOUR_SLOTS) {
            const slotStartMin = toMinutes(hourSlot.start)
            const slotEndMin = toMinutes(hourSlot.end)
            // Find a course whose window overlaps this hour slot
            const match = slots.find(s => {
                if (!s.course) return false
                const cStart = toMinutes(s.start)
                const cEnd = toMinutes(s.end)
                // overlap: course starts before slot ends AND course ends after slot starts
                return cStart < slotEndMin && cEnd > slotStartMin
            })
            map[day][hourSlot.start] = match ?? null
        }
    }
    return map
})

// ── Ongoing detection ─────────────────────────────────────────────────────────
function isOngoing(slot) {
    if (!slot) return false
    const now = nowMinutes()
    const start = toMinutes(slot.start)
    const end = toMinutes(slot.end)
    return now >= start && now < end
}

function isOngoingRow(hourSlot) {
    if (!DAYS.includes(todayKey.value)) return false
    const slot = timetableMap.value[todayKey.value]?.[hourSlot.start]
    return isOngoing(slot)
}
</script>

<template>
    <div class="flex px-4 md:px-12 flex-col h-full bg-white">
        <!-- Loading -->
        <div v-if="loading" class="flex-1 flex flex-col items-center justify-center gap-3">
            <div class="w-8 h-8 rounded-full border-4 border-primary/20 border-t-primary animate-spin"/>
            <p class="text-xs text-gray-400 font-medium">Loading timetable…</p>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="flex-1 flex items-center justify-center px-6">
            <p class="text-red-500 text-sm font-semibold text-center">{{ error }}</p>
        </div>

        <!-- Table -->
        <template v-else-if="rawData">

            <!--
              Single overflow-auto container.
              sticky top-0  → header row stays fixed while scrolling vertically
              sticky left-0 → time column stays fixed while scrolling horizontally
            -->

            <header class="flex justify-between items-center w-full bg-white border-b px-4 py-3 sticky top-0 z-50 shadow-sm ">
                <div>

                    <h1 class="text-2xl font-display font-bold text-primary">
                        Full Timetable
                    </h1>

                    <p class="text-sm text-gray-500 font-medium">
                        View all lectures and free periods for the week
                    </p>
                </div>
                <RouterLink to="/" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary hover:bg-gray-100 rounded-md transition">
                    <span class="text-lg">←</span>
                    Back
                </RouterLink>
            </header>

            <div class="flex-1 overflow-auto" id="tt-scroll">
                <table class="border-collapse" style="min-width: 640px; width: 100%;">

                    <!-- ── HEADER ROW (sticky top) ──────────────────────────────────── -->
                    <thead>
                    <tr>
                        <!-- Corner cell -->
                        <th
                            class="sticky top-0 left-0 z-40 bg-white border-b border-r border-gray-200 px-3 py-3 w-24 min-w-[96px]"
                        >
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Time</span>
                        </th>

                        <!-- Day columns -->
                        <th
                            v-for="day in DAYS"
                            :key="day"
                            :class="[
                  'sticky top-0 z-30 border-b border-r border-gray-200 last:border-r-0 px-2 py-3 text-center',
                  day === todayKey ? 'bg-secondary/5' : 'bg-white',
                ]"
                        >
                            <div class="flex flex-col items-center gap-0.5">
                  <span
                      :class="[
                      'text-[11px] font-extrabold uppercase tracking-widest',
                      day === todayKey ? 'text-secondary' : 'text-gray-500',
                    ]"
                  >
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

                    <!-- ── BODY ROWS (one per hour slot) ───────────────────────────── -->
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
                        <!-- Time label (sticky left) -->
                        <td
                            :class="[
                  'sticky left-0 z-20 border-b border-r border-gray-200 px-3 py-0 w-24 min-w-[96px]',
                  isOngoingRow(hourSlot) ? 'bg-secondary/5' : 'bg-white group-hover:bg-gray-50/60',
                ]"
                        >
                <span
                    :class="[
                    'text-[11px] font-semibold whitespace-nowrap',
                    isOngoingRow(hourSlot) ? 'text-secondary' : 'text-gray-400',
                  ]"
                >
                  {{ hourSlot.label }}
                </span>
                        </td>

                        <!-- Day cells -->
                        <td
                            v-for="day in DAYS"
                            :key="day"
                            :class="[
                  'border-b border-r border-gray-100 last:border-r-0 px-3 align-middle',
                  'h-16',
                ]"
                            style="vertical-align: middle;"
                        >
                            <template v-if="timetableMap[day]?.[hourSlot.start]">
                                <!-- Has a lecture -->
                                <div
                                    :class="[
                      'flex flex-col gap-0',
                      isOngoing(timetableMap[day][hourSlot.start]) && day === todayKey
                        ? 'opacity-100'
                        : '',
                    ]"
                                >
                                    <!-- Ongoing indicator dot (only on today's active cell) -->
                                    <div
                                        v-if="isOngoing(timetableMap[day][hourSlot.start]) && day === todayKey"
                                        class="flex items-center gap-1 mb-1"
                                    >
                      <span class="relative flex h-1.5 w-1.5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-60"/>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-secondary"/>
                      </span>
                                        <span
                                            class="text-[9px] font-bold text-secondary uppercase tracking-widest">Live</span>
                                    </div>

                                    <!-- Course name -->
                                    <p
                                        :class="[
                        'text-[12px] font-bold leading-tight',
                        isOngoing(timetableMap[day][hourSlot.start]) && day === todayKey
                          ? 'text-secondary'
                          : 'text-gray-800',
                      ]"
                                    >
                                        {{ timetableMap[day][hourSlot.start].course }}
                                    </p>

                                    <!-- Venue -->
                                    <p class="text-[11px] text-gray-400 font-medium leading-tight mt-0.5 truncate">
                                        {{ timetableMap[day][hourSlot.start].venue ?? 'TBA' }}
                                    </p>
                                </div>
                            </template>

                            <!-- Empty slot: dash -->
                            <template v-else>
                                <span class="text-gray-300 text-sm select-none">—</span>
                            </template>
                        </td>

                    </tr>
                    </tbody>

                </table>
            </div>

        </template>
    </div>
</template>

<style scoped>
#tt-scroll {
    -webkit-overflow-scrolling: touch;
}
</style>
