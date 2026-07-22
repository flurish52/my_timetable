<script setup lang="ts">
import {ref, computed} from 'vue'
import {router, Link} from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({layout: AdminLayout})

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
    router.get(route('admin.users.index'), {search: search.value, role: role.value}, {preserveState: true})
}

const updateRole = (userId: number, newRole: string) => {
    router.patch(route('admin.users.updateRole', userId), {role: newRole})
}

const goToPage = (url: string | null) => {
    if (url) router.get(url, {}, {preserveState: true, preserveScroll: true})
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
            {user_ids: selected.value, role: bulkRole.value},
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

// --- Online status: computed live from last_seen_at, never trust a stored flag ---
const ONLINE_WINDOW_MS = 2 * 60 * 1000 // must match backend reaper window

const isOnline = (user: any) => {
    if (!user.last_seen_at) return false
    return Date.now() - new Date(user.last_seen_at).getTime() < ONLINE_WINDOW_MS
}

const timeAgo = (dateString: string | null) => {
    if (!dateString) return 'Never'

    const seconds = Math.floor((Date.now() - new Date(dateString).getTime()) / 1000)
    if (seconds < 60) return 'Just now'

    const minutes = Math.floor(seconds / 60)
    if (minutes < 60) return `${minutes}m ago`

    const hours = Math.floor(minutes / 60)
    if (hours < 24) return `${hours}h ago`

    const days = Math.floor(hours / 24)
    if (days < 30) return `${days}d ago`

    return new Date(dateString).toLocaleDateString()
}
</script>

<template>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Users</h1>

    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <input v-model="search" @keyup.enter="applyFilters" type="text" placeholder="Search name or email..."
               class="flex-1 rounded-lg border-gray-200 text-sm min-w-0"/>
        <select v-model="role" @change="applyFilters" class="rounded-lg border-gray-200 text-sm w-full sm:w-auto">
            <option value="">All roles</option>
            <option v-for="r in availableRoles" :key="r" :value="r">{{ r }}</option>
        </select>
    </div>

    <!-- Bulk action bar -->
    <div
        v-if="selected.length > 0"
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 p-3 rounded-xl bg-primary/5 border border-primary/15"
    >
        <p class="text-sm text-primary font-medium">{{ selected.length }} selected</p>
        <div class="flex items-center gap-2 flex-wrap">
            <select v-model="bulkRole" class="text-xs rounded-lg border-gray-200 flex-1 sm:flex-none min-w-0">
                <option value="">Set role to...</option>
                <option v-for="r in availableRoles" :key="r" :value="r">{{ r }}</option>
            </select>
            <button
                @click="applyBulkRole"
                :disabled="!bulkRole"
                class="px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-medium disabled:opacity-40 whitespace-nowrap"
            >
                Apply
            </button>
            <button @click="selected = []"
                    class="px-3 py-1.5 rounded-lg text-gray-500 text-xs hover:bg-gray-100 whitespace-nowrap">
                Clear
            </button>
        </div>
    </div>

    <!-- Desktop / tablet table -->
    <div class="hidden md:block bg-white rounded-xl border border-gray-100 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-gray-500 border-b border-gray-100">
            <tr>
                <th class="p-3 w-10">
                    <input type="checkbox" :checked="allOnPageSelected" @change="toggleSelectAll"
                           class="rounded border-gray-300"/>
                </th>
                <th class="p-3">Name</th>
                <th class="p-3">Email</th>
                <th class="p-3">Status</th>
                <th class="p-3">Programme</th>
                <th class="p-3">Level</th>
                <th class="p-3">Last login</th>
                <th class="p-3">Role</th>
                <th class="p-3">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="user in users.data" :key="user.id" class="border-b border-gray-50 last:border-0">
                <td class="p-3">
                    <input type="checkbox" v-model="selected" :value="user.id" class="rounded border-gray-300"/>
                </td>
                <td class="p-3 font-medium text-gray-900">{{ user.name }}</td>
                <td class="p-3 text-gray-600">{{ user.email }}</td>
                <td class="p-3">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full shrink-0"
                              :class="user.is_online ? 'bg-secondary animate-pulse' : 'bg-gray-300'"/>
                        <span class="text-xs" :class="user.is_online ? 'text-secondary font-medium' : 'text-gray-400'">
    {{ user.is_online === 1 ? 'Online' : `Last seen ${timeAgo(user.last_seen_at)}` }}
</span>
                    </div>
                </td>
                <td class="p-3 text-gray-600">{{ user.programme?.name ?? '—' }}</td>
                <td class="p-3 text-gray-600">{{ user.level?.name ?? '—' }}</td>
                <td class="p-3 text-gray-600 whitespace-nowrap">{{ timeAgo(user.last_login_at) }}</td>
                <td class="p-3">
                    <select :value="user.roles[0]?.name"
                            @change="updateRole(user.id, ($event.target as HTMLSelectElement).value)"
                            class="text-xs rounded-lg border-gray-200">
                        <option v-for="r in availableRoles" :key="r" :value="r">{{ r }}</option>
                    </select>
                </td>
                <td class="p-3">
                    <Link :href="route('admin.users.history', user.id)" class="text-primary text-xs font-medium">View
                        history
                    </Link>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Mobile card list -->
    <div class="md:hidden space-y-3">
        <div v-if="users.data.length > 0" class="flex items-center gap-2 px-1">
            <input type="checkbox" :checked="allOnPageSelected" @change="toggleSelectAll"
                   class="rounded border-gray-300"/>
            <span class="text-xs text-gray-500">Select all on page</span>
        </div>

        <div
            v-for="user in users.data"
            :key="user.id"
            class="bg-white rounded-xl border border-gray-100 p-4"
        >
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3 min-w-0">
                    <input type="checkbox" v-model="selected" :value="user.id"
                           class="rounded border-gray-300 mt-1 shrink-0"/>
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 break-words">{{ user.name }}</p>
                        <p class="text-gray-600 text-xs break-all">{{ user.email }}</p>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span
                                class="w-2 h-2 rounded-full shrink-0"
                                :class="isOnline(user) ? 'bg-secondary animate-pulse' : 'bg-gray-300'"
                            />
                            <span class="text-xs"
                                  :class="isOnline(user) ? 'text-secondary font-medium' : 'text-gray-400'">
                                {{ isOnline(user) ? 'Online' : timeAgo(user.last_seen_at) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t border-gray-50 text-xs">
                <div>
                    <p class="text-gray-400">Programme</p>
                    <p class="text-gray-700 font-medium">{{ user.programme?.name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Level</p>
                    <p class="text-gray-700 font-medium">{{ user.level?.name ?? '—' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-gray-400">Last login</p>
                    <p class="text-gray-700 font-medium">{{ timeAgo(user.last_login_at) }}</p>
                </div>
            </div>

            <div class="flex flex-col gap-2 mt-3 pt-3 border-t border-gray-50">
                <select :value="user.roles[0]?.name"
                        @change="updateRole(user.id, ($event.target as HTMLSelectElement).value)"
                        class="text-xs rounded-lg border-gray-200 w-full">
                    <option v-for="r in availableRoles" :key="r" :value="r">{{ r }}</option>
                </select>
                <Link :href="route('admin.users.history', user.id)" class="text-primary text-xs font-medium self-start">
                    View history
                </Link>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div v-if="users.links.length > 3" class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4 gap-3">
        <p class="text-xs text-gray-400 text-center sm:text-left">
            Showing {{ users.from ?? 0 }}–{{ users.to ?? 0 }} of {{ users.total }}
        </p>
        <div class="flex gap-1 flex-wrap justify-center sm:justify-end">
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
