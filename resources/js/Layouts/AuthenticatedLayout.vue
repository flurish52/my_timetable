<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { setupNotifications } from '../composables/useNotifications.js'
import {onMounted} from "vue";

const showingNavigationDropdown = ref(false);

onMounted(()=>{
    setupNotifications()
})

</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <div class="flex items-center gap-2.5 mt-1">

                <button
                    aria-label="Notifications"
                    class="relative w-10 h-10 rounded-full border border-gray-200 bg-white shadow-sm flex items-center justify-center hover:shadow-md transition-shadow"
                >
                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                        />
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 border-2 border-white"/>
                </button>

                <button
                    aria-label="Profile"
                    class="w-10 h-10 rounded-full bg-secondary text-white font-bold text-base shadow-sm flex items-center justify-center hover:opacity-90 transition-opacity"
                >
                    {{ user?.avatar }}
                </button>

            </div>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
