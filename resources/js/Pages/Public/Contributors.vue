<template>
    <Head title="Contributors" />
    <div class="min-h-screen flex items-start justify-center bg-[var(--color-bg,#f5f7fa)] font-inherit">
        <div class="w-full max-w-[680px] bg-[var(--color-surface,#fff)] border border-[var(--color-border,#e2e8f0)] rounded-2xl p-6 md:p-8 shadow-[0_2px_12px_rgba(0,0,0,0.06)]">
            <BackButton route="/pastquestions" />

            <!-- Header -->
            <div class="mb-6 text-center">
                <div class="flex items-center justify-center gap-2 mb-1">
                    <span class="text-[0.7rem] font-semibold tracking-widest uppercase text-primary/60">{{ school.name }}</span>
                </div>
                <h1 class="m-0 mb-1.5 text-[1.5rem] font-bold text-[var(--color-text,#1a202c)] leading-tight">
                    Contributors board
                </h1>
                <p class="m-0 text-sm text-[var(--color-muted,#718096)] leading-relaxed max-w-[440px] mx-auto">
                    Every paper here was shared by someone who's been where you are.
                    Add yours, and help the next set of students walk in a little more prepared.
                </p>
            </div>

            <!-- Search -->
            <div v-if="contributors.length > 0" class="relative mb-6">
                <span class="absolute left-[0.85rem] top-1/2 -translate-y-1/2 flex pointer-events-none text-[var(--color-muted,#a0aec0)]">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </span>
                <input
                    v-model="query"
                    type="text"
                    placeholder="Find a contributor by username..."
                    class="w-full box-border py-[0.65rem] pr-10 pl-[2.4rem] border-[1.5px] border-[var(--color-border,#e2e8f0)] rounded-[0.6rem] text-[0.9rem] bg-[var(--color-bg,#f8fafc)] text-[var(--color-text,#1a202c)] outline-none transition-[border-color,box-shadow] duration-200 font-inherit focus:border-primary focus:bg-[var(--color-surface,#fff)]"
                />
                <button
                    v-if="query"
                    @click="query = ''"
                    class="absolute right-[0.85rem] top-1/2 -translate-y-1/2 bg-transparent border-none cursor-pointer flex p-[2px] rounded-full text-[var(--color-muted,#a0aec0)] transition-colors duration-150 hover:text-[var(--color-text,#1a202c)]"
                >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M18 6 6 18M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Match dropdown -->
                <div
                    v-if="query && matches.length"
                    class="absolute z-10 top-[calc(100%+6px)] left-0 right-0 bg-[var(--color-surface,#fff)] border border-[var(--color-border,#e2e8f0)] rounded-xl shadow-[0_6px_20px_rgba(0,0,0,0.09)] overflow-hidden"
                >
                    <button
                        v-for="m in matches"
                        :key="m.id"
                        @click="jumpTo(m.id)"
                        class="w-full flex items-center gap-2.5 px-3.5 py-2.5 bg-transparent border-none cursor-pointer text-left hover:bg-primary/5 transition-colors duration-150"
                    >
                        <span class="shrink-0 w-6 h-6 rounded-full bg-primary/8 text-primary flex items-center justify-center text-[0.68rem] font-semibold">
                            {{ rankOf(m.id) }}
                        </span>
                        <span class="flex-1 min-w-0 text-[0.82rem] font-semibold text-[var(--color-text,#1a202c)] truncate">
                            {{ m.username }}
                        </span>
                        <span class="shrink-0 text-[0.72rem] text-[var(--color-muted,#a0aec0)]">
                            #{{ rankOf(m.id) }} · {{ m.contributions_count }} paper{{ m.contributions_count !== 1 ? 's' : '' }}
                        </span>
                    </button>
                </div>
                <div
                    v-else-if="query && !matches.length"
                    class="absolute z-10 top-[calc(100%+6px)] left-0 right-0 bg-[var(--color-surface,#fff)] border border-[var(--color-border,#e2e8f0)] rounded-xl shadow-[0_6px_20px_rgba(0,0,0,0.09)] px-3.5 py-3 text-[0.8rem] text-[var(--color-muted,#a0aec0)]"
                >
                    No contributor found for "{{ query }}"
                </div>
            </div>

            <!-- Encouraging banner -->
            <div class="mb-7 rounded-xl bg-primary/6 border border-primary/15 px-4 py-3.5 flex items-start gap-3">
                <div class="shrink-0 w-8 h-8 rounded-lg bg-primary/12 text-primary flex items-center justify-center mt-0.5">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                    </svg>
                </div>
                <p class="m-0 text-[0.82rem] text-[var(--color-text,#1a202c)] leading-relaxed">
                    <span class="font-semibold">Leave something behind.</span>
                    Contribute a past question and your name goes up here — for every
                    junior who searches this course after you.
                </p>
            </div>

            <!-- Empty state -->
            <div v-if="contributors.length === 0"
                 class="flex flex-col items-center gap-3 py-14 px-4 text-[var(--color-muted,#a0aec0)] text-[0.9rem]">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-1">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-gray-400">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <span class="font-medium text-gray-500">No contributors yet</span>
                <span class="text-xs text-gray-400 text-center max-w-[220px]">Be the first to add a past question for {{ school.name }}</span>
            </div>

            <template v-else>
                <!-- Podium: top 3 -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5 mb-6">
                    <div
                        v-for="(c, i) in podium"
                        :key="c.id"
                        :id="`u-${c.id}`"
                        class="relative rounded-xl border p-3.5 flex flex-col items-center text-center animate-[fadeUp_0.35s_ease_both] transition-shadow duration-300"
                        :class="[podiumStyle(i).card, { 'ring-2 ring-primary/60': highlightedId === c.id }]"
                        :style="{ animationDelay: `${i * 60}ms` }"
                    >
                        <span class="text-[1.35rem] leading-none mb-1.5">{{ podiumStyle(i).icon }}</span>
                        <span class="font-semibold text-[0.82rem] truncate max-w-full" :class="podiumStyle(i).text">
                            {{ c.username }}
                        </span>
                        <span class="text-[0.68rem] text-[var(--color-muted,#a0aec0)] mt-0.5">{{ c.year }}</span>
                        <span class="mt-2 text-[0.7rem] font-semibold px-2 py-0.5 rounded-full" :class="podiumStyle(i).badge">
                            {{ c.contributions_count }} paper{{ c.contributions_count !== 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>

                <!-- Rest of the list -->
                <ul v-if="rest.length" class="list-none m-0 p-0 flex flex-col gap-2">
                    <li
                        v-for="(c, i) in rest"
                        :key="c.id"
                        :id="`u-${c.id}`"
                        class="flex items-center gap-3 rounded-xl border border-[var(--color-border,#e8edf2)] bg-white px-4 py-3 animate-[fadeUp_0.35s_ease_both] transition-colors duration-300"
                        :class="{ 'ring-2 ring-primary/60 bg-primary/4': highlightedId === c.id }"
                        :style="{ animationDelay: `${(i + 3) * 40}ms` }"
                    >
                        <span class="shrink-0 w-6 text-center text-[0.78rem] font-semibold text-[var(--color-muted,#a0aec0)]">
                            {{ i + 4 }}
                        </span>
                        <div class="shrink-0 w-8 h-8 rounded-full bg-primary/8 text-primary flex items-center justify-center text-[0.72rem] font-semibold">
                            {{ initials(c.username) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="font-semibold text-[0.85rem] text-[var(--color-text,#1a202c)] truncate">{{ c.username }}</span>
                                <span v-if="c.contributions_count >= FIRE_THRESHOLD" title="On a roll">🔥</span>
                            </div>
                            <span class="text-[0.72rem] text-[var(--color-muted,#a0aec0)]">{{ c.year }}</span>
                        </div>
                        <span class="shrink-0 text-[0.75rem] font-semibold text-primary">
                            {{ c.contributions_count }} paper{{ c.contributions_count !== 1 ? 's' : '' }}
                        </span>
                    </li>
                </ul>
            </template>

            <!-- Bottom CTA -->
            <div class="mt-7 pt-5 border-t border-[var(--color-border,#e2e8f0)] flex flex-col items-center text-center gap-2">
                <p class="m-0 text-[0.8rem] text-[var(--color-muted,#718096)]">
                    Have a past question sitting on your laptop? It could be exactly what someone needs tonight.
                </p>
                <Link
                    v-if="isOnlyStudent"
                    href="/become-contributor"
                    class="inline-flex items-center gap-1.5 px-5 py-2 bg-primary text-white rounded-lg text-[0.82rem] font-semibold no-underline transition-all duration-150 hover:opacity-90 hover:-translate-y-px active:translate-y-0 shadow-[0_2px_8px_rgba(0,0,0,0.12)]">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Contribute a paper
                </Link>
                <Link
                    v-else
                    href="/contributor/past-questions/create"
                    class="inline-flex items-center gap-1.5 px-5 py-2 bg-primary text-white rounded-lg text-[0.82rem] font-semibold no-underline transition-all duration-150 hover:opacity-90 hover:-translate-y-px active:translate-y-0 shadow-[0_2px_8px_rgba(0,0,0,0.12)]">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Contribute a paper
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, nextTick } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import BackButton from "@/Components/BackButton.vue"
const page = usePage()

const props = defineProps({
    contributors: { type: Array, required: true },
    school: { type: Object, required: true },
})

const FIRE_THRESHOLD = 10
const query = ref('')
const highlightedId = ref(null)
let highlightTimeout = null

const podium = computed(() => props.contributors.slice(0, 3))
const rest = computed(() => props.contributors.slice(3))

const matches = computed(() => {
    const q = query.value.trim().toLowerCase()
    if (!q) return []
    return props.contributors
        .filter(c => (c.username ?? '').toLowerCase().includes(q))
        .slice(0, 5)
})

const rankOf = (id) => props.contributors.findIndex(c => c.id === id) + 1

const initials = (name) =>
    (name ?? '')
        .split(/[\s_-]+/)
        .filter(Boolean)
        .slice(0, 2)
        .map(w => w[0].toUpperCase())
        .join('')

const jumpTo = async (id) => {
    query.value = ''
    await nextTick()

    const el = document.getElementById(`u-${id}`)
    if (!el) return

    el.scrollIntoView({ behavior: 'smooth', block: 'center' })

    if (highlightTimeout) clearTimeout(highlightTimeout)
    highlightedId.value = id
    highlightTimeout = setTimeout(() => {
        highlightedId.value = null
    }, 2200)
}

const podiumStyle = (index) => {
    const styles = [
        {
            icon: '👑',
            card: 'border-amber-200 bg-amber-50/60 scale-[1.04] shadow-[0_4px_14px_rgba(217,164,6,0.12)]',
            text: 'text-amber-700',
            badge: 'bg-amber-100 text-amber-700',
        },
        {
            icon: '🥈',
            card: 'border-gray-200 bg-gray-50',
            text: 'text-gray-600',
            badge: 'bg-gray-100 text-gray-600',
        },
        {
            icon: '🥉',
            card: 'border-orange-200 bg-orange-50/60',
            text: 'text-orange-700',
            badge: 'bg-orange-100 text-orange-700',
        },
    ]
    return styles[index] ?? styles[2]
}

const isOnlyStudent = computed(() => {
    const roles = page.props.auth.user.roles
    return roles.length === 1 && roles[0].name === 'student'
})
</script>

<style>
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
