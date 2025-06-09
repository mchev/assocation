<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  href: {
    type: String,
    required: true
  },
  active: {
    type: Boolean,
    default: false
  },
  icon: {
    type: [Object, Function],
    default: null
  },
  external: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'sm',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  }
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
    class="group inline-flex items-center gap-2 px-1 pt-1 font-medium leading-5 transition duration-150 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-primary-500"
    :class="[
      classes,
      active
        ? 'opacity-100'
        : 'opacity-70 hover:opacity-100'
    ]"
  >
    <component
      v-if="icon"
      :is="icon"
      class="size-4"
      :class="active ? 'text-primary' : 'text-muted-foreground group-hover:text-foreground'"
    />
    <slot />
  </Link>

  <a
    v-else
    :href="href"
    target="_blank"
    rel="noopener noreferrer"
    class="group inline-flex items-center gap-2 px-1 pt-1 font-medium leading-5 transition duration-150 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-primary-500"
    :class="[
      classes,
      active
        ? 'opacity-100'
        : 'opacity-70 hover:opacity-100'
    ]"
  >
    <component
      v-if="icon"
      :is="icon"
      class="size-4"
      :class="active ? 'text-primary' : 'text-muted-foreground group-hover:text-foreground'"
    />
    <slot />
  </a>
</template> 