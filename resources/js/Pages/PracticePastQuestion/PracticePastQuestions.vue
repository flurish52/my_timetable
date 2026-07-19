<template>
        <Head :title="`${past_question.course.code} ${past_question.semester.name}
        semester ${past_question.session}   - Index`" />
        <div class="min-h-screen bg-[#f8f9fb] font-sans text-[#1a1f2e] pb-20">
            <!-- Top Nav Bar -->
            <header class="flex items-center justify-between px-12 py-5 bg-white border-b border-[#e8eaf0]">
                <Link href="/" class="inline-flex items-center gap-2 text-primary text-sm font-medium px-4 py-2 rounded-lg border border-[#e0ecf5] bg-[#f4f9fd] transition-all duration-200 hover:bg-[#e5f2fb] hover:border-primary no-underline">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5M12 5l-7 7 7 7"/>
                    </svg>
                    <span>Back</span>
                </Link>
                <div class="inline-flex items-center gap-2 text-xs font-medium text-[#5a6070] bg-[#f1f3f7] rounded-full px-4 py-1.5">
                    <span class="w-2 h-2 rounded-full bg-secondary inline-block"></span>
                    <span>{{ past_question.school.acronym }}</span>
                </div>
            </header>

            <!-- Main -->
            <main
                class="max-w-[1200px] mx-auto px-4 md:px-12 pt-14 flex flex-col-reverse md:grid gap-12"
                style="grid-template-columns: 1fr 380px; align-items: start;"
            >

                <!-- Left -->
                <div>
                    <!-- Chips -->
                    <div class="flex flex-wrap gap-2 mb-5 anim-up"
                         style="--delay:0s">
                        <span class="text-[0.72rem] font-semibold tracking-wider uppercase rounded-md px-3 py-1 bg-[#e0f0fa] text-primary">{{ past_question.course.code }}</span>
                        <span class="text-[0.72rem] font-semibold tracking-wider uppercase rounded-md px-3 py-1 bg-[#fde9d6] text-[#c95200]">{{ capitalize(past_question.semester.name) }} Semester</span>
                        <span class="text-[0.72rem] font-semibold tracking-wider uppercase rounded-md px-3 py-1 bg-[#e8f8f2] text-[#0a7a54]">{{ past_question.session }}</span>
                    </div>

                    <h1 class="exam-title text-[2.4rem] font-bold leading-tight text-[#0f1422] mb-3 anim-up" style="--delay:0.05s">
                        {{ past_question.title }}
                    </h1>
                    <p class="text-[0.95rem] text-[#6b7280] mb-7 anim-up" style="--delay:0.1s">{{ past_question.description }}</p>

                    <!-- Instructions -->
                    <div class="bg-white border border-[#e4e9f0] rounded-xl p-5 mb-9 anim-up" style="--delay:0.15s; border-left: 4px solid #01629c;">
                        <div class="flex items-center gap-1.5 text-[0.72rem] font-bold tracking-widest uppercase text-primary mb-2">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            Instructions
                        </div>
                        <p class="text-[0.9rem] text-[#374151] leading-relaxed m-0">{{ past_question.instructions }}</p>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-4 gap-3 mb-10 anim-up" style="--delay:0.2s">
                        <div class="bg-white border border-[#e8eaf0] rounded-xl p-4 flex items-center gap-3 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-[#e0f0fa] text-primary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <span class="stat-value text-[1.15rem] font-bold text-[#0f1422] leading-none">{{ past_question.duration_minutes }} min</span>
                                <span class="text-[0.7rem] font-medium text-[#9ca3af] uppercase tracking-wider">Duration</span>
                            </div>
                        </div>
                        <div class="bg-white border border-[#e8eaf0] rounded-xl p-4 flex items-center gap-3 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-[#fde9d6] text-tertiary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                </svg>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <span class="stat-value text-[1.15rem] font-bold text-[#0f1422] leading-none">{{ past_question.questions.length }}</span>
                                <span class="text-[0.7rem] font-medium text-[#9ca3af] uppercase tracking-wider">Questions</span>
                            </div>
                        </div>
                        <div class="bg-white border border-[#e8eaf0] rounded-xl p-4 flex items-center gap-3 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-[#e8f8f2] text-secondary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
                                </svg>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <span class="stat-value text-[1.15rem] font-bold text-[#0f1422] leading-none">{{ past_question.sections.length }}</span>
                                <span class="text-[0.7rem] font-medium text-[#9ca3af] uppercase tracking-wider">Sections</span>
                            </div>
                        </div>
                        <div class="bg-white border border-[#e8eaf0] rounded-xl p-4 flex items-center gap-3 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-[#e0f0fa] text-primary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                </svg>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <span class="stat-value text-[1.15rem] font-bold text-[#0f1422] leading-none">{{ totalMarks }}</span>
                                <span class="text-[0.7rem] font-medium text-[#9ca3af] uppercase tracking-wider">Total Marks</span>
                            </div>
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="flex flex-col md:flex-row items-center gap-4 anim-up" style="--delay:0.25s">

                        <a
                            v-if="past_question.source_file"
                            :href="`/storage/${past_question.source_file}`"
                            :download="past_question.title"
                            class="inline-flex w-full items-center gap-2.5 bg-white border-2 border-primary text-primary text-[0.95rem] font-semibold px-8 py-3.5 rounded-xl no-underline transition-all duration-200 hover:bg-[#014e7e] hover:-translate-y-0.5 active:translate-y-0"
                        >
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            Download
                        </a>
                        <Link
                            :href="`/practice/${past_question.id}/start`"
                            class="inline-flex w-full items-center gap-2.5 bg-primary text-white text-[0.95rem] font-semibold px-8 py-3.5 rounded-xl no-underline transition-all duration-200 hover:bg-[#014e7e] hover:-translate-y-0.5 active:translate-y-0"
                            style="box-shadow: 0 4px 14px rgba(1,98,156,0.3);"
                        >
                            <span>Begin Practice</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="5 3 19 12 5 21 5 3"/>
                            </svg>
                        </Link>
                    </div>
                </div>
            </main>

        </div>
</template>

<script setup>
import GuestLayout from "@/Layouts/AppLayout.vue";
import {Head, Link} from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    past_question: Object,
});

const capitalize = (str) => str ? str.charAt(0).toUpperCase() + str.slice(1) : "";

const totalMarks = computed(() =>
    props.past_question.questions.reduce(
        (sum, q) => sum + Number(q.marks || 0),
        0
    )
);

const questionsInSection = (sectionId) =>
    props.past_question.questions.filter((q) => q.question_section_id === sectionId).length;
</script>

<style scoped>

.font-sans { font-family: 'DM Sans', sans-serif; }

.exam-title { font-family: 'Fraunces', serif; }
.stat-value  { font-family: 'Fraunces', serif; }

/* Staggered fade-up animation */
.anim-up {
    animation: fadeUp 0.45s var(--delay, 0s) ease both;
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 900px) {
    main { grid-template-columns: 1fr !important; padding: 32px 24px 0 !important; }
    header { padding-left: 24px !important; padding-right: 24px !important; }
    aside { order: -1; }
}
@media (max-width: 640px) {
    .grid-cols-4 { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (max-width: 480px) {
    h1 { font-size: 1.75rem !important; }
}
</style>
