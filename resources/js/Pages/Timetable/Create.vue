<template>
    <div class="px-4 py-8 md:px-8">
        <div class="mb-6">
            <Link :href="route('timetable.index')" class="text-sm font-medium text-secondary hover:text-secondary/80">
                ← Back to timetable
            </Link>
            <h1 class="mt-2 text-xl font-bold text-primary">Add timetable slot</h1>
            <p class="text-sm text-primary/60">This will be added to your department's schedule.</p>
        </div>

        <FlashMessages />

        <div v-if="flash.venueWarning"
             class="mb-4 rounded-lg border border-tertiary/30 bg-tertiary/10 px-4 py-3 text-sm text-tertiary">
            <p class="font-medium mb-2">{{ flash.venueWarning }}</p>
            <div class="flex gap-2">
                <button
                    type="button"
                    @click="confirmAnyway"
                    class="rounded-md bg-tertiary px-3 py-1.5 text-xs font-medium text-white hover:bg-tertiary/90"
                >
                    Add anyway
                </button>
                <button
                    type="button"
                    @click="dismissWarning"
                    class="rounded-md px-3 py-1.5 text-xs font-medium text-tertiary/80 hover:bg-tertiary/10"
                >
                    I'll change the venue
                </button>
            </div>
        </div>

        <form @submit.prevent="submit"
              class="space-y-5 rounded-xl border border-primary/10 bg-white p-6">
            <div>
                <label class="block text-xs font-medium text-primary/60 mb-1">Course</label>
                <SearchableSelect
                    v-model="form.course_id"
                    :options="courseOptions"
                    placeholder="Select a course"
                    hint="Search by course code or title"
                />
                <p v-if="form.errors.course_id" class="mt-1 text-xs text-red-600">{{ form.errors.course_id }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-primary/60 mb-1">Day</label>
                    <select
                        v-model="form.day_of_week"
                        class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                    >
                        <option v-for="day in days" :key="day" :value="day" class="capitalize">{{ day }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-primary/60 mb-1">Venue</label>
                    <input
                        v-model="form.venue"
                        type="text"
                        placeholder="e.g. MPH"
                        class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                    />
                    <p v-if="form.errors.venue" class="mt-1 text-xs text-red-600">{{ form.errors.venue }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-primary/60 mb-1">Start time</label>
                    <input
                        v-model="form.start_time"
                        type="time"
                        class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                    />
                    <p v-if="form.errors.start_time" class="mt-1 text-xs text-red-600">{{ form.errors.start_time }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-primary/60 mb-1">End time</label>
                    <input
                        v-model="form.end_time"
                        type="time"
                        class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                    />
                    <p v-if="form.errors.end_time" class="mt-1 text-xs text-red-600">{{ form.errors.end_time }}</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-primary/60 mb-1">Lecturer (optional)</label>
                <input
                    v-model="form.lecturer"
                    type="text"
                    placeholder="e.g. Dr. Adamu"
                    class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                />
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <Link
                    :href="route('timetable.index')"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-primary/60 hover:bg-primary/5"
                >
                    Cancel
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90 disabled:opacity-60"
                >
                    Save slot
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import SearchableSelect from '@/Components/SearchableSelect.vue'
import FlashMessages from "@/Components/FlashMessages.vue";

const props = defineProps({
    courses: { type: Array, default: () => [] },
})

const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']

const page = usePage()
const flash = computed(() => page.props.flash ?? {})

const courseOptions = computed(() =>
    props.courses.map((c) => ({ id: c.id, name: `${c.code} — ${c.title}` }))
)

const form = useForm({
    course_id: null,
    day_of_week: 'monday',
    start_time: '',
    end_time: '',
    venue: '',
    lecturer: '',
    confirmed: false,
})

function submit() {
    form.confirmed = false
    form.post(route('timetable.store'), {
        preserveScroll: true,
    })
}

function confirmAnyway() {
    form.confirmed = true
    form.post(route('timetable.store'), {
        preserveScroll: true,
    })
}

function dismissWarning() {
    page.props.flash.venueWarning = null
}
</script>
