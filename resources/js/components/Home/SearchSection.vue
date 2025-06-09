<template>
  <div class="relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Search Bar Container -->
      <div class="relative rounded-xl shadow-xl border border-gray-200/60">
        <!-- Search Header -->
        <div class="px-6 pt-6 pb-4">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Trouvez le matériel dont vous avez besoin</h2>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Recherchez parmi des milliers d'équipements disponibles près de chez vous.</p>
        </div>

        <!-- Main Search Bar -->
        <div class="px-6 pb-6">
          <div class="flex flex-col md:flex-row gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200/60 dark:bg-gray-800 dark:border-gray-700">
            <!-- Search Input -->
            <div class="flex-1">
              <Label class="text-sm font-medium mb-2 text-gray-700 dark:text-white">Que recherchez-vous ?</Label>
              <SearchInput 
                v-model="form.search" 
                placeholder="Ex: Vidéoprojecteur, Enceinte, Table..." 
              />
            </div>

            <!-- Location Input -->
            <div class="flex-1">
              <Label class="text-sm font-medium mb-2 text-gray-700 dark:text-white">Où ?</Label>
              <CityInput 
                v-model="form.city"
                @update:modelValue="handleCity"
                placeholder="Entrez une ville ou un code postal"
              />
            </div>

            <!-- Search Button -->
            <div class="flex items-end">
              <Button
                type="submit"
                size="lg"
                :disabled="isSearching"
                class="w-full md:w-auto min-w-[140px] bg-primary hover:bg-primary/90 shadow-sm dark:bg-primary dark:hover:bg-primary/90"
                @click="onSearch"
              >
                <Search v-if="!isSearching" class="h-5 w-5 mr-2" />
                <span v-else class="h-5 w-5 mr-2">
                  <span class="animate-spin inline-block h-5 w-5 border-[3px] border-current border-t-transparent rounded-full" />
                </span>
                {{ isSearching ? 'Recherche...' : 'Rechercher' }}
              </Button>
            </div>
          </div>

          <!-- Quick Filters -->
          <div class="mt-4 flex items-center gap-4">
            <!-- Advanced Filters Toggle -->
            <Button
              type="button"
              variant="outline"
              size="sm"
              @click="showAdvancedFilters = !showAdvancedFilters"
              class="text-sm group hover:border-primary hover:text-primary dark:hover:border-primary dark:hover:text-primary"
            >
              <Filter 
                class="h-4 w-4 mr-1.5 transition-transform duration-200" 
                :class="{ 'rotate-180': showAdvancedFilters }" 
              />
              {{ showAdvancedFilters ? 'Masquer les filtres' : 'Filtres avancés' }}
              <span 
                v-if="activeFiltersCount" 
                class="ml-2 bg-primary/10 text-primary px-2 py-0.5 rounded-full text-xs dark:bg-primary/10 dark:text-primary"
              >
                {{ activeFiltersCount }}
              </span>
            </Button>

            <!-- Popular Categories -->
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
              <span class="hidden sm:inline">Populaire :</span>
              <div class="flex flex-wrap gap-2">
                <Button
                  v-for="category in popularCategories"
                  :key="category.id"
                  variant="ghost"
                  size="sm"
                  class="text-xs hover:text-primary dark:hover:text-primary"
                  @click="selectCategory(category)"
                >
                  {{ category.name }}
                </Button>
              </div>
            </div>
          </div>
        </div>

        <!-- Advanced Filters -->
        <Transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="transform -translate-y-2 opacity-0"
          enter-to-class="transform translate-y-0 opacity-100"
          leave-active-class="transition duration-200 ease-in"
          leave-from-class="transform translate-y-0 opacity-100"
          leave-to-class="transform -translate-y-2 opacity-0"
        >
          <div
            v-if="showAdvancedFilters"
            class="px-6 pb-6"
          >
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200/60 dark:bg-gray-800 dark:border-gray-700">
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Category Select -->
                <div class="space-y-2">
                  <Label for="category" class="flex items-center gap-2">
                    Catégorie
                    <TooltipProvider>
                      <Tooltip>
                        <TooltipTrigger>
                          <HelpCircle class="h-4 w-4 text-muted-foreground" />
                        </TooltipTrigger>
                        <TooltipContent>
                          <p>Filtrer par type d'équipement</p>
                        </TooltipContent>
                      </Tooltip>
                    </TooltipProvider>
                  </Label>
                  <Select v-model="form.category">
                    <SelectTrigger id="category" class="w-full bg-white dark:bg-gray-800">
                      <SelectValue placeholder="Toutes les catégories" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">Toutes les catégories</SelectItem>
                      <SelectItem v-for="category in stats.categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <!-- Radius Select -->
                <div class="space-y-2">
                  <Label for="radius" class="flex items-center gap-2">
                    Rayon de recherche
                    <TooltipProvider>
                      <Tooltip>
                        <TooltipTrigger>
                          <HelpCircle class="h-4 w-4 text-muted-foreground" />
                        </TooltipTrigger>
                        <TooltipContent>
                          <p>Distance maximale de recherche</p>
                        </TooltipContent>
                      </Tooltip>
                    </TooltipProvider>
                  </Label>
                  <Select v-model="form.radius">
                    <SelectTrigger id="radius" class="w-full bg-white dark:bg-gray-800">
                      <SelectValue placeholder="Sélectionner un rayon" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="5">5 km</SelectItem>
                      <SelectItem value="10">10 km</SelectItem>
                      <SelectItem value="25">25 km</SelectItem>
                      <SelectItem value="50">50 km</SelectItem>
                      <SelectItem value="100">100 km</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <!-- Date Range -->
                <div class="space-y-2">
                  <Label class="flex items-center gap-2">
                    Période de location
                    <TooltipProvider>
                      <Tooltip>
                        <TooltipTrigger>
                          <HelpCircle class="h-4 w-4 text-muted-foreground" />
                        </TooltipTrigger>
                        <TooltipContent>
                          <p>Période de location souhaitée</p>
                        </TooltipContent>
                      </Tooltip>
                    </TooltipProvider>
                  </Label>
                  <Popover>
                    <PopoverTrigger asChild>
                      <Button
                        variant="outline"
                        class="w-full justify-start text-left font-normal bg-white dark:bg-gray-800"
                        :class="{ 'text-muted-foreground': !dateRange.from }"
                      >
                        <CalendarIcon class="mr-2 h-4 w-4" />
                        <span v-if="dateRange.from">
                          {{ format(dateRange.from, "LLL dd, y") }}
                          <span v-if="dateRange.to">
                            - {{ format(dateRange.to, "LLL dd, y") }}
                          </span>
                        </span>
                        <span v-else>Sélectionner une période</span>
                      </Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-auto p-0" align="start">
                      <Calendar
                        v-model:selected="dateRange"
                        mode="range"
                        :min="minDate"
                        :max="maxDate"
                        :numberOfMonths="2"
                        initialFocus
                      />
                    </PopoverContent>
                  </Popover>
                </div>

                <!-- Availability Checkbox -->
                <div class="flex items-start space-x-3 pt-8">
                  <Checkbox
                    id="available"
                    v-model="form.available"
                  />
                  <div class="grid gap-1.5 leading-none">
                    <Label 
                      for="available" 
                      class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                    >
                      Disponible uniquement
                    </Label>
                    <p class="text-xs text-muted-foreground">
                      Afficher uniquement les équipements disponibles
                    </p>
                  </div>
                </div>
              </div>

              <!-- Reset Filters Button -->
              <div v-if="activeFiltersCount > 0" class="mt-6 flex justify-end">
                <Button 
                  variant="outline"
                  size="sm"
                  @click="resetFilters"
                  class="text-sm hover:text-destructive hover:border-destructive dark:hover:text-destructive dark:hover:border-destructive"
                >
                  <X class="h-4 w-4 mr-1.5" />
                  Réinitialiser les filtres
                </Button>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Search, Filter, HelpCircle, Calendar as CalendarIcon, X } from 'lucide-vue-next';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import SearchInput from './SearchInput.vue';
import CityInput from './CityInput.vue';

const props = defineProps({
  filters: {
    type: Object,
    default: () => ({})
  },
  stats: {
    type: Object,
    required: true
  },
  isSearching: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['search']);

const showAdvancedFilters = ref(false);

// Date range setup
const today = new Date();
const minDate = today;
const maxDate = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate());

const dateRange = ref({
  from: props.filters.start_date ? new Date(props.filters.start_date) : undefined,
  to: props.filters.end_date ? new Date(props.filters.end_date) : undefined
});

// Watch for date range changes and update form values
watch(dateRange, (newRange) => {
  form.value.start_date = newRange.from ? newRange.from.toISOString().split('T')[0] : '';
  form.value.end_date = newRange.to ? newRange.to.toISOString().split('T')[0] : '';
}, { deep: true });

const form = ref({
  search: props.filters.search || '',
  location: props.filters.location || '',
  radius: props.filters.radius || 50,
  category: props.filters.category || 'all',
  available: props.filters.available || false,
  min_price: props.filters.min_price || props.stats.min_price,
  max_price: props.filters.max_price || props.stats.max_price,
  start_date: props.filters.start_date || '',
  end_date: props.filters.end_date || '',
  coordinates: props.filters.coordinates || null,
  city: props.filters.city || null
});

const hasActiveFilters = computed(() => {
  return form.value.category !== 'all' || 
         form.value.radius !== 10 || 
         form.value.min_price || 
         form.value.max_price || 
         form.value.start_date || 
         form.value.end_date || 
         form.value.available;
});

const activeFiltersCount = computed(() => {
  let count = 0;
  if (form.value.category !== 'all') count++;
  if (form.value.radius !== 50) count++;
  if (form.value.min_price) count++;
  if (form.value.max_price) count++;
  if (form.value.start_date) count++;
  if (form.value.available) count++;
  if (form.value.search) count++;
  if (form.value.city) count++;
  return count;
});

// Popular categories for quick access
const popularCategories = computed(() => {
  return props.stats.categories.slice(0, 4); // Show top 4 categories
});

const selectCategory = (category) => {
  form.value.category = category.id;
  onSearch();
};

// Load filters from localStorage
const loadFiltersFromStorage = () => {
  const savedFilters = localStorage.getItem('search_filters');
  if (savedFilters) {
    try {
      const parsedFilters = JSON.parse(savedFilters);
      // Convert date strings back to Date objects if they exist
      if (parsedFilters.start_date) {
        dateRange.value.from = new Date(parsedFilters.start_date);
      }
      if (parsedFilters.end_date) {
        dateRange.value.to = new Date(parsedFilters.end_date);
      }
      // Merge saved filters with default values
      form.value = {
        ...form.value,
        ...parsedFilters
      };
    } catch (e) {
      console.error('Error loading filters from localStorage:', e);
    }
  }
};

// Save filters to localStorage
const saveFiltersToStorage = (filters) => {
  try {
    localStorage.setItem('search_filters', JSON.stringify(filters));
  } catch (e) {
    console.error('Error saving filters to localStorage:', e);
  }
};

// Watch for changes in form values and save to localStorage
watch(form, (newValue) => {
  saveFiltersToStorage(newValue);
}, { deep: true });

// Load saved filters on component mount
onMounted(() => {
  loadFiltersFromStorage();
});

const resetFilters = () => {
  dateRange.value = {
    from: undefined,
    to: undefined
  };
  form.value = {
    search: form.value.search,
    location: form.value.location,
    radius: 50,
    category: 'all',
    available: false,
    min_price: props.stats.min_price,
    max_price: props.stats.max_price,
    start_date: '',
    end_date: '',
    coordinates: null,
    city: null,
    departement: null
  };
  // Clear localStorage when resetting filters
  localStorage.removeItem('search_filters');
  emit('search', form.value);
};

const onSearch = () => {
  emit('search', form.value);
};

// Handle city information
const handleCity = (cityInfo) => {
  form.value.city = cityInfo.name;
  form.value.coordinates = {
    lat: cityInfo.lat,
    lng: cityInfo.lng
  };
  form.value.radius = form.value.radius || 5;
};
</script> 