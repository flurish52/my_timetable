<template>
    <div>
        <!-- Header bar -->
        <div class="flex items-center justify-between shadow">
            <span class="font-bold text-primary text-lg tracking-tight">
<!--                <InstallPWA />-->
            </span>
            <div class="flex items-center gap-2">
                <button
                    @click="showForm('login')"
                    class="text-sm px-4 py-2 rounded-lg bg-white text-primary border border-gray-300 hover:bg-primary/10 hover:text-white transition"
                >
                    Log in
                </button>
                <button
                    @click="showForm('register')"
                    class="text-sm px-4 py-2 rounded-lg bg-primary border-2 border-white text-white hover:bg-gray-200 transition"
                >
                    Sign up
                </button>
            </div>
        </div>

        <!-- Modal overlay -->
        <div
            v-if="activeForm"
            class="fixed inset-0 bg-black/40 flex items-start justify-center pt-4 px-3 z-50"
            @click.self="closeForm"
        >
            <div class="bg-white rounded-xl border border-gray-200 p-6 w-full max-w-sm relative shadow-sm">
                <button @click="closeForm" class="absolute top-3 right-4 text-gray-400 hover:text-gray-600 text-lg">
                    &times;
                </button>

                <h2 class="font-semibold text-gray-900 text-lg mb-2">
                    {{ mode === 'login' ? 'Log in' : 'Create account' }}
                </h2>

                <!-- ── REGISTER FIELDS ── -->
                <template v-if="mode === 'register'">
                    <!-- Name -->
                    <div class="mb-3 flex justify-between flex-col items-start">
                        <label class="text-xs text-gray-500 block mb-1">Full name</label>
                        <input
                            v-model="registerForm.name"
                            type="text"
                            placeholder="Ada Lovelace"
                            class="w-full border border-gray-200 text-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                        <p v-if="registerForm.errors.name" class="text-xs text-red-500 mt-1">{{
                                registerForm.errors.name
                            }}</p>
                    </div>

                    <!-- Username -->
                    <div class="mb-3 flex justify-between flex-col items-start">
                        <label class="text-xs text-gray-500 block mb-1">Username</label>
                        <input
                            v-model="registerForm.username"
                            type="text"
                            placeholder="ada_lovelace"
                            class="w-full border border-gray-200 text-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                        <p v-if="registerForm.errors.username" class="text-xs text-red-500 mt-1">
                            {{ registerForm.errors.username }}</p>
                    </div>

                    <!-- Email -->
                    <div class="mb-3 flex justify-between flex-col items-start">
                        <label class="text-xs text-gray-500 block mb-1">Email, Phone or Username</label>
                        <input
                            v-model="registerForm.email"
                            type="text"
                            placeholder="Email, Phone or Username"
                            class="w-full border border-gray-200 text-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                        <p v-if="registerForm.errors.email" class="text-xs text-red-500 mt-1">
                            {{ registerForm.errors.email }}</p>
                    </div>

                    <!-- Phone -->
                    <div class="mb-3 flex justify-between flex-col items-start">
                        <label class="text-xs text-gray-500 text-gray-700 block mb-1">Phone <span class="text-gray-400">(optional)</span></label>
                        <input
                            v-model="registerForm.phone"
                            type="tel"
                            placeholder="+1 234 567 8900"
                            class="w-full border border-gray-200 text-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                        <p v-if="registerForm.errors.phone" class="text-xs text-red-500 mt-1">
                            {{ registerForm.errors.phone }}</p>
                    </div>

                    <!-- Password -->
                    <div class="mb-3 flex justify-between flex-col items-start">
                        <label class="text-xs text-gray-500 text-gray-700 block mb-1">Password</label>
                        <input
                            v-model="registerForm.password"
                            type="password"
                            placeholder="••••••••"
                            class="w-full border border-gray-200 text-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                        <p v-if="registerForm.errors.password" class="text-xs text-red-500 mt-1">
                            {{ registerForm.errors.password }}</p>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3 flex justify-between flex-col items-start">
                        <label class="text-xs text-gray-500 block mb-1">Confirm password</label>
                        <input
                            v-model="registerForm.password_confirmation"
                            type="password"
                            placeholder="••••••••"
                            class="w-full border border-gray-200 text-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                        <p v-if="registerForm.errors.password_confirmation" class="text-xs text-red-500 mt-1">
                            {{ registerForm.errors.password_confirmation }}</p>
                    </div>

                    <button
                        @click="submitRegister"
                        :disabled="registerForm.processing"
                        class="w-full mt-1 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/50 transition disabled:opacity-50"
                    >
                        {{ registerForm.processing ? 'Please wait...' : 'Sign up' }}
                    </button>
                </template>

                <!-- ── LOGIN FIELDS ── -->
                <template v-else>
                    <!-- Email -->
                    <div class="mb-3">
                        <label class="text-xs text-gray-500 block mb-1">Email, Phone or Username</label>
                        <input
                            v-model="loginForm.email"
                            type="text"
                            placeholder="Email, Phone or Username"
                            class="w-full border border-gray-200 text-primary  rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                        <p v-if="loginForm.errors.email" class="text-xs text-red-500 mt-1">{{
                                loginForm.errors.email
                            }}</p>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="text-xs text-gray-500 block mb-1">Password</label>
                        <input
                            v-model="loginForm.password"
                            type="password"
                            placeholder="••••••••"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-primary text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                        <p v-if="loginForm.errors.password" class="text-xs text-red-500 mt-1">
                            {{ loginForm.errors.password }}</p>
                    </div>

                    <!-- Remember me -->
                    <div class="mb-3">
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" v-model="loginForm.remember" class="rounded border-gray-300"/>
                            Remember me
                        </label>
                    </div>

                    <button
                        @click="submitLogin"
                        :disabled="loginForm.processing"
                        class="w-full mt-1 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/50 transition disabled:opacity-50"
                    >
                        {{ loginForm.processing ? 'Please wait...' : 'Log in' }}
                    </button>
                </template>

                <p class="text-xs text-gray-500 text-center mt-4">
                    {{ mode === 'login' ? "Don't have an account?" : 'Already have an account?' }}
                    <button @click="toggleMode" class="text-blue-600 underline ml-1">
                        {{ mode === 'login' ? 'Sign up' : 'Log in' }}
                    </button>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref} from 'vue'
import {useForm} from '@inertiajs/vue3'
import {setupNotifications} from "@/composables/useNotifications.js";

const activeForm = ref(false)
const mode = ref('login')

// ── Separate Inertia forms — same as the full pages ──
const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
    currentUrl: window.location.href,
})

const registerForm = useForm({
    name: '',
    username: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    currentUrl: window.location.href,
})

function showForm(m) {
    mode.value = m
    activeForm.value = true
}

function closeForm() {
    activeForm.value = false
}

function toggleMode() {
    mode.value = mode.value === 'login' ? 'register' : 'login'
}

function submitLogin() {
    loginForm.email = loginForm.email.toLowerCase();
    loginForm.post(route('login'), {
        onFinish: () => loginForm.reset('password'),

        onSuccess: async () => {
            closeForm()
            window.location.reload()
            await setupNotifications()
        },
    })
}

function submitRegister() {
    registerForm.email = registerForm.email.toLowerCase();
    registerForm.post(route('register'), {
        onFinish: () =>
            registerForm.reset('password', 'password_confirmation'),

        onSuccess: async () => {
            window.location.reload()
            await setupNotifications()
        },
    })
}
</script>
