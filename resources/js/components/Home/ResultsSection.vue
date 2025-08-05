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
    </div>

    <!-- Results Grid -->
    <div v-else>
      <!-- Results Count -->
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-lg font-medium text-foreground">
          {{ equipments_pagination.total }} équipement{{ equipments_pagination.total > 1 ? 's' : '' }} trouvé{{ equipments_pagination.total > 1 ? 's' : '' }}
        </h2>
        <div class="flex items-center space-x-4">
          <p class="text-sm text-muted-foreground">
            {{ equipments.length }} affiché{{ equipments.length > 1 ? 's' : '' }} sur {{ equipments_pagination.total }}
          </p>
          <!-- Debug info -->
          <div v-if="equipments_pagination.has_more" class="text-xs text-muted-foreground bg-muted px-2 py-1 rounded">
            Page {{ equipments_pagination.current_page }}
          </div>
        </div>
      </div>

      <!-- Equipment Grid -->
      <div 
        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6"
      >
        <EquipmentCard
          v-for="(item, index) in equipments"
          :key="`${item.id}-${item.updated_at}`"
          :equipment="item"
          :start-date="startDate"
          :end-date="endDate"
          class="transition-all duration-300 ease-in-out animate-fade-in"
          :style="{ animationDelay: `${index * 50}ms` }"
        />
      </div>

      <!-- Infinite Load with WhenVisible -->
      <div class="mt-12 flex justify-center">
        <WhenVisible
          always
          :params="{
            data: {
              page: equipments_pagination.current_page + 1,
            },
            only: ['equipments', 'equipments_pagination'],
          }"
        >
          <template #fallback>
            <div class="flex items-center space-x-2">
              <div class="w-5 h-5 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
              <span class="text-sm text-muted-foreground">Chargement...</span>
            </div>
          </template>
        </WhenVisible>
      </div>

    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { WhenVisible } from '@inertiajs/vue3';
import EquipmentCard from './EquipmentCard.vue';
import { SearchX, PackageSearch } from 'lucide-vue-next';

const props = defineProps({
  equipments: {
    type: Object,
    required: true
  },
  equipments_pagination: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
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

// Computed properties
const hasResults = computed(() => props.equipments.length > 0);

const hasFilters = computed(() => {
  const filters = props.filters;
  return filters.search || 
         (filters.categories && filters.categories.length > 0) || 
         (filters.organizations && filters.organizations.length > 0) || 
         filters.radius || 
         filters.city || 
         filters.postcode;
});

</script>