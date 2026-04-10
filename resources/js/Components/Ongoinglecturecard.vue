<script setup>
defineProps({
    /** The currently ongoing schedule item, or null */
    ongoing: {
        type: Object,
        default: null,
    },
    /** The next upcoming schedule item, or null */
    next: {
        type: Object,
        default: null,
    },
})
</script>

<template>
    <div class="mx-5 mt-4 rounded-2xl bg-gradient-to-br from-primary to-primary/80 p-5 shadow-lg shadow-primary/25">

        <!-- ── State 1: A lecture is actively ongoing ─────────────────────────── -->
        <template v-if="ongoing">

            <!-- Badge -->
            <div class="flex items-center gap-2 mb-3">
        <span class="relative flex h-2.5 w-2.5">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75" />
          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-400" />
        </span>
                <span class="text-xs font-semibold text-green-300 tracking-widest uppercase">In Progress</span>
            </div>

            <!-- Title row -->
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                        />
                    </svg>
                </div>
                <div>
                    <h2 class="text-white text-xl font-extrabold tracking-tight leading-tight">
                        {{ ongoing.courseCode }} is ongoing
                    </h2>
                    <p class="text-white/60 text-xs font-medium mt-0.5">{{ ongoing.courseName }}</p>
                    <div class="flex items-center gap-1 mt-0.5">
                        <svg class="w-3 h-3 text-white/50 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="text-white/50 text-[11px]">{{ ongoing.venue }}</p>
                    </div>
                </div>
            </div>

            <!-- Progress bar -->
            <div class="w-full bg-white/20 rounded-full h-1.5 mb-4 overflow-hidden">
                <div
                    class="h-full rounded-full bg-green-400 transition-all duration-700"
                    :style="{ width: ongoing.progress + '%' }"
                />
            </div>

            <!-- Next lecture + View details -->
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2 text-white/80 text-sm font-medium flex-wrap">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Your next lecture is</span>
                    <template v-if="next">
                        <span class="text-white font-bold">{{ next.courseCode }}</span>
                        <span class="bg-white/20 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
              {{ next.time }}
            </span>
                    </template>
                    <span v-else class="text-white/60 italic text-xs">No more lectures today</span>
                </div>

                <button class="flex items-center gap-1 text-white text-sm font-semibold bg-white/15 hover:bg-white/25 transition-colors px-3 py-1.5 rounded-xl">
<!--                    View details-->
<!--                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">-->
<!--                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />-->
<!--                    </svg>-->
                </button>
            </div>

        </template>

        <!-- ── State 2: Between lectures — nothing ongoing right now ──────────── -->
        <template v-else-if="next">

            <div class="flex items-center gap-2 mb-3">
                <svg class="w-4 h-4 text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs font-semibold text-yellow-300 tracking-widest uppercase">Up Next</span>
            </div>

            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                        />
                    </svg>
                </div>
                <div>
                    <h2 class="text-white text-xl font-extrabold tracking-tight leading-tight">{{ next.courseCode }}</h2>
                    <p class="text-white/70 text-xs font-medium mt-0.5">{{ next.courseName }}</p>
                    <p class="text-white/50 text-xs mt-0.5">Starts at {{ next.time }}</p>
                    <div class="flex items-center gap-1 mt-0.5">
                        <svg class="w-3 h-3 text-white/40 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="text-white/40 text-[11px]">{{ next.venue }}</p>
                    </div>
                </div>
            </div>

        </template>

        <!-- ── State 3: No more lectures for the day ──────────────────────────── -->
        <template v-else>

            <div class="flex items-center gap-2 mb-3">
                <span class="text-xs font-semibold text-white/50 tracking-widest uppercase">Today's Schedule</span>
            </div>
            <h2 class="text-white text-xl font-extrabold tracking-tight">All done for today 🎉</h2>
            <p class="text-white/60 text-sm mt-1">No more lectures remaining.</p>

        </template>

    </div>
</template>
