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

const form = useForm({
    school_id: props.user?.school_id || '',
    department_id: props.user?.department_id || '',
    programme_type_id: props.user?.programme_type_id || '',
    programme_id: props.user?.programme_id || '',
    level_id: props.user?.level_id || '',
})

const filteredDepartments = computed(() => {
    if (!form.school_id) return []
    const school = props.schools.find(s => s.id === Number(form.school_id))
    return school?.departments ?? []
})

const onSchoolChange = () => {
    form.department_id = ''
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
                Complete Your Profile
            </h1>

            <p class="text-gray-500 mb-6">
                Set your school details to continue
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
                        <option value="">Select school</option>
                        <option v-for="s in schools" :key="s.id" :value="s.id">
                            {{ s.name }}
                        </option>
                    </select>
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Department</label>
                    <select
                        v-model="form.department_id"
                        :disabled="!form.school_id"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary disabled:bg-gray-100 disabled:cursor-not-allowed"
                    >
                        <option value="">Select department</option>
                        <option v-for="d in filteredDepartments" :key="d.id" :value="d.id">
                            {{ d.name }}
                        </option>
                    </select>
                </div>

                <!-- Programme Type -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Programme Type</label>
                    <select
                        v-model="form.programme_type_id"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                        <option value="">Select type</option>
                        <option v-for="p in programmeTypes" :key="p.id" :value="p.id">
                            {{ p.name }}
                        </option>
                    </select>
                </div>

                <!-- Level -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Level</label>
                    <select
                        v-model="form.level_id"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                        <option value="">Select level</option>
                        <option v-for="l in levels" :key="l.id" :value="l.id">
                            {{ l.name }}
                        </option>
                    </select>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full bg-primary text-white py-2 rounded-lg hover:bg-opacity-90 transition"
                    :disabled="form.processing"
                >
                    Save Profile
                </button>

            </form>

        </div>
    </div>
    </GuestLayout>
</template>
