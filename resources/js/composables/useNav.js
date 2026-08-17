// composables/useNav.js
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

import studentNav from '@/navigation/studentNav'
import lecturerNav from '@/navigation/lecturerNav'
import contributorNav from '@/navigation/contributorNav'
import adminNav from '@/navigation/adminNav'
import IndependentNav from "@/navigation/independentNav.js";

const navMap = {
    independent: IndependentNav,
    student: studentNav,
    lecturer: lecturerNav,
    contributor: contributorNav,
    admin: adminNav,
}

export function useNav() {
    const page = usePage()

    const navItems = computed(() => {
        const role = page.props.auth?.role   // ← fixed: no `.user.`
        return navMap[role] ?? []
    })

    return { navItems }
}
