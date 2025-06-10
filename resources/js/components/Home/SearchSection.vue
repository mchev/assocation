<template>
  <div :class="{ 'relative mt-8': user }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Search Bar Container -->
      <div class="relative rounded-xl shadow-xl border border-gray-200/60">
        <!-- Search Header -->
        <div v-if="!user" class="px-6 pt-6 pb-4">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Trouvez le matériel dont vous avez besoin</h2>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Recherchez parmi des milliers d'équipements disponibles près de chez vous.</p>
        </div>

        <!-- Main Search Bar -->
        <div class="px-6 pb-6">
          <div class="flex flex-col md:flex-row gap-4 p-4">
            
            <!-- Location Input -->
            <div class="flex-1">
              <Label class="text-sm font-medium mb-2 text-gray-700 dark:text-white">Dans quel secteur ?</Label>
              <CityInput 
                v-model="form.city"
                @update:modelValue="handleCity"
                placeholder="Entrez une ville ou un code postal"
              />
            </div>
            
            <!-- Search Input -->
            <div class="flex-1">
              <Label class="text-sm font-medium mb-2 text-gray-700 dark:text-white">Que recherchez-vous ?</Label>
              <SearchInput 
                v-model="form.search" 
                placeholder="Ex: Vidéoprojecteur, Enceinte, Table..." 
                @search:submit="handleSearch"
              />
            </div>



            <!-- Search Button -->
            <div class="flex items-end">
              <Button
                type="submit"
                size="lg"
                :disabled="isSearching"
                class="w-full md:w-auto min-w-[140px] bg-primary hover:bg-primary/90 shadow-sm dark:bg-primary dark:hover:bg-primary/90"
                @click="handleSearch"
              >
                <Search v-if="!isSearching" class="h-5 w-5 mr-2" />
                <span v-else class="h-5 w-5 mr-2">
                  <span class="animate-spin inline-block h-5 w-5 border-[3px] border-current border-t-transparent rounded-full" />
                </span>
                {{ isSearching ? 'Recherche...' : 'Rechercher' }}
              </Button>
            </div>
          </div>
        </div>

          <div class="px-6 pb-6">
            <div class="">
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Category Select -->
                <!-- <CategoryTreeSelect
                  v-model="form.category"
                  :categories="stats.categories"
                  @update:modelValue="handleSearch"
                /> -->

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
                  <Select v-model="form.radius" @update:modelValue="handleSearch">
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
                  <DateRangePicker v-model="dateRange" @update:modelValue="handleSearch" />
                </div>
                <div class="flex justify-end items-end">
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
          </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { Search, HelpCircle, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import CategoryTreeSelect from '@/components/CategoryTreeSelect.vue';
import { parseDate, getLocalTimeZone } from '@internationalized/date';
import SearchInput from './SearchInput.vue';
import CityInput from './CityInput.vue';
import DateRangePicker from '@/components/DateRangePicker.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth.user;

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

const emit = defineEmits(['searching']);

const isSearching = ref(false);

const dateRange = ref({
  from: props.filters.start_date ? parseDate(props.filters.start_date) : undefined,
  to: props.filters.end_date ? parseDate(props.filters.end_date) : undefined
});

// Watch for date range changes and update form values
watch(dateRange, (newRange) => {
  form.start_date = newRange.from ? newRange.from.toDate(getLocalTimeZone()).toISOString().split('T')[0] : '';
  form.end_date = newRange.to ? newRange.to.toDate(getLocalTimeZone()).toISOString().split('T')[0] : '';
}, { deep: true });

const form = useForm({
  search: props.filters.search || '',
  radius: props.filters.radius || 50,
  category: props.filters.category || 'all',
  start_date: props.filters.start_date || '',
  end_date: props.filters.end_date || '',
  coordinates: props.filters.coordinates || null,
  city: props.filters.city || null,
  postcode: props.filters.postcode || null
});

const handleSearch = () => {
  isSearching.value = true;
  emit('searching', isSearching.value);
  form.get(route('home'), {
    only: ['filters', 'equipments'],
    preserveScroll: true,
    preserveState: true,
    onFinish: () => {
      isSearching.value = false;
      emit('searching', isSearching.value);
    }
  });
};

// Load filters from localStorage
const loadFiltersFromStorage = () => {
  const savedFilters = localStorage.getItem('search_filters');
  if (savedFilters) {
    try {
      const parsedFilters = JSON.parse(savedFilters);
      // Only update city, coordinates, and radius
      form.city = parsedFilters.city || null;
      form.coordinates = parsedFilters.coordinates || null;
      form.radius = parsedFilters.radius || 50;
      form.postcode = parsedFilters.postcode || null;
    } catch (e) {
      console.error('Error loading filters from localStorage:', e);
    }
  }
};

// Save filters to localStorage
const saveFiltersToStorage = (filters) => {
  try {
    const filtersToSave = {
      city: filters.city,
      coordinates: filters.coordinates,
      radius: filters.radius,
      postcode: filters.postcode
    };
    localStorage.setItem('search_filters', JSON.stringify(filtersToSave));
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
  form.reset({
    search: '',
    radius: 50,
    category: 'all',
    start_date: '',
    end_date: '',
    coordinates: null,
    city: null,
    postcode: null
  });
  // Clear localStorage when resetting filters
  localStorage.removeItem('search_filters');
  handleSearch();
};

// Handle city information
const handleCity = (cityInfo) => {
  form.city = cityInfo.name;
  form.postcode = cityInfo.postcode;
  form.coordinates = {
    lat: cityInfo.lat,
    lng: cityInfo.lng
  };
  form.radius = form.radius || 5;
};
</script> 