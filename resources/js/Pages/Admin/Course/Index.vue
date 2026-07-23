<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    courses: { type: Array, default: () => [] },
})

const showDialog = ref(false)
const editing = ref(null)

const form = useForm({
    code: '',
    title: '',
    credit_unit: '',
})

function openCreate() {
    editing.value = null
    form.reset()
    form.clearErrors()
    showDialog.value = true
}

function openEdit(course) {
    editing.value = course
    form.code = course.code
    form.title = course.title
    form.credit_unit = course.credit_unit
    form.clearErrors()
    showDialog.value = true
}

function submit() {
    if (editing.value) {
        form.put(route('admin.courses.update', editing.value.id), {
            preserveScroll: true,
            onSuccess: () => (showDialog.value = false),
        })
    } else {
        form.post(route('admin.courses.store'), {
            preserveScroll: true,
            onSuccess: () => (showDialog.value = false),
        })
    }
}
</script>

<template>
    <div class="mx-auto max-w-3xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-semibold text-gray-900">Courses</h1>
            <button
                type="button"
                @click="openCreate"
                class="rounded-md bg-[#01629c] px-4 py-2 text-sm font-medium text-white"
            >
                Add course
            </button>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-4 py-2 font-medium">Code</th>
                    <th class="px-4 py-2 font-medium">Title</th>
                    <th class="px-4 py-2 font-medium">Units</th>
                    <th class="px-4 py-2"></th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                <tr v-for="course in courses" :key="course.id">
                    <td class="px-4 py-2 font-medium text-gray-900">{{ course.code }}</td>
                    <td class="px-4 py-2 text-gray-700">{{ course.title }}</td>
                    <td class="px-4 py-2 text-gray-700">{{ course.credit_unit }}</td>
                    <td class="px-4 py-2 text-right">
                        <button
                            type="button"
                            @click="openEdit(course)"
                            class="text-sm font-medium text-[#01629c] hover:underline"
                        >
                            Edit
                        </button>
                    </td>
                </tr>
                <tr v-if="!courses.length">
                    <td colspan="4" class="px-4 py-6 text-center text-gray-400">No courses yet.</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Dialog -->
        <div v-if="showDialog" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-lg">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ editing ? 'Edit course' : 'Add course' }}
                </h2>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                        <input
                            v-model="form.code"
                            type="text"
                            placeholder="e.g. CHEM 101"
                            class="w-full rounded-md border-gray-300 text-sm focus:border-[#01629c] focus:ring-[#01629c]"
                        />
                        <p v-if="form.errors.code" class="mt-1 text-xs text-red-600">{{ form.errors.code }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input
                            v-model="form.title"
                            type="text"
                            placeholder="e.g. Chemistry 101"
                            class="w-full rounded-md border-gray-300 text-sm focus:border-[#01629c] focus:ring-[#01629c]"
                        />
                        <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Credit unit</label>
                        <input
                            v-model="form.credit_unit"
                            type="number"
                            min="1"
                            class="w-full rounded-md border-gray-300 text-sm focus:border-[#01629c] focus:ring-[#01629c]"
                        />
                        <p v-if="form.errors.credit_unit" class="mt-1 text-xs text-red-600">{{ form.errors.credit_unit }}</p>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="showDialog = false"
                            class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-md bg-[#01629c] px-4 py-2 text-sm font-medium text-white disabled:opacity-50"
                        >
                            {{ form.processing ? 'Saving…' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
