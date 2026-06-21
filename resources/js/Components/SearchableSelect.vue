<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
    modelValue: { type: [String, Number], default: '' },
    options:     { type: Array, default: () => [] },
    placeholder: { type: String, default: 'Select an option' },
    disabled:    { type: Boolean, default: false },
    hint:        { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue', 'change'])

const open         = ref(false)
const search       = ref('')
const containerRef = ref(null)
const searchRef    = ref(null)

const selected = computed(() =>
    props.options.find(o => o.id === props.modelValue || o.id === Number(props.modelValue))
)

const filtered = computed(() => {
    const q = search.value.trim().toLowerCase()
    if (!q) return props.options
    return props.options.filter(o => o.name.toLowerCase().includes(q))
})

const toggle = () => {
    if (props.disabled) return
    open.value = !open.value
    if (open.value) {
        search.value = ''
        setTimeout(() => searchRef.value?.focus(), 50)
    }
}

const select = (opt) => {
    emit('update:modelValue', opt.id)
    emit('change', opt.id)
    open.value = false
    search.value = ''
}

const clear = (e) => {
    e.stopPropagation()
    emit('update:modelValue', '')
    emit('change', '')
    open.value = false
}

const isActive = (opt) =>
    opt.id === props.modelValue || opt.id === Number(props.modelValue)

const handleOutside = (e) => {
    if (containerRef.value && !containerRef.value.contains(e.target))
        open.value = false
}

onMounted(() => document.addEventListener('mousedown', handleOutside))
onBeforeUnmount(() => document.removeEventListener('mousedown', handleOutside))
</script>

<template>
    <div ref="containerRef" class="relative w-full">

        <!-- Trigger -->
        <button
            type="button"
            :disabled="disabled"
            @click="toggle"
            :aria-expanded="open"
            class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 bg-white border rounded-lg text-sm text-left transition-all duration-150"
            :class="[
                disabled
                    ? 'border-gray-200 bg-gray-50 cursor-not-allowed opacity-60'
                    : open
                        ? 'border-primary ring-2 ring-primary/10 cursor-pointer'
                        : 'border-gray-300 hover:border-primary/60 cursor-pointer'
            ]"
        >
            <span
                class="flex-1 truncate"
                :class="selected ? 'text-gray-800 font-medium' : 'text-gray-400'"
            >
                {{ selected ? selected.name : placeholder }}
            </span>

            <span class="flex items-center gap-1 shrink-0">
                <!-- Clear -->
                <span
                    v-if="selected && !disabled"
                    @click="clear"
                    aria-label="Clear"
                    class="flex items-center justify-center w-5 h-5 rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
                >
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <path d="M2 2l8 8M10 2l-8 8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </span>
                <!-- Chevron -->
                <span
                    class="flex text-gray-400 transition-transform duration-200"
                    :class="open ? 'rotate-180' : ''"
                >
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path d="M3 5l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </span>
        </button>

        <!-- Dropdown -->
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-1"
        >
            <div
                v-if="open"
                class="absolute z-50 top-[calc(100%+6px)] left-0 right-0 bg-white border border-gray-200 rounded-xl shadow-lg shadow-black/5 overflow-hidden"
            >
                <!-- Search -->
                <div class="flex items-center gap-2 px-3 py-2.5 border-b border-gray-100">
                    <svg class="shrink-0 text-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <circle cx="6" cy="6" r="4" stroke="currentColor" stroke-width="1.4"/>
                        <path d="M9.5 9.5l2.5 2.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                    </svg>
                    <input
                        ref="searchRef"
                        v-model="search"
                        type="text"
                        placeholder="Search..."
                        class="flex-1 text-sm text-gray-700 bg-transparent outline-none placeholder-gray-300"
                    />
                </div>

                <!-- List -->
                <ul role="listbox" class="max-h-56 overflow-y-auto p-1.5">
                    <li
                        v-if="filtered.length === 0"
                        class="px-3 py-4 text-center text-sm text-gray-400"
                    >
                        No results found
                    </li>
                    <li
                        v-for="opt in filtered"
                        :key="opt.id"
                        role="option"
                        @click="select(opt)"
                        class="flex items-center justify-between px-3 py-2.5 rounded-lg cursor-pointer text-sm transition-colors duration-100"
                        :class="isActive(opt)
                            ? 'bg-primary/10 text-primary font-medium'
                            : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'"
                    >
                        <span class="truncate">{{ opt.name }}</span>
                        <svg
                            v-if="isActive(opt)"
                            class="shrink-0 ml-2 text-primary"
                            width="14" height="14" viewBox="0 0 14 14" fill="none"
                        >
                            <path d="M2 7l4 4 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </li>
                </ul>
            </div>
        </Transition>

        <!-- Hint -->
        <p v-if="hint && !open" class="mt-1 text-xs text-gray-400">{{ hint }}</p>
    </div>
</template>
