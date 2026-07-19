<template>
    <AppLayout>
        <div class="min-h-screen flex bg-neutral-50 px-4">
            <!-- Mobile backdrop -->
            <Transition
                enter-active-class="transition-opacity duration-200"
                leave-active-class="transition-opacity duration-150"
                enter-from-class="opacity-0"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="isOpen"
                    @click="isOpen = false"
                    class="md:hidden fixed inset-0 z-30 bg-neutral-900/50 backdrop-blur-[2px]"
                />
            </Transition>

            <!-- Hamburger — mobile only -->
            <button
                @click="isOpen = !isOpen"
                class="md:hidden fixed top-10 left-6 z-50 flex h-10 w-10 items-center justify-center rounded-lg bg-primary border border-white text-white shadow-sm transition-transform active:scale-95 hover:bg-primary/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                :aria-expanded="isOpen"
                aria-label="Toggle menu"
            >
                <svg v-if="!isOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <line x1="4" y1="6" x2="20" y2="6" /><line x1="4" y1="12" x2="20" y2="12" /><line x1="4" y1="18" x2="20" y2="18" />
                </svg>
                <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <line x1="6" y1="6" x2="18" y2="18" /><line x1="6" y1="18" x2="18" y2="6" />
                </svg>
            </button>

            <!-- Sidebar: fixed at all breakpoints so it never scrolls with the page -->
            <aside
                class="fixed inset-y-0 left-0 z-40 w-72 h-screen shrink-0 flex flex-col bg-white border-r border-neutral-200 transition-transform duration-200 ease-out md:translate-x-0"
                :class="isOpen ? 'translate-x-0 shadow-xl' : '-translate-x-full'"
            >
                <!-- Brand -->
                <div class="h-16 flex items-center gap-2.5 px-5 border-b border-neutral-200 shrink-0">
<!--                    <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white text-sm font-bold shrink-0">-->
<!--                        C-->
<!--                    </div>-->
<!--                    <div class="min-w-0">-->
<!--                        <p class="text-sm font-bold text-neutral-900 leading-tight truncate">Contributor</p>-->
<!--                        <p class="text-[11px] text-neutral-400 leading-tight">Contributor workspace</p>-->
<!--                    </div>-->
                </div>

                <!-- Nav: only this area scrolls when items overflow -->
                <nav class="flex-1 min-h-0 overflow-y-auto px-3 py-4 space-y-1">
                    <div v-for="item in navItems" :key="item.key">
                        <div
                            class="group relative flex items-center rounded-lg overflow-hidden transition-colors"
                            :class="groupActive(item) ? 'bg-primary/[0.08]' : 'hover:bg-neutral-100/80'"
                        >
                            <span v-if="groupActive(item)" class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-full bg-primary" />

                            <Link
                                :href="hrefFor(item.indexRoute)"
                                @click="closeOnMobile"
                                class="flex-1 min-w-0 flex items-center gap-3 pl-4 pr-2 py-2.5 text-sm font-medium focus-visible:outline-none"
                                :class="groupActive(item) ? 'text-primary' : 'text-neutral-600 group-hover:text-neutral-900'"
                            >
                            <span
                                v-html="item.icon"
                                class="w-[18px] h-[18px] shrink-0 [&>svg]:w-full [&>svg]:h-full"
                                :class="groupActive(item) ? 'text-primary' : 'text-neutral-400 group-hover:text-neutral-500'"
                            />
                                <span class="truncate">{{ item.label }}</span>
                            </Link>

                            <button
                                @click="toggleExpand(item.key)"
                                class="shrink-0 p-2.5 text-neutral-400 hover:text-neutral-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/30 rounded-md"
                                :aria-label="`Toggle ${item.label}`"
                                :aria-expanded="expanded[item.key]"
                            >
                                <svg
                                    class="w-3.5 h-3.5 transition-transform duration-150"
                                    :class="expanded[item.key] && 'rotate-180'"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.65a.75.75 0 01-1.08 0l-4.25-4.65a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        <Transition
                            enter-active-class="transition-all duration-150 ease-out"
                            leave-active-class="transition-all duration-100 ease-in"
                            enter-from-class="opacity-0 -translate-y-1"
                            leave-to-class="opacity-0 -translate-y-1"
                        >
                            <div v-show="expanded[item.key]" class="mt-1 ml-[1.7rem] pl-3 border-l border-neutral-200 space-y-0.5 mb-1">
                                <Link
                                    v-for="child in item.children"
                                    :key="child.route"
                                    :href="hrefFor(child.route)"
                                    @click="closeOnMobile"
                                    class="flex items-center gap-2 rounded-md px-3 py-1.5 text-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/30"
                                    :class="isActive(child.route)
                                    ? 'text-secondary font-medium bg-secondary/10'
                                    : 'text-neutral-500 hover:text-neutral-800 hover:bg-neutral-100/80'"
                                >
                                    {{ child.label }}
                                </Link>
                            </div>
                        </Transition>
                    </div>
                </nav>

                <!-- Footer / account -->
                <div class="flex items-center gap-3 px-4 py-3 border-t border-neutral-200 shrink-0">
                    <div class="w-8 h-8 rounded-full bg-neutral-100 text-neutral-600 flex items-center justify-center text-xs font-semibold shrink-0">
                        {{ userInitial }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-neutral-400 leading-tight">Signed in as</p>
                        <p class="text-sm font-medium text-neutral-700 truncate leading-tight">{{ userName }}</p>
                    </div>
                </div>
            </aside>

            <!-- Page content -->
            <div class="flex-1 min-w-0 flex flex-col md:ml-72  ">
                <slot />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppLayout from "@/Layouts/AppLayout.vue";

const DOCUMENT_ICON = '<svg viewBox="0 0 20 20" fill="currentColor"><path d="M5 2a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V8.414a2 2 0 00-.586-1.414l-4.414-4.414A2 2 0 0010.586 2H5zm5 1.5V7a1 1 0 001 1h3.5L10 3.5zM7 11a1 1 0 100 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7z"/></svg>'
const CALENDAR_ICON = '<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 011 1v1h6V3a1 1 0 112 0v1h1a2 2 0 012 2v11a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2h1V3a1 1 0 011-1zm10 6H4v9a.5.5 0 00.5.5h11a.5.5 0 00.5-.5V8zM7 11a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h.01a1 1 0 100-2H10zm3 0a1 1 0 100 2h.01a1 1 0 100-2H13z" clip-rule="evenodd"/></svg>'
const BOOK_ICON = '<svg viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 3.104a.75.75 0 00-1.5 0v.395a30.29 30.29 0 00-6.393 1.13.75.75 0 00-.532.86l1.5 8.5a.75.75 0 00.83.615A28.73 28.73 0 0110 14a28.73 28.73 0 015.345.604.75.75 0 00.83-.615l1.5-8.5a.75.75 0 00-.532-.86 30.29 30.29 0 00-6.393-1.13v-.395zM10 15.5c-1.978 0-3.912.181-5.79.528l.13.735A28.73 28.73 0 0110 16.25c1.887 0 3.72.176 5.66.513l.13-.735A30.23 30.23 0 0010 15.5z"/></svg>'

const page = usePage()

const isOpen = ref(false)

const expanded = reactive({
    'past-questions': true,
    courses: false,
    timetable: false,
})

const navItems = [
    {
        key: 'past-questions',
        label: 'Past Questions',
        icon: DOCUMENT_ICON,
        indexRoute: 'past-questions.index',
        children: [
            { label: 'All papers', route: 'past-questions.index' },
            { label: 'Create new', route: 'past-questions.create' },
        ],
    },
    {
        key: 'courses_offerings',
        label: 'Course Offerings',
        icon: BOOK_ICON,
        indexRoute: 'course_offerings.index',
        children: [
            { label: 'All courses', route: 'course_offerings.index' },
            { label: 'Add course', route: 'course_offerings.create' },
        ],
    },
    {
        key: 'timetable',
        label: 'Timetable',
        icon: CALENDAR_ICON,
        indexRoute: 'timetable.index',
        children: [
            { label: 'My timetable', route: 'timetable.index' },
            { label: 'Create new', route: 'timetable.create' },
        ],
    },
]

const userName = computed(() => page.props.auth?.user?.name ?? 'Contributor')
const userInitial = computed(() => userName.value.trim().charAt(0).toUpperCase() || 'C')

function toggleExpand(key) {
    expanded[key] = !expanded[key]
}

function closeOnMobile() {
    isOpen.value = false
}

// Routes may not exist yet (e.g. timetable.*, courses.*) — fail quietly to "#"
// instead of throwing, so the layout still renders during development.
function hrefFor(name) {
    try {
        return route(name)
    } catch (e) {
        return '#'
    }
}

function isActive(name) {
    try {
        return route().current(name)
    } catch (e) {
        return false
    }
}

function groupActive(item) {
    try {
        return route().current(`${item.indexRoute.split('.')[0]}.*`)
    } catch (e) {
        return false
    }
}

// Auto-expand whichever section the current page belongs to
onMounted(() => {
    for (const item of navItems) {
        if (groupActive(item)) expanded[item.key] = true
    }
})
</script>
