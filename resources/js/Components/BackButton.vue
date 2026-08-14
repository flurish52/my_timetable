<script setup>
const goBack = () => {
    window.history.back()
}
</script>

<template>
    <button
        type="button"
        @click="goBack"
        class="inline-flex items-center gap-2 text-sm font-medium text-tertiary/60 transition hover:text-primary"
    >
        <svg
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="M19 12H5" />
            <path d="m12 19-7-7 7-7" />
        </svg>

        Back
    </button>
</template>
