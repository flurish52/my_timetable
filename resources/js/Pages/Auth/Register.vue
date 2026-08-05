<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { setupNotifications } from "@/composables/useNotifications.js";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import LegalAgreementNotice from "@/Components/LegalAgreementNotice.vue";
import { ref } from 'vue';

const showPassword = ref(false);
const showConfirmPassword = ref(false);

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
        <Head title="Register" />

        <div class="min-h-screen flex items-center justify-center px-4 py-6 bg-gray-50">
            <div class="w-full max-w-sm">

                <!-- Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-10">

                    <!-- Logo + Header -->
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary/10 mb-4">
                            <ApplicationLogo class="w-8 h-8" />
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Create account</h1>
                        <p class="mt-1.5 text-sm text-gray-500 leading-relaxed">
                            Join myUniAlly to manage your timetable, access<br class="hidden sm:block" /> past questions and stay organized.
                        </p>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="space-y-4">

                        <!-- Full Name -->
                        <div>
                            <InputLabel for="name" value="Full name" class="text-sm font-medium text-gray-700 mb-1.5" />
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                </span>
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:ring-primary transition-colors text-sm"
                                    v-model="form.name"
                                    placeholder="Ada Lovelace"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />
                            </div>
                            <InputError class="mt-1.5" :message="form.errors.name" />
                        </div>

                        <!-- Username -->
                        <div>
                            <InputLabel for="username" value="Username" class="text-sm font-medium text-gray-700 mb-1.5" />
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
                                    </svg>
                                </span>
                                <TextInput
                                    id="username"
                                    type="text"
                                    class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:ring-primary transition-colors text-sm"
                                    v-model="form.username"
                                    placeholder="ada_lovelace"
                                    required
                                    autocomplete="username"
                                />
                            </div>
                            <InputError class="mt-1.5" :message="form.errors.username" />
                        </div>

                        <!-- Email -->
                        <div>
                            <InputLabel for="email" value="Email address" class="text-sm font-medium text-gray-700 mb-1.5" />
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                </span>
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:ring-primary transition-colors text-sm"
                                    v-model="form.email"
                                    placeholder="you@university.edu"
                                    required
                                    autocomplete="email"
                                />
                            </div>
                            <InputError class="mt-1.5" :message="form.errors.email" />
                        </div>

                        <!-- Phone (optional) -->
                        <div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <InputLabel for="phone" value="Phone" class="text-sm font-medium text-gray-700 !mb-0" />
                                <span class="text-xs text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded-md">optional</span>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                    </svg>
                                </span>
                                <TextInput
                                    id="phone"
                                    type="tel"
                                    class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:ring-primary transition-colors text-sm"
                                    v-model="form.phone"
                                    placeholder="+234 800 000 0000"
                                    autocomplete="tel"
                                />
                            </div>
                            <InputError class="mt-1.5" :message="form.errors.phone" />
                        </div>

                        <!-- Password -->
                        <div>
                            <InputLabel for="password" value="Password" class="text-sm font-medium text-gray-700 mb-1.5" />
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                </span>
                                <TextInput
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="pl-10 pr-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:ring-primary transition-colors text-sm"
                                    v-model="form.password"
                                    placeholder="Create a strong password"
                                    required
                                    autocomplete="new-password"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-primary transition-colors"
                                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                >
                                    <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            <InputError class="mt-1.5" :message="form.errors.password" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <InputLabel for="password_confirmation" value="Confirm password" class="text-sm font-medium text-gray-700 mb-1.5" />
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                    </svg>
                                </span>
                                <TextInput
                                    id="password_confirmation"
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    class="pl-10 pr-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-primary focus:ring-primary transition-colors text-sm"
                                    v-model="form.password_confirmation"
                                    placeholder="Re-enter your password"
                                    required
                                    autocomplete="new-password"
                                />
                                <button
                                    type="button"
                                    @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-primary transition-colors"
                                    :aria-label="showConfirmPassword ? 'Hide password' : 'Show password'"
                                >
                                    <svg v-if="!showConfirmPassword" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            <InputError class="mt-1.5" :message="form.errors.password_confirmation" />
                        </div>

                        <!-- Submit -->
                        <div class="pt-1">
                            <PrimaryButton
                                class="w-full justify-center py-2.5 text-sm font-semibold rounded-xl transition-opacity"
                                :class="{ 'opacity-50 pointer-events-none': form.processing }"
                                :disabled="form.processing"
                            >
                                <span v-if="!form.processing" class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                                    </svg>
                                    Create account
                                </span>
                                <span v-else class="flex items-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    Creating account…
                                </span>
                            </PrimaryButton>

                            <LegalAgreementNotice class="mt-3" />
                        </div>

                    </form>

                    <!-- Login link -->
                    <p class="mt-6 text-center text-sm text-gray-500">
                        Already have an account?
                        <Link href="/login" class="font-semibold text-primary hover:underline ml-1">
                            Sign in
                        </Link>
                    </p>

                </div>

                <!-- Footer note -->
                <p class="mt-6 text-center text-xs text-gray-400">
                    Takes less than 10 seconds &mdash; instant access, no card needed.
                </p>

            </div>
        </div>
</template>
