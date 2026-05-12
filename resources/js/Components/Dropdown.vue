<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const open = ref(false)
function toggle() { open.value = !open.value }
function close()  { open.value = false }
</script>

<template>
    <div class="relative"
         v-if="page.props.auth.user">

        <!-- Trigger button -->
        <button
            @click="toggle"
            class="flex items-center gap-1 focus:outline-none"
            aria-haspopup="true"
            :aria-expanded="open"
        >
            <!-- Avatar image -->
            <img
                v-if="page.props.auth.user.avatar"
                :src="page.props.auth.user.avatar"
                @error="e => e.target.src = '/images/avatar-placeholder.png'"
                class="w-10 h-10 rounded-full object-cover"
            />

            <!-- Initials fallback -->
            <div
                v-else
                class="w-10 h-10 rounded-full bg-primary hover:bg-primary/50 border-2 text-white font-bold text-base flex items-center justify-center"
            >
                {{ page.props.auth.user.username[0].toUpperCase() }}
            </div>

            <!-- Chevron indicator -->
            <svg
                :class="['w-3 h-3 text-gray-500 text-white absolute right-0 bg-primary transition-transform duration-200',
                open ? 'rotate-180' : '']"
                viewBox="0 0 12 12"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <polyline points="2,4 6,8 10,4" />
            </svg>
        </button>

        <!-- Dropdown -->
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 scale-95 -translate-y-1"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 -translate-y-1"
        >
            <div
                v-if="open"
                v-click-outside="close"
                class="absolute right-0 mt-2 w-44 bg-white rounded-xl border border-gray-100 shadow-lg z-50 p-1.5 flex flex-col gap-1"
            >
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    @click="close"
                    class="text-sm px-3 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition text-center w-full"
                >
                    Log Out
                </Link>
            </div>
        </Transition>
    </div>
</template>
