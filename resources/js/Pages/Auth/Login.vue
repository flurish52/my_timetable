<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {setupNotifications} from "@/composables/useNotifications.js";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.email = form.email.toLowerCase();
    form.post(route('login'), {
        onSuccess: async () => {
            window.location.reload()
            await setupNotifications()
        },

        onError: (errors) => {
            console.log(errors)
        },

        onFinish: () => {
            form.reset('password')
        },
    })
}
</script>

<template>
    <GuestLayout>
        <div class="px-6 mt-14">

        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>


            <div class="text-center space-y-3">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary/10">
                    <ApplicationLogo />
                </div>

                <div class="space-y-2">
                    <h1 class="text-3xl font-bold text-gray-900">
                        Welcome Back
                    </h1>

                    <p class="text-sm text-gray-500 leading-relaxed">
                        Login to access your timetable, download past questions,
                        manage your classes and receive important school updates
                        from myUniAlly.
                    </p>

                    <p class="text-xs text-gray-400 leading-relaxed">
                        New here? Click create account below and get started in
                        less than 10 seconds.
                    </p>
                </div>
            </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600"
                        >Remember me</span
                    >
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
            <p class="flex mt-4 flex-col items-center justify-between text-sm text-gray-600">
                Don’t have an account?
                <Link
                    href="/register"
                    class="text-primary font-semibold hover:underline">
                    Create account
                </Link>
            </p>
        </form>


        </div>
    </GuestLayout>
</template>
