<template>
  <div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="rounded-lg shadow-lg border border-gray-200">
        <form @submit.prevent="onSearch" class="p-4">
          <div class="flex flex-col sm:flex-row gap-3">
            <!-- Search Input -->
            <div class="flex-1">
              <SearchInput v-model="form.search" />
            </div>

            <!-- Location Input -->
            <div class="flex-1">
              <CityInput 
                v-model="form.city"
                @update:modelValue="handleCity"
              />
            </div>

            <!-- Search Button -->
            <Button
              type="submit"
              variant="default"
              class="h-12"
            >
              <Search class="h-5 w-5 mr-2" />
              Rechercher
            </Button>
          </div>

          <!-- Advanced Filters Toggle -->
          <div class="mt-4 flex items-center justify-between border-t pt-4">
            <Button
              type="button"
              variant="ghost"
              @click="showAdvancedFilters = !showAdvancedFilters"
              class="text-sm group"
            >
              <Filter class="h-5 w-5 mr-1 transition-transform duration-200" :class="{ 'rotate-180': showAdvancedFilters }" />
              {{ showAdvancedFilters ? 'Masquer les filtres' : 'Afficher les filtres' }}
            </Button>
            <Button
              v-if="hasActiveFilters"
              type="button"
              variant="ghost"
              @click="resetFilters"
              class="text-sm text-muted-foreground hover:text-foreground"
            >
              Réinitialiser les filtres
            </Button>
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
              v-show="showAdvancedFilters"
              class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4"
            >
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
                  <SelectTrigger id="category" class="w-full">
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
                  Rayon
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
                  <SelectTrigger id="radius" class="w-full">
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
                  Dates
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
                      class="w-full justify-start text-left font-normal"
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
              <div class="flex items-center space-x-2">
                <Checkbox
                  id="available"
                  v-model="form.available"
                />
                <Label for="available" class="text-sm font-normal">
                  Uniquement les équipements disponibles
                </Label>
              </div>
            </div>
          </Transition>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Search, MapPin, Filter, HelpCircle, Calendar as CalendarIcon } from 'lucide-vue-next';
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
  }
});

const emit = defineEmits(['search']);

const showAdvancedFilters = ref(false);

// Calcul des dates min/max pour le date picker
const today = new Date();
const minDate = today.toISOString().split('T')[0];
const maxDate = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate()).toISOString().split('T')[0];

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
  radius: props.filters.radius || 10,
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

const resetFilters = () => {
  dateRange.value = {
    from: undefined,
    to: undefined
  };
  form.value = {
    search: form.value.search,
    location: form.value.location,
    radius: 10,
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
};

const onSearch = () => {
  emit('search', form.value);
};

// Autocomplete suggestions
const searchSuggestions = ref([]);
const locationSuggestions = ref([]);
const showSearchSuggestions = ref(false);
const showLocationSuggestions = ref(false);

// Mock data for suggestions - replace with actual API calls
const mockSearchSuggestions = [
  'Équipement de construction',
  'Outillage électrique',
  'Matériel de jardinage',
  'Équipement de sécurité',
  'Matériel de bricolage'
];

const mockLocationSuggestions = [
  'Paris',
  'Lyon',
  'Marseille',
  'Bordeaux',
  'Lille'
];

// Watch for input changes to show suggestions
watch(() => form.value.search, (newValue) => {
  if (newValue.length > 2) {
    searchSuggestions.value = mockSearchSuggestions.filter(suggestion =>
      suggestion.toLowerCase().includes(newValue.toLowerCase())
    );
    showSearchSuggestions.value = true;
  } else {
    showSearchSuggestions.value = false;
  }
});

watch(() => form.value.location, (newValue) => {
  if (newValue.length > 2) {
    locationSuggestions.value = mockLocationSuggestions.filter(suggestion =>
      suggestion.toLowerCase().includes(newValue.toLowerCase())
    );
    showLocationSuggestions.value = true;
  } else {
    showLocationSuggestions.value = false;
  }
});

// Handle suggestion selection
const selectSearchSuggestion = (suggestion) => {
  form.value.search = suggestion;
  showSearchSuggestions.value = false;
};

const selectLocationSuggestion = (suggestion) => {
  form.value.location = suggestion;
  showLocationSuggestions.value = false;
};

// Handle city information
const handleCity = (cityInfo) => {
  console.log(cityInfo);
  form.value.city = cityInfo.name;
  form.value.coordinates = {
    lat: cityInfo.lat,
    lng: cityInfo.lng
  };
  form.value.radius = 5;
};
</script> 