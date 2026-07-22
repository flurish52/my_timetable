<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps<{
    papers: { data: any[]; links: any[] }
    filters: { search?: string; status?: string }
}>()

const search = ref(props.filters.search ?? '')
const status = ref(props.filters.status ?? '')

const applyFilters = () => {
    router.get(route('admin.papers.index'), { search: search.value, status: status.value }, { preserveState: true })
}

const approve = (id: number) => router.patch(route('admin.papers.approve', id))
const unpublish = (id: number) => router.patch(route('admin.papers.unpublish', id))
const reject = (id: number) => {
    const reason = prompt('Reason for rejection:')
    if (reason) router.patch(route('admin.papers.reject', id), { rejection_reason: reason })
}
</script>

<template>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Past Questions Moderation</h1>

    <div class="flex gap-3 mb-5">
        <input
            v-model="search"
            @keyup.enter="applyFilters"
            type="text"
            placeholder="Search by title, contributor..."
            class="flex-1 rounded-lg border-gray-200 text-sm"
        />
        <select v-model="status" @change="applyFilters" class="rounded-lg border-gray-200 text-sm">
            <option value="">All statuses</option>
            <option value="draft">Draft</option>
            <option value="published">Published</option>
        </select>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-gray-500 border-b border-gray-100">
            <tr>
                <th class="p-3">Title</th>
                <th class="p-3">Contributor</th>
                <th class="p-3">Course</th>
                <th class="p-3">Status</th>
                <th class="p-3">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="paper in papers.data" :key="paper.id" class="border-b border-gray-50 last:border-0">
                <td class="p-3 font-medium text-gray-900">{{ paper.title }}</td>
                <td class="p-3 text-gray-600">
                    {{ paper?.creator?.name }}
                    <span class="block text-xs text-gray-400">{{ paper?.creator?.email }}</span>
                </td>
                <td class="p-3 text-gray-600">{{ paper.course.title }}</td>
                <td class="p-3">
            <span
                class="text-xs px-2 py-0.5 rounded-full"
                :class="paper.status === 'published' ? 'bg-secondary/10 text-secondary' : 'bg-tertiary/10 text-tertiary'"
            >
              {{ paper.status }}
            </span>
                </td>
                <td class="p-3 space-x-2">
                    <button v-if="paper.status !== 'published'" @click="approve(paper.id)" class="text-secondary text-xs font-medium">Approve</button>
                    <button v-if="paper.status === 'published'" @click="unpublish(paper.id)" class="text-tertiary text-xs font-medium">Unpublish</button>
                    <button @click="reject(paper.id)" class="text-red-500 text-xs font-medium">Reject</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
