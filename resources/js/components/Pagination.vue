<template>
  <div v-if="links.length > 3">
    <div class="flex flex-wrap -mb-1">
      <template v-for="(link, key) in links" :key="key">
        <div v-if="link.url === null" 
             class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded">
             {{ link.label }}
        </div>
        <Link v-else
              class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-gray-100 focus:border-primary focus:text-primary"
              :class="{ 'bg-primary text-white': link.active }"
              :href="link.url">
              {{ link.label }}
            </Link>
      </template>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  links: {
    type: Array,
    required: true,
  },
});
</script> 