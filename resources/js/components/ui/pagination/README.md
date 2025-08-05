# Composants de Pagination

Les composants de pagination ont été traduits en français et optimisés pour l'affichage.

## Utilisation

### Pagination simple avec shadcn/ui

```vue
<template>
  <Pagination v-slot="{ page, setPage, pageCount }">
    <PaginationContent>
      <PaginationItem>
        <PaginationFirst />
      </PaginationItem>
      <PaginationItem>
        <PaginationPrevious />
      </PaginationItem>
      
      <PaginationItem v-for="pageNumber in pageCount" :key="pageNumber">
        <PaginationItem :is-active="page === pageNumber">
          {{ pageNumber }}
        </PaginationItem>
      </PaginationItem>
      
      <PaginationItem>
        <PaginationNext />
      </PaginationItem>
      <PaginationItem>
        <PaginationLast />
      </PaginationItem>
    </PaginationContent>
  </Pagination>
</template>

<script setup>
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationFirst,
  PaginationItem,
  PaginationLast,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination'
</script>
```

### Pagination avec Laravel (recommandé)

```vue
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
```

## Composants disponibles

- `Pagination` - Composant racine
- `PaginationContent` - Conteneur des éléments de pagination
- `PaginationEllipsis` - Points de suspension (...)
- `PaginationFirst` - Bouton "Premier"
- `PaginationItem` - Élément de pagination
- `PaginationLast` - Bouton "Dernier"
- `PaginationNext` - Bouton "Suivant"
- `PaginationPrevious` - Bouton "Précédent"

## Traductions

Tous les textes sont maintenant en français :
- "Next" → "Suivant"
- "Previous" → "Précédent"
- "First" → "Premier"
- "Last" → "Dernier"
- "More pages" → "Plus de pages" 