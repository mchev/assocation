<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

interface Props {
  href: string
  active?: boolean
  icon?: any
  external?: boolean
  size?: 'sm' | 'md' | 'lg'
}

const props = withDefaults(defineProps<Props>(), {
  active: false,
  external: false,
  size: 'md'
})

const classes = computed(() => ({
  sm: 'text-sm',
  md: 'text-base',
  lg: 'text-lg'
})[props.size])
</script>

<template>
  <Link
    v-if="!external"
    :href="href"
    class="group inline-flex items-center gap-2 px-1 pt-1 border-b-2 font-medium leading-5 transition duration-150 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-primary-500"
    :class="[
      classes,
      active
        ? 'border-primary-500 text-primary-700'
        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
    ]"
  >
    <component
      v-if="icon"
      :is="icon"
      class="h-4 w-4"
      :class="active ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500'"
    />
    <slot />
  </Link>

  <a
    v-else
    :href="href"
    target="_blank"
    rel="noopener noreferrer"
    class="group inline-flex items-center gap-2 px-1 pt-1 border-b-2 font-medium leading-5 transition duration-150 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-primary-500"
    :class="[
      classes,
      active
        ? 'border-primary-500 text-primary-700'
        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
    ]"
  >
    <component
      v-if="icon"
      :is="icon"
      class="h-4 w-4"
      :class="active ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500'"
    />
    <slot />
  </a>
</template> 