<template>
    <aside class="hidden lg:flex w-72 flex-shrink-0 flex-col border-l border-gray-200 bg-gray-50/80 overflow-y-auto">
        <div class="p-5">
            <Transition name="tip-fade" mode="out-in">
                <div :key="questionIndex">

                    <!-- Has tip -->
                    <div v-if="tip" class="rounded-xl border-[1.5px] border-amber-200 bg-amber-50 p-4">
                        <div class="flex items-center gap-2 mb-2.5">
                            <span class="text-lg">💡</span>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-800">Answer Tip</span>
                        </div>
                        <RichText :content="tip" class="text-[13px] text-amber-900 leading-relaxed" />
                    </div>

                    <!-- No tip -->
                    <div v-else class="flex flex-col items-center text-center gap-3 py-10 px-4">
                        <span class="text-4xl opacity-30">🔒</span>
                        <p class="text-[12px] text-gray-400 leading-relaxed m-0">
                            No tip for this question.<br>Trust your knowledge!
                        </p>
                    </div>

                </div>
            </Transition>
        </div>
    </aside>
</template>

<script setup>
import RichText from './RichText.vue'

defineProps({
    tip: { type: String, default: null },
    questionIndex: Number,
})
</script>

<style scoped>
.tip-fade-enter-active,
.tip-fade-leave-active { transition: all .2s ease; }
.tip-fade-enter-from,
.tip-fade-leave-to { opacity: 0; transform: translateY(6px); }
</style>
