<script setup>
defineProps({
    step: [Number, String],
    label: String,
})
</script>

<template>
    <label class="flex items-center gap-2 text-sm font-medium text-gray-600">
        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-primary/10 text-primary text-[11px] font-semibold">
            {{ step }}
        </span>
        {{ label }}
    </label>
</template>
