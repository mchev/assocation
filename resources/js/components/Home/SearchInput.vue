<template>
  <div class="relative w-full">
    <div class="flex items-center border rounded-lg shadow-md bg-background">
      <span class="pl-3 text-muted-foreground">
        <Search class="h-5 w-5" />
      </span>
      <input
        ref="inputRef"
        v-model="searchQuery"
        type="text"
        class="flex-1 h-12 px-3 bg-transparent focus:outline-none"
        placeholder="Que recherchez-vous ?"
        @focus="onFocus"
        @blur="onBlur"
        @keydown.down.prevent="onArrowDown"
        @keydown.up.prevent="onArrowUp"
        @keydown.enter.prevent="onEnter"
        autocomplete="off"
      />
      <button
        v-if="searchQuery"
        @mousedown.prevent="resetSearch"
        class="pr-3 text-muted-foreground hover:text-foreground"
        tabindex="-1"
      >
        <X class="h-4 w-4" />
      </button>
    </div>
    <ul
      v-show="showSuggestions && (suggestions.length > 0 || isLoading || hasMorePages)"
      class="absolute left-0 right-0 mt-1 z-50 bg-popover border rounded-lg shadow-md max-h-72 overflow-auto"
      @mousedown.prevent
    >
      <li v-if="isLoading" class="py-3 px-4 text-sm text-muted-foreground flex items-center">
        <span class="loader mr-2"></span> Recherche en cours...
      </li>
      <li
        v-for="(suggestion, idx) in suggestions"
        :key="suggestion.id"
        :class="[
          'px-4 py-2 cursor-pointer flex items-center gap-2',
          idx === highlightedIndex ? 'bg-accent text-accent-foreground' : 'hover:bg-accent hover:text-accent-foreground'
        ]"
        @mousedown.prevent="selectSuggestion(suggestion)"
        @mouseenter="highlightedIndex = idx"
      >
        <component :is="suggestion.icon" class="h-4 w-4 text-muted-foreground" />
        <div class="flex flex-col">
          <span>{{ suggestion.name }}</span>
          <span class="text-xs text-muted-foreground">{{ suggestion.category?.name }}</span>
        </div>
      </li>
      <li v-if="!isLoading && suggestions.length === 0 && searchQuery.length >= 3" class="px-4 py-2 text-sm text-muted-foreground">
        Aucun résultat trouvé
      </li>
      <li v-if="hasMorePages && !isLoading" class="border-t">
        <button
          @mousedown.prevent="loadMore"
          class="w-full px-4 py-2 text-left text-sm text-muted-foreground hover:bg-accent hover:text-accent-foreground"
          :disabled="isLoadingMore"
        >
          <span v-if="isLoadingMore">Chargement...</span>
          <span v-else>Charger plus de résultats</span>
        </button>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { Search, Wrench, Leaf, Shield, Hammer, Drill, X } from 'lucide-vue-next';
import debounce from 'lodash/debounce';
import axios from 'axios';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  }
});
const emit = defineEmits(['update:modelValue']);

const inputRef = ref(null);
const searchQuery = ref(props.modelValue);
const suggestions = ref([]);
const isLoading = ref(false);
const isLoadingMore = ref(false);
const showSuggestions = ref(false);
const currentPage = ref(1);
const lastPage = ref(1);
const highlightedIndex = ref(-1);
let blurTimeout = null;

const hasMorePages = computed(() => currentPage.value < lastPage.value);

const categoryIcons = {
  'construction': Drill,
  'electrical': Wrench,
  'gardening': Leaf,
  'safety': Shield,
  'diy': Hammer,
  'default': Search
};

const onFocus = () => {
  if (blurTimeout) clearTimeout(blurTimeout);
  showSuggestions.value = true;
};

const onBlur = () => {
  blurTimeout = setTimeout(() => {
    showSuggestions.value = false;
    highlightedIndex.value = -1;
  }, 150);
};

const resetSearch = () => {
  searchQuery.value = '';
  suggestions.value = [];
  showSuggestions.value = false;
  currentPage.value = 1;
  lastPage.value = 1;
  highlightedIndex.value = -1;
  emit('update:modelValue', '');
  nextTick(() => {
    inputRef.value?.focus();
  });
};

const selectSuggestion = (suggestion) => {
  searchQuery.value = suggestion.name;
  showSuggestions.value = false;
  highlightedIndex.value = -1;
  emit('update:modelValue', suggestion.name);
  nextTick(() => {
    inputRef.value?.focus();
  });
};

const onArrowDown = () => {
  if (!showSuggestions.value || suggestions.value.length === 0) return;
  highlightedIndex.value = (highlightedIndex.value + 1) % suggestions.value.length;
};
const onArrowUp = () => {
  if (!showSuggestions.value || suggestions.value.length === 0) return;
  highlightedIndex.value = (highlightedIndex.value - 1 + suggestions.value.length) % suggestions.value.length;
};
const onEnter = () => {
  if (highlightedIndex.value >= 0 && highlightedIndex.value < suggestions.value.length) {
    selectSuggestion(suggestions.value[highlightedIndex.value]);
  }
};

const loadMore = async () => {
  if (isLoadingMore.value || !hasMorePages.value) return;
  isLoadingMore.value = true;
  currentPage.value++;
  try {
    const response = await axios.get('/api/search/suggestions', {
      params: {
        query: searchQuery.value,
        page: currentPage.value
      }
    });
    suggestions.value = [
      ...suggestions.value,
      ...response.data.data.map(item => ({
        id: item.id,
        name: item.name,
        category: item.category,
        icon: categoryIcons[item.category?.slug] || categoryIcons.default
      }))
    ];
    lastPage.value = response.data.last_page;
  } catch (error) {
    // Optionally handle error
  } finally {
    isLoadingMore.value = false;
  }
};

const fetchSuggestions = debounce(async (query) => {
  if (query.length < 3) {
    suggestions.value = [];
    return;
  }
  isLoading.value = true;
  currentPage.value = 1;
  try {
    const response = await axios.get('/api/search/suggestions', {
      params: {
        query,
        page: 1
      }
    });
    suggestions.value = response.data.data.map(item => ({
      id: item.id,
      name: item.name,
      category: item.category,
      icon: categoryIcons[item.category?.slug] || categoryIcons.default
    }));
    lastPage.value = response.data.last_page;
    showSuggestions.value = true;
    highlightedIndex.value = -1;
  } catch (error) {
    suggestions.value = [];
  } finally {
    isLoading.value = false;
  }
}, 300);

watch(searchQuery, (newValue) => {
  emit('update:modelValue', newValue);
  fetchSuggestions(newValue);
});
</script>

<style scoped>
.loader {
  border: 2px solid #e5e7eb;
  border-top: 2px solid #6366f1;
  border-radius: 50%;
  width: 1rem;
  height: 1rem;
  animation: spin 1s linear infinite;
  display: inline-block;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style> 