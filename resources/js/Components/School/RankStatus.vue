<script setup>
defineProps({
    schoolName: String,
    joined: Boolean,
    position: [Number, String],
    schoolRank: [Number, String],
})
</script>

<template>
    <div class="text-center py-6">
        <template v-if="joined">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-primary/10 text-primary mb-3">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
                    <path d="M4.5 11.5l4 4 9-9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="text-2xl font-semibold text-primary leading-tight">
                You're #{{ position }} from {{ schoolName }}
            </p>
            <p class="text-sm text-gray-400 mt-1">
                {{ schoolName }} is currently #{{ schoolRank }} most requested
            </p>
        </template>

        <template v-else>
            <p class="text-lg font-semibold text-gray-900">
                MyUniAlly is coming to {{ schoolName }}
            </p>
            <p class="text-sm text-gray-400 mt-1">
                {{ schoolName }} is currently #{{ schoolRank }} most requested
            </p>
        </template>
    </div>
</template>
