<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps<{
    users: {
        data: any[]
        links: Array<{ url: string | null; label: string; active: boolean }>
        from: number | null
        to: number | null
        total: number
    }
    filters: { search?: string; role?: string }
    availableRoles: string[]
}>()

const search = ref(props.filters.search ?? '')
const role = ref(props.filters.role ?? '')
const selected = ref<number[]>([])
const bulkRole = ref('')

const applyFilters = () => {
    selected.value = []
    router.get(route('admin.users.index'), { search: search.value, role: role.value }, { preserveState: true })
}

const updateRole = (userId: number, newRole: string) => {
    router.patch(route('admin.users.updateRole', userId), { role: newRole })
}

const goToPage = (url: string | null) => {
    if (url) router.get(url, {}, { preserveState: true, preserveScroll: true })
}

const allOnPageSelected = computed(() =>
    props.users.data.length > 0 && props.users.data.every((u) => selected.value.includes(u.id))
)

const toggleSelectAll = () => {
    if (allOnPageSelected.value) {
        selected.value = selected.value.filter((id) => !props.users.data.some((u) => u.id === id))
    } else {
        const pageIds = props.users.data.map((u) => u.id)
        selected.value = [...new Set([...selected.value, ...pageIds])]
    }
}

const applyBulkRole = () => {
    if (!bulkRole.value || selected.value.length === 0) return

    if (confirm(`Set ${selected.value.length} user(s) to "${bulkRole.value}"?`)) {
        router.patch(
            route('admin.users.bulkUpdateRole'),
            { user_ids: selected.value, role: bulkRole.value },
            {
                preserveScroll: true,
                onSuccess: () => {
                    selected.value = []
                    bulkRole.value = ''
                },
            }
        )
    }
}
</script>

<template>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Users</h1>

    <div class="flex gap-3 mb-5">
        <input v-model="search" @keyup.enter="applyFilters" type="text" placeholder="Search name or email..." class="flex-1 rounded-lg border-gray-200 text-sm" />
        <select v-model="role" @change="applyFilters" class="rounded-lg border-gray-200 text-sm">
            <option value="">All roles</option>
            <option v-for="r in availableRoles" :key="r" :value="r">{{ r }}</option>
        </select>
    </div>

    <!-- Bulk action bar -->
    <div
        v-if="selected.length > 0"
        class="flex items-center justify-between gap-3 mb-4 p-3 rounded-xl bg-primary/5 border border-primary/15 flex-wrap"
    >
        <p class="text-sm text-primary font-medium">{{ selected.length }} selected</p>
        <div class="flex items-center gap-2">
            <select v-model="bulkRole" class="text-xs rounded-lg border-gray-200">
                <option value="">Set role to...</option>
                <option v-for="r in availableRoles" :key="r" :value="r">{{ r }}</option>
            </select>
            <button
                @click="applyBulkRole"
                :disabled="!bulkRole"
                class="px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-medium disabled:opacity-40"
            >
                Apply
            </button>
            <button @click="selected = []" class="px-3 py-1.5 rounded-lg text-gray-500 text-xs hover:bg-gray-100">
                Clear
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-gray-500 border-b border-gray-100">
            <tr>
                <th class="p-3 w-10">
                    <input type="checkbox" :checked="allOnPageSelected" @change="toggleSelectAll" class="rounded border-gray-300" />
                </th>
                <th class="p-3">Name</th>
                <th class="p-3">Email</th>
                <th class="p-3">Role</th>
                <th class="p-3">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="user in users.data" :key="user.id" class="border-b border-gray-50 last:border-0">
                <td class="p-3">
                    <input type="checkbox" v-model="selected" :value="user.id" class="rounded border-gray-300" />
                </td>
                <td class="p-3 font-medium text-gray-900">{{ user.name }}</td>
                <td class="p-3 text-gray-600">{{ user.email }}</td>
                <td class="p-3">
                    <select :value="user.roles[0]?.name" @change="updateRole(user.id, ($event.target as HTMLSelectElement).value)" class="text-xs rounded-lg border-gray-200">
                        <option v-for="r in availableRoles" :key="r" :value="r">{{ r }}</option>
                    </select>
                </td>
                <td class="p-3">
                    <Link :href="route('admin.users.history', user.id)" class="text-primary text-xs font-medium">View history</Link>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div v-if="users.links.length > 3" class="flex items-center justify-between mt-4 flex-wrap gap-3">
        <p class="text-xs text-gray-400">
            Showing {{ users.from ?? 0 }}–{{ users.to ?? 0 }} of {{ users.total }}
        </p>
        <div class="flex gap-1 flex-wrap">
            <button
                v-for="(link, i) in users.links"
                :key="i"
                @click="goToPage(link.url)"
                :disabled="!link.url"
                v-html="link.label"
                class="px-3 py-1.5 rounded-lg text-xs transition"
                :class="[
                    link.active ? 'bg-primary text-white font-medium' : 'text-gray-600 hover:bg-gray-50',
                    !link.url ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer',
                ]"
            />
        </div>
    </div>
</template>
