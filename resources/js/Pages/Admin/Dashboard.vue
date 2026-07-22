<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

defineProps<{
    stats: {
        total_papers: number
        published_papers: number
        draft_papers: number
        total_contributors: number
        total_students: number
        papers_per_school: Array<{ school: string; total: number }>
        recent_activity: Array<{ id: number; status: string; created_at: string; user: { name: string }; course: { title: string } }>
    }
}>()
</script>

<template>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Overview</h1>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <p class="text-sm text-gray-500">Total Papers</p>
            <p class="text-2xl font-bold text-primary">{{ stats.total_papers }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <p class="text-sm text-gray-500">Published</p>
            <p class="text-2xl font-bold text-secondary">{{ stats.published_papers }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <p class="text-sm text-gray-500">Drafts</p>
            <p class="text-2xl font-bold text-tertiary">{{ stats.draft_papers }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <p class="text-sm text-gray-500">Contributors</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total_contributors }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl p-5 border border-gray-100">
            <h2 class="font-semibold text-gray-900 mb-4">Papers per School</h2>
            <div v-for="row in stats.papers_per_school" :key="row.school" class="flex justify-between text-sm py-1.5 border-b border-gray-50 last:border-0">
                <span class="text-gray-600">{{ row.school }}</span>
                <span class="font-medium text-gray-900">{{ row.total }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-gray-100">
            <h2 class="font-semibold text-gray-900 mb-4">Recent Activity</h2>
            <div v-for="item in stats.recent_activity" :key="item.id" class="flex justify-between items-center text-sm py-1.5 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-gray-900">{{ item?.creator?.name }} — {{ item.course.title }}</p>
                    <p class="text-gray-400 text-xs">{{ new Date(item.created_at).toLocaleDateString() }}</p>
                </div>
                <span
                    class="text-xs px-2 py-0.5 rounded-full"
                    :class="item.status === 'published' ? 'bg-secondary/10 text-secondary' : 'bg-tertiary/10 text-tertiary'"
                >
          {{ item.status }}
        </span>
            </div>
        </div>
    </div>
</template>
