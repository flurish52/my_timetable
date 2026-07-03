<script setup>
defineProps({
    schoolName: String,
})
</script>

<template>
    <div class="flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-100 rounded-xl">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="text-amber-500 shrink-0 mt-0.5">
            <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.4"/>
            <path d="M8 5.5v3.5M8 11.5h.007" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <p class="text-xs text-amber-700 leading-relaxed">
            <span class="font-medium">{{ schoolName || 'This school' }}</span>
            hasn't been fully set up yet — no departments are available. Join the waitlist and we'll notify you as soon as it's ready.
        </p>
    </div>
</template>
