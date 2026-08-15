<template>

    <div class="min-h-screen bg-white text-slate-900 antialiased">
        <!-- NAV -->
        <header class="sticky top-0 z-50 border-b border-slate-100 bg-white/80 backdrop-blur">
            <nav class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-sm font-bold text-white">
                        <ApplicationLogo class="w-8 h-8 rounded-md"/>
                    </div>
                    <span class="text-lg font-semibold tracking-tight">myUniAlly</span>
                </div>
                <div class="hidden items-center gap-8 text-sm font-medium text-slate-600 md:flex">
                    <a href="/#features" class="transition hover:text-primary">Features</a>
                    <a href="/#how-it-works" class="transition hover:text-primary">How it works</a>
                    <a href="/#contributors" class="transition hover:text-primary">Contributors</a>
                </div>
                <div class="flex items-center gap-3">
                    <Link href="/login" class="hidden text-sm font-medium text-slate-600 hover:text-primary sm:block">
                        Log in
                    </Link>
                    <Link
                        href="/register"
                        class="rounded-full bg-primary px-5 py-2 text-sm font-semibold text-white shadow-sm shadow-primary/30 transition hover:opacity-90"
                    >
                        Get started free
                    </Link>
                </div>
            </nav>
        </header>

        <slot />


        <!-- FOOTER -->
        <footer class="border-t border-slate-100 py-10">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-6 text-sm text-slate-500 sm:flex-row">
                <span>© {{ new Date().getFullYear() }} myUniAlly</span>
                <div class="flex gap-6">
                    <a href="/#features" class="hover:text-primary">Features</a>
                    <a href="/#how-it-works" class="hover:text-primary">How it works</a>
                    <Link href="/login" class="hover:text-primary">Log in</Link>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {Link} from "@inertiajs/vue3";
</script>

<style scoped>

</style>
