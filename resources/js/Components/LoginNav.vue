<template>
    <div>
        <!-- Header bar buttons (unchanged) -->
        <div class="flex items-center justify-between shadow">
            <span class="font-bold text-primary text-lg tracking-tight"></span>
            <div class="flex items-center gap-2">
                 <span class="font-bold text-blue-600 text-lg tracking-tight">
                <InstallPWA/>
            </span>
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
            class="fixed inset-0 bg-black/40 flex items-center justify-center px-4 z-50"
            @click.self="closeForm"
        >
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm w-full max-w-sm relative">

                <!-- Close button -->
                <button
                    @click="closeForm"
                    class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Scrollable inner content -->
                <div class="overflow-y-auto max-h-[85vh] px-6 py-7">

                    <!-- Header -->
                    <div class="mb-5">
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">
                            {{ mode === 'login' ? 'Welcome back' : 'Create account' }}
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ mode === 'login'
                            ? 'Sign in to access your timetable and school updates.'
                            : 'Join myUniAlly and stay on top of your academic life.' }}
                        </p>
                    </div>

                    <!-- ── LOGIN FIELDS ── -->
                    <template v-if="mode === 'login'">
                        <div class="space-y-3">

                            <!-- Email -->
                            <div>
                                <label class="text-xs font-medium text-gray-700 block mb-1">Email, Phone or Username</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                        </svg>
                                    </span>
                                    <input
                                        v-model="loginForm.email"
                                        type="text"
                                        placeholder="Email, phone or username"
                                        class="pl-9 w-full border border-gray-200 bg-gray-50 text-gray-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white focus:border-primary transition-colors"
                                    />
                                </div>
                                <p v-if="loginForm.errors.email" class="text-xs text-red-500 mt-1">{{ loginForm.errors.email }}</p>
                            </div>

                            <!-- Password -->
                            <div>
                                <label class="text-xs font-medium text-gray-700 block mb-1">Password</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    </span>
                                    <input
                                        v-model="loginForm.password"
                                        :type="showLoginPassword ? 'text' : 'password'"
                                        placeholder="Enter your password"
                                        class="pl-9 pr-9 w-full border border-gray-200 bg-gray-50 text-gray-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white focus:border-primary transition-colors"
                                    />
                                    <button
                                        type="button"
                                        @click="showLoginPassword = !showLoginPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-primary transition-colors"
                                    >
                                        <svg v-if="!showLoginPassword" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                                <p v-if="loginForm.errors.password" class="text-xs text-red-500 mt-1">{{ loginForm.errors.password }}</p>
                            </div>

                            <!-- Remember me -->
                            <div>
                                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                                    <input type="checkbox" v-model="loginForm.remember" class="rounded border-gray-300 text-primary focus:ring-primary" />
                                    Remember me
                                </label>
                            </div>

                        </div>

                        <!-- Submit -->
                        <button
                            @click="submitLogin"
                            :disabled="loginForm.processing"
                            class="w-full mt-4 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold flex items-center justify-center gap-2 hover:bg-primary/90 transition disabled:opacity-50"
                        >
                            <template v-if="!loginForm.processing">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                                Sign in
                            </template>
                            <template v-else>
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Signing in…
                            </template>
                        </button>
                    </template>

                    <!-- ── REGISTER FIELDS ── -->
                    <template v-else>
                        <div class="space-y-3">

                            <!-- Name -->
                            <div>
                                <label class="text-xs font-medium text-gray-700 block mb-1">Full name</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                    </span>
                                    <input
                                        v-model="registerForm.name"
                                        type="text"
                                        placeholder="Ada Lovelace"
                                        class="pl-9 w-full border border-gray-200 bg-gray-50 text-gray-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white focus:border-primary transition-colors"
                                    />
                                </div>
                                <p v-if="registerForm.errors.name" class="text-xs text-red-500 mt-1">{{ registerForm.errors.name }}</p>
                            </div>

                            <!-- Username -->
                            <div>
                                <label class="text-xs font-medium text-gray-700 block mb-1">Username</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
                                        </svg>
                                    </span>
                                    <input
                                        v-model="registerForm.username"
                                        type="text"
                                        placeholder="ada_lovelace"
                                        class="pl-9 w-full border border-gray-200 bg-gray-50 text-gray-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white focus:border-primary transition-colors"
                                    />
                                </div>
                                <p v-if="registerForm.errors.username" class="text-xs text-red-500 mt-1">{{ registerForm.errors.username }}</p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="text-xs font-medium text-gray-700 block mb-1">Email, Phone or Username</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                        </svg>
                                    </span>
                                    <input
                                        v-model="registerForm.email"
                                        type="text"
                                        placeholder="Email, phone or username"
                                        class="pl-9 w-full border border-gray-200 bg-gray-50 text-gray-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white focus:border-primary transition-colors"
                                    />
                                </div>
                                <p v-if="registerForm.errors.email" class="text-xs text-red-500 mt-1">{{ registerForm.errors.email }}</p>
                            </div>

                            <!-- Phone -->
                            <div>
                                <div class="flex items-center gap-1.5 mb-1">
                                    <label class="text-xs font-medium text-gray-700">Phone</label>
                                    <span class="text-xs text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded-md">optional</span>
                                </div>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                        </svg>
                                    </span>
                                    <input
                                        v-model="registerForm.phone"
                                        type="tel"
                                        placeholder="+234 800 000 0000"
                                        class="pl-9 w-full border border-gray-200 bg-gray-50 text-gray-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white focus:border-primary transition-colors"
                                    />
                                </div>
                                <p v-if="registerForm.errors.phone" class="text-xs text-red-500 mt-1">{{ registerForm.errors.phone }}</p>
                            </div>

                            <!-- Password -->
                            <div>
                                <label class="text-xs font-medium text-gray-700 block mb-1">Password</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    </span>
                                    <input
                                        v-model="registerForm.password"
                                        :type="showRegisterPassword ? 'text' : 'password'"
                                        placeholder="Create a strong password"
                                        class="pl-9 pr-9 w-full border border-gray-200 bg-gray-50 text-gray-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white focus:border-primary transition-colors"
                                    />
                                    <button type="button" @click="showRegisterPassword = !showRegisterPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-primary transition-colors">
                                        <svg v-if="!showRegisterPassword" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                                <p v-if="registerForm.errors.password" class="text-xs text-red-500 mt-1">{{ registerForm.errors.password }}</p>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label class="text-xs font-medium text-gray-700 block mb-1">Confirm password</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                        </svg>
                                    </span>
                                    <input
                                        v-model="registerForm.password_confirmation"
                                        :type="showRegisterConfirm ? 'text' : 'password'"
                                        placeholder="Re-enter your password"
                                        class="pl-9 pr-9 w-full border border-gray-200 bg-gray-50 text-gray-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white focus:border-primary transition-colors"
                                    />
                                    <button type="button" @click="showRegisterConfirm = !showRegisterConfirm" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-primary transition-colors">
                                        <svg v-if="!showRegisterConfirm" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                                <p v-if="registerForm.errors.password_confirmation" class="text-xs text-red-500 mt-1">{{ registerForm.errors.password_confirmation }}</p>
                            </div>

                        </div>

                        <!-- Submit -->
                        <button
                            @click="submitRegister"
                            :disabled="registerForm.processing"
                            class="w-full mt-4 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold flex items-center justify-center gap-2 hover:bg-primary/90 transition disabled:opacity-50"
                        >
                            <template v-if="!registerForm.processing">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                                </svg>
                                Create account
                            </template>
                            <template v-else>
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Creating account…
                            </template>
                        </button>
                    </template>

                    <!-- Toggle mode -->
                    <p class="text-xs text-gray-500 text-center mt-4">
                        {{ mode === 'login' ? "Don't have an account?" : 'Already have an account?' }}
                        <button @click="toggleMode" class="text-primary font-semibold underline ml-1">
                            {{ mode === 'login' ? 'Sign up' : 'Sign in' }}
                        </button>
                    </p>

                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { setupNotifications } from "@/composables/useNotifications.js";
import InstallPWA from "@/Components/InstallPWA.vue";

const activeForm = ref(false)
const mode = ref('login')

const showLoginPassword = ref(false)
const showRegisterPassword = ref(false)
const showRegisterConfirm = ref(false)

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
        onFinish: () => registerForm.reset('password', 'password_confirmation'),
        onSuccess: async () => {
            window.location.reload()
            await setupNotifications()
        },
    })
}
</script>
