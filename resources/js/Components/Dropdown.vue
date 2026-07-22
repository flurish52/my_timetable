<script setup>
import {ref} from 'vue'
import {Link, usePage} from '@inertiajs/vue3'

const page = usePage()
const open = ref(false)

function toggle() {
    open.value = !open.value
}

function close() {
    open.value = false
}
</script>

<template>
    <div class="relative" v-if="page.props.auth.user">

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
                <polyline points="2,4 6,8 10,4"/>
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
                class="absolute right-0 mt-2.5 w-56 bg-white rounded-2xl border border-gray-100 shadow-xl z-50 overflow-hidden"
            >
                <!-- User info header -->
                <div class="px-4 py-3.5 border-b border-gray-100 bg-gray-50/60">
                    <p class="text-sm font-semibold text-gray-900 text-left pl-6">
                        {{ page.props.auth.user.username || page.props.auth.user.name }}
                    </p>
                    <p class="text-xs text-gray-400 truncate mt-0.5">
                        {{ page.props.auth.user.name }}
                    </p>
                    <p class="text-xs text-gray-400 truncate mt-0.5">
                        {{ page.props.auth.user.email }}
                    </p>
                </div>

                <!-- Menu items -->
                <div class="p-1.5 flex flex-col gap-0.5">

                    <!-- Profile -->
                    <Link
                        :href="route('profile.edit')"
                        @click="close"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition group"
                    >
                        <span
                            class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary/15 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                        </span>
                        Profile
                    </Link>

                    <!-- Course -->
                    <Link
                        href="/course_offerings"
                        @click="close"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition group"
                    >
                        <span
                            class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary/15 transition">
                           <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 6.253v13m0-13C10.832 5.483 9.246 5 7.5 5 5.755 5 4.168 5.483 3 6.253v13C4.168 18.483 5.755 18 7.5 18c1.746 0 3.332.483 4.5 1.253m0-13C13.168 5.483 14.754 5 16.5 5c1.745 0 3.332.483 4.5 1.253v13C19.832 18.483 18.245 18 16.5 18c-1.746 0-3.332.483-4.5 1.253"
                                />
                           </svg>
                        </span>
                        Manage courses
                    </Link>

                    <!--                    Become a contributor -->
                    <Link
                         href="/become-contributor"
                        @click="close"
                        class="flex items-left gap-2.5  px-3 py-2 rounded-xl text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition group"
                    >
                        <span
                            class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary/15 transition">
                           <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 14.25L4.5 9.75 12 5.25l7.5 4.5-7.5 4.5zm0 0v6m-7.5-3.75V16.5c0 .621 1.5 1.5 3 1.5h9c1.5 0 3-.879 3-1.5v-2.25"
                                />
                           </svg>
                        </span>
                        Become a Contributor
                    </Link>

                    <!-- Settings -->
                    <Link
                        href="/setup"
                        @click="close"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition group"
                    >
                        <span
                            class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary/15 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </span>
                        Settings
                    </Link>

                </div>

                <!-- Divider + Logout -->
                <div class="p-1.5 border-t border-gray-100">
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        @click="close"
                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm text-red-600 hover:bg-red-50 transition group"
                    >
                        <span
                            class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center text-red-500 group-hover:bg-red-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                            </svg>
                        </span>
                        Log out
                    </Link>
                </div>

            </div>
        </Transition>
    </div>
</template>
