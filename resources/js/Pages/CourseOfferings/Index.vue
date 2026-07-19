<template>
    <div class="max-w-5xl px-4 py-6 sm:py-8 md:px-8">
        <FlashMessages />
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
            <div>
                <h1 class="text-lg sm:text-xl font-bold text-primary">Course Offerings</h1>
                <p class="text-sm text-primary/60">Courses offered by your department, by level and semester.</p>
            </div>
            <button
                @click="openCreate"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 w-full sm:w-auto"
            >
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 4a1 1 0 011 1v4h4a1 1 0 110 2h-4v4a1 1 0 11-2 0v-4H5a1 1 0 110-2h4V5a1 1 0 011-1z" />
                </svg>
                Add offering
            </button>
        </div>

        <!-- Plain search over the listed offerings -->
        <SearchInput v-model="search" placeholder="Search by course code or title…" />

        <div v-if="courseOfferings.length === 0" class="rounded-xl border border-primary/10 bg-white p-8 sm:p-10 text-center">
            <p class="text-sm text-primary/50">No course offerings yet for your department.</p>
            <button
                @click="openCreate"
                class="mt-3 inline-block text-sm font-medium text-secondary hover:text-secondary/80"
            >
                Add your first offering
            </button>
        </div>

        <div v-else-if="filteredOfferings.length === 0" class="rounded-xl border border-primary/10 bg-white p-8 sm:p-10 text-center">
            <p class="text-sm text-primary/50">No offerings match "{{ search }}".</p>
        </div>

        <template v-else>
            <!-- Table view — sm and up -->
            <div class="hidden sm:block rounded-xl border border-primary/10 bg-white overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-primary/10 bg-primary/[0.04] text-left text-xs font-semibold uppercase tracking-wide text-primary/60">
                            <th class="px-5 py-3">Course</th>
                            <th class="px-5 py-3">Level</th>
                            <th class="px-5 py-3">Semester</th>
                            <th class="px-5 py-3">Type</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-primary/10">
                        <tr v-for="offering in filteredOfferings" :key="offering.id" class="hover:bg-primary/[0.03]">
                            <td class="px-5 py-3">
                                <p class="font-medium text-neutral-800">{{ offering.course?.code }}</p>
                                <p class="text-xs text-primary/50">{{ offering.course?.title }}</p>
                            </td>
                            <td class="px-5 py-3 text-primary/70">{{ offering.level?.name }}</td>
                            <td class="px-5 py-3 text-primary/70">{{ offering.semester?.name ?? '—' }}</td>
                            <td class="px-5 py-3">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium"
                                            :class="typeBadge(offering)"
                                        >
                                            {{ offering.is_general ? 'General' : offering.type }}
                                        </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <button
                                    @click="openEdit(offering)"
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

            <!-- Card view — mobile only -->
            <div class="sm:hidden space-y-3">
                <div
                    v-for="offering in filteredOfferings"
                    :key="offering.id"
                    class="rounded-xl border border-primary/10 bg-white p-4"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-neutral-800 truncate">{{ offering.course?.code }}</p>
                            <p class="text-xs text-primary/50 truncate">{{ offering.course?.title }}</p>
                        </div>
                        <span
                            class="shrink-0 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="typeBadge(offering)"
                        >
                                {{ offering.is_general ? 'General' : offering.type }}
                            </span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs text-primary/60">
                        <span>{{ offering.level?.name }} · {{ offering.semester?.name ?? '—' }}</span>
                        <button
                            @click="openEdit(offering)"
                            class="text-sm font-medium text-secondary hover:text-secondary/80 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary/30 rounded px-1"
                        >
                            Edit
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Add offering modal -->
    <Transition
        enter-active-class="transition-opacity duration-150"
        leave-active-class="transition-opacity duration-100"
        enter-from-class="opacity-0"
        leave-to-class="opacity-0"
    >
        <div v-if="creating" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:px-4">
            <div class="absolute inset-0 bg-neutral-900/40" @click="closeCreate" />

            <div class="relative w-full sm:max-w-md max-h-[92vh] overflow-y-auto rounded-t-2xl sm:rounded-xl bg-white p-5 sm:p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-primary">Add course offering</h2>
                    <button @click="closeCreate" class="text-primary/40 hover:text-primary/70 p-1 -m-1" aria-label="Close">
                        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <Transition
                    enter-active-class="transition-all duration-150"
                    leave-active-class="transition-all duration-100"
                    enter-from-class="opacity-0 -translate-y-1"
                    leave-to-class="opacity-0 -translate-y-1"
                >
                    <div v-if="flash.success" class="mb-3 rounded-lg border border-secondary/20 bg-secondary/10 px-3 py-2 text-xs font-medium text-secondary">
                        {{ flash.success }}
                    </div>
                </Transition>

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

                    <div>
                        <label class="block text-xs font-medium text-primary/60 mb-1">Semester</label>
                        <select
                            v-model="createForm.semester_id"
                            class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                        >
                            <option :value="null" disabled>Select semester</option>
                            <option v-for="semester in semesters" :key="semester.id" :value="semester.id">{{ semester.name }}</option>
                        </select>
                        <p v-if="createForm.errors.semester_id" class="mt-1 text-xs text-red-600">{{ createForm.errors.semester_id }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:items-end">
                        <div>
                            <label class="block text-xs font-medium text-primary/60 mb-1">Type</label>
                            <select
                                v-model="createForm.type"
                                class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                            >
                                <option value="core">Core</option>
                                <option value="elective">Elective</option>
                            </select>
                        </div>
                        <label class="flex items-center gap-2 sm:pb-2 text-sm text-neutral-700">
                            <input v-model="createForm.is_general" type="checkbox" class="rounded border-primary/30 text-tertiary focus:ring-tertiary/30" />
                            General course
                        </label>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 pt-2">
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
                            {{ createForm.processing ? 'Adding…' : 'Add offering' }}
                        </button>
                    </div>

                    <p class="text-xs text-primary/40 text-center pt-1">Adding won't close this window — add as many courses as you need, then hit "Done".</p>
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
        <div v-if="editing" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:px-4">
            <div class="absolute inset-0 bg-neutral-900/40" @click="closeEdit" />

            <div class="relative w-full sm:max-w-md max-h-[92vh] overflow-y-auto rounded-t-2xl sm:rounded-xl bg-white p-5 sm:p-6 shadow-xl">
                <h2 class="text-base font-bold text-primary mb-4">Edit course offering</h2>

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

                    <div>
                        <label class="block text-xs font-medium text-primary/60 mb-1">Semester</label>
                        <select
                            v-model="form.semester_id"
                            class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                        >
                            <option v-for="semester in semesters" :key="semester.id" :value="semester.id">{{ semester.name }}</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:items-end">
                        <div>
                            <label class="block text-xs font-medium text-primary/60 mb-1">Type</label>
                            <select
                                v-model="form.type"
                                class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                            >
                                <option value="core">Core</option>
                                <option value="elective">Elective</option>
                            </select>
                        </div>
                        <label class="flex items-center gap-2 sm:pb-2 text-sm text-neutral-700">
                            <input v-model="form.is_general" type="checkbox" class="rounded border-primary/30 text-tertiary focus:ring-tertiary/30" />
                            General course
                        </label>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 pt-2">
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
import SearchableSelect from '@/Components/SearchableSelect.vue'
import FlashMessages from '@/Components/FlashMessages.vue'
import SearchInput from '@/Components/SearchInput.vue'

const props = defineProps({
    courseOfferings: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
    semesters: { type: Array, default: () => [] },
})

const page = usePage()
const flash = computed(() => page.props.flash ?? {})

const search = ref('')
const filteredOfferings = computed(() => {
    const q = search.value.trim().toLowerCase()
    if (!q) return props.courseOfferings
    return props.courseOfferings.filter((o) =>
        o.course?.code?.toLowerCase().includes(q) || o.course?.title?.toLowerCase().includes(q)
    )
})

const courseOptions = computed(() =>
    props.courses.map((c) => ({ id: c.id, name: `${c.code} — ${c.title}` }))
)

const creating = ref(false)
const editing = ref(null)

const createForm = useForm({
    course_id: null,
    semester_id: null,
    type: 'core',
    is_general: false,
})

const form = useForm({
    course_id: null,
    semester_id: null,
    type: 'core',
    is_general: false,
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
    createForm.post(route('course_offerings.store'), {
        preserveScroll: true,
        onSuccess: () => createForm.reset(),
    })
}

function openEdit(offering) {
    editing.value = offering
    form.course_id = offering.course_id
    form.semester_id = offering.semester_id
    form.type = offering.type
    form.is_general = !!offering.is_general
}

function closeEdit() {
    editing.value = null
    form.clearErrors()
}

function submitEdit() {
    form.put(route('course_offerings.update', editing.value.id), {
        preserveScroll: true,
        onSuccess: () => closeEdit(),
    })
}

function typeBadge(offering) {
    if (offering.is_general) return 'bg-tertiary/10 text-tertiary'
    return offering.type === 'core' ? 'bg-primary/10 text-primary' : 'bg-secondary/10 text-secondary'
}
</script>
