<template>
    <RouterLink :to="route" class="absolute top-10 right-0 z-40 bg-white text-secondary w-fit flex items-center justify-end  gap-2 px-3 mr-4 text-sm font-medium text-gray-700 hover:text-primary hover:bg-gray-100 rounded-md transition">
        <span class="text-lg">←</span>
        Back
    </RouterLink>
</template>

<script setup>
import {RouterLink} from "vue-router";

defineProps({
    route:{
        type: String,
        required: true,
        default: '/',
    }
})
</script>

<style scoped>

</style>
