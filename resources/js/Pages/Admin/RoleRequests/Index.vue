<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

defineProps<{
    requests: Array<{
        id: number
        requested_role: string
        reason: string | null
        status: string
        created_at: string
        user: { id: number; name: string; email: string }
    }>
}>()

const approve = (id: number) => {
    if (confirm('Approve this role request?')) {
        router.patch(route('admin.role-requests.approve', id))
    }
}

const reject = (id: number) => {
    const note = prompt('Optional note for rejection:')
    router.patch(route('admin.role-requests.reject', id), { review_note: note ?? '' })
}
</script>

<template>
    <h1 class="text-2xl font-bold text-gray-900 mb-1">Role Requests</h1>
    <p class="text-gray-500 mb-6">Pending applications to become a contributor, lecturer, or admin.</p>

    <div v-if="requests.length === 0" class="bg-white rounded-xl border border-gray-100 p-8 text-center text-gray-400 text-sm">
        No pending requests right now.
    </div>

    <div v-else class="bg-white rounded-xl border border-gray-100 divide-y divide-gray-50">
        <div v-for="req in requests" :key="req.id" class="p-4 md:flex md:items-start md:justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="font-medium text-gray-900">{{ req.user.name }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-primary/10 text-primary capitalize">
            wants: {{ req.requested_role }}
          </span>
                </div>
                <p class="text-xs text-gray-400 mt-0.5">{{ req.user.email }} · {{ new Date(req.created_at).toLocaleDateString() }}</p>
                <p v-if="req.reason" class="text-sm text-gray-600 mt-2 bg-gray-50 rounded-lg p-2.5">
                    {{ req.reason }}
                </p>
            </div>

            <div class="flex gap-2 mt-3 md:mt-0 shrink-0">
                <button
                    @click="approve(req.id)"
                    class="px-3 py-1.5 rounded-lg bg-secondary/10 text-secondary text-xs font-medium hover:bg-secondary/15 transition"
                >
                    Approve
                </button>
                <button
                    @click="reject(req.id)"
                    class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100 transition"
                >
                    Reject
                </button>
            </div>
        </div>
    </div>
</template>
