<script setup lang="ts">
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppLayout from "@/Layouts/AppLayout.vue";

const drawerOpen = ref(false)
const page = usePage()

const nav = [
    { label: 'Dashboard', href: route('admin.dashboard'), icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { label: 'Past Questions', href: route('admin.papers.index'), icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { label: 'Users', href: route('admin.users.index'), icon: 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4' },
    { label: 'Role Requests', href: route('admin.role-requests.index'), icon: 'M12 6.253v13m0-13C10.832 5.483 9.246 5 7.5 5 5.755 5 4.168 5.483 3 6.253v13C4.168 18.483 5.755 18 7.5 18c1.746 0 3.332.483 4.5 1.253m0-13C13.168 5.483 14.754 5 16.5 5c1.745 0 3.332.483 4.5 1.253v13C19.832 18.483 18.245 18 16.5 18c-1.746 0-3.332.483-4.5 1.253' },
    { label: 'Settings', href: route('admin.settings.index'),
        icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z' }
]
const isActive = (href: string) => page.url.startsWith(new URL(href, window.location.origin).pathname)
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-gray-50 flex">
        <!-- mobile overlay -->
        <div v-if="drawerOpen" @click="drawerOpen = false" class="fixed inset-0 bg-black/30 z-30 md:hidden" />

        <!-- sidebar -->
        <aside
            class="fixed md:static z-40 top-0 left-0 h-full w-64 bg-white border-r border-gray-100 transition-transform duration-200 md:translate-x-0"
            :class="drawerOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="h-16 flex items-center px-5 border-b border-gray-100">
                <span class="font-bold text-primary">myUniAlly Admin</span>
            </div>

            <nav class="p-3 space-y-1">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    @click="drawerOpen = false"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm transition group"
                    :class="isActive(item.href)
            ? 'bg-primary/10 text-primary font-medium'
            : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'"
                >
          <span
              class="w-7 h-7 rounded-lg flex items-center justify-center transition"
              :class="isActive(item.href) ? 'bg-primary/15 text-primary' : 'bg-primary/10 text-primary group-hover:bg-primary/15'"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
            </svg>
          </span>
                    {{ item.label }}
                </Link>
            </nav>
        </aside>

        <!-- main -->
        <div class="flex-1 md:ml-0">
            <header class="h-16 bg-white border-b border-gray-100 flex items-center px-4 md:hidden">
                <button @click="drawerOpen = true" class="text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="ml-3 font-semibold text-primary">Admin</span>
            </header>

            <main class="p-4 ">
                <slot />
            </main>
        </div>
    </div>
    </AppLayout>
</template>
