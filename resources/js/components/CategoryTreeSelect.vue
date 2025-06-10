<template>
  <div class="space-y-1.5">
    <Label :for="id" class="text-sm font-medium text-gray-700 dark:text-white">
      {{ label }}
    </Label>

    <Popover v-model:open="isOpen" :modal="true">
      <PopoverTrigger as-child>
        <Button 
          :id="id"
          variant="outline" 
          role="combobox" 
          :aria-expanded="isOpen"
          :aria-label="label"
          class="w-full justify-between font-normal bg-white dark:bg-gray-800"
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
        class="w-[var(--radix-popover-trigger-width)] p-0 shadow-lg"
        :style="{ '--radix-popover-trigger-width': 'min(calc(100vw - 2rem), 400px)' }"
        :align="align"
        :side="side"
        :sideOffset="sideOffset"
        :alignOffset="alignOffset"
      >
        <Command class="rounded-lg border bg-popover">
          <CommandInput 
            v-model="searchQuery"
            :placeholder="'Rechercher une catégorie...'"
            class="h-11"
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
                  <!-- Parent category -->
                  <CommandItem 
                    :value="category.name"
                    @select="handleSelect(category)"
                    class="rounded-sm cursor-pointer data-[selected=true]:bg-accent"
                    :class="{ 'bg-accent': modelValue === category.id }"
                  >
                    <div class="flex items-center w-full gap-2">
                      <Check 
                        v-if="modelValue === category.id" 
                        class="h-4 w-4 text-accent-foreground flex-shrink-0" 
                      />
                      <span 
                        :class="[
                          'flex-grow text-sm',
                          modelValue === category.id ? 'pl-0 font-bold text-accent-foreground' : 'pl-6',
                          'font-bold'
                        ]"
                      >
                        {{ category.name }}
                      </span>
                      <ChevronRight 
                        v-if="category.children?.length" 
                        class="h-4 w-4 text-muted-foreground flex-shrink-0 opacity-75" 
                      />
                    </div>
                  </CommandItem>

                  <!-- Children categories -->
                  <template v-if="category.children?.length">
                    <CommandItem 
                      v-for="child in category.children"
                      :key="child.id"
                      :value="child.name"
                      @select="handleSelect(child)"
                      class="rounded-sm cursor-pointer data-[selected=true]:bg-accent ml-4"
                      :class="{ 'bg-accent': modelValue === child.id }"
                    >
                      <div class="flex items-center w-full gap-2">
                        <Check 
                          v-if="modelValue === child.id" 
                          class="h-4 w-4 text-accent-foreground flex-shrink-0" 
                        />
                        <span 
                          :class="[
                            'flex-grow text-sm',
                            modelValue === child.id ? 'pl-0 font-bold text-accent-foreground' : 'pl-6'
                          ]"
                        >
                          {{ child.name }}
                        </span>
                      </div>
                    </CommandItem>
                  </template>
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
import { ChevronDown, ChevronRight, Check, X } from 'lucide-vue-next';
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

// Simple search function that checks both parent and children
const searchInCategory = (category, query) => {
  if (!category || !query) return false;
  
  const lowerQuery = query.toLowerCase();
  const matchesSearch = category.name.toLowerCase().includes(lowerQuery);
  
  if (matchesSearch) return true;
  
  return category.children?.some(child => 
    child.name.toLowerCase().includes(lowerQuery)
  ) || false;
};

// Filtered categories computed property
const filteredCategories = computed(() => {
  if (!searchQuery.value) return props.categories;
  if (!props.categories?.length) return [];
  
  return props.categories.filter(category => 
    searchInCategory(category, searchQuery.value)
  ).map(category => ({
    ...category,
    children: category.children?.filter(child =>
      child.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }));
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