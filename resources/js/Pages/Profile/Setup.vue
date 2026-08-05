<script setup>
import {computed} from 'vue'
import {Head, useForm} from '@inertiajs/vue3'
import GuestLayout from "@/Layouts/AppLayout.vue"
import SearchableSelect from "@/Components/SearchableSelect.vue"
import StepLabel from "@/Components/Setup/StepLabel.vue"
import WaitlistNotice from "@/Components/Setup/WaitlistNotice.vue"
import LegalAgreementNotice from "@/Components/LegalAgreementNotice.vue";

const props = defineProps({
    user: Object,
    schools: Array,
    programmeTypes: Array,
    levels: Array,
})

const existingDepartmentId = props.user?.programme?.department_id || ''
const existingProgrammeTypeId = props.user?.programme?.programme_type_id || ''

const existingSchool = props.schools?.find(s =>
    s.departments?.some(d => d.id === Number(existingDepartmentId))
)

const form = useForm({
    school_id: existingSchool?.id || '',
    department_id: existingDepartmentId,
    programme_type_id: existingProgrammeTypeId,
    level_id: props.user?.level_id || '',
})

const isUpdate = computed(() => !!props.user?.programme_id)

const currentSchool = computed(() =>
    props.schools.find(s => s.id === Number(form.school_id))
)

const filteredDepartments = computed(() => {
    if (!form.school_id) return []
    return currentSchool.value?.departments ?? []
})

// A school is "on waitlist" once picked if it has no departments set up yet
const isWaitlist = computed(() => !!form.school_id && filteredDepartments.value.length === 0)

const programmeName = computed(() => {
    if (!form.department_id || !form.programme_type_id) return ''
    const dept = filteredDepartments.value.find(d => d.id === Number(form.department_id))
    const type = props.programmeTypes.find(p => p.id === Number(form.programme_type_id))
    return dept && type ? `${dept.name} (${type.name})` : ''
})

const levelStep = computed(() => isWaitlist.value ? 2 : 4)

const levelDisabled = computed(() =>
    isWaitlist.value ? !form.school_id : !form.programme_type_id
)

const levelHint = computed(() => {
    if (isWaitlist.value) return !form.school_id ? 'Select a school first' : ''
    return !form.programme_type_id ? 'Select a programme type first' : ''
})

const canSubmit = computed(() => {
    if (isWaitlist.value) return !!form.level_id
    return !!form.department_id && !!form.programme_type_id && !!form.level_id
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
    if (isWaitlist.value) {
        form.post(`/schools/${currentSchool.value.acronym}/waitlist`)
    } else {
        form.put('/setup')
    }
}
</script>

<template>
    <Head :title="isWaitlist ? 'Join Waitlist' : 'Setup'"/>

    <div class="flex items-center justify-center min-h-screen bg-gray-50 p-4">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

            <!-- Header -->
            <div class="flex items-center gap-4 mb-7">
                <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-primary/10 text-primary shrink-0">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="6.5" r="3" stroke="currentColor" stroke-width="1.6"/>
                        <path d="M3 17.5c0-3.866 3.134-7 7-7s7 3.134 7 7" stroke="currentColor" stroke-width="1.6"
                              stroke-linecap="round"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-semibold text-gray-900 leading-tight">
                        {{
                            isWaitlist ? 'Join the waitlist' : (isUpdate ? 'Update your profile' : 'Complete your profile')
                        }}
                    </h1>
                    <p class="text-sm text-gray-400 mt-0.5">
                        {{
                            isWaitlist ? 'Your school is being set up' : (isUpdate ? 'Edit your school details below' : 'Set your school details to get started')
                        }}
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-100 mb-7"/>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-5">

                <!-- School -->
                <div class="space-y-1.5">
                    <StepLabel :step="1" label="School"/>
                    <SearchableSelect
                        v-model="form.school_id"
                        :options="schools"
                        placeholder="Select your school"
                        @change="onSchoolChange"
                    />
                    <p v-if="form.errors.school_id" class="text-xs text-red-500">{{ form.errors.school_id }}</p>
                </div>

                <!-- Waitlist notice -->
                <WaitlistNotice v-if="isWaitlist" :school-name="currentSchool?.name"/>

                <!-- Department -->
                <div v-if="!isWaitlist" class="space-y-1.5">
                    <StepLabel :step="2" label="Department"/>
                    <SearchableSelect
                        v-model="form.department_id"
                        :options="filteredDepartments"
                        placeholder="Select your department"
                        :disabled="!form.school_id"
                        :hint="!form.school_id ? 'Select a school first' : ''"
                        @change="onDepartmentChange"
                    />
                    <p v-if="form.errors.department_id" class="text-xs text-red-500">{{ form.errors.department_id }}</p>
                </div>

                <!-- Programme Type -->
                <div v-if="!isWaitlist" class="space-y-1.5">
                    <StepLabel :step="3" label="Programme type"/>
                    <SearchableSelect
                        v-model="form.programme_type_id"
                        :options="programmeTypes"
                        placeholder="Select programme type"
                        :disabled="!form.department_id"
                        :hint="!form.department_id ? 'Select a department first' : ''"
                        @change="onProgrammeTypeChange"
                    />
                    <!-- Preview badge -->
                    <div v-if="programmeName"
                         class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-primary/10 text-primary rounded-full text-xs font-medium">
                        <svg width="11" height="11" viewBox="0 0 11 11" fill="none">
                            <path
                                d="M5.5 1l1.2 2.4 2.65.39-1.92 1.87.45 2.64L5.5 7.05 3.12 8.3l.45-2.64L1.65 3.79l2.65-.39L5.5 1z"
                                fill="currentColor"/>
                        </svg>
                        {{ programmeName }}
                    </div>
                    <p v-if="form.errors.programme_type_id" class="text-xs text-red-500">
                        {{ form.errors.programme_type_id }}</p>
                </div>

                <!-- Level (universal — available even on the waitlist path) -->
                <div class="space-y-1.5">
                    <StepLabel :step="levelStep" label="Level"/>
                    <SearchableSelect
                        v-model="form.level_id"
                        :options="levels"
                        placeholder="Select your level"
                        :disabled="levelDisabled"
                        :hint="levelHint"
                    />
                    <p v-if="form.errors.level_id" class="text-xs text-red-500">{{ form.errors.level_id }}</p>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    :disabled="form.processing || !canSubmit"
                    class="w-full flex items-center justify-center gap-2 py-2.5 bg-primary text-white text-sm font-medium rounded-lg transition-all duration-150 hover:bg-primary/90 active:scale-[0.99] disabled:opacity-40 disabled:cursor-not-allowed mt-2"
                >
                    <svg v-if="form.processing" class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="32"
                                stroke-dashoffset="12" stroke-linecap="round"/>
                    </svg>
                    <span>{{ isWaitlist ? 'Join Waitlist' : (isUpdate ? 'Update profile' : 'Save profile') }}</span>
                    <svg v-if="!form.processing" width="15" height="15" viewBox="0 0 15 15" fill="none">
                        <path d="M3 7.5h9M8.5 4l3.5 3.5L8.5 11" stroke="currentColor" stroke-width="1.6"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <LegalAgreementNotice/>
            </form>
        </div>
    </div>
</template>
