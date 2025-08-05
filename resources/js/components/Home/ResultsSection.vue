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
          {{ equipments.total }} équipement{{ equipments.total > 1 ? 's' : '' }} trouvé{{ equipments.total > 1 ? 's' : '' }}
        </h2>
        <div class="flex items-center space-x-4">
          <p class="text-sm text-muted-foreground">
            {{ equipments.data.length }} affiché{{ equipments.data.length > 1 ? 's' : '' }} sur {{ equipments.total }}
          </p>
          <!-- Debug info -->
          <div v-if="equipments.has_more" class="text-xs text-muted-foreground bg-muted px-2 py-1 rounded">
            Page {{ equipments.current_page }}
          </div>
        </div>
      </div>

      <!-- Equipment Grid -->
      <div 
        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6"
      >
        <EquipmentCard
          v-for="(item, index) in equipments.data"
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
        v-if="equipments.has_more || equipments.next_page_url"
        :href="route('home')"
        :params="loadMoreParams"
        :method="'get'"
        :preserve-state="true"
        :preserve-scroll="true"
        :only="['equipments']"
        @before="onBeforeLoad"
        @success="onLoadSuccess"
        @error="onLoadError"
        class="mt-12 flex justify-center"
      >
        <template #default="{ loading }">
          <div class="flex flex-col items-center space-y-4">
            <!-- Success message -->
            <div v-if="showSuccessMessage" class="text-sm text-green-600 bg-green-50 px-4 py-2 rounded-md border border-green-200">
              ✓ Nouveaux équipements chargés avec succès
            </div>
            
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
        v-else-if="equipments.data.length > 0"
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
import { computed, ref, onMounted } from 'vue';
import { WhenVisible } from '@inertiajs/vue3';
import EquipmentCard from './EquipmentCard.vue';
import { SearchX, PackageSearch, Loader2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

// Debug: Vérifier que WhenVisible est bien importé
onMounted(() => {
  console.log('WhenVisible component available:', !!WhenVisible);
  console.log('Current equipments:', props.equipments);
  console.log('Has more pages:', props.equipments.has_more || props.equipments.next_page_url);
});

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
const showSuccessMessage = ref(false);

// Paramètres pour charger plus d'équipements
const loadMoreParams = computed(() => {
  const nextPage = props.equipments.current_page + 1;
  return {
    page: nextPage,
    ...(props.startDate && { start_date: props.startDate }),
    ...(props.endDate && { end_date: props.endDate })
  };
});

// Callbacks pour WhenVisible
const onBeforeLoad = () => {
  console.log('WhenVisible: Chargement en cours...');
};

const onLoadSuccess = (event) => {
  console.log('WhenVisible: Chargement réussi:', event);
  showSuccessMessage.value = true;
  setTimeout(() => {
    showSuccessMessage.value = false;
  }, 2000);
};

const onLoadError = (error) => {
  console.error('WhenVisible: Erreur lors du chargement:', error);
};
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