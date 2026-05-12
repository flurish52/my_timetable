<template>
    <GuestLayout>
        <div class="md:p-4">
            <Dashboardhome
            :timetable="timetable"
            :user="user"
            :programme="programme"
            />
        </div>
    </GuestLayout>
</template>

<script setup>
import Dashboardhome from "@/Components/Dashboardhome.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";

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
