<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
    programmes: { id: number; name: string }[]
    levels: { id: number; name: string }[]
    semesters: { id: number; name: string }[]
    assignments: { programme_id: number; level_id: number; semester_id: number }[]
}>()

const programmeId = ref<number | null>(null)
const levelId = ref<number | null>(null)
const semesterId = ref<number | null>(null)
const saving = ref(false)

function save() {
    if (!programmeId.value || !levelId.value || !semesterId.value) return
    saving.value = true
    router.put(route('admin.settings.update-current-semester'), {
        programme_id: programmeId.value,
        level_id: levelId.value,
        semester_id: semesterId.value,
    }, {
        preserveScroll: true,
        onFinish: () => (saving.value = false),
    })
}
</script>

<template>
    <div class="rounded-xl border border-gray-200 bg-white p-5 max-w-lg">
        <h3 class="text-base font-semibold text-gray-900">Assign current semester</h3>
        <p class="mt-1 text-sm text-gray-500">Set the active semester for any programme and level.</p>

        <div class="mt-4 grid grid-cols-3 gap-3">
            <select v-model="programmeId" class="rounded-md border-gray-300 text-sm">
                <option :value="null" disabled>Programme</option>
                <option v-for="p in programmes" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>

            <select v-model="levelId" class="rounded-md border-gray-300 text-sm">
                <option :value="null" disabled>Level</option>
                <option v-for="l in levels" :key="l.id" :value="l.id">{{ l.name }}</option>
            </select>

            <select v-model="semesterId" class="rounded-md border-gray-300 text-sm">
                <option :value="null" disabled>Semester</option>
                <option v-for="s in semesters" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
        </div>

        <button
            type="button"
            :disabled="saving || !programmeId || !levelId || !semesterId"
            @click="save"
            class="mt-4 w-full rounded-md bg-[#01629c] px-4 py-2 text-sm font-medium text-white disabled:opacity-50"
        >
            {{ saving ? 'Saving…' : 'Save' }}
        </button>

        <div v-if="assignments.length" class="mt-5 border-t border-gray-100 pt-4 space-y-1">
            <p
                v-for="a in assignments"
                :key="`${a.programme_id}-${a.level_id}`"
                class="text-sm text-gray-600"
            >
                {{ a?.programme?.name }} — {{ a?.level?.name }}: <span class="font-medium">{{ a.semester?.name }}</span>
            </p>
        </div>
    </div>
</template>
