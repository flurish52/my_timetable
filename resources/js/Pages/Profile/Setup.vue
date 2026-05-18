<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import GuestLayout from "@/Layouts/GuestLayout.vue";

const props = defineProps({
    user: Object,
    schools: Array,
    programmeTypes: Array,
    levels: Array,
})

// Derive school_id — find which school owns the user's existing department
const existingDepartmentId = props.user?.programme?.department_id || ''
const existingProgrammeTypeId = props.user?.programme?.programme_type_id || ''

const existingSchool = props.schools?.find(s =>
    s.departments?.some(d => d.id === Number(existingDepartmentId))
)

const form = useForm({
    school_id:        existingSchool?.id || '',
    department_id:    existingDepartmentId,
    programme_type_id: existingProgrammeTypeId,
    level_id:         props.user?.level_id || '',
})

const isUpdate = computed(() => !!props.user?.programme_id)

const filteredDepartments = computed(() => {
    if (!form.school_id) return []
    const school = props.schools.find(s => s.id === Number(form.school_id))
    return school?.departments ?? []
})

const onSchoolChange = () => {
    form.department_id = ''
    form.programme_type_id = ''
    form.level_id = ''
}

const onDepartmentChange = () => {
    form.programme_type_id = ''
    form.level_id = ''
}

const onProgrammeTypeChange = () => {
    form.level_id = ''
}

const submit = () => {
    form.put('/setup')
}
</script>

<template>
    <GuestLayout>
        <div class="flex items-center justify-center bg-gray-50 p-4">
            <div class="w-full max-w-xl bg-white rounded-xl shadow-md p-4 border border-gray-100">

                <h1 class="text-2xl font-semibold text-primary mb-1">
                    {{ isUpdate ? 'Update Your Profile' : 'Complete Your Profile' }}
                </h1>
                <p class="text-gray-500 mb-6">
                    {{ isUpdate ? 'Update your school details below' : 'Set your school details to continue' }}
                </p>

                <form @submit.prevent="submit" class="space-y-5">

                    <!-- School -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">School</label>
                        <select
                            v-model="form.school_id"
                            @change="onSchoolChange"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                            <option value="">Select your school</option>
                            <option v-for="s in schools" :key="s.id" :value="s.id">
                                {{ s.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Department — disabled until school picked -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Department</label>
                        <select
                            v-model="form.department_id"
                            :disabled="!form.school_id"
                            @change="onDepartmentChange"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary disabled:bg-gray-100 disabled:cursor-not-allowed"
                        >
                            <option value="">Select your department</option>
                            <option v-for="d in filteredDepartments" :key="d.id" :value="d.id">
                                {{ d.name }}
                            </option>
                        </select>
                        <p v-if="!form.school_id" class="text-xs text-gray-400 mt-1">
                            Select a school first
                        </p>
                    </div>

                    <!-- Programme Type — disabled until department picked -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Programme Type</label>
                        <select
                            v-model="form.programme_type_id"
                            :disabled="!form.department_id"
                            @change="onProgrammeTypeChange"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary disabled:bg-gray-100 disabled:cursor-not-allowed"
                        >
                            <option value="">Select programme type</option>
                            <option v-for="p in programmeTypes" :key="p.id" :value="p.id">
                                {{ p.name }}
                            </option>
                        </select>
                        <p v-if="!form.department_id" class="text-xs text-gray-400 mt-1">
                            Select a department first
                        </p>
                        <!-- Preview what programme will be created/used -->
                        <p v-if="form.department_id && form.programme_type_id" class="text-xs text-primary mt-1 font-medium">
                            Programme: {{
                                filteredDepartments.find(d => d.id === Number(form.department_id))?.name
                            }} ({{
                                programmeTypes.find(p => p.id === Number(form.programme_type_id))?.name
                            }})
                        </p>
                    </div>

                    <!-- Level -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Level</label>
                        <select
                            v-model="form.level_id"
                            :disabled="!form.programme_type_id"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary disabled:bg-gray-100 disabled:cursor-not-allowed"
                        >
                            <option value="">Select your level</option>
                            <option v-for="l in levels" :key="l.id" :value="l.id">
                                {{ l.name }}
                            </option>
                        </select>
                        <p v-if="!form.programme_type_id" class="text-xs text-gray-400 mt-1">
                            Select a programme type first
                        </p>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing || !form.department_id || !form.programme_type_id || !form.level_id"
                        class="w-full bg-primary text-white py-2 rounded-lg hover:bg-opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ isUpdate ? 'Update Profile' : 'Save Profile' }}
                    </button>

                </form>
            </div>
        </div>
    </GuestLayout>
</template>
