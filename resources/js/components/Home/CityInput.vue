<template>
  <div class="relative w-full">
    <Popover v-model:open="showSuggestions">
      <PopoverTrigger asChild>
        <div class="flex items-center border rounded-lg shadow-md bg-background">
          <span class="pl-3 text-muted-foreground">
            <MapPin class="h-5 w-5" />
          </span>
          <input
            v-model="cityQuery"
            type="text"
            class="flex-1 h-12 px-3 bg-transparent focus:outline-none"
            placeholder="Ajouter une localisation"
          />
          <button
            v-if="cityQuery"
            @mousedown.prevent="resetSearch"
            class="pr-3 text-muted-foreground hover:text-foreground"
            tabindex="-1"
          >
            <X class="h-4 w-4" />
          </button>
        </div>
      </PopoverTrigger>
      <PopoverContent class="w-full p-0" align="start">
        <div v-if="isLoading" class="py-6 text-center text-sm text-muted-foreground">
          <div class="flex items-center justify-center">
            <div class="h-4 w-4 animate-spin rounded-full border-2 border-primary border-t-transparent"></div>
            <span class="ml-2">Recherche en cours...</span>
          </div>
        </div>
        <div v-else-if="error" class="py-6 text-center text-sm text-destructive">
          {{ error }}
        </div>
        <div v-else-if="suggestions.length === 0" class="py-6 text-center text-sm text-muted-foreground">
          Aucun résultat trouvé
        </div>
        <div v-else class="max-h-[300px] overflow-y-auto">
          <div
            v-for="suggestion in suggestions"
            :key="suggestion.id"
            class="flex items-center gap-2 px-4 py-2 cursor-pointer hover:bg-accent hover:text-accent-foreground"
            @click="selectSuggestion(suggestion)"
          >
            <MapPin class="h-4 w-4 text-muted-foreground" />
            <div class="flex flex-col">
              <span>{{ suggestion.name }}</span>
              <span class="text-xs text-muted-foreground">{{ suggestion.postcode }}</span>
            </div>
          </div>
        </div>
      </PopoverContent>
    </Popover>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { MapPin, X } from 'lucide-vue-next';
import debounce from 'lodash/debounce';
import axios from 'axios';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['update:modelValue', 'selected']);

const cityQuery = ref(props.modelValue);
const suggestions = ref([]);
const showSuggestions = ref(false);
const isLoading = ref(false);
const error = ref('');
const selectedSuggestion = ref(null);

// Debounced function to fetch city suggestions
const fetchCitySuggestions = debounce(async (query) => {
  if (query.length < 3) {
    suggestions.value = [];
    showSuggestions.value = false;
    error.value = '';
    return;
  }

  isLoading.value = true;
  showSuggestions.value = true;
  error.value = '';

  try {
    const response = await axios.get(
      `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&type=municipality&limit=5`
    );
    
    if (!response.data.features || response.data.features.length === 0) {
      suggestions.value = [];
      return;
    }

    suggestions.value = response.data.features.map(feature => ({
      id: feature.properties.id,
      name: feature.properties.city,
      postcode: feature.properties.postcode,
      coordinates: {
        lat: feature.geometry.coordinates[1],
        lng: feature.geometry.coordinates[0]
      }
    }));
  } catch (error) {
    console.error('Error fetching city suggestions:', error);
    suggestions.value = [];
    error.value = 'Erreur lors de la recherche de villes. Veuillez réessayer.';
  } finally {
    isLoading.value = false;
  }
}, 150);

// Watch for input changes to show suggestions
watch(cityQuery, (newValue) => {
  emit('update:modelValue', newValue);
  if (newValue.length >= 3 && newValue !== selectedSuggestion.value) {
    fetchCitySuggestions(newValue);
  } else {
    suggestions.value = [];
    showSuggestions.value = false;
  }
});

// Handle suggestion selection
const selectSuggestion = (suggestion) => {
  selectedSuggestion.value = suggestion.name + ' (' + suggestion.postcode + ')';
  cityQuery.value = selectedSuggestion.value;
  error.value = '';
  let cityInfos = {
    name: suggestion.name,
    lat: suggestion.coordinates.lat,
    lng: suggestion.coordinates.lng,
    postcode: suggestion.postcode,
    departement: suggestion.postcode.substring(0, 2)
  };
  emit('selected', cityInfos);
  showSuggestions.value = false;
};

// Reset search
const resetSearch = () => {
  cityQuery.value = '';
  suggestions.value = [];
  showSuggestions.value = false;
  error.value = '';
  selectedSuggestion.value = null;
  emit('update:modelValue', '');
  emit('selected', null);
};
</script>