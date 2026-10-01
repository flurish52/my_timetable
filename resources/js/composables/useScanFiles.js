import { ref, computed, onBeforeUnmount } from 'vue'

const MAX_FILE_BYTES = 8 * 1024 * 1024
const MAX_DIMENSION = 2000
const TARGET_BYTES = 1024 * 1024
const MIN_QUALITY = 0.5

async function compressImage(file) {
    let bitmap
    try {
        bitmap = await createImageBitmap(file, { imageOrientation: 'from-image' })
    } catch {
        return file // let server validation reject it with a proper message
    }

    let { width, height } = bitmap
    if (width > MAX_DIMENSION || height > MAX_DIMENSION) {
        const scale = MAX_DIMENSION / Math.max(width, height)
        width = Math.round(width * scale)
        height = Math.round(height * scale)
    }

    const canvas = document.createElement('canvas')
    canvas.width = width
    canvas.height = height
    canvas.getContext('2d').drawImage(bitmap, 0, 0, width, height)
    bitmap.close?.()

    const toBlob = (q) => new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', q))
    let quality = 0.85
    let blob = await toBlob(quality)
    while (blob && blob.size > TARGET_BYTES && quality > MIN_QUALITY) {
        quality -= 0.1
        blob = await toBlob(quality)
    }

    if (!blob) return file
    return new File([blob], file.name.replace(/\.\w+$/, '.jpg'), { type: 'image/jpeg' })
}

export function useScanFiles(maxFiles = 6) {
    const previews = ref([])
    const fileError = ref('')
    const isProcessing = ref(false)
    let nextId = 0

    const hasPdf = computed(() => previews.value.some((p) => p.isPdf))
    const remaining = computed(() => maxFiles - previews.value.length)
    const canAddMore = computed(() => !hasPdf.value && remaining.value > 0 && !isProcessing.value)
    const files = computed(() => previews.value.map((p) => p.file))

    const revokeAll = () => previews.value.forEach((p) => p.url && URL.revokeObjectURL(p.url))

    async function addFiles(fileList) {
        fileError.value = ''
        const incoming = Array.from(fileList)
        if (!incoming.length) return

        const pdf = incoming.find((f) => f.type === 'application/pdf')
        if (pdf) {
            if (incoming.length > 1) {
                fileError.value = `Upload either a single PDF or up to ${maxFiles} photos, not both.`
                return
            }
            if (previews.value.length && !hasPdf.value) {
                fileError.value = 'Remove your photos first to upload a PDF instead.'
                return
            }
            revokeAll()
            previews.value = [{ id: nextId++, file: pdf, url: null, isPdf: true }]
            return
        }

        if (hasPdf.value) {
            fileError.value = 'Remove the PDF first to upload photos instead.'
            return
        }
        if (remaining.value <= 0) {
            fileError.value = `You've already added ${maxFiles} pages. That's the limit per scan.`
            return
        }

        const images = incoming.filter((f) => f.type.startsWith('image/'))
        const reasons = new Set()
        if (images.length < incoming.length) reasons.add('Only image files (or a single PDF) are allowed.')
        if (!images.length) {
            fileError.value = [...reasons].join(' ')
            return
        }

        isProcessing.value = true
        let compressed
        try {
            compressed = await Promise.all(images.map((f) => compressImage(f)))
        } finally {
            isProcessing.value = false
        }

        const accepted = compressed.filter((f) => {
            if (f.size > MAX_FILE_BYTES) {
                reasons.add('One of your photos is still too large even after compression.')
                return false
            }
            return true
        })

        const room = remaining.value
        if (accepted.length > room) reasons.add(`Only added ${room} more. ${maxFiles} pages is the max per scan.`)

        accepted.slice(0, room).forEach((file) =>
            previews.value.push({ id: nextId++, file, url: URL.createObjectURL(file), isPdf: false })
        )

        if (reasons.size) fileError.value = [...reasons].join(' ')
    }

    function removePreview(id) {
        const idx = previews.value.findIndex((p) => p.id === id)
        if (idx === -1) return
        if (previews.value[idx].url) URL.revokeObjectURL(previews.value[idx].url)
        previews.value.splice(idx, 1)
        fileError.value = ''
    }

    onBeforeUnmount(revokeAll)

    return { previews, files, fileError, isProcessing, hasPdf, canAddMore, addFiles, removePreview }
}
