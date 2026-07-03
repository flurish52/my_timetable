<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import GuestLayout from "@/Layouts/AppLayout.vue"
import ProgressSteps from "@/Components/School/ProgressSteps.vue"
import RankStatus from "@/Components/School/RankStatus.vue"
import ShareInvite from "@/Components/School/ShareInvite.vue"

const props = defineProps({
    school: Object,       // { id, name, slug }
    joined: Boolean,
    position: [Number, String],
    schoolRank: [Number, String],
    shareUrl: String,
})

const form = useForm({})
const join = () => form.post(`/schools/${props.school.slug}/waitlist`)
</script>

<template>
    <GuestLayout>
        <Head :title="`${school.name} — Waitlist`" />

        <div class="flex items-center justify-center min-h-screen bg-gray-50 p-4">
            <div class="w-full max-w-lg bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

                <RankStatus
                    :school-name="school.name"
                    :joined="joined"
                    :position="position"
                    :school-rank="schoolRank"
                />

                <div class="border-t border-gray-100 my-6" />

                <ProgressSteps />

                <div class="mt-6">
                    <button
                        v-if="!joined"
                        @click="join"
                        :disabled="form.processing"
                        class="w-full py-2.5 bg-primary text-white text-sm font-medium rounded-lg transition-all duration-150 hover:bg-primary/90 active:scale-[0.99] disabled:opacity-40"
                    >
                        Join Waitlist
                    </button>

                    <div v-else class="w-full flex items-center justify-center gap-2 py-2.5 bg-primary/10 text-primary text-sm font-medium rounded-lg">
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M3 7.5l3 3 6-6.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        You're on the list
                    </div>
                </div>

                <ShareInvite v-if="joined" :share-url="shareUrl" :school-name="school.name" />

                <div class="text-center mt-6">
                    <a href="/leaderboard" class="text-xs text-gray-400 hover:text-primary transition-colors">
                        See full rankings →
                    </a>
                </div>

            </div>
        </div>
    </GuestLayout>
</template>
