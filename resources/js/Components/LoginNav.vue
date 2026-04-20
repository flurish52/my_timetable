<template>
    <div>
        <!-- Header bar -->
        <div class="flex items-center justify-between p-4 bg-white shadow border-b border-gray-100">
            <span class="font-bold text-blue-600 text-lg tracking-tight">
                  <InstallPWA/>
            </span>
            <div class="flex items-center gap-2">
                <button
                    @click="showForm('login')"
                    class="text-sm px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition"
                >
                    Log in
                </button>
                <button
                    @click="showForm('register')"
                    class="text-sm px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition"
                >
                    Sign up
                </button>
            </div>
        </div>

        <!-- Modal overlay -->
        <div
            v-if="activeForm"
            class="fixed inset-0 bg-black/40 flex items-start justify-center pt-16 z-50"
            @click.self="closeForm"
        >
            <div class="bg-white rounded-xl border border-gray-200 p-6 w-full max-w-sm relative shadow-sm">
                <button @click="closeForm" class="absolute top-3 right-4 text-gray-400 hover:text-gray-600 text-lg">&times;</button>

                <h2 class="font-semibold text-gray-900 text-lg mb-5">
                    {{ mode === 'login' ? 'Log in' : 'Create account' }}
                </h2>

                <!-- Name (register only) -->
                <div v-if="mode === 'register'" class="mb-3">
                    <label class="text-xs text-gray-500 block mb-1">Full name</label>
                    <input v-model="form.name" type="text" placeholder="Ada Lovelace"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="mb-3">
                    <label class="text-xs text-gray-500 block mb-1">Email</label>
                    <input v-model="form.email" type="email" placeholder="you@example.com"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="mb-3">
                    <label class="text-xs text-gray-500 block mb-1">Password</label>
                    <input v-model="form.password" type="password" placeholder="••••••••"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <!-- Confirm password (register only) -->
                <div v-if="mode === 'register'" class="mb-3">
                    <label class="text-xs text-gray-500 block mb-1">Confirm password</label>
                    <input v-model="form.password_confirmation" type="password" placeholder="••••••••"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <p v-if="error" class="text-xs text-red-500 mb-3">{{ error }}</p>

                <button
                    @click="submit"
                    :disabled="loading"
                    class="w-full mt-1 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition disabled:opacity-50"
                >
                    {{ loading ? 'Please wait...' : (mode === 'login' ? 'Log in' : 'Sign up') }}
                </button>

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
import { ref, reactive } from 'vue'
import InstallPWA from "@/Components/InstallPWA.vue";

const activeForm = ref(false)
const mode = ref('login')
const loading = ref(false)
const error = ref('')

const form = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

function showForm(m) {
    mode.value = m
    activeForm.value = true
    error.value = ''
}

function closeForm() {
    activeForm.value = false
    error.value = ''
}

function toggleMode() {
    mode.value = mode.value === 'login' ? 'register' : 'login'
    error.value = ''
}

async function submit() {
    loading.value = true
    error.value = ''
    try {
        const url = mode.value === 'login' ? '/login' : '/register'
        const payload = mode.value === 'login'
            ? { email: form.email, password: form.password }
            : { name: form.name, email: form.email, password: form.password, password_confirmation: form.password_confirmation }

        await axios.post(url, payload)
        closeForm()
        window.location.reload() // or emit an event / update global auth state
    } catch (e) {
        error.value = e.response?.data?.message || 'Something went wrong.'
    } finally {
        loading.value = false
    }
}
</script>
