<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import Icon from '@/components/ui/icon/Icon.vue'

const isDark = ref(false)

const toggleTheme = () => {
    isDark.value = !isDark.value
    document.documentElement.classList.toggle('dark', isDark.value)
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
}

onMounted(() => {
    // Check for saved theme preference or system preference
    const savedTheme = localStorage.getItem('theme')
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    
    isDark.value = savedTheme === 'dark' || (!savedTheme && systemPrefersDark)
    document.documentElement.classList.toggle('dark', isDark.value)
})

// Watch for system theme changes
watch(() => window.matchMedia('(prefers-color-scheme: dark)').matches, (isDark) => {
    if (!localStorage.getItem('theme')) {
        document.documentElement.classList.toggle('dark', isDark)
    }
})
</script>

<template>
    <button
        @click="toggleTheme"
        class="inline-flex items-center justify-center p-2 rounded-md text-muted-foreground hover:text-foreground hover:bg-muted focus:outline-none focus:bg-muted focus:text-foreground transition"
        :title="isDark ? 'Passer au mode clair' : 'Passer au mode sombre'"
    >
        <Icon
            :name="isDark ? 'sun' : 'moon'"
            class="h-5 w-5"
        />
    </button>
</template> 