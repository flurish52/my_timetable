<script setup>
defineProps({
    modelValue: {type: String, default: ''},
    placeholder: {type: String, default: 'Search…'},
})

defineEmits(['update:modelValue'])
</script>

<template>
    <div class="relative mb-4">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-primary/30" viewBox="0 0 20 20"
             fill="none" stroke="currentColor" stroke-width="1.6">
            <circle cx="9" cy="9" r="6"/>
            <path d="M17 17l-4-4" stroke-linecap="round"/>
        </svg>
        <input
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            type="text"
            :placeholder="placeholder"
            class="w-full rounded-lg border border-primary/20 pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
        />
    </div>
</template>
