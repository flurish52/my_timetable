<script setup>
import GuestLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
        <Head title="Forgot Password" />

        <div class="flex min-h-[70vh] items-center justify-center px-4 py-10">
            <div
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-lg sm:p-8"
            >
                <div class="mb-6 text-center">
                    <h1 class="text-2xl font-bold text-gray-900">
                        Forgot Password
                    </h1>

                    <p class="mt-3 text-sm leading-6 text-gray-600">
                        Forgot your password? No problem. Just let us know your
                        email address and we will email you a password reset
                        link that will allow you to choose a new one.
                    </p>
                </div>

                <div
                    v-if="status"
                    class="mb-6 rounded-lg border border-green-200 bg-green-50 p-3 text-sm font-medium text-green-700"
                >
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <InputLabel
                            for="email"
                            value="Email Address"
                        />

                        <TextInput
                            id="email"
                            type="email"
                            class="mt-2 block w-full"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.email"
                        />
                    </div>

                    <div>
                        <PrimaryButton
                            class="w-full justify-center py-3"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Email Password Reset Link
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
</template>
