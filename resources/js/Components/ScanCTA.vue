<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    title: { type: String, default: 'Turn your notes into a practice quiz' },
    description: {
        type: String,
        default: 'Snap a photo of your lecture notes or a past paper. Get exam-style questions with answers, ready to practice.',
    },
    buttonText: { type: String, default: 'Try it now' },
    courseId: { type: [String, Number], default: null },
})

const open = ref(false)
const suffix = computed(() => (props.courseId ? `/${props.courseId}` : ''))
const paperHref = computed(() => `/scan${suffix.value}`)
const notesHref = computed(() => `/scan-notes${suffix.value}`)

const perks = ['Answers explained', 'CBT practice mode', 'Photos or PDF']

const onKey = (e) => e.key === 'Escape' && (open.value = false)
onMounted(() => window.addEventListener('keydown', onKey))
onBeforeUnmount(() => window.removeEventListener('keydown', onKey))
</script>

<template>
    <button
        type="button"
        class="relative my-4 block w-full overflow-hidden rounded-2xl bg-primary p-4 text-left text-white shadow-sm transition hover:shadow-md active:scale-[0.99] sm:p-5"
        @click="open = true"
    >
        <span class="pointer-events-none absolute -right-8 -top-8 h-28 w-28 rounded-full bg-white/10" aria-hidden="true" />
        <span class="pointer-events-none absolute -bottom-10 right-12 h-20 w-20 rounded-full bg-white/5" aria-hidden="true" />

        <span class="relative mb-2 inline-flex items-center gap-1.5 rounded-full bg-amber-300 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide text-amber-950">
            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-950" />
            New
        </span>

        <span class="relative block text-base font-bold leading-snug sm:text-lg">{{ title }}</span>
        <span class="relative mt-1 block text-sm text-white/80">{{ description }}</span>

        <span class="relative mt-3 flex flex-wrap gap-1.5">
            <span v-for="perk in perks" :key="perk" class="rounded-full bg-white/15 px-2.5 py-1 text-[11px] font-medium text-white">
                {{ perk }}
            </span>
        </span>

        <span class="relative mt-4 flex items-center justify-center gap-2 rounded-xl bg-white py-2.5 text-sm font-bold text-primary">
            {{ buttonText }}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                <path d="M5 12h14M13 6l6 6-6 6" />
            </svg>
        </span>
    </button>

    <Teleport to="body">
        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 p-4 sm:items-center"
            @click.self="open = false"
        >
            <div class="w-full max-w-sm rounded-2xl bg-white p-5 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-slate-900">What do you want to scan?</h2>
                    <button type="button" class="text-slate-400 hover:text-slate-600" aria-label="Close" @click="open = false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-5 w-5"><path d="M18 6L6 18M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <Link :href="notesHref" class="block rounded-xl border-2 border-primary bg-primary/5 p-4 hover:bg-primary/10">
                        <span class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-slate-900">Scan from your notes</span>
                            <span class="rounded-full bg-amber-300 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-950">New</span>
                        </span>
                        <span class="mt-0.5 block text-xs text-slate-600">Turn your lecture notes or handouts into a quiz you can practice right away.</span>
                    </Link>

                    <Link :href="paperHref" class="block rounded-xl border border-slate-200 p-4 hover:border-primary hover:bg-primary/5">
                        <span class="block text-sm font-semibold text-slate-900">Scan from a past question</span>
                        <span class="mt-0.5 block text-xs text-slate-500">Turn a photo or PDF of an exam paper into practice questions.</span>
                    </Link>
                </div>
            </div>
        </div>
    </Teleport>
</template>
