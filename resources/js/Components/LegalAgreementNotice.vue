<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <p class="text-xs text-slate-500 text-center leading-relaxed">
        By signing up, you agree to our
        <Link href="/t_and_c" class="text-primary font-medium hover:underline">
            Terms
        </Link>
        and
        <Link href="/privacy_policy" class="text-primary font-medium hover:underline">
            Privacy Policy
        </Link>
    </p>
</template>
