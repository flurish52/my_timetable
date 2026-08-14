<template>
    <Link
        :href="href"
        class="block rounded-lg border-2 border-dashed p-3 no-underline transition hover:opacity-90"
        style="border-color:#1d9e75;background:#e1f5ee"
    >
        <div class="mb-1 flex items-center gap-2">
            <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#085041"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z" />
                <circle cx="12" cy="13" r="3.5" />
            </svg>
            <span class="text-sm font-bold" style="color:#085041">{{ title }}</span>
        </div>

        <p class="mb-2 text-xs" style="color:#0f6e56">
            {{ description }}
        </p>

        <span
            class="block w-full rounded-lg border bg-white py-2 text-center text-xs font-bold"
            style="color:#0f6e56;border-color:#5dcaa5"
        >
      {{ buttonText }}
    </span>
    </Link>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    title: {
        type: String,
        default: "Can't wait? Scan a past question",
    },
    description: {
        type: String,
        default: "Have a paper on hand? Start practicing in CBT mode right now.",
    },
    buttonText: {
        type: String,
        default: "Scan and practice now",
    },
    courseId: {
        type: [String, Number],
        default: null,
    },
})

const href = computed(() =>
    props.courseId ? `/scan/${props.courseId}` : '/scan'
)
</script>
