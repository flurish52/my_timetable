<script setup>
import ContributorCurrentSemesterselector from "@/Components/Settings/ContributorCurrentSemesterselector.vue";
import AdminCurrentSemesterSelector from "@/Components/Settings/AdminCurrentSemesterSelector.vue";

defineProps({
    role: String, // 'admin' | 'contributor'
    // admin props
    programmes: {type: Array, default: () => []},
    levels: {type: Array, default: () => []},
    assignments: {type: Array, default: () => []},
    // contributor props
    programme: {type: Object, default: null},
    level: {type: Object, default: null},
    currentSemesterId: {type: Number, default: null},
    // shared
    semesters: {type: Array, default: () => []},
})
</script>

<template>
    <div class="mx-auto max-w-2xl p-6">
        <h1 class="text-xl font-semibold text-gray-900 mb-6">Settings</h1>

        <AdminCurrentSemesterSelector
            v-if="role === 'admin'"
            :programmes="programmes"
            :levels="levels"
            :semesters="semesters"
            :assignments="assignments"
        />

        <ContributorCurrentSemesterselector
            v-else
            :programme="programme"
            :level="level"
            :semesters="semesters"
            :current-semester-id="currentSemesterId"
            :levels="levels"/>
    </div>
</template>
