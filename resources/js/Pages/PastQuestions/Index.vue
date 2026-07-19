<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import PublishToggle from '@/Components/PastQuestions/Publishtoggle.vue'

const props = defineProps({
    pastQuestions: { type: Array, required: true },
})

const search = ref('')
const filteredPastQuestions = computed(() => {
    const q = search.value.trim().toLowerCase()
    if (!q) return props.pastQuestions
    return props.pastQuestions.filter((pq) =>
        pq.title?.toLowerCase().includes(q)
        || pq.course?.toLowerCase().includes(q)
        || pq.session?.toLowerCase().includes(q)
    )
})
</script>

<template>
    <div class="max-w-7xl   px-4 sm:px-6 py-8">
        <div class="flex items-center justify-between gap-4 mb-4">
            <h1 class="text-xl font-bold text-neutral-900">Past questions</h1>
            <Link :href="route('past-questions.create')"
                  class="shrink-0 text-sm font-medium px-4 py-2 rounded-md bg-primary text-white hover:opacity-90">
                + Create new
            </Link>
        </div>

        <!-- Plain search over the listed past questions -->
        <div class="relative mb-6">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-300" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6">
                <circle cx="9" cy="9" r="6" /><path d="M17 17l-4-4" stroke-linecap="round" />
            </svg>
            <input
                v-model="search"
                type="text"
                placeholder="Search by title, course, or session…"
                class="w-full rounded-md border border-neutral-300 pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
            />
        </div>

        <div v-if="!pastQuestions.length" class="text-center py-16 border border-dashed border-neutral-300 rounded-xl">
            <p class="text-sm text-neutral-500 mb-3">You haven't added any past questions yet.</p>
            <Link :href="route('past-questions.create')" class="text-primary text-sm font-medium hover:underline">
                Create your first one
            </Link>
        </div>

        <div v-else-if="!filteredPastQuestions.length" class="text-center py-16 border border-dashed border-neutral-300 rounded-xl">
            <p class="text-sm text-neutral-500">No past questions match "{{ search }}".</p>
        </div>

        <div v-else class="border border-neutral-200 rounded-xl overflow-hidden divide-y divide-neutral-200 bg-white">
            <div v-for="pq in filteredPastQuestions" :key="pq.id"
                 class="flex items-center gap-4 px-4 py-3 hover:bg-primary/10">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-neutral-900 truncate">{{ pq.title }}</p>
                    <p class="text-xs text-neutral-500 mt-0.5">
                        {{ pq.course }} · {{ pq.session }} · {{ pq.questions_count }} question{{ pq.questions_count === 1 ? '' : 's' }} · updated {{ pq.updated_at }}
                    </p>
                </div>
                <PublishToggle :past-question="pq" />
                <Link :href="route('past-question.show', pq.id)"
                      class="shrink-0 text-sm text-neutral-500 hover:text-primary px-2 py-1">View</Link>
                <Link :href="route('past-questions.build', pq.id)"
                      class="shrink-0 text-sm text-primary font-medium px-2 py-1">Edit</Link>
            </div>
        </div>
    </div>
</template>
