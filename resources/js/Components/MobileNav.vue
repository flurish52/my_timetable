<template>
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-[var(--color-surface,#fff)] border-t border-[var(--color-border,#e2e8f0)] px-2 pb-[env(safe-area-inset-bottom)] shadow-[0_-2px_12px_rgba(0,0,0,0.06)] md:hiddene">
        <ul class="flex items-stretch m-0 p-0 list-none">
            <li
                v-for="item in navItems"
                :key="item.to"
                class="flex-1"
            >
                <RouterLink
                    :to="item.to"
                    class="flex flex-col items-center justify-center gap-1 py-2.5 px-1 rounded-xl mx-0.5 my-1 text-[var(--color-muted,#94a3b8)] transition-all duration-200 no-underline group"
                    :class="{ 'is-active': route.path === item.to }"
                >
                    <span
                        class="flex items-center justify-center w-6 h-6 transition-transform duration-200 group-[.is-active]:scale-110"
                        v-html="item.icon"
                    ></span>
                    <span class="text-[0.65rem] font-semibold tracking-wide leading-none">
                        {{ item.label }}
                    </span>
                </RouterLink>
            </li>
        </ul>
    </nav>
</template>

<script setup>
import { RouterLink, useRoute } from 'vue-router'

const route = useRoute()

const navItems = [
    {
        to: '/',
        label: 'Home',
        icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z"/>
            <path d="M9 21V12h6v9"/>
        </svg>`,
    },
    {
        to: '/pastquestions',
        label: 'Materials',
        icon: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            <line x1="9" y1="7" x2="15" y2="7"/>
            <line x1="9" y1="11" x2="15" y2="11"/>
        </svg>`,
    },
]
</script>

<style scoped>
a.is-active {
    color: var(--color-primary, #3b82f6);
    background: color-mix(in srgb, var(--color-primary, #3b82f6) 8%, transparent);
}
</style>
