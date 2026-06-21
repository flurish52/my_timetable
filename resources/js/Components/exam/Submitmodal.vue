<template>
    <Teleport to="body">
        <Transition name="modal-fade">
            <div
                v-if="open"
                class="fixed inset-0 bg-black/50 z-[70] flex items-end sm:items-center justify-center"
                @click.self="$emit('close')"
            >
                <div class="bg-white w-full max-w-[420px] rounded-t-3xl sm:rounded-2xl px-6 pt-7 pb-8 flex flex-col items-center gap-4">

                    <!-- Icon -->
                    <div class="w-14 h-14 rounded-2xl bg-primary/5 text-primary flex items-center justify-center">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>

                    <h2 class="text-[17px] font-extrabold text-gray-900 text-center m-0">
                        Submit Examination?
                    </h2>

                    <p class="text-[13px] text-gray-500 text-center leading-relaxed m-0">
                        You've answered <strong class="text-gray-800">{{ answeredCount }}</strong> of
                        <strong class="text-gray-800">{{ total }}</strong> questions.
                        <template v-if="unansweredCount > 0">
                            <br>
                            <span class="text-red-500 font-medium">
                {{ unansweredCount }} question{{ unansweredCount !== 1 ? 's' : '' }} will be left blank.
              </span>
                        </template>
                    </p>

                    <!-- Stats band -->
                    <div class="flex w-full border-[1.5px] border-gray-200 rounded-2xl overflow-hidden">
                        <div class="flex-1 flex flex-col items-center py-4 gap-1 bg-primary/20">
                            <span class="text-3xl font-black text-primary leading-none">{{ answeredCount }}</span>
                            <span class="text-[11px] font-medium text-primary uppercase tracking-wide">Answered</span>
                        </div>
                        <div class="w-px bg-gray-200 self-stretch"/>
                        <div class="flex-1 flex flex-col items-center py-4 gap-1 bg-red-50/40">
                            <span class="text-3xl font-black text-red-500 leading-none">{{ unansweredCount }}</span>
                            <span class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">Unanswered</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2.5 w-full">
                        <button
                            @click="$emit('close')"
                            class="flex-1 py-3.5 rounded-xl border-[1.5px] border-gray-200 bg-white text-[14px] font-bold text-gray-800 cursor-pointer hover:opacity-75 transition-opacity"
                        >
                            Go Back
                        </button>
                        <button
                            @click="$emit('confirm')"
                            :disabled="submitting"
                            class="flex-[1.4] py-3.5 rounded-xl bg-primary hover:bg-primary border-none text-white text-[14px] font-extrabold cursor-pointer transition-all duration-150 flex items-center justify-center disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <span v-if="submitting" class="w-[18px] h-[18px] rounded-full border-[2.5px] border-white/30 border-t-white animate-spin"/>
                            <span v-else>Submit Now</span>
                        </button>
                    </div>

                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
defineProps({
    open: Boolean,
    answeredCount: Number,
    total: Number,
    unansweredCount: Number,
    submitting: Boolean,
})

defineEmits(['close', 'confirm'])
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active { transition: opacity .2s ease; }
.modal-fade-enter-from,
.modal-fade-leave-to { opacity: 0; }
</style>
