<template>
  <div class="space-y-1.5">
    <Label :for="id" class="flex items-center gap-2 text-sm font-medium leading-none text-muted-foreground">
      {{ label }}
      <TooltipProvider v-if="tooltip">
        <Tooltip :delayDuration="0">
          <TooltipTrigger asChild>
            <HelpCircle class="h-4 w-4 text-muted-foreground hover:text-foreground transition-colors" />
          </TooltipTrigger>
          <TooltipContent side="right" class="text-xs bg-popover">
            <p>{{ tooltip }}</p>
          </TooltipContent>
        </Tooltip>
      </TooltipProvider>
    </Label>

    <Popover v-model:open="isOpen" :modal="true">
      <PopoverTrigger as-child>
        <Button 
          :id="id"
          variant="outline" 
          role="combobox" 
          :aria-expanded="isOpen"
          :aria-label="label"
          class="w-full justify-between font-normal bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60"
          :class="[
            !modelValue && 'text-muted-foreground',
            'focus-visible:ring-1 focus-visible:ring-ring',
            'transition-all duration-200'
          ]"
        >
          <span class="truncate">{{ selected?.name || allItemsLabel }}</span>
          <div class="flex items-center gap-1">
            <button
              v-if="modelValue !== 'all'"
              type="button"
              @click.stop.prevent="handleReset"
              class="outline-none focus:outline-none"
            >
              <X
                class="h-4 w-4 shrink-0 opacity-50 hover:opacity-100 transition duration-200 cursor-pointer"
              />
            </button>
            <ChevronDown 
              class="h-4 w-4 shrink-0 opacity-50 transition duration-200"
              :class="{ 'rotate-180': isOpen }"
            />
          </div>
        </Button>
      </PopoverTrigger>
      
      <PopoverContent 
        class="w-screen p-0 sm:w-[var(--radix-popover-trigger-width)] shadow-lg"
        :align="align"
        :side="side"
        :sideOffset="sideOffset"
        :alignOffset="alignOffset"
      >
        <Command class="rounded-lg border bg-popover">
          <CommandInput 
            v-model="searchQuery"
            :placeholder="`Rechercher une catégorie...`"
            class="h-9"
          />
          
          <CommandList class="max-h-[300px] overflow-y-auto overscroll-contain">
            <CommandEmpty v-if="searchQuery && !filteredCategories.length" class="py-6 text-center text-sm">
              Aucune catégorie trouvée.
            </CommandEmpty>
            
            <CommandGroup v-if="!searchQuery || filteredCategories.length">
              <div class="px-1 py-2">
                <CommandItem
                  @select="handleSelect({ id: 'all', name: allItemsLabel })"
                  :value="allItemsLabel"
                  class="rounded-sm cursor-pointer data-[selected=true]:bg-accent"
                  :class="{ 'bg-accent': modelValue === 'all' }"
                >
                  <div class="flex items-center w-full">
                    <Check 
                      v-if="modelValue === 'all'" 
                      class="h-4 w-4 text-accent-foreground flex-shrink-0" 
                    />
                    <span 
                      :class="[
                        'flex-grow',
                        modelValue !== 'all' ? 'pl-6' : 'pl-0',
                        'text-sm font-medium'
                      ]"
                    >
                      {{ allItemsLabel }}
                    </span>
                  </div>
                </CommandItem>
              </div>
              
              <CommandSeparator class="bg-border/50" />
              
              <div class="px-1 py-2">
                <template v-for="category in (searchQuery ? filteredCategories : categories)" :key="category.id">
                  <RecursiveCategoryItem
                    :category="category"
                    :modelValue="modelValue"
                    :searchQuery="searchQuery"
                    @select="handleSelect"
                  />
                </template>
              </div>
            </CommandGroup>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { HelpCircle, ChevronDown, Check, X } from 'lucide-vue-next';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { 
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
  CommandSeparator,
} from '@/components/ui/command';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import RecursiveCategoryItem from './RecursiveCategoryItem.vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: 'all'
  },
  categories: {
    type: Array,
    required: true
  },
  label: {
    type: String,
    default: 'Catégorie'
  },
  id: {
    type: String,
    default: 'category-select'
  },
  allItemsLabel: {
    type: String,
    default: 'Toutes les catégories'
  },
  tooltip: {
    type: String,
    default: 'Filtrer par type d\'équipement'
  },
  align: {
    type: String,
    default: 'start'
  },
  side: {
    type: String,
    default: 'bottom'
  },
  sideOffset: {
    type: Number,
    default: 4
  },
  alignOffset: {
    type: Number,
    default: 0
  }
});

const emit = defineEmits(['update:modelValue']);
const isOpen = ref(false);
const selected = ref(null);
const searchQuery = ref('');

// Recursive function to search categories and their descendants
const searchInCategory = (category, query) => {
  const matchesSearch = category.name.toLowerCase().includes(query.toLowerCase());
  
  if (matchesSearch) return true;
  
  if (category.descendants?.length) {
    return category.descendants.some(descendant => searchInCategory(descendant, query));
  }
  
  return false;
};

// Recursive function to get all matching categories and their ancestors
const getMatchingCategories = (categories, query) => {
  return categories.reduce((matches, category) => {
    if (searchInCategory(category, query)) {
      const categoryWithFilteredDescendants = {
        ...category,
        descendants: category.descendants 
          ? getMatchingCategories(category.descendants, query)
          : []
      };
      matches.push(categoryWithFilteredDescendants);
    }
    return matches;
  }, []);
};

// Computed property for filtered categories
const filteredCategories = computed(() => {
  if (!searchQuery.value) return props.categories;
  return getMatchingCategories(props.categories, searchQuery.value);
});

const handleSelect = (category) => {
  selected.value = category;
  emit('update:modelValue', category.id);
  isOpen.value = false;
  searchQuery.value = '';
};

const handleReset = (e) => {
  e.preventDefault();
  e.stopPropagation();
  handleSelect({ id: 'all', name: props.allItemsLabel });
  if (isOpen.value) {
    isOpen.value = false;
  }
};
</script>

<style scoped>
.command-input {
  @apply bg-transparent border-0 outline-none ring-0 focus-visible:ring-0 focus-visible:ring-offset-0;
}
</style> 