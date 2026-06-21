<script setup>
import { ref } from 'vue'

const isOpen = ref(false)
const whatsappUrl = 'https://chat.whatsapp.com/CNAj5o8adyxFVfDIFTIKkB'

function shareApp() {
    if (navigator.share) {
        navigator.share({
            title: 'myUniAlly',
            text: 'Get past questions, timetables and more — free for Nigerian students!',
            url: window.location.origin,
        })
    } else {
        navigator.clipboard.writeText(window.location.origin)
        alert('Link copied!')
    }
    isOpen.value = false
}
</script>

<template>
    <!-- Trigger button -->
    <button
        @click="isOpen = true"
        class="fixed bottom-20 right-4 z-40 inline-flex items-center gap-1 px-2 py-2.5
           bg-primary hover:bg-primary/90 text-white text-sm font-semibold
           rounded-full shadow-lg transition-colors duration-150 text-xs font-thin"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
       Join Community
    </button>

    <!-- Backdrop -->
    <Transition name="fade">
        <div
            v-if="isOpen"
            @click="isOpen = false"
            class="fixed inset-0 z-50 bg-black/50"
        />
    </Transition>

    <!-- Bottom sheet -->
    <Transition name="slide-up">
        <div
            v-if="isOpen"
            class="fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-2xl pb-safe shadow-2xl"
        >
            <!-- Drag handle -->
            <div class="flex justify-center pt-3 pb-1">
                <div class="w-9 h-1 bg-gray-300 rounded-full"/>
            </div>

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest text-center mt-3 mb-4">
                Community
            </p>

            <div class="px-4 pb-6 flex flex-col gap-3">
                <!-- Share -->
                <button
                    @click="shareApp"
                    class="flex items-center gap-3 w-full px-4 py-3.5 bg-gray-50
                 rounded-xl border border-gray-100 text-left"
                >
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/>
                            <circle cx="18" cy="19" r="3"/>
                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Share myUniAlly</p>
                        <p class="text-xs text-gray-500">Tell your course mates about it</p>
                    </div>
                </button>

                <!-- WhatsApp -->

              <a  :href="whatsappUrl"
                target="_blank"
                rel="noopener noreferrer"
                @click="isOpen = false"
                class="flex items-center gap-3 w-full px-4 py-3.5 bg-gray-50
                rounded-xl border border-gray-100 text-left"
                >
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="#25D366">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.124.554 4.118 1.523 5.847L.057 23.882a.75.75 0 00.921.921l6.035-1.466A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75a9.724 9.724 0 01-4.964-1.36l-.355-.212-3.685.895.913-3.595-.231-.372A9.718 9.718 0 012.25 12C2.25 6.615 6.615 2.25 12 2.25S21.75 6.615 21.75 12 17.385 21.75 12 21.75z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">Join WhatsApp community</p>
                    <p class="text-xs text-gray-500">Connect with other students</p>
                </div>
                </a>

                <!-- Cancel -->
                <button
                    @click="isOpen = false"
                    class="w-full py-3 text-sm text-gray-500"
                >
                    Cancel
                </button>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-up-enter-active, .slide-up-leave-active { transition: transform 0.25s ease; }
.slide-up-enter-from, .slide-up-leave-to { transform: translateY(100%); }

.pb-safe { padding-bottom: env(safe-area-inset-bottom, 1rem); }
</style>
