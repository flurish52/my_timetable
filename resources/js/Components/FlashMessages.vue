<script setup>
import {computed} from 'vue'
import {usePage} from '@inertiajs/vue3'

// No props needed — it reads flash straight from the shared Inertia page props.
const page = usePage()
const flash = computed(() => page.props.flash ?? {})
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-150"
        leave-active-class="transition-all duration-100"
        enter-from-class="opacity-0 -translate-y-1"
        leave-to-class="opacity-0 -translate-y-1"
    >
        <div v-if="flash.success"
             className="mb-4 rounded-lg border border-secondary/20 bg-secondary/10 px-4 py-3 text-sm font-medium text-secondary">
            {{ flash.success }}
        </div>
    </Transition>
    <Transition
        enter-active-class="transition-all duration-150"
        leave-active-class="transition-all duration-100"
        enter-from-class="opacity-0 -translate-y-1"
        leave-to-class="opacity-0 -translate-y-1"
    >
        <div v-if="flash.error"
             className="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            {{ flash.error }}
        </div>
    </Transition>
</template>
