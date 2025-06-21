<template>
  <Link
    :href="route('equipments.show', equipment)"
    class="group relative flex flex-col overflow-hidden rounded-xl border border-border/50 bg-card shadow-sm transition-all duration-200 hover:shadow-lg"
  >
    <div
      class="relative block aspect-[4/3] overflow-hidden"
      :class="{ 'shimmer': hasImage && !imageLoaded }"
    >
      <img
        v-if="hasImage"
        :src="equipment.images[0].url"
        :alt="equipment.name"
        loading="lazy"
        class="h-full w-full object-cover transition-all duration-300 group-hover:scale-105"
        :class="{ 'opacity-0': !imageLoaded }"
        @load="onImageLoad"
      />
      <div
        v-else
        class="flex h-full w-full items-center justify-center bg-accent/10 text-muted-foreground/30"
      >
        <ImageIcon class="h-12 w-12" />
      </div>
    </div>

    <div class="flex flex-grow flex-col p-4">
      <div class="flex-grow">
        <h3 class="line-clamp-1 font-semibold text-foreground transition-colors duration-200 group-hover:text-primary">
          {{ equipment.name }}
        </h3>
        <div class="text-sm">
          <span class="font-semibold text-foreground">{{ formatPrice(equipment.rental_price) }}</span>
          <span class="text-sm text-muted-foreground"> / jour</span>
        </div>

        <div class="mt-2 space-y-2 text-xs text-muted-foreground">
          <span class="truncate">{{ equipment.organization?.name || 'Particulier' }} - {{ equipment.depot?.city || 'Non localisé' }}</span>
        </div>
        <div class="mt-2 flex items-center text-xs text-muted-foreground">
          <Tag class="mr-2 h-4 w-4" />
          <span class="truncate">{{ equipment.category?.name || 'Sans catégorie' }}</span>
        </div>
      </div>
    </div>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import {
  Image as ImageIcon,
  Tag,
} from 'lucide-vue-next'
import { computed, ref } from 'vue'

const props = defineProps({
  equipment: {
    type: Object,
    required: true,
  },
})

const imageLoaded = ref(false)
const hasImage = computed(() => props.equipment.images && props.equipment.images.length > 0)

const onImageLoad = () => {
  imageLoaded.value = true
}

const formatPrice = (price) => {
  if (price === null || price === undefined)
    return ''
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
  }).format(price)
}
</script> 