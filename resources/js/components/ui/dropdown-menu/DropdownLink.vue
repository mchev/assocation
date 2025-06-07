<template>
  <Link
    v-if="as === 'a'"
    :href="href"
    class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
  >
    <slot />
  </Link>

  <button
    v-else
    type="submit"
    class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
  >
    <slot />
  </button>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

defineProps({
  href: {
    type: String,
    required: true
  },
  as: {
    type: String,
    default: 'a'
  }
})
</script> 