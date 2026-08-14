<script setup>
import ScanCTA from "@/Components/ScanCTA.vue";
import {router} from "@inertiajs/vue3";

defineProps({
    steps: {
        type: Array,
        default: () => [
            { label: 'School Added', status: 'done' },
            { label: 'Building Content', status: 'active' },
            { label: 'Launch', status: 'pending' },
        ],
    },
})
</script>

<template>
    <div class="space-y-3">
        <div
            v-for="(step, i) in steps"
            :key="i"
            class="flex items-center justify-between py-2.5 px-3.5 rounded-lg"
            :class="{
                'bg-primary/5': step.status === 'done',
                'bg-amber-50': step.status === 'active',
                'bg-gray-50': step.status === 'pending',
            }"
        >
            <span
                class="text-sm font-medium"
                :class="{
                    'text-primary': step.status === 'done',
                    'text-amber-700': step.status === 'active',
                    'text-gray-400': step.status === 'pending',
                }"
            >
                {{ step.label }}
            </span>

            <svg v-if="step.status === 'done'" width="16" height="16" viewBox="0 0 16 16" fill="none" class="text-primary shrink-0">
                <path d="M3.5 8.5l3 3 6-6.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span v-else-if="step.status === 'active'" class="text-xs font-medium text-amber-600 shrink-0">In Progress</span>
            <span v-else class="text-xs font-medium text-gray-400 shrink-0">Coming Soon</span>
        </div>
    </div>
</template>
