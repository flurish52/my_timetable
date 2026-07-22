<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

defineProps<{
    contributor: { id: number; name: string; email: string }
    papers: Array<{ id: number; title: string; status: string; created_at: string; course: { title: string } }>
}>()
</script>

<template>
    <h1 class="text-2xl font-bold text-gray-900">{{ contributor.name }}</h1>
    <p class="text-gray-500 mb-6">{{ contributor.email }}</p>

    <div class="bg-white rounded-xl border border-gray-100 divide-y divide-gray-50">
        <div v-for="paper in papers" :key="paper.id" class="p-4 flex justify-between items-center">
            <div>
                <p class="font-medium text-gray-900">{{ paper.title }}</p>
                <p class="text-xs text-gray-400">{{ paper.course.title }} · {{ new Date(paper.created_at).toLocaleDateString() }}</p>
            </div>
            <span
                class="text-xs px-2 py-0.5 rounded-full"
                :class="paper.status === 'published' ? 'bg-secondary/10 text-secondary' : 'bg-tertiary/10 text-tertiary'"
            >
        {{ paper.status }}
      </span>
        </div>
    </div>
</template>
