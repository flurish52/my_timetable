<script setup>
import { computed } from 'vue'
import {Head, Link} from '@inertiajs/vue3'
import StatCard from '@/Components/Contributor/Statcard.vue'
import CurrentSemesterSelector from "@/Components/Settings/AdminCurrentSemesterSelector.vue";
import ContributorCurrentSemesterselector from "@/Components/Settings/ContributorCurrentSemesterselector.vue";

const props = defineProps({
    stats: { type: Object, required: true },
    draftPastQuestions: { type: Array, default: () => [] },
    coursesWithoutPastQuestions: { type: Array, default: () => [] },
    peerContributorsCount: { type: Number, default: 0 },
})

const needsAttentionCount = computed(
    () => props.draftPastQuestions.length + props.coursesWithoutPastQuestions.length
)

const quickActions = [
    { label: 'Add course offering', href: () => route('course_offerings.create'), tone: 'primary' },
    { label: 'Add timetable slot', href: () => route('timetable.create'), tone: 'secondary' },
    { label: 'Add past question', href: () => route('past-questions.create'), tone: 'tertiary' },
]

const quickActionClasses = {
    primary: 'border-primary/20 text-primary hover:bg-primary/5',
    secondary: 'border-secondary/20 text-secondary hover:bg-secondary/5',
    tertiary: 'border-tertiary/20 text-tertiary hover:bg-tertiary/5',
}
</script>

<template>
    <Head title="Contributor_dashboard" />
        <div class="max-w-5xl mx-auto px-4 py-6 sm:py-8 md:px-8">
            <div class="mb-6">
                <h1 class="text-lg sm:text-xl font-bold text-primary">Dashboard</h1>
                <p class="text-sm text-primary/60">An overview of your department's data.</p>
            </div>
            <!-- Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-8">
                <StatCard label="Course offerings" :value="stats.course_offerings" tone="primary" />
                <StatCard label="Timetable slots" :value="stats.timetable_slots" tone="primary" />
                <StatCard label="Published papers" :value="stats.past_questions_published" tone="secondary" />
                <StatCard label="Draft papers" :value="stats.past_questions_draft" tone="tertiary" />
            </div>

            <!-- Needs attention -->
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-3">
                    <h2 class="text-sm font-bold text-neutral-800">Needs attention</h2>
                    <span
                        v-if="needsAttentionCount > 0"
                        class="inline-flex items-center justify-center rounded-full bg-tertiary/10 text-tertiary text-xs font-medium px-2 py-0.5"
                    >
                        {{ needsAttentionCount }}
                    </span>
                </div>

                <div v-if="needsAttentionCount === 0" class="rounded-xl border border-primary/10 bg-white p-6 text-center">
                    <p class="text-sm text-primary/50">Nothing pending — everything's published and covered.</p>
                </div>

                <div v-else class="grid sm:grid-cols-2 gap-4">
                    <!-- Draft papers -->
                    <div v-if="draftPastQuestions.length" class="rounded-xl border border-primary/10 bg-white overflow-hidden">
                        <p class="px-4 py-3 border-b border-primary/10 text-xs font-semibold uppercase tracking-wide text-primary/60">
                            Draft papers
                        </p>
                        <div class="divide-y divide-primary/10">
                            <Link
                                v-for="pq in draftPastQuestions"
                                :key="pq.id"
                                :href="route('past-questions.build', pq.id)"
                                class="flex items-center justify-between gap-3 px-4 py-3 text-sm hover:bg-primary/[0.03]"
                            >
                                <span class="truncate text-neutral-700">{{ pq.title }}</span>
                                <span class="shrink-0 text-secondary font-medium">Finish →</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Courses with no past questions yet -->
                    <div v-if="coursesWithoutPastQuestions.length" class="rounded-xl border border-primary/10 bg-white overflow-hidden">
                        <p class="px-4 py-3 border-b border-primary/10 text-xs font-semibold uppercase tracking-wide text-primary/60">
                            Courses with no past questions
                        </p>
                        <div class="divide-y divide-primary/10">
                            <Link
                                v-for="course in coursesWithoutPastQuestions"
                                :key="course.id"
                                :href="route('past-questions.create')"
                                class="flex items-center justify-between gap-3 px-4 py-3 text-sm hover:bg-primary/[0.03]"
                            >
                                <span class="truncate text-neutral-700">{{ course.code }} — {{ course.title }}</span>
                                <span class="shrink-0 text-secondary font-medium">Add →</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick actions -->
            <div class="mb-8">
                <h2 class="text-sm font-bold text-neutral-800 mb-3">Quick actions</h2>
                <div class="grid sm:grid-cols-3 gap-3">
                    <Link
                        v-for="action in quickActions"
                        :key="action.label"
                        :href="action.href()"
                        class="rounded-lg border bg-white px-4 py-3 text-sm font-medium text-center transition-colors"
                        :class="quickActionClasses[action.tone]"
                    >
                        {{ action.label }}
                    </Link>
                </div>
            </div>

            <!-- Peer contributors — small, secondary -->
            <p v-if="peerContributorsCount > 0" class="text-xs text-primary/40 text-center">
                You're not alone — {{ peerContributorsCount }} other contributor{{ peerContributorsCount === 1 ? '' : 's' }} in your department.
            </p>
        </div>
</template>
