<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    pastQuestion: { type: Object, required: true },
})

const processing = ref(false)

const isPublished = computed(() => props.pastQuestion.status === 'published')

function toggle() {
    processing.value = true
    router.patch(route('past-questions.publish', props.pastQuestion.id), {}, {
        preserveScroll: true,
        onFinish: () => {
            processing.value = false
        },
    })
}
</script>

<template>
    <div class="flex flex-col items-center justify-between rounded-lg py-3">
        <div class="min-w-0">
            <p class="text-sm font-medium text-neutral-800">{{ isPublished ? 'Published' : 'Draft' }}</p>
            <p class="text-xs text-primary/50 hidden md:flex">{{ isPublished ? 'Visible to students.' : 'Only visible to you until published.' }}</p>
        </div>
        <button
            type="button"
            role="switch"
            :aria-checked="isPublished"
            :disabled="processing"
            @click="toggle"
            class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 disabled:opacity-60"
            :class="isPublished ? 'bg-secondary' : 'bg-neutral-300'"
        >
            <span
                class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                :class="isPublished ? 'translate-x-6' : 'translate-x-1'"
            />
        </button>
    </div>
</template>
