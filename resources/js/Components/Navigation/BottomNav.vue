<template>
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-[var(--color-surface,#fff)] border-t border-[var(--color-border,#e2e8f0)] px-2 pb-[env(safe-area-inset-bottom)] shadow-[0_-2px_12px_rgba(0,0,0,0.06)]">
        <ul class="flex items-stretch m-0 p-0 list-none">
            <li
                v-for="item in navItems"
                :key="item.to"
                class="flex-1"
            >
                <Link
                    :href="item.to"
                    class="flex flex-col items-center justify-center gap-1 py-2.5 px-1 rounded-xl mx-0.5 my-1 text-[var(--color-muted,#94a3b8)] transition-all duration-200 no-underline group"
                    :class="{ 'is-active': page.url === item.to }"
                >
                    <span
                        class="flex items-center justify-center w-6 h-6 transition-transform duration-200 group-[.is-active]:scale-110"
                        v-html="item.icon"
                    ></span>
                    <span class="text-[0.65rem] text-center font-semibold tracking-wide leading-none">
                        {{ item.label }}
                    </span>
                </Link>
            </li>
        </ul>
    </nav>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { useNav } from '@/composables/useNav'

const page = usePage()
const { navItems } = useNav()
</script>

<style scoped>
a.is-active {
    color: var(--color-primary, #3b82f6);
    background: color-mix(in srgb, var(--color-primary, #3b82f6) 8%, transparent);
}
</style>
