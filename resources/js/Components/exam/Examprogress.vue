<template>
    <div class="h-[3px] bg-gray-200 flex-shrink-0">
        <div
            class="h-full bg-primary transition-all duration-500 ease-out"
            :style="{ width: percent + '%' }"
        />
    </div>
</template>

<script setup>
defineProps({
    percent: {
        type: Number,
        default: 0,
    },
})
</script>
