<template>
        <Head title="Dashboard" />
        <div class="md:p-4">
            <Dashboardhome
            :timetable="timetable"
            :user="user"
            :programme="programme"
            />
        </div>
</template>

<script setup>
import Dashboardhome from "@/Components/Dashboardhome.vue";
import GuestLayout from "@/Layouts/AppLayout.vue";
import {Head} from "@inertiajs/vue3";

const props = defineProps({
    timetable: {
        type: Array,
        required: true,
    },
    programme: {
        type: Object,
        required: true,
    },
    user: {
        type: Object,
        required: true,
    }
})
</script>

<style scoped>

</style>
