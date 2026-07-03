<script setup>
import { computed, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import GuestLayout from "@/Layouts/AppLayout.vue"

const props = defineProps({
    schools: Array,        // [{ id, name, slug, count }]
    userSchoolSlug: String,
})

const search = ref('')

// Attach true rank (based on full, unfiltered order) before any filtering happens
const rankedSchools = computed(() =>
    props.schools.map((school, i) => ({ ...school, rank: i + 1 }))
)

const filteredSchools = computed(() => {
    if (!search.value.trim()) return rankedSchools.value

    const query = search.value.trim().toLowerCase()
    return rankedSchools.value.filter(school =>
        school.name.toLowerCase().includes(query)
    )
})
</script>

<template>
    <GuestLayout>
        <Head title="Leaderboard" />

        <div class="min-h-screen bg-gray-50 ">
            <div class="w-full max-w-lg mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

                <div class="mb-7">
                    <h1 class="text-lg font-semibold text-gray-900 leading-tight">
                        Top Schools Waiting
                    </h1>
                    <p class="text-sm text-gray-400 mt-0.5">
                        Every join moves your school up the list
                    </p>
                </div>

                <!-- Search -->
                <div class="relative mb-5">
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-300">
                        <circle cx="6.5" cy="6.5" r="4.8" stroke="currentColor" stroke-width="1.4"/>
                        <path d="M10.2 10.2L13 13" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                    </svg>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search for your school"
                        class="w-full pl-9 pr-3 py-2.5 text-sm text-gray-900 placeholder:text-gray-300 bg-gray-50 border border-gray-100 rounded-lg outline-none focus:border-primary/40 focus:bg-white transition-colors"
                    />
                </div>

                <div class="border-t border-gray-100 mb-6" />

                <div class="space-y-2">
                    <a v-for="school in filteredSchools"
                       :key="school.id"
                       :href="`/schools/${school.slug}`"
                       class="flex items-center gap-3.5 py-3 px-3.5 rounded-lg transition-colors"
                       :class="school.slug === userSchoolSlug ? 'bg-primary/5' : 'hover:bg-gray-50'"
                    >
                    <span
                        class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-semibold shrink-0"
                        :class="school.rank === 1
                                ? 'bg-primary text-white'
                                : school.rank <= 3
                                    ? 'bg-primary/10 text-primary'
                                    : 'bg-gray-100 text-gray-400'"
                    >
                            {{ school.rank }}
                        </span>

                        <span class="flex-1 text-sm font-medium text-gray-900">
                            {{ school.name }}
                            <span v-if="school.slug === userSchoolSlug" class="text-xs text-primary font-normal ml-1">
                                (your school)
                            </span>
                        </span>

                        <span class="text-sm text-gray-400">
                            {{ school.count }} students
                        </span>
                    </a>
                </div>

                <p v-if="!filteredSchools.length && schools.length" class="text-sm text-gray-400 text-center py-8">
                    No schools match "{{ search }}"
                </p>

                <p v-else-if="!schools.length" class="text-sm text-gray-400 text-center py-8">
                    No schools on the waitlist yet.
                </p>

            </div>
        </div>
    </GuestLayout>
</template>
