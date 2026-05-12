<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { setupNotifications } from "@/composables/useNotifications.js";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

const form = useForm({
    name: '',
    username: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.email = form.email.toLowerCase();
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        onSuccess: async () => {
            window.location.reload()
            await setupNotifications()
        },
    });
};
</script>

<template>
    <GuestLayout>
        <div class="px-6 mt-14">
            <Head title="Register" />

            <div class="text-center space-y-3">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary/10">
                    <ApplicationLogo />
                </div>

                <div class="space-y-2">
                    <h1 class="text-3xl font-bold text-gray-900">Create Account</h1>

                    <p class="text-sm text-gray-500 leading-relaxed">
                        Join myUniAlly to manage your timetable, access past questions,
                        receive updates, and stay organized throughout your academic life.
                    </p>

                    <p class="text-xs text-gray-400 leading-relaxed">
                        Registration takes less than 10 seconds. Start now and get instant access.
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="mt-6">

                <!-- Name -->
                <div>
                    <InputLabel for="name" value="Full Name" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        placeholder="Ada Lovelace"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <!-- Username -->
                <div class="mt-4">
                    <InputLabel for="username" value="Username" />
                    <TextInput
                        id="username"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.username"
                        placeholder="ada_lovelace"
                        required
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.username" />
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        placeholder="you@example.com"
                        required
                        autocomplete="email"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- Phone -->
                <div class="mt-4">
                    <InputLabel for="phone" value="Phone" />
                    <span class="text-xs text-gray-400">(optional)</span>
                    <TextInput
                        id="phone"
                        type="tel"
                        class="mt-1 block w-full"
                        v-model="form.phone"
                        placeholder="+1 234 567 8900"
                        autocomplete="tel"
                    />
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <InputLabel for="password" value="Password" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        placeholder="••••••••"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <InputLabel for="password_confirmation" value="Confirm Password" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        placeholder="••••••••"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <div class="mt-6">
                    <PrimaryButton
                        class="w-full justify-center"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Please wait...' : 'Sign up' }}
                    </PrimaryButton>
                </div>

                <p class="flex flex-col items-center justify-between mt-6 text-sm text-gray-600">
                    Already have an account?
                    <Link href="/login" class="text-primary font-semibold hover:underline">
                        Login
                    </Link>
                </p>
            </form>
        </div>
    </GuestLayout>
</template>
