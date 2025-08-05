<template>
  <div v-if="links.length > 3">
    <div class="flex flex-wrap -mb-1">
      <template v-for="(link, key) in links" :key="key">
        <div v-if="link.url === null" 
             class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded">
             {{ translateLabel(link.label) }}
        </div>
        <Link v-else
              class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-gray-100 focus:border-primary focus:text-primary transition-colors"
              :class="{ 'bg-primary text-white hover:bg-primary/90': link.active }"
              :href="link.url">
              {{ translateLabel(link.label) }}
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

const translateLabel = (label) => {
  const translations = {
    '&laquo; Previous': '&laquo; Précédent',
    'Next &raquo;': 'Suivant &raquo;',
    'Previous': 'Précédent',
    'Next': 'Suivant',
    'First': 'Premier',
    'Last': 'Dernier',
    '« Previous': '« Précédent',
    'Next »': 'Suivant »',
    '‹ Previous': '‹ Précédent',
    'Next ›': 'Suivant ›',
  };
  
  return translations[label] || label;
};
</script> 