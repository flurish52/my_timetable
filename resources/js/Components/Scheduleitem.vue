<script setup>
defineProps({
    item: {
        type: Object,
        required: true,
    },
})

// Static colour-token → Tailwind class maps.
// Using explicit strings (not dynamic) so Tailwind's JIT scanner can detect them.
const colours = {
    primary: {
        border:  'bg-primary',
        text:    'text-primary',
        iconBg:  'bg-primary/10 text-primary',
        badge:   'border-primary/20 bg-primary/5 text-primary',
    },
    secondary: {
        border:  'bg-secondary',
        text:    'text-secondary',
        iconBg:  'bg-secondary/10 text-secondary',
        badge:   'border-secondary/20 bg-secondary/5 text-secondary',
    },
    tertiary: {
        border:  'bg-tertiary',
        text:    'text-tertiary',
        iconBg:  'bg-tertiary/10 text-tertiary',
        badge:   'border-tertiary/20 bg-tertiary/5 text-tertiary',
    },
}

const c = (item) => colours[item.colour] ?? colours.primary
</script>

<template>
    <div class="flex items-center gap-1 md:gap-3 py-3.5">

        <!-- Accent bar -->
        <div :class="['w-[3px] h-11 rounded-full flex-shrink-0 bg-primary', c(item).border]" />

        <!-- Time -->
        <div class="w-[52px] flex-shrink-0 text-right leading-none">
      <span class="text-gray-800 font-bold text-[15px]">
        {{ item.time.split(' ')[0] }}
      </span>
            <span class="block text-gray-400 text-[11px] font-medium mt-0.5">
        {{ item.time.split(' ')[1] }}
      </span>
        </div>

        <!-- Icon bubble -->
        <div :class="['w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0', c(item).iconBg]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                />
            </svg>
        </div>

        <!-- Course info -->
        <div class="flex-1 min-w-0">
            <!-- Course code — bold, coloured, prominent -->
            <p :class="['font-extrabold text-[15px] leading-tight truncate', c(item).text]">
                {{ item.courseCode }}
            </p>
            <!-- Course full name -->
            <p class="text-gray-600 text-xs font-semibold truncate mt-0.5">
                {{ item.courseName }}
            </p>
            <!-- Venue -->
            <div class="flex items-center gap-1 mt-0.5">
                <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p class="text-gray-400 text-[11px] font-medium truncate">{{ item.venue }}</p>
            </div>
        </div>

        <!-- Status badge -->
        <div class="flex-shrink-0">

            <!-- Ongoing -->
            <span
                v-if="item.status === 'ongoing'"
                class="flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1.5 rounded-full border border-green-200 bg-green-50 text-green-600"
            >
        <span class="relative flex h-1.5 w-1.5">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75" />
          <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-green-400" />
        </span>
        Live
      </span>

            <!-- Upcoming -->
            <span
                v-else-if="item.status === 'upcoming'"
                :class="['flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1.5 rounded-full border', c(item).badge]"
            >
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Upcoming
      </span>

        </div>
    </div>
</template>
