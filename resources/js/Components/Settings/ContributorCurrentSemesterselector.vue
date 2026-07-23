<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
    programme: { id: number; name: string }
    levels: { id: number; name: string }[]
    level: { id: number; name: string }
    semesters: { id: number; name: string }[]
    currentSemesterId: number | null
}>()

const selectedLevel = ref<number | null>(props.level?.id ?? null)
const selectedSemester = ref<number | null>(props.currentSemesterId)
const saving = ref(false)

function save() {
    if (!selectedLevel.value || !selectedSemester.value) return
    saving.value = true
    router.put(route('settings.contributor.update-current-semester'), {
        level_id: selectedLevel.value,
        semester_id: selectedSemester.value,
    }, {
        preserveScroll: true,
        onFinish: () => (saving.value = false),
    })
}
</script>

<template>
    <div class="rounded-xl border border-gray-200 bg-white p-5 max-w-sm">
        <h3 class="text-base font-semibold text-gray-900">Current level & semester</h3>
        <p class="mt-1 text-sm text-gray-500">{{ programme.name }}</p>

        <div class="mt-4 space-y-3">
            <select
                v-model="selectedLevel"
                class="w-full rounded-md border-gray-300 text-sm focus:border-[#01629c] focus:ring-[#01629c]"
            >
                <option :value="null" disabled>Select level</option>
                <option v-for="l in levels" :key="l.id" :value="l.id">{{ l.name }}</option>
            </select>

            <select
                v-model="selectedSemester"
                class="w-full rounded-md border-gray-300 text-sm focus:border-[#01629c] focus:ring-[#01629c]"
            >
                <option :value="null" disabled>Select semester</option>
                <option v-for="s in semesters" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>

            <button
                type="button"
                :disabled="saving || !selectedLevel || !selectedSemester"
                @click="save"
                class="w-full rounded-md bg-[#01629c] px-4 py-2 text-sm font-medium text-white disabled:opacity-50"
            >
                {{ saving ? 'Saving…' : 'Save' }}
            </button>
        </div>
    </div>
</template>
