<template>
    <div>
        <Link :href="$page.props.auth.user? '/dashboard' : '/'" >
        <img src="/icons/pwa-192x192.png" alt="Logo" class="rounded-md">
        </Link>
    </div>
</template>
<script setup>
import {Link} from "@inertiajs/vue3";
</script>
