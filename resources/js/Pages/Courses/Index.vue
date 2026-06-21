<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import GuestLayout from "@/Layouts/AppLayout.vue"
import SearchableSelect from "@/Components/SearchableSelect.vue"

const props = defineProps({
    courseOfferings:  Array,  // all offerings for this student's programme + level
    studentElectives: Array,  // electives already enrolled: [{ id, course_offering_id }]
})

// ── Partition ──────────────────────────────────────────────────────────────
const generals      = computed(() => props.courseOfferings.filter(o => o.is_general))
const departmentals = computed(() => props.courseOfferings.filter(o => !o.is_general && o.type === 'core'))
const allElectives  = computed(() => props.courseOfferings.filter(o => !o.is_general && o.type === 'elective'))

const enrolledIds = computed(() => (props.studentElectives ?? []).map(e => e.course_offering_id))

const availableElectives = computed(() =>
    allElectives.value
        .filter(o => !enrolledIds.value.includes(o.id))
        .map(o => ({ id: o.id, name: `${o.course.code} — ${o.course.title}` }))
)

const enrolledElectives = computed(() =>
    allElectives.value.filter(o => enrolledIds.value.includes(o.id))
)

// ── Add ────────────────────────────────────────────────────────────────────
const addForm = useForm({ course_offering_id: '' })

const handleAdd = () => {
    if (!addForm.course_offering_id) return
    addForm.post('/course_offering', {
        preserveScroll: true,
        onSuccess: () => addForm.reset(),
    })
}

// ── Remove ─────────────────────────────────────────────────────────────────
const removeForm  = useForm({})
const removingId  = ref(null)

const handleRemove = (enrollmentId) => {
    removingId.value = enrollmentId
    removeForm.delete(`/course_offering/${enrollmentId}`, {
        preserveScroll: true,
        onFinish: () => { removingId.value = null },
    })
}
</script>

<template>
    <GuestLayout>
        <div class="min-h-screen bg-gray-50 px-4 py-8">
            <div class="max-w-5xl mx-auto space-y-10">

                <!-- Page heading -->
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Course Offerings</h1>
                    <p class="text-sm text-gray-400 mt-0.5">{{ courseOfferings.length }} courses this semester</p>
                </div>

                <!-- ══════════════════════════════════════════
                     SECTION 1 · GENERAL COURSES
                ═════════════════════════════════════════════-->
                <section>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 shrink-0">
                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                                <circle cx="7.5" cy="7.5" r="5.5" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M4.5 7.5l2 2 3.5-3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-sm font-semibold text-gray-800">General Courses</h2>
                            <p class="text-xs text-gray-400">{{ generals.length }} course{{ generals.length !== 1 ? 's' : '' }} · compulsory for all students</p>
                        </div>
                    </div>

                    <div v-if="generals.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div
                            v-for="item in generals"
                            :key="item.id"
                            class="group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 overflow-hidden"
                        >
                            <!-- Colour accent bar -->
                            <div class="h-1 w-full bg-emerald-400"></div>

                            <div class="p-4">
                                <!-- Top row: code + badge -->
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="inline-block text-[11px] font-bold tracking-widest text-emerald-600 uppercase">{{ item.course.code }}</span>
                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-semibold">General</span>
                                </div>

                                <!-- Title -->
                                <h3 class="text-sm font-semibold text-gray-900 leading-snug mb-4">{{ item.course.title }}</h3>

                                <!-- Footer -->
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <span class="text-[11px] text-gray-400 uppercase tracking-wide font-medium">Credit Units</span>
                                    <span class="text-sm font-bold text-gray-800 bg-gray-100 rounded-lg px-2.5 py-0.5">{{ item.course.credit_unit }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="py-10 text-center text-sm text-gray-400 bg-white rounded-xl border border-dashed border-gray-200">
                        No general courses this semester.
                    </div>
                </section>

                <!-- ══════════════════════════════════════════
                     SECTION 2 · DEPARTMENTAL / CORE
                ═════════════════════════════════════════════-->
                <section>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary shrink-0">
                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                                <rect x="2.5" y="2.5" width="10" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M5 7.5h5M7.5 5v5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-sm font-semibold text-gray-800">Departmental Courses</h2>
                            <p class="text-xs text-gray-400">{{ departmentals.length }} core course{{ departmentals.length !== 1 ? 's' : '' }} · required for your programme</p>
                        </div>
                    </div>

                    <div v-if="departmentals.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div
                            v-for="item in departmentals"
                            :key="item.id"
                            class="group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 overflow-hidden"
                        >
                            <div class="h-1 w-full bg-primary"></div>

                            <div class="p-4">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="inline-block text-[11px] font-bold tracking-widest text-primary uppercase">{{ item.course.code }}</span>
                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-primary/10 text-primary font-semibold">Core</span>
                                </div>

                                <h3 class="text-sm font-semibold text-gray-900 leading-snug mb-4">{{ item.course.title }}</h3>

                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <span class="text-[11px] text-gray-400 uppercase tracking-wide font-medium">Credit Units</span>
                                    <span class="text-sm font-bold text-gray-800 bg-gray-100 rounded-lg px-2.5 py-0.5">{{ item.course.credit_unit }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="py-10 text-center text-sm text-gray-400 bg-white rounded-xl border border-dashed border-gray-200">
                        No departmental courses this semester.
                    </div>
                </section>

                <!-- ══════════════════════════════════════════
                     SECTION 3 · ELECTIVES
                ═════════════════════════════════════════════-->
                <section>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 text-amber-600 shrink-0">
                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                                <path d="M7.5 1.5l1.55 3.14 3.47.5-2.51 2.45.59 3.46L7.5 9.3l-3.1 1.75.59-3.46L2.48 5.14l3.47-.5L7.5 1.5z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-sm font-semibold text-gray-800">Elective Courses</h2>
                            <p class="text-xs text-gray-400">{{ enrolledElectives.length }} selected · pick from {{ allElectives.length }} available</p>
                        </div>
                    </div>

                    <!-- Add elective panel -->
                    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-4 shadow-sm">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Add an elective</p>
                        <div class="flex items-start gap-3">
                            <div class="flex-1">
                                <SearchableSelect
                                    v-model="addForm.course_offering_id"
                                    :options="availableElectives"
                                    placeholder="Search and select an elective…"
                                    @change="() => {}"
                                />
                                <p v-if="addForm.errors.course_offering_id" class="mt-1 text-xs text-red-500">
                                    {{ addForm.errors.course_offering_id }}
                                </p>
                                <p v-if="availableElectives.length === 0" class="mt-1 text-xs text-gray-400">
                                    All available electives have been selected.
                                </p>
                            </div>
                            <button
                                type="button"
                                :disabled="!addForm.course_offering_id || addForm.processing"
                                @click="handleAdd"
                                class="flex items-center gap-1.5 px-4 h-[42px] bg-primary text-white text-sm font-medium rounded-lg transition-all duration-150 hover:bg-primary/90 active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed shrink-0"
                            >
                                <svg v-if="addForm.processing" class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="32" stroke-dashoffset="12" stroke-linecap="round"/>
                                </svg>
                                <svg v-else width="13" height="13" viewBox="0 0 13 13" fill="none">
                                    <path d="M6.5 2v9M2 6.5h9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                Add
                            </button>
                        </div>
                    </div>

                    <!-- Enrolled electives grid -->
                    <div v-if="enrolledElectives.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div
                            v-for="item in enrolledElectives"
                            :key="item.id"
                            class="group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 overflow-hidden"
                        >
                            <div class="h-1 w-full bg-amber-400"></div>

                            <div class="p-4">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="inline-block text-[11px] font-bold tracking-widest text-amber-600 uppercase">{{ item.course.code }}</span>
                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">Elective</span>
                                </div>

                                <h3 class="text-sm font-semibold text-gray-900 leading-snug mb-4">{{ item.course.title }}</h3>

                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[11px] text-gray-400 uppercase tracking-wide font-medium">Credit Units</span>
                                        <span class="text-sm font-bold text-gray-800 bg-gray-100 rounded-lg px-2.5 py-0.5">{{ item.course.credit_unit }}</span>
                                    </div>
                                    <button
                                        type="button"
                                        :disabled="removingId === item.id"
                                        @click="handleRemove(item.id)"
                                        class="flex items-center gap-1 text-[11px] font-medium text-red-400 hover:text-red-600 hover:bg-red-50 px-2 py-1 rounded-lg transition-all duration-100 disabled:opacity-40 disabled:cursor-not-allowed"
                                    >
                                        <svg v-if="removingId === item.id" class="animate-spin w-3 h-3" viewBox="0 0 24 24" fill="none">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="32" stroke-dashoffset="12" stroke-linecap="round"/>
                                        </svg>
                                        <svg v-else width="11" height="11" viewBox="0 0 12 12" fill="none">
                                            <path d="M2 2l8 8M10 2l-8 8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                        </svg>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="py-10 text-center text-sm text-gray-400 bg-white rounded-xl border border-dashed border-amber-200">
                        You haven't added any electives yet. Use the picker above to add one.
                    </div>
                </section>

            </div>
        </div>
    </GuestLayout>
</template>
