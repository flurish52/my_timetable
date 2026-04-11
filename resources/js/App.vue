<template>

    <nav class=" bg-red-600">
        <InstallPWA />   <!-- drop it right here -->
    </nav>

    <router-view />
</template>
<script setup>
import InstallPWA from "./Components/InstallPWA.vue";
</script>
