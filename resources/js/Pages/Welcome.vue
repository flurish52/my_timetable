<script setup>
import {Head, Link} from '@inertiajs/vue3'
import {ref} from 'vue'
import ScanCTA from "@/Components/ScanCTA.vue";

const scanStep = ref(3)

const features = [
    {
        title: 'Real past questions, sorted for you',
        desc: 'Every question is scoped to your exact school, programme, level and semester, no more scrolling through PDFs meant for a different department.',
        icon: 'stack',
    },
    {
        title: 'Scan a past question, AI does the rest',
        desc: 'Snap a photo or upload a PDF of any past question paper. Our AI reads it, splits it into individual questions, and adds it to your library in seconds.',
        icon: 'scan',
    },
    {
        title: 'Practice like it\'s the real exam',
        desc: 'Timed sessions, instant scoring, and a corrections page that shows you exactly what you missed and why, so mistakes only cost you once.',
        icon: 'clock',
    },
    {
        title: 'A streak that keeps you sharp',
        desc: 'One Question of the Day, every day. Small enough to never skip, consistent enough that revision stops feeling like a last-minute scramble.',
        icon: 'flame',
    },
    {
        title: 'Built by students who show up',
        desc: 'Contributors upload the questions that keep the library alive, and get recognised for it on their school\'s leaderboard.',
        icon: 'users',
    },
]

const steps = [
    {
        n: '01',
        title: 'Find your school',
        desc: 'Search for your institution, programme and level. If it\'s not listed yet, join the waitlist and we\'ll notify you the moment it\'s live.',
    },
    {
        n: '02',
        title: 'Open your question bank',
        desc: 'Browse verified past questions by course and semester, or scan a paper of your own to add it instantly.',
    },
    {
        n: '03',
        title: 'Practice until it sticks',
        desc: 'Run timed practice sessions, keep your daily streak alive, and walk into the exam hall having already seen the pattern.',
    },
]
</script>

<template>
    <Head title="myUniAlly — Home."/>


    <!-- HERO -->
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 -z-10">
            <div
                class="absolute -top-24 left-1/2 h-[32rem] w-[32rem] -translate-x-1/2 rounded-full bg-primary/10 blur-3xl"></div>
        </div>

        <div class="mx-auto grid max-w-6xl items-center gap-12 px-6 pb-20 pt-16 lg:grid-cols-2 lg:pt-24">
            <div>
                    <span
                        class="inline-flex items-center gap-2 rounded-full bg-tertiary/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-tertiary">
                        Built for Nigerian university &amp; college students
                    </span>

                <h1 class="mt-6 text-4xl font-bold leading-tight tracking-tight text-slate-900 sm:text-5xl">
                    Walk into every exam
                    already knowing what's coming.
                </h1>

                <p class="mt-5 max-w-lg text-lg leading-relaxed text-slate-600">
                    myUniAlly organizes your school's real past questions, reads new ones for you with AI, and turns
                    daily
                    revision into a habit that actually sticks, instead of a scramble the night before.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <Link
                        href="/register"
                        class="rounded-full bg-primary px-7 py-3.5 text-base font-semibold text-white shadow-lg shadow-primary/30 transition hover:opacity-90"
                    >
                        Find your school
                    </Link>
                    <a href="#how-it-works" class="text-base font-semibold text-slate-700 hover:text-primary">
                        See how it works →
                    </a>
                </div>

                <p class="mt-6 text-sm text-slate-500">
                    Free to join. No card required.
                </p>
            </div>

            <!-- SIGNATURE ELEMENT: scan → organized demo -->
            <div class="relative">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 shadow-xl shadow-slate-200/60">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                        <span
                            class="text-xs font-semibold uppercase tracking-wide text-slate-400">Scan a past question</span>
                        <span
                            class="flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">
                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none"><path
                                    d="M12 3l2.4 5.4L20 10l-5.4 2.4L12 18l-2.4-5.6L4 10l5.6-2.4L12 3z"
                                    fill="currentColor"/></svg>
                                AI read
                            </span>
                    </div>

                    <!-- toggle demo -->
                    <div class="mt-4 flex gap-2">
                        <button
                            @click="scanStep = 1"
                            :class="scanStep === 1 ? 'bg-primary text-white' : 'bg-white text-slate-500 border border-slate-200'"
                            class="rounded-full px-3 py-1 text-xs font-semibold transition"
                        >
                            Raw scan
                        </button>
                        <button
                            @click="scanStep = 2"
                            :class="scanStep === 2 ? 'bg-primary text-white' : 'bg-white text-slate-500 border border-slate-200'"
                            class="rounded-full px-3 py-1 text-xs font-semibold transition"
                        >
                            Organized by AI
                        </button>
                        <button
                            @click="scanStep = 3"
                            :class="scanStep === 3 ? 'bg-primary text-white' : 'bg-white text-slate-500 border border-slate-200'"
                            class="rounded-full px-3 py-1 text-xs font-semibold transition"
                        >
                            Try it out
                        </button>
                    </div>

                    <div class="mt-4 min-h-[220px] rounded-xl bg-white p-4">
                        <template v-if="scanStep === 1">
                            <div class="space-y-2 font-mono text-[13px] leading-relaxed text-slate-400">
                                <p>MTH 201 — SECOND SEMESTER EXAM</p>
                                <p>1a) Solve the differential equation dy/dx...</p>
                                <p>1b) Given matrix A, find det(A)...</p>
                                <p>2) State and prove the mean value theorem...</p>
                                <p class="text-slate-300">— photo of a crumpled exam paper —</p>
                            </div>
                        </template>
                        <template v-else-if="scanStep === 2">
                            <div class="space-y-3">
                                <div class="rounded-lg border border-slate-100 p-3">
                                    <p class="text-xs font-semibold text-primary">Question 1a</p>
                                    <p class="mt-1 text-sm text-slate-700">Solve the differential equation dy/dx =
                                        2xy.</p>
                                </div>
                                <div class="rounded-lg border border-slate-100 p-3">
                                    <p class="text-xs font-semibold text-primary">Question 1b</p>
                                    <p class="mt-1 text-sm text-slate-700">Given matrix A, find det(A).</p>
                                </div>
                                <div class="rounded-lg border border-slate-100 p-3">
                                    <p class="text-xs font-semibold text-primary">Question 2</p>
                                    <p class="mt-1 text-sm text-slate-700">State and prove the mean value theorem.</p>
                                </div>
                                <p class="pt-1 text-center text-xs font-medium text-slate-400">Added to MTH 201 · 2nd
                                    Semester ✓</p>
                            </div>
                        </template>
                        <template v-else>
                            <ScanCTA
                                title="Try it now, scan a past question"
                                description="Have a paper on hand? Snap it and jump straight into CBT practice, no waiting, no setup."
                                button-text="Scan and practice now"/>
                        </template>
                    </div>
                </div>

                <div
                    class="absolute -bottom-5 -left-5 hidden rounded-xl bg-white px-4 py-3 shadow-lg ring-1 ring-slate-100 sm:block">
                    <p class="text-xs font-medium text-slate-500">Question of the Day</p>
                    <p class="mt-1 flex items-center gap-1.5 text-sm font-semibold text-slate-800">
                        🔥 12-day streak
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section id="features" class="border-t border-slate-100 bg-slate-50/60 py-20">
        <div class="mx-auto max-w-6xl px-6">
            <div class="max-w-2xl">
                <span class="text-sm font-semibold uppercase tracking-wide text-primary">Why students switch to myUniAlly</span>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    Everything you need to stop guessing what's on the exam.
                </h2>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="f in features"
                    :key="f.title"
                    class="rounded-2xl border border-slate-200 bg-white p-6 transition hover:border-primary/30 hover:shadow-lg hover:shadow-primary/5"
                >
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
                        <svg v-if="f.icon === 'stack'" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8">
                            <path d="M12 3l9 5-9 5-9-5 9-5z"/>
                            <path d="M3 13l9 5 9-5"/>
                        </svg>
                        <svg v-else-if="f.icon === 'scan'" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8">
                            <path
                                d="M4 7V5a1 1 0 011-1h2M20 7V5a1 1 0 00-1-1h-2M4 17v2a1 1 0 001 1h2M20 17v2a1 1 0 01-1 1h-2M4 12h16"/>
                        </svg>
                        <svg v-else-if="f.icon === 'clock'" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M12 7v5l3 3"/>
                        </svg>
                        <svg v-else-if="f.icon === 'flame'" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8">
                            <path
                                d="M12 3s4 4 4 8a4 4 0 11-8 0c0-1.2.6-2 1-2.5C9.5 10 10 12 10 12s2-2 2-4-1-3-1-3c1 0 3 1 4 3z"/>
                        </svg>
                        <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.8">
                            <circle cx="9" cy="8" r="3.2"/>
                            <path d="M2.5 19c.6-3.2 3-5 6.5-5s5.9 1.8 6.5 5"/>
                            <circle cx="17.5" cy="9" r="2.4"/>
                            <path d="M15.8 14c2.8.2 4.6 1.9 5.1 4.6"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-slate-900">{{ f.title }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ f.desc }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section id="how-it-works" class="py-20">
        <div class="mx-auto max-w-6xl px-6">
            <div class="max-w-2xl">
                <span class="text-sm font-semibold uppercase tracking-wide text-primary">How it works</span>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    From "which school is this again?" to exam-ready, in three steps.
                </h2>
            </div>

            <div class="mt-12 grid gap-8 md:grid-cols-3">
                <div v-for="s in steps" :key="s.n" class="relative">
                    <span class="text-5xl font-bold text-primary/15">{{ s.n }}</span>
                    <h3 class="mt-2 text-lg font-semibold text-slate-900">{{ s.title }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ s.desc }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTRIBUTORS / SOCIAL PROOF -->
    <section id="contributors" class="border-t border-slate-100 bg-slate-900 py-20 text-white">
        <div class="mx-auto max-w-6xl px-6">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div>
                    <span class="text-sm font-semibold uppercase tracking-wide text-tertiary">A library that keeps growing</span>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">
                        Every question you add helps the next student behind you.
                    </h2>
                    <p class="mt-4 max-w-lg text-slate-300">
                        Contributors are the backbone of myUniAlly. Upload a past question, help your department's
                        library grow, and see your name climb your school's leaderboard.
                    </p>
                    <Link
                        href="/become-contributor"
                        class="mt-6 inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-900 transition hover:opacity-90"
                    >
                        Join as a contributor
                    </Link>
                </div>

                <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">School leaderboard</p>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between rounded-xl bg-white/5 px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-full bg-secondary text-xs font-bold text-white">1</span>
                                <span class="text-sm font-medium">Chidinma A.</span>
                            </div>
                            <span class="text-sm text-slate-400">142 questions</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-white/5 px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-full bg-white/10 text-xs font-bold text-white">2</span>
                                <span class="text-sm font-medium">Tunde O.</span>
                            </div>
                            <span class="text-sm text-slate-400">98 questions</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-white/5 px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-full bg-white/10 text-xs font-bold text-white">3</span>
                                <span class="text-sm font-medium">Blessing E.</span>
                            </div>
                            <span class="text-sm text-slate-400">81 questions</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FINAL CTA -->
    <section class="py-24">
        <div class="mx-auto max-w-3xl px-6 text-center">
            <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                Stop revising blind.
            </h2>
            <p class="mt-4 text-lg text-slate-600">
                Find your school, open your question bank, and start your first streak today, it's free.
            </p>
            <Link
                href="/register"
                class="mt-8 inline-flex rounded-full bg-primary px-8 py-4 text-base font-semibold text-white shadow-lg shadow-primary/30 transition hover:opacity-90"
            >
                Get started free
            </Link>
        </div>
    </section>


    <!-- SCAN DEMO -->
    <section class="border-t border-slate-100 bg-slate-50/60 py-20">
        <div class="mx-auto max-w-3xl px-6 text-center">
            <span class="text-sm font-semibold uppercase tracking-wide text-primary">See it in action</span>
            <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                From crumpled paper to CBT practice in seconds.
            </h2>
            <p class="mt-4 text-base leading-relaxed text-slate-600">
                Flip through what actually happens when you scan a past question, then try it yourself.
            </p>

            <!-- toggle demo -->
            <div class="mt-8 inline-flex gap-1 rounded-full border border-slate-200 bg-white p-1 shadow-sm">
                <button
                    @click="scanStep = 1"
                    :class="scanStep === 1 ? 'bg-primary text-white shadow' : 'text-slate-500 hover:text-slate-700'"
                    class="rounded-full px-4 py-1.5 text-xs font-semibold transition"
                >
                    Raw scan
                </button>
                <button
                    @click="scanStep = 2"
                    :class="scanStep === 2 ? 'bg-primary text-white shadow' : 'text-slate-500 hover:text-slate-700'"
                    class="rounded-full px-4 py-1.5 text-xs font-semibold transition"
                >
                    Organized by AI
                </button>
                <button
                    @click="scanStep = 3"
                    :class="scanStep === 3 ? 'bg-primary text-white shadow' : 'text-slate-500 hover:text-slate-700'"
                    class="rounded-full px-4 py-1.5 text-xs font-semibold transition"
                >
                    Try it out
                </button>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 text-left shadow-xl shadow-slate-200/60 sm:p-8">
                <div class="min-h-[220px]">
                    <template v-if="scanStep === 1">
                        <div class="space-y-2 font-mono text-[13px] leading-relaxed text-slate-400">
                            <p>MTH 201 — SECOND SEMESTER EXAM</p>
                            <p>1a) Solve the differential equation dy/dx...</p>
                            <p>1b) Given matrix A, find det(A)...</p>
                            <p>2) State and prove the mean value theorem...</p>
                            <p class="text-slate-300">— photo of a crumpled exam paper —</p>
                        </div>
                    </template>
                    <template v-else-if="scanStep === 2">
                        <div class="space-y-3">
                            <div class="rounded-lg border border-slate-100 p-3">
                                <p class="text-xs font-semibold text-primary">Question 1a</p>
                                <p class="mt-1 text-sm text-slate-700">Solve the differential equation dy/dx = 2xy.</p>
                            </div>
                            <div class="rounded-lg border border-slate-100 p-3">
                                <p class="text-xs font-semibold text-primary">Question 1b</p>
                                <p class="mt-1 text-sm text-slate-700">Given matrix A, find det(A).</p>
                            </div>
                            <div class="rounded-lg border border-slate-100 p-3">
                                <p class="text-xs font-semibold text-primary">Question 2</p>
                                <p class="mt-1 text-sm text-slate-700">State and prove the mean value theorem.</p>
                            </div>
                            <p class="pt-1 text-center text-xs font-medium text-slate-400">Added to MTH 201 · 2nd Semester ✓</p>
                        </div>
                    </template>
                    <template v-else>
                        <ScanCTA
                            title="Try it now, scan a past question"
                            description="Have a paper on hand? Snap it and jump straight into CBT practice, no waiting, no setup."
                            button-text="Scan and practice now"
                        />
                    </template>
                </div>
            </div>
        </div>
    </section>

</template>
