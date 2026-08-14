<template>
  <span :class="['inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold capitalize', styles]">
    <svg v-if="status === 'private'" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>
    </svg>
    <svg v-else width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>
    </svg>
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({ status: String })

const map = {
    private: { label: 'Private', styles: 'bg-primary/10 text-primary' },
    public:  { label: 'Public',  styles: 'bg-secondary/15 text-secondary' },
}

const entry = computed(() => map[props.status] ?? { label: props.status ?? 'Unknown', styles: 'bg-primary/10 text-primary' })
const label = computed(() => entry.value.label)
const styles = computed(() => entry.value.styles)
</script>
