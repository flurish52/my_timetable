<script setup>
defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    tone: { type: String, default: 'primary' }, // primary | secondary | tertiary
})

const toneText = {
    primary: 'text-primary',
    secondary: 'text-secondary',
    tertiary: 'text-tertiary',
}
</script>

<template>
    <div class="rounded-xl border border-primary/10 bg-white p-5">
        <p class="text-xs font-medium text-primary/50 uppercase tracking-wide">{{ label }}</p>
        <p class="mt-2 text-2xl font-bold" :class="toneText[tone]">{{ value }}</p>
    </div>
</template>
