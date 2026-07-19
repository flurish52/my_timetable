<template>
    <div class="max-w-5xl  px-4 py-8 md:px-8">
        <FlashMessages />

        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-xl font-bold text-primary">Timetable</h1>
                <p class="text-sm text-primary/60">Class schedule for your department, by day and time.</p>
            </div>
            <button
                @click="openCreate"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
            >
                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 4a1 1 0 011 1v4h4a1 1 0 110 2h-4v4a1 1 0 11-2 0v-4H5a1 1 0 110-2h4V5a1 1 0 011-1z" />
                </svg>
                Add slot
            </button>
        </div>

        <!-- Plain search over the listed slots -->
        <div class="relative mb-4">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-primary/30" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6">
                <circle cx="9" cy="9" r="6" /><path d="M17 17l-4-4" stroke-linecap="round" />
            </svg>
            <input
                v-model="search"
                type="text"
                placeholder="Search by course, venue, or lecturer…"
                class="w-full rounded-lg border border-primary/20 pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
            />
        </div>

        <div v-if="timetable.length === 0" class="rounded-xl border border-primary/10 bg-white p-10 text-center">
            <p class="text-sm text-primary/50">No timetable slots yet for your department.</p>
            <button @click="openCreate" class="mt-3 inline-block text-sm font-medium text-secondary hover:text-secondary/80">
                Add your first slot
            </button>
        </div>

        <div v-else-if="filteredTimetable.length === 0" class="rounded-xl border border-primary/10 bg-white p-10 text-center">
            <p class="text-sm text-primary/50">No slots match "{{ search }}".</p>
        </div>

        <div v-else class="rounded-xl border border-primary/10 bg-white overflow-scroll">
            <table class="w-full text-sm">
                <thead>
                <tr class="border-b border-primary/10 bg-primary/[0.04] text-left text-xs font-semibold uppercase tracking-wide text-primary/60">
                    <th class="px-5 py-3">Course</th>
                    <th class="px-5 py-3">Day</th>
                    <th class="px-5 py-3">Time</th>
                    <th class="px-5 py-3">Venue</th>
                    <th class="px-5 py-3">Lecturer</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-primary/10">
                <tr v-for="slot in filteredTimetable" :key="slot.id" class="hover:bg-primary/[0.03]">
                    <td class="px-5 py-3">
                        <p class="font-medium text-neutral-800">{{ slot.course?.code }}</p>
                        <p class="text-xs text-primary/50">{{ slot.course?.title }}</p>
                    </td>
                    <td class="px-5 py-3 text-primary/70 capitalize">{{ slot.day_of_week }}</td>
                    <td class="px-5 py-3 text-primary/70">{{ slot.start_time }} – {{ slot.end_time }}</td>
                    <td class="px-5 py-3 text-primary/70">{{ slot.venue }}</td>
                    <td class="px-5 py-3 text-primary/70">{{ slot.lecturer ?? '—' }}</td>
                    <td class="px-5 py-3 text-right">
                        <button
                            @click="openEdit(slot)"
                            class="text-sm font-medium text-secondary hover:text-secondary/80 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary/30 rounded px-1"
                        >
                            Edit
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add slot modal -->
    <Transition
        enter-active-class="transition-opacity duration-150"
        leave-active-class="transition-opacity duration-100"
        enter-from-class="opacity-0"
        leave-to-class="opacity-0"
    >
        <div v-if="creating" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-neutral-900/40" @click="closeCreate" />

            <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-primary">Add timetable slot</h2>
                    <button @click="closeCreate" class="text-primary/40 hover:text-primary/70" aria-label="Close">
                        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <FlashMessages />

                <!-- Venue clash — soft warning, not a rejection -->
                <div v-if="flash.venueWarning" class="mb-3 rounded-lg border border-tertiary/30 bg-tertiary/10 px-3 py-3 text-xs text-tertiary">
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

                <form @submit.prevent="submitCreate" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-primary/60 mb-1">Course</label>
                        <SearchableSelect
                            v-model="createForm.course_id"
                            :options="courseOptions"
                            placeholder="Select a course"
                            hint="Search by course code or title"
                        />
                        <p v-if="createForm.errors.course_id" class="mt-1 text-xs text-red-600">{{ createForm.errors.course_id }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-primary/60 mb-1">Day</label>
                            <select
                                v-model="createForm.day_of_week"
                                class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                            >
                                <option v-for="day in days" :key="day" :value="day" class="capitalize">{{ day }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-primary/60 mb-1">Venue</label>
                            <input
                                v-model="createForm.venue"
                                type="text"
                                placeholder="e.g. MPH"
                                class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                            />
                            <p v-if="createForm.errors.venue" class="mt-1 text-xs text-red-600">{{ createForm.errors.venue }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-primary/60 mb-1">Start time</label>
                            <input
                                v-model="createForm.start_time"
                                type="time"
                                class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                            />
                            <p v-if="createForm.errors.start_time" class="mt-1 text-xs text-red-600">{{ createForm.errors.start_time }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-primary/60 mb-1">End time</label>
                            <input
                                v-model="createForm.end_time"
                                type="time"
                                class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                            />
                            <p v-if="createForm.errors.end_time" class="mt-1 text-xs text-red-600">{{ createForm.errors.end_time }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-primary/60 mb-1">Lecturer (optional)</label>
                        <input
                            v-model="createForm.lecturer"
                            type="text"
                            placeholder="e.g. Dr. Adamu"
                            class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                        />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="closeCreate"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-primary/60 hover:bg-primary/5"
                        >
                            Done
                        </button>
                        <button
                            type="submit"
                            :disabled="createForm.processing"
                            class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90 disabled:opacity-60"
                        >
                            {{ createForm.processing ? 'Adding…' : 'Add slot' }}
                        </button>
                    </div>

                    <p class="text-xs text-primary/40 text-center pt-1">Adding won't close this window — add as many slots as you need, then hit "Done".</p>
                </form>
            </div>
        </div>
    </Transition>

    <!-- Edit modal -->
    <Transition
        enter-active-class="transition-opacity duration-150"
        leave-active-class="transition-opacity duration-100"
        enter-from-class="opacity-0"
        leave-to-class="opacity-0"
    >
        <div v-if="editing" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-neutral-900/40" @click="closeEdit" />

            <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <h2 class="text-base font-bold text-primary mb-4">Edit timetable slot</h2>

                <div v-if="flash.venueWarning" class="mb-3 rounded-lg border border-tertiary/30 bg-tertiary/10 px-3 py-3 text-xs text-tertiary">
                    <p class="font-medium mb-2">{{ flash.venueWarning }}</p>
                    <div class="flex gap-2">
                        <button
                            type="button"
                            @click="confirmEditAnyway"
                            class="rounded-md bg-tertiary px-3 py-1.5 text-xs font-medium text-white hover:bg-tertiary/90"
                        >
                            Save anyway
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

                <form @submit.prevent="submitEdit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-primary/60 mb-1">Course</label>
                        <SearchableSelect
                            v-model="form.course_id"
                            :options="courseOptions"
                            placeholder="Select a course"
                        />
                        <p v-if="form.errors.course_id" class="mt-1 text-xs text-red-600">{{ form.errors.course_id }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
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
                                class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-primary/60 mb-1">Start time</label>
                            <input
                                v-model="form.start_time"
                                type="time"
                                class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-primary/60 mb-1">End time</label>
                            <input
                                v-model="form.end_time"
                                type="time"
                                class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-primary/60 mb-1">Lecturer (optional)</label>
                        <input
                            v-model="form.lecturer"
                            type="text"
                            class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                        />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="closeEdit"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-primary/60 hover:bg-primary/5"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90 disabled:opacity-60"
                        >
                            Save changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SearchableSelect from '@/Components/SearchableSelect.vue'
import FlashMessages from "@/Components/FlashMessages.vue";

const props = defineProps({
    timetable: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
})

const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']

const page = usePage()
const flash = computed(() => page.props.flash ?? {})

const search = ref('')
const filteredTimetable = computed(() => {
    const q = search.value.trim().toLowerCase()
    if (!q) return props.timetable
    return props.timetable.filter((slot) =>
        slot.course?.code?.toLowerCase().includes(q)
        || slot.course?.title?.toLowerCase().includes(q)
        || slot.venue?.toLowerCase().includes(q)
        || slot.lecturer?.toLowerCase().includes(q)
    )
})

const courseOptions = computed(() =>
    props.courses.map((c) => ({ id: c.id, name: `${c.code} — ${c.title}` }))
)

const creating = ref(false)
const editing = ref(null)

const createForm = useForm({
    course_id: null,
    day_of_week: 'monday',
    start_time: '',
    end_time: '',
    venue: '',
    lecturer: '',
    confirmed: false,
})

const form = useForm({
    course_id: null,
    day_of_week: 'monday',
    start_time: '',
    end_time: '',
    venue: '',
    lecturer: '',
    confirmed: false,
})

function openCreate() {
    creating.value = true
    createForm.reset()
    createForm.clearErrors()
}

function closeCreate() {
    creating.value = false
}

function submitCreate() {
    createForm.confirmed = false
    createForm.post(route('timetable.store'), {
        preserveScroll: true,
        onSuccess: () => {
            if (!page.props.flash.venueWarning) {
                createForm.reset()
            }
        },
    })
}

function confirmAnyway() {
    createForm.confirmed = true
    createForm.post(route('timetable.store'), {
        preserveScroll: true,
        onSuccess: () => createForm.reset(),
    })
}

function dismissWarning() {
    page.props.flash.venueWarning = null
}

function openEdit(slot) {
    editing.value = slot
    form.course_id = slot.course_id
    form.day_of_week = slot.day_of_week
    form.start_time = slot.start_time?.slice(0, 5)
    form.end_time = slot.end_time?.slice(0, 5)
    form.venue = slot.venue
    form.lecturer = slot.lecturer ?? ''
    form.confirmed = false
}

function closeEdit() {
    editing.value = null
    form.clearErrors()
}

function submitEdit() {
    form.confirmed = false
    form.put(route('timetable.update', editing.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            if (!page.props.flash.venueWarning) closeEdit()
        },
    })
}

function confirmEditAnyway() {
    form.confirmed = true
    form.put(route('timetable.update', editing.value.id), {
        preserveScroll: true,
        onSuccess: () => closeEdit(),
    })
}
</script>
