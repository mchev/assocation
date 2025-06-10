<template>
    <div class="relative w-full" data-address-input>
        <div class="relative">
            <Input
                v-model="addressQuery"
                type="text"
                :placeholder="placeholder"
                :disabled="isLoading"
                :aria-expanded="showSuggestions"
                :aria-controls="dropdownId"
                :aria-describedby="error ? `${dropdownId}-error` : undefined"
                role="combobox"
                @focus="showSuggestions = true"
                @keydown.down.prevent="navigateSuggestions('next')"
                @keydown.up.prevent="navigateSuggestions('prev')"
                @keydown.enter.prevent="handleEnterKey"
                @keydown.esc="handleEscape"
            >
                <template #prefix>
                    <MapPin class="h-5 w-5 text-muted-foreground" aria-hidden="true" />
                </template>
                <template #suffix>
                    <div class="flex items-center gap-2">
                        <button
                            v-if="addressQuery"
                            @click.prevent="resetSearch"
                            class="text-muted-foreground hover:text-foreground"
                            aria-label="Effacer la recherche"
                        >
                            <X class="h-4 w-4" />
                        </button>
                        <Loader v-if="isLoading" class="h-4 w-4 animate-spin" aria-hidden="true" />
                    </div>
                </template>
            </Input>
        </div>

        <!-- Suggestions Dropdown -->
        <div 
            v-show="showSuggestions && (isLoading || error || suggestions.length > 0)"
            :id="dropdownId"
            class="absolute z-50 w-full mt-1 bg-background rounded-md border shadow-md"
            role="listbox"
        >
            <div v-if="isLoading" class="py-6 text-center text-sm text-muted-foreground">
                <div class="flex items-center justify-center">
                    <Loader class="h-4 w-4 animate-spin" aria-hidden="true" />
                    <span class="ml-2">Recherche en cours...</span>
                </div>
            </div>
            <div 
                v-else-if="error" 
                :id="`${dropdownId}-error`"
                class="py-6 text-center text-sm text-destructive"
            >
                {{ error }}
            </div>
            <div 
                v-else-if="suggestions.length === 0 && addressQuery.length >= minQueryLength" 
                class="py-6 text-center text-sm text-muted-foreground"
            >
                Aucun résultat trouvé
            </div>
            <div v-else-if="suggestions.length > 0" class="max-h-[300px] overflow-y-auto">
                <div
                    v-for="(suggestion, index) in suggestions"
                    :key="suggestion.id"
                    class="flex items-center gap-2 px-4 py-2 cursor-pointer"
                    :class="[
                        'hover:bg-accent hover:text-accent-foreground',
                        { 'bg-accent text-accent-foreground': selectedIndex === index }
                    ]"
                    role="option"
                    :aria-selected="selectedIndex === index"
                    @mousedown.prevent="selectSuggestion(suggestion)"
                    @mouseover="selectedIndex = index"
                >
                    <MapPin class="h-4 w-4 text-muted-foreground" aria-hidden="true" />
                    <div class="flex flex-col gap-0.5">
                        <span class="font-medium">{{ suggestion.name }}</span>
                        <span class="text-sm text-muted-foreground">{{ suggestion.city }}, {{ suggestion.postcode }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, computed } from 'vue';
import { Input } from '@/components/ui/input';
import { MapPin, X, Loader } from 'lucide-vue-next';
import debounce from 'lodash/debounce';
import axios from 'axios';

// Props and emits
const props = defineProps({
    modelValue: {
        type: [String, Object],
        default: null
    },
    placeholder: {
        type: String,
        default: 'Adresse'
    },
    minQueryLength: {
        type: Number,
        default: 3
    },
    debounceTime: {
        type: Number,
        default: 300
    }
});

const emit = defineEmits(['update:modelValue', 'error']);

// State
const addressQuery = ref('');
const suggestions = ref([]);
const showSuggestions = ref(false);
const isLoading = ref(false);
const error = ref('');
const selectedIndex = ref(-1);
const dropdownId = computed(() => `address-dropdown-${Math.random().toString(36).substr(2, 9)}`);

// Initialize component
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    initializeAddress();
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Methods
function initializeAddress() {
    if (!props.modelValue) return;
    
    addressQuery.value = typeof props.modelValue === 'string' 
        ? props.modelValue 
        : props.modelValue.name || '';
}

function handleClickOutside(event) {
    const container = event.target.closest('[data-address-input]');
    if (!container) {
        showSuggestions.value = false;
        selectedIndex.value = -1;
    }
}

function navigateSuggestions(direction) {
    if (!suggestions.value.length) return;
    
    if (direction === 'next') {
        selectedIndex.value = selectedIndex.value < suggestions.value.length - 1 
            ? selectedIndex.value + 1 
            : 0;
    } else {
        selectedIndex.value = selectedIndex.value > 0 
            ? selectedIndex.value - 1 
            : suggestions.value.length - 1;
    }
}

function handleEnterKey() {
    if (selectedIndex.value >= 0 && suggestions.value[selectedIndex.value]) {
        selectSuggestion(suggestions.value[selectedIndex.value]);
    }
}

function handleEscape() {
    showSuggestions.value = false;
    selectedIndex.value = -1;
}

const fetchAddressSuggestions = debounce(async (query) => {
    if (query.length < props.minQueryLength) {
        suggestions.value = [];
        return;
    }

    isLoading.value = true;
    error.value = '';

    try {
        const response = await axios.get(
            `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=5`,
            { timeout: 5000 }
        );
        
        suggestions.value = response.data.features?.length 
            ? response.data.features.map(feature => ({
                id: feature.properties.id,
                name: feature.properties.name,
                housenumber: feature.properties.housenumber,
                street: feature.properties.street,
                city: feature.properties.city,
                postcode: feature.properties.postcode,
                context: feature.properties.context,
                type: feature.properties.type,
                coordinates: {
                    lat: feature.geometry.coordinates[1],
                    lng: feature.geometry.coordinates[0]
                }
            }))
            : [];
    } catch (err) {
        console.error('Error fetching address suggestions:', err);
        error.value = 'Une erreur est survenue lors de la recherche. Veuillez réessayer.';
        emit('error', error.value);
        suggestions.value = [];
    } finally {
        isLoading.value = false;
    }
}, props.debounceTime);

function selectSuggestion(suggestion) {
    const addressParts = [
        suggestion.housenumber,
        suggestion.street
    ].filter(Boolean);
    
    addressQuery.value = addressParts.join(' ');
    showSuggestions.value = false;
    error.value = '';
    selectedIndex.value = -1;
    
    emit('update:modelValue', {
        name: addressParts.join(' '),
        street: suggestion.street,
        housenumber: suggestion.housenumber,
        city: suggestion.city,
        postcode: suggestion.postcode,
        departement: suggestion.postcode.substring(0, 2),
        context: suggestion.context,
        type: suggestion.type,
        lat: suggestion.coordinates.lat,
        lng: suggestion.coordinates.lng
    });
}

function resetSearch() {
    addressQuery.value = '';
    suggestions.value = [];
    showSuggestions.value = false;
    error.value = '';
    emit('update:modelValue', null);
}

// Watchers
watch(() => props.modelValue, initializeAddress);

watch(addressQuery, (newValue) => {
    if (newValue.length >= props.minQueryLength) {
        fetchAddressSuggestions(newValue);
    } else {
        suggestions.value = [];
    }
});
</script>