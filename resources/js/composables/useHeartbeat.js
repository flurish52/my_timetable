import { onMounted, onUnmounted } from 'vue'
import axios from 'axios'

export function useHeartbeat(intervalMs = 45000) {
    let timer = null

    const ping = () => {
        if (document.visibilityState === 'visible') {
            axios.post(route('heartbeat')).catch(() => {})
        }
    }

    onMounted(() => {
        ping() // immediate on mount
        timer = setInterval(ping, intervalMs)
        document.addEventListener('visibilitychange', ping)
    })

    onUnmounted(() => {
        if (timer) clearInterval(timer)
        document.removeEventListener('visibilitychange', ping)
    })
}
