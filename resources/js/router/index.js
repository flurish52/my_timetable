import { createRouter, createWebHistory } from 'vue-router'

import Home from '../pages/Home.vue'
import FullTimeTable from "@/pages/FullTimeTable.vue";

const routes = [
    {
        path: '/',
        component: Home,
    },
    {
        path: '/full_timetable',
        component: FullTimeTable,
    },
]

export default createRouter({
    history: createWebHistory(),
    routes,
})
