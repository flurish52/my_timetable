<template>
    <Head title="Create_courseOfferings" />
    <div class="max-w-xl mx-auto px-4 py-8 md:px-8">
        <div class="mb-6">
            <Link :href="route('course_offerings.index')" class="text-sm font-medium text-secondary hover:text-secondary/80">
                ← Back to course offerings
            </Link>
            <h1 class="mt-2 text-xl font-bold text-primary">Add course offering</h1>
            <p class="text-sm text-primary/60">This will be added to your department's offerings.</p>
        </div>

        <FlashMessages />
        <form @submit.prevent="submit" class="space-y-5 rounded-xl border border-primary/10 bg-white p-6">
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

            <div>
                <label class="block text-xs font-medium text-primary/60 mb-1">Semester</label>
                <select
                    v-model="form.semester_id"
                    class="w-full rounded-lg border border-primary/20 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                >
                    <option :value="null" disabled>Select semester</option>
                    <option v-for="semester in semesters" :key="semester.id" :value="semester.id">{{ semester.name }}</option>
                </select>
                <p v-if="form.errors.semester_id" class="mt-1 text-xs text-red-600">{{ form.errors.semester_id }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 items-end">
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

                <label class="flex items-center gap-2 pb-2 text-sm text-neutral-700">
                    <input v-model="form.is_general" type="checkbox" class="rounded border-primary/30 text-tertiary focus:ring-tertiary/30" />
                    General course
                </label>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <Link
                    :href="route('course_offerings.index')"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-primary/60 hover:bg-primary/5"
                >
                    Cancel
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90 disabled:opacity-60"
                >
                    Save offering
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import {Head, Link, useForm, usePage} from '@inertiajs/vue3'
import SearchableSelect from '@/Components/SearchableSelect.vue'
import FlashMessages from "@/Components/FlashMessages.vue";

const props = defineProps({
    courses: { type: Array, default: () => [] },
    semesters: { type: Array, default: () => [] },
})

const page = usePage()
const flash = computed(() => page.props.flash ?? {})

const courseOptions = computed(() =>
    props.courses.map((c) => ({ id: c.id, name: `${c.code} — ${c.title}` }))
)

const form = useForm({
    course_id: null,
    semester_id: null,
    type: 'core',
    is_general: false,
})

function submit() {
    form.post(route('course_offerings.store'), {
        preserveScroll: true,
    })
}
</script>
