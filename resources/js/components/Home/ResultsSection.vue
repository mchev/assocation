<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Loading State -->
    <div 
      v-if="isSearching"
      class="min-h-[400px] flex flex-col items-center justify-center"
    >
      <div class="w-16 h-16 mb-4">
        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-primary"></div>
      </div>
      <h3 class="text-lg font-medium text-foreground">Recherche en cours...</h3>
      <p class="mt-2 text-sm text-muted-foreground">
        Nous recherchons les meilleurs équipements pour vous
      </p>
    </div>

    <!-- No Results State -->
    <div 
      v-else-if="!hasResults" 
      class="min-h-[400px] flex flex-col items-center justify-center text-center"
    >
      <div class="w-24 h-24 text-muted-foreground mb-6">
        <SearchX v-if="hasFilters" class="w-full h-full" />
        <PackageSearch v-else class="w-full h-full" />
      </div>
      
      <h3 class="text-xl font-medium text-foreground">
        {{ hasFilters ? 'Aucun équipement trouvé' : 'Commencez votre recherche' }}
      </h3>
      
      <p class="mt-2 text-base text-muted-foreground max-w-md">
        {{ hasFilters 
          ? 'Essayez de modifier vos critères de recherche pour trouver plus de résultats.'
          : 'Utilisez la barre de recherche ci-dessus pour trouver l\'équipement dont vous avez besoin.'
        }}
      </p>

      <div v-if="hasFilters" class="mt-6">
        <Button 
          variant="outline"
          @click="$emit('reset-filters')"
        >
          Réinitialiser les filtres
        </Button>
      </div>
    </div>

    <!-- Results Grid -->
    <div v-else>
      <!-- Results Count -->
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-lg font-medium text-foreground">
          {{ equipments.total }} équipement{{ equipments.total > 1 ? 's' : '' }} trouvé{{ equipments.total > 1 ? 's' : '' }}
        </h2>
        <p class="text-sm text-muted-foreground">
          Page {{ equipments.current_page }} sur {{ equipments.last_page }}
        </p>
      </div>

      <!-- Equipment Grid -->
      <div 
        class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
      >
        <EquipmentCard
          v-for="item in equipments.data"
          :key="item.id"
          :equipment="item"
          :start-date="startDate"
          :end-date="endDate"
          class="transition-all duration-300 ease-in-out"
        />
      </div>

      <!-- Pagination -->
      <div class="mt-12">
        <Pagination :links="equipments.links" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import Pagination from '@/components/Pagination.vue';
import EquipmentCard from './EquipmentCard.vue';
import { Button } from '@/components/ui/button';
import { SearchX, PackageSearch } from 'lucide-vue-next';

const props = defineProps({
  equipments: {
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
  },
  isSearching: {
    type: Boolean,
    default: false
  }
});

const hasResults = computed(() => props.equipments.data.length > 0);
const hasFilters = computed(() => props.startDate || props.endDate);
</script> 