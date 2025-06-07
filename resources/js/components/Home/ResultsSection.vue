<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div v-if="equipment.data.length === 0" class="text-center py-12">
      <div class="mx-auto w-24 h-24 text-muted-foreground">
        <Icon name="search-x" class="w-full h-full" />
      </div>
      <h3 class="mt-4 text-lg font-medium text-foreground">Aucun équipement trouvé</h3>
      <p class="mt-2 text-sm text-muted-foreground">
        Essayez de modifier vos critères de recherche.
      </p>
    </div>

    <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <EquipmentCard
        v-for="item in equipment.data"
        :key="item.id"
        :equipment="item"
        :start-date="startDate"
        :end-date="endDate"
      />
    </div>

    <!-- Pagination -->
    <div class="mt-12">
      <Pagination :links="equipment.links" />
    </div>
  </div>
</template>

<script setup>
import Icon from '@/components/ui/icon/Icon.vue';
import Pagination from '@/components/Pagination.vue';
import EquipmentCard from './EquipmentCard.vue';

defineProps({
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
</script> 