<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import SearchableSelect from '@/Components/SearchableSelect.vue'

const props = defineProps({
    courses: { type: Array, required: true },
    semesters: { type: Array, required: true },
})

const courseOptions = computed(() =>
    props.courses.map((c) => ({ id: c.id, name: `${c.code} — ${c.title}` }))
)

const form = useForm({
    course_id: '',
    semester_id: '',
    session: '',
    title: '',
    instructions: '',
    description: '',
    duration_minutes: '',
    source_file: null,
})

function submit() {
    form.post(route('past-questions.store'), {
        forceFormData: true,
    })
}
</script>

<template>
    <div class="max-w-4xl min-w-[150px] py-10 px-4">
        <h1 class="text-2xl font-bold mb-1">New past question</h1>
        <p class="text-sm text-gray-500 mb-6">
            Step 1 of 2, paper details. You'll add the actual questions next.
        </p>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label class="block text-sm font-medium mb-1">Course</label>
                <SearchableSelect
                    v-model="form.course_id"
                    :options="courseOptions"
                    placeholder="Select a course"
                    hint="Search by course code or title"
                />
                <p v-if="form.errors.course_id" class="text-sm text-red-600 mt-1">{{ form.errors.course_id }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Semester</label>
                    <select v-model="form.semester_id" class="w-full border rounded-md px-3 py-2">
                        <option value="" disabled>Select</option>
                        <option v-for="s in semesters" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                    <p v-if="form.errors.semester_id" class="text-sm text-red-600 mt-1">{{ form.errors.semester_id }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Session</label>
                    <input
                        v-model="form.session"
                        type="text"
                        placeholder="2024/2025"
                        class="w-full border rounded-md px-3 py-2"
                    />
                    <p v-if="form.errors.session" class="text-sm text-red-600 mt-1">{{ form.errors.session }}</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Title</label>
                <input v-model="form.title" type="text" class="w-full border rounded-md px-3 py-2" />
                <p v-if="form.errors.title" class="text-sm text-red-600 mt-1">{{ form.errors.title }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Instructions</label>
                <textarea v-model="form.instructions" rows="2" class="w-full border rounded-md px-3 py-2"
                          placeholder="Answer all questions from Section A, B and C" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Description (optional)</label>
                <textarea v-model="form.description" rows="2" class="w-full border rounded-md px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Duration (minutes)</label>
                <input v-model="form.duration_minutes" type="number" class="w-40 border rounded-md px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Original file (optional)</label>
                <input
                    type="file"
                    accept="application/pdf"
                    @change="form.source_file = $event.target.files[0]"
                    class="w-full text-sm"
                />
                <p v-if="form.errors.source_file" class="text-sm text-red-600 mt-1">{{ form.errors.source_file }}</p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="bg-primary text-white px-5 py-2 rounded-md font-medium disabled:opacity-50"
            >
                {{ form.processing ? 'Saving...' : 'Continue to questions' }}
            </button>
        </form>
    </div>
</template>
