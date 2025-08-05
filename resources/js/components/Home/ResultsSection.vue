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
          {{ localEquipments.total }} équipement{{ localEquipments.total > 1 ? 's' : '' }} trouvé{{ localEquipments.total > 1 ? 's' : '' }}
        </h2>
        <div class="flex items-center space-x-4">
          <p class="text-sm text-muted-foreground">
            {{ localEquipments.data.length }} affiché{{ localEquipments.data.length > 1 ? 's' : '' }} sur {{ localEquipments.total }}
          </p>
          <!-- Debug info -->
          <div v-if="localEquipments.has_more" class="text-xs text-muted-foreground bg-muted px-2 py-1 rounded">
            Page {{ localEquipments.current_page }}
          </div>
        </div>
      </div>

      <!-- Equipment Grid -->
      <div 
        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6"
      >
        <EquipmentCard
          v-for="(item, index) in localEquipments.data"
          :key="`${item.id}-${item.updated_at}`"
          :equipment="item"
          :start-date="startDate"
          :end-date="endDate"
          class="transition-all duration-300 ease-in-out animate-fade-in"
          :style="{ animationDelay: `${index * 50}ms` }"
        />
      </div>

      <!-- Infinite Load with WhenVisible -->
      <WhenVisible
        v-if="hasMorePages"
        :params="{
          data: {
            page: localEquipments.current_page + 1,
          },
          preserveState: true,
          preserveScroll: true,
          only: ['equipments'],
        }"
        class="mt-12 flex justify-center"
      >
        <template #default="{ loading }">
          <div class="flex flex-col items-center space-y-4">
            <!-- Loading spinner -->
            <div v-if="loading" class="flex items-center space-x-2">
              <div class="w-5 h-5 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
              <span class="text-sm text-muted-foreground">Chargement...</span>
            </div>
            
            <!-- Load more button (fallback) -->
            <Button 
              v-else
              variant="outline"
              :disabled="loading"
              class="px-6"
            >
              <Loader2 v-if="loading" class="w-4 h-4 mr-2 animate-spin" />
              Charger plus d'équipements
            </Button>
          </div>
        </template>
      </WhenVisible>

      <!-- End of results message -->
      <div 
        v-else-if="localEquipments.data.length > 0"
        class="mt-12 text-center"
      >
        <p class="text-sm text-muted-foreground">
          Vous avez vu tous les équipements disponibles
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue';
import { WhenVisible } from '@inertiajs/vue3';
import EquipmentCard from './EquipmentCard.vue';
import { SearchX, PackageSearch, Loader2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

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

// Computed properties
const hasResults = computed(() => props.equipments.data.length > 0);
const hasFilters = computed(() => props.startDate || props.endDate);
const hasMorePages = computed(() => props.equipments.has_more || props.equipments.next_page_url);

// Reactive state
const localEquipments = ref({
  data: [...props.equipments.data],
  current_page: props.equipments.current_page,
  last_page: props.equipments.last_page,
  total: props.equipments.total,
  has_more: props.equipments.has_more,
  next_page_url: props.equipments.next_page_url
});

// Watch for new equipment data and merge
watch(() => props.equipments, (newEquipments) => {
  if (newEquipments?.data && newEquipments.current_page > 1) {
    localEquipments.value.data.push(...newEquipments.data);
    localEquipments.value.current_page = newEquipments.current_page;
    localEquipments.value.has_more = newEquipments.has_more;
    localEquipments.value.next_page_url = newEquipments.next_page_url;
  }
}, { deep: true });

// Debug logging (remove in production)
onMounted(() => {
  if (import.meta.env.DEV) {
    console.log('WhenVisible component available:', !!WhenVisible);
    console.log('Current equipments:', localEquipments.value);
    console.log('Has more pages:', hasMorePages.value);
  }
});
</script>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.5s ease-out forwards;
}
</style> 