<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
    user: { type: Object },
});

const page = usePage();
const user = computed(() => props.user);

const editing = ref(false);
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    name: user.value?.name ?? '',
    username: user.value?.username ?? '',
    email: user.value?.email ?? '',
    phone: user.value?.phone ?? '',
    password: '',
    password_confirmation: '',
});

function startEdit() {
    form.name = user.value?.name ?? '';
    form.username = user.value?.username ?? '';
    form.email = user.value?.email ?? '';
    form.phone = user.value?.phone ?? '';
    form.password = '';
    form.password_confirmation = '';
    editing.value = true;
}

function cancelEdit() {
    form.reset();
    form.clearErrors();
    editing.value = false;
}

function submit() {
    form.patch(route('profile.update'), {
        onSuccess: () => {
            editing.value = false;
            form.password = '';
            form.password_confirmation = '';
        },
    });
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric', month: 'long', day: 'numeric',
    });
}

function formatLastLogin(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
}
</script>

<template>
    <Head title="Profile" />

    <GuestLayout>
        <div class="min-h-screen bg-gray-50 py-10 px-4">
            <div class="max-w-2xl mx-auto space-y-5">

                <!-- Page heading -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">My Profile</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Manage your personal information and account settings.</p>
                    </div>
<!--                    <button-->
<!--                        v-if="!editing"-->
<!--                        @click="startEdit"-->
<!--                        class="flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition"-->
<!--                    >-->
<!--                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">-->
<!--                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />-->
<!--                        </svg>-->
<!--                        Edit profile-->
<!--                    </button>-->
                </div>

                <!-- Avatar + identity card -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <!-- Cover strip -->
                    <div class="h-20 bg-gradient-to-r from-primary/80 to-primary"></div>

                    <div class="px-6 pb-6">
                        <!-- Avatar -->
                        <div class="-mt-10 mb-4 flex items-end justify-between">
                            <div class="relative">
                                <img
                                    v-if="user?.avatar"
                                    :src="user.avatar"
                                    class="w-20 h-20 rounded-2xl object-cover ring-4 ring-white shadow"
                                />
                                <div
                                    v-else
                                    class="w-20 h-20 rounded-2xl bg-primary text-white text-2xl font-bold flex items-center justify-center ring-4 ring-white shadow"
                                >
                                    {{ user?.name?.[0]?.toUpperCase() ?? '?' }}
                                </div>
                                <!-- Online dot -->
                                <span
                                    v-if="user?.is_online"
                                    class="absolute bottom-1 right-1 w-3.5 h-3.5 bg-emerald-400 border-2 border-white rounded-full"
                                ></span>
                            </div>

                            <!-- Verified badge -->
                            <div class="flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-full"
                                 :class="user?.email_verified_at ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-600'"
                            >
                                <svg v-if="user?.email_verified_at" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z" />
                                </svg>
                                {{ user?.email_verified_at ? 'Verified' : 'Unverified' }}
                            </div>
                        </div>

                        <h2 class="text-xl font-bold text-gray-900">{{ user?.name ?? '—' }}</h2>
                        <p class="text-sm text-gray-500">@{{ user?.username ?? '—' }}</p>

                        <!-- Meta row -->
                        <div class="mt-3 flex flex-wrap gap-3">
                            <span class="flex items-center gap-1.5 text-xs text-gray-500 bg-gray-50 px-2.5 py-1 rounded-lg border border-gray-100">
                                <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                                </svg>
                                {{ user?.programme?.name ?? '—' }}
                            </span>
                            <span class="flex items-center gap-1.5 text-xs text-gray-500 bg-gray-50 px-2.5 py-1 rounded-lg border border-gray-100">
                                <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zm9.75-4.5a1.125 1.125 0 00-1.125 1.125v10.125c0 .621.504 1.125 1.125 1.125h2.25c.621 0 1.125-.504 1.125-1.125V9.75A1.125 1.125 0 0014.625 8.625h-2.25z" />
                                </svg>
                                {{ user?.level.name ?? '—' }}
                            </span>
                            <span class="flex items-center gap-1.5 text-xs text-gray-500 bg-gray-50 px-2.5 py-1 rounded-lg border border-gray-100">
                                <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Last seen {{ formatLastLogin(user?.last_login_at) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Personal information -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-800">Personal Information</h3>
                    </div>

                    <form @submit.prevent="submit" class="px-6 py-5 space-y-4">

                        <!-- Name -->
                        <div>
                            <label class="text-xs font-medium text-gray-500 block mb-1">Full name</label>
                            <div v-if="!editing" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-800">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                {{ user?.name ?? '—' }}
                            </div>
                            <div v-else class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                </span>
                                <input v-model="form.name" type="text" class="pl-9 w-full border border-gray-200 bg-gray-50 text-gray-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white focus:border-primary transition-colors" />
                            </div>
                            <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
                        </div>

                        <!-- Username -->
                        <div>
                            <label class="text-xs font-medium text-gray-500 block mb-1">Username</label>
                            <div v-if="!editing" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-800">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
                                </svg>
                                @{{ user?.username ?? '—' }}
                            </div>
                            <div v-else class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
                                    </svg>
                                </span>
                                <input v-model="form.username" type="text" class="pl-9 w-full border border-gray-200 bg-gray-50 text-gray-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white focus:border-primary transition-colors" />
                            </div>
                            <p v-if="form.errors.username" class="text-xs text-red-500 mt-1">{{ form.errors.username }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="text-xs font-medium text-gray-500 block mb-1">Email address</label>
                            <div v-if="!editing" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-800">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                                {{ user?.email ?? '—' }}
                            </div>
                            <div v-else class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                </span>
                                <input v-model="form.email" type="email" class="pl-9 w-full border border-gray-200 bg-gray-50 text-gray-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white focus:border-primary transition-colors" />
                            </div>
                            <p v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="text-xs font-medium text-gray-500 block mb-1">Phone number</label>
                            <div v-if="!editing" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-800">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                                {{ user?.phone ?? '—' }}
                            </div>
                            <div v-else class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                    </svg>
                                </span>
                                <input v-model="form.phone" type="tel" class="pl-9 w-full border border-gray-200 bg-gray-50 text-gray-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white focus:border-primary transition-colors" />
                            </div>
                            <p v-if="form.errors.phone" class="text-xs text-red-500 mt-1">{{ form.errors.phone }}</p>
                        </div>

                        <!-- Edit mode: change password section -->
                        <template v-if="editing">
                            <div class="pt-2 border-t border-gray-100">
                                <p class="text-xs font-medium text-gray-400 mb-3 uppercase tracking-wide">Change password <span class="normal-case text-gray-400">(leave blank to keep current)</span></p>

                                <!-- New password -->
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 block mb-1">New password</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                                </svg>
                                            </span>
                                            <input
                                                v-model="form.password"
                                                :type="showPassword ? 'text' : 'password'"
                                                placeholder="New password"
                                                class="pl-9 pr-9 w-full border border-gray-200 bg-gray-50 text-gray-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white focus:border-primary transition-colors"
                                            />
                                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-primary transition-colors">
                                                <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                </svg>
                                            </button>
                                        </div>
                                        <p v-if="form.errors.password" class="text-xs text-red-500 mt-1">{{ form.errors.password }}</p>
                                    </div>

                                    <!-- Confirm password -->
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 block mb-1">Confirm new password</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                                </svg>
                                            </span>
                                            <input
                                                v-model="form.password_confirmation"
                                                :type="showConfirmPassword ? 'text' : 'password'"
                                                placeholder="Confirm new password"
                                                class="pl-9 pr-9 w-full border border-gray-200 bg-gray-50 text-gray-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white focus:border-primary transition-colors"
                                            />
                                            <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-primary transition-colors">
                                                <svg v-if="!showConfirmPassword" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                </svg>
                                            </button>
                                        </div>
                                        <p v-if="form.errors.password_confirmation" class="text-xs text-red-500 mt-1">{{ form.errors.password_confirmation }}</p>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Edit mode actions -->
                        <div v-if="editing" class="flex items-center gap-3 pt-2">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition disabled:opacity-50"
                            >
                                <template v-if="!form.processing">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    Save changes
                                </template>
                                <template v-else>
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    Saving…
                                </template>
                            </button>
                            <button
                                type="button"
                                @click="cancelEdit"
                                class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition"
                            >
                                Cancel
                            </button>
                        </div>

                    </form>
                </div>

                <!-- Read-only academic info -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-800">Academic Information</h3>
                        <span class="ml-auto text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-md">Read only</span>
                    </div>

                    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <!-- Programme -->
                        <div>
                            <label class="text-xs font-medium text-gray-400 block mb-1">Programme</label>
                            <div class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                                </svg>
                                <span class="truncate">{{ user?.programme?.name ?? '—' }}</span>
                            </div>
                        </div>

                        <!-- Department -->
                        <div>
                            <label class="text-xs font-medium text-gray-400 block mb-1">Department</label>
                            <div class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                                <span class="truncate">{{ user?.programme?.department?.name ?? '—' }}</span>
                            </div>
                        </div>

                        <!-- Level -->
                        <div>
                            <label class="text-xs font-medium text-gray-400 block mb-1">Level</label>
                            <div class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zm9.75-4.5a1.125 1.125 0 00-1.125 1.125v10.125c0 .621.504 1.125 1.125 1.125h2.25c.621 0 1.125-.504 1.125-1.125V9.75A1.125 1.125 0 0014.625 8.625h-2.25z" />
                                </svg>
                                {{ user?.level?.name ?? '—' }}
                            </div>
                        </div>

                        <!-- Programme type -->
                        <div>
                            <label class="text-xs font-medium text-gray-400 block mb-1">Programme type</label>
                            <div class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ user?.programme?.programme_type?.name ?? '—' }}
                            </div>
                        </div>

                        <!-- Member since -->
                        <div>
                            <label class="text-xs font-medium text-gray-400 block mb-1">Member since</label>
                            <div class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                {{ formatDate(user?.created_at) }}
                            </div>
                        </div>

                        <!-- Last login -->
                        <div>
                            <label class="text-xs font-medium text-gray-400 block mb-1">Last login</label>
                            <div class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ formatLastLogin(user?.last_login_at) }}
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </GuestLayout>
</template>
