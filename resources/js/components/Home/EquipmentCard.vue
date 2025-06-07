<template>
  <div class="group bg-card rounded-xl shadow-sm border border-border/50 overflow-hidden hover:shadow-lg transition-all duration-200">
    <div class="p-6">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-foreground group-hover:text-primary transition-colors duration-200">
          {{ equipment.name }}
        </h3>
        <Badge
          :variant="equipment.is_available ? 'accent' : 'destructive'"
          class="ml-2"
        >
          {{ equipment.is_available ? 'Disponible' : 'Indisponible' }}
        </Badge>
      </div>

      <div class="mt-4 space-y-3">
        <div class="flex items-center text-sm text-muted-foreground">
          <Icon name="map-pin" class="h-4 w-4 mr-2 text-primary/70" />
          {{ equipment.depot?.city || 'Non localisé' }}
        </div>
        <div class="flex items-center text-sm text-muted-foreground">
          <Icon name="tag" class="h-4 w-4 mr-2 text-primary/70" />
          {{ equipment.category?.name || 'Sans catégorie' }}
        </div>
        <div class="flex items-center text-sm text-muted-foreground">
          <Icon name="euro" class="h-4 w-4 mr-2 text-primary/70" />
          {{ equipment.rental_price }}€ / jour
        </div>
        <div v-if="startDate && endDate" class="flex items-center text-sm text-muted-foreground">
          <Icon name="calendar" class="h-4 w-4 mr-2 text-primary/70" />
          Total: {{ calculateTotalPrice }}€
        </div>
      </div>

      <p class="mt-4 text-sm text-muted-foreground line-clamp-2">
        {{ equipment.description }}
      </p>

      <div class="mt-6">
        <Link
          v-if="equipment.organization_id"
          :href="route('equipments.show', { organization: equipment.organization_id, equipment: equipment.id })"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200 shadow-sm"
        >
          Voir les détails
          <Icon name="arrow-right" class="ml-2 h-4 w-4" />
        </Link>
        <span v-else class="text-sm text-muted-foreground">
          Équipement non associé à une organisation
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import Badge from '@/components/ui/badge/Badge.vue';
import Icon from '@/components/ui/icon/Icon.vue';

const props = defineProps({
  equipment: {
    type: Object,
    required: true
  },
  startDate: {
    type: String,
    default: ''
  },
  endDate: {
    type: String,
    default: ''
  }
});

const calculateTotalPrice = computed(() => {
  if (!props.startDate || !props.endDate) {
    return props.equipment.rental_price;
  }

  const start = new Date(props.startDate);
  const end = new Date(props.endDate);
  const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
  
  return (props.equipment.rental_price * days).toFixed(2);
});
</script> 