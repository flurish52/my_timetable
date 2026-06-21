<template>
    <!--
      RichText.vue
      Renders question/answer text that may contain:
      - HTML tags: <u>, <b>, <i>, <sup>, <sub>, <br>
      - LaTeX math wrapped in \( ... \) or \[ ... \] or $...$ or $$...$$
      - Chemical formulas like H₂O written as H<sub>2</sub>O
      We sanitise to a safe allowlist then hand off to v-html.
      KaTeX is loaded from CDN on first render (lazy).
    -->
    <span class="rich-text" v-html="rendered" />
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
    content: {
        type: String,
        default: '',
    },
    /** pass 'block' to render as block-level (uses <div> semantics via CSS) */
    block: Boolean,
})

const rendered = ref('')

/* ── KaTeX loader (CDN, lazy) ─────────────────────── */
let katexReady = false
let katexPromise = null

function loadKaTeX() {
    if (katexReady) return Promise.resolve()
    if (katexPromise) return katexPromise

    katexPromise = new Promise((resolve) => {
        if (window.katex) { katexReady = true; resolve(); return }

        // Load CSS
        const link = document.createElement('link')
        link.rel = 'stylesheet'
        link.href = 'https://cdn.jsdelivr.net/npm/katex@0.16.10/dist/katex.min.css'
        document.head.appendChild(link)

        // Load JS
        const script = document.createElement('script')
        script.src = 'https://cdn.jsdelivr.net/npm/katex@0.16.10/dist/katex.min.js'
        script.onload = () => { katexReady = true; resolve() }
        document.head.appendChild(script)
    })
    return katexPromise
}

/* ── Sanitise: allow only safe inline HTML ──────────── */
const ALLOWED_TAGS = /^(u|b|i|em|strong|sup|sub|br|span|mark|s|del|ins|var|code)$/i

function sanitise(html) {
    // Remove script/style/on* attrs
    return html
        .replace(/<script[\s\S]*?<\/script>/gi, '')
        .replace(/<style[\s\S]*?<\/style>/gi, '')
        .replace(/\son\w+="[^"]*"/gi, '')
        .replace(/\son\w+='[^']*'/gi, '')
        // Strip tags not in allowlist (keep content)
        .replace(/<\/?([a-z][a-z0-9]*)\b[^>]*>/gi, (match, tag) => {
            return ALLOWED_TAGS.test(tag) ? match : ''
        })
}

/* ── Math renderer ──────────────────────────────────── */
function renderMath(html) {
    if (!window.katex) return html

    // Block math: \[ ... \] or $$ ... $$
    html = html.replace(/\\\[([\s\S]+?)\\\]|\$\$([\s\S]+?)\$\$/g, (_, a, b) => {
        try {
            return window.katex.renderToString((a || b).trim(), { displayMode: true, throwOnError: false })
        } catch { return _ }
    })

    // Inline math: \( ... \) or $ ... $
    html = html.replace(/\\\((.+?)\\\)|\$(.+?)\$/g, (_, a, b) => {
        try {
            return window.katex.renderToString((a || b).trim(), { displayMode: false, throwOnError: false })
        } catch { return _ }
    })

    return html
}

/* ── Main render ────────────────────────────────────── */
async function renderContent() {
    if (!props.content) { rendered.value = ''; return }

    await loadKaTeX()
    const safe = sanitise(props.content)
    rendered.value = renderMath(safe)
}

onMounted(renderContent)
watch(() => props.content, renderContent)
</script>

<style>
/* Allow KaTeX to render without scoped interference */
.rich-text u { text-decoration: underline; }
.rich-text mark { background: #fef08a; padding: 0 2px; border-radius: 2px; }
.rich-text code {
    font-family: 'Fira Code', 'Courier New', monospace;
    background: #f3f4f6; padding: 1px 5px; border-radius: 4px; font-size: .9em;
}
.rich-text .katex-display { margin: 8px 0; overflow-x: auto; }
.rich-text sup, .rich-text sub { font-size: 0.75em; }
</style>
