<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3'

defineProps<{
    currentRole: string
    latestRequest: { status: string; requested_role: string; review_note: string | null } | null
}>()

const form = useForm({
    requested_role: 'contributor',
    reason: '',
})

const submit = () => form.post(route('role-requests.store'))

const roles = [
    { value: 'contributor', label: 'Contributor', desc: 'Add courses, upload past questions, manage the timetable.' },
    { value: 'lecturer', label: 'Lecturer', desc: 'Attach yourself to the courses you teach.' },
    { value: 'admin', label: 'Admin', desc: 'Full platform access.' },
]
</script>

<template>
    <div class="max-w-lg mx-auto py-10 px-4">
        <h1 class="text-2xl font-bold text-primary">Become a Contributor</h1>
        <p class="text-gray-500 mt-1">
            You're currently a <span class="font-medium capitalize">{{ currentRole }}</span>.
            Request an upgraded role below.
        </p>

        <div v-if="latestRequest?.status === 'pending'" class="mt-6 rounded-lg bg-amber-50 border border-amber-200 p-4 text-amber-800">
            Your request for <strong class="capitalize">{{ latestRequest.requested_role }}</strong> is pending review.
        </div>

        <div v-else-if="latestRequest?.status === 'rejected'" class="mt-6 rounded-lg bg-red-50 border border-red-200 p-4 text-red-700">
            Your last request was declined.
            <span v-if="latestRequest.review_note">{{ latestRequest.review_note }}</span>
        </div>

        <form v-else @submit.prevent="submit" class="mt-6 space-y-5">
            <div class="space-y-2">
                <label class="text-sm font-medium text-gray-700">Which role are you applying for?</label>
                <div class="space-y-2">
                    <label
                        v-for="r in roles"
                        :key="r.value"
                        class="flex items-start gap-3 rounded-lg border p-3 cursor-pointer"
                        :class="form.requested_role === r.value ? 'border-primary ring-1 ring-primary' : 'border-gray-200'"
                    >
                        <input type="radio" v-model="form.requested_role" :value="r.value" class="mt-1" />
                        <span>
              <span class="block font-medium">{{ r.label }}</span>
              <span class="block text-sm text-gray-500">{{ r.desc }}</span>
            </span>
                    </label>
                </div>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Why should we approve you?</label>
                <textarea
                    v-model="form.reason"
                    rows="4"
                    class="mt-1 w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                    placeholder="e.g. I'm a 200-level EDU student and want to contribute past questions for my courses."
                />
                <p v-if="form.errors.reason" class="text-sm text-red-600 mt-1">{{ form.errors.reason }}</p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full bg-primary text-white rounded-lg py-2.5 font-medium disabled:opacity-50"
            >
                Submit Request
            </button>
        </form>

        <Link :href="route('dashboard')" class="block mt-6 text-sm text-gray-500">← Back to dashboard</Link>
    </div>
</template>
