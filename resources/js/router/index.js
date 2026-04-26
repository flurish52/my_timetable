import { createRouter, createWebHistory } from 'vue-router'

import Home from '../pages/Home.vue'
import FullTimeTable from "@/pages/FullTimeTable.vue";
import PastQuestions from "@/pages/PastQuestions.vue";
import PastQuestionsPerCourse from "../pages/PastQuestionsPerCourse.vue";

const routes = [
    {
        path: '/',
        component: Home,
    },
    {
        path: '/full_timetable',
        component: FullTimeTable,
    },
    {
    path: '/pastquestions',
    component: PastQuestions
    },
    {
    path: '/pastquestions/:course_title',
    component: PastQuestionsPerCourse
    }
]

export default createRouter({
    history: createWebHistory(),
    routes,
})
