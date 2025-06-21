<template>
  <div>
    <Popover v-model:open="open">
      <PopoverTrigger asChild>
        <Button
          variant="outline"
          role="combobox"
          :aria-expanded="open"
          class="w-full justify-between"
        >
          <span class="truncate">
            {{ selectedCategories.length > 0 ? selectedCategories.map(cat => cat.name).join(', ') : "Sélectionner une ou plusieurs catégories" }}
          </span>
          <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
        </Button>
      </PopoverTrigger>
      <PopoverContent class="w-[--radix-popover-trigger-width] p-0">
        <Command :filter-function="filterFunction">
          <div class="flex items-center justify-between border-b p-2">
            <p class="text-sm text-muted-foreground">
              {{ selectedCategories.length }} sélectionnée(s)
            </p>
            <Button
              v-if="selectedCategories.length > 0"
              variant="ghost"
              size="sm"
              @click="clearSelection"
            >
              Effacer
            </Button>
          </div>
          <CommandInput
            v-model="searchQuery"
            placeholder="Rechercher une catégorie..."
          />
          <CommandEmpty>
            <span v-if="isLoading">Chargement...</span>
            <span v-else>Aucune catégorie trouvée.</span>
          </CommandEmpty>
          <CommandList>
            <CommandGroup
              v-for="category in categories"
              :key="category.id"
              :heading="category.name"
            >
              <CommandItem
                :value="category.name"
                @select="(event) => onSelect(category, event)"
              >
                <Check
                  :class="[
                    'mr-2 h-4 w-4',
                    isSelected(category) ? 'opacity-100' : 'opacity-0',
                  ]"
                />
                {{ category.name }} (toute la catégorie)
              </CommandItem>
              <CommandItem
                v-for="child in category.children"
                :key="child.id"
                :value="child.name"
                @select="(event) => onSelect(child, event)"
                class="pl-8"
              >
                <Check
                  :class="[
                    'mr-2 h-4 w-4',
                    isSelected(child) ? 'opacity-100' : 'opacity-0',
                  ]"
                />
                {{ child.name }}
              </CommandItem>
            </CommandGroup>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
    <div v-if="selectedCategories.length > 0" class="mt-2 flex flex-wrap gap-1">
      <Badge
        v-for="category in selectedCategories"
        :key="`selected-${category.id}`"
        variant="secondary"
      >
        {{ category.name }}
        <button
          type="button"
          class="ml-1 rounded-full outline-none ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2"
          @click="toggleSelection(category)"
        >
          <X class="h-3 w-3 text-muted-foreground hover:text-foreground" />
        </button>
      </Badge>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Badge } from '@/components/ui/badge';
import { Check, ChevronsUpDown, X } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const searchQuery = ref('');
const categories = ref([]);
const flatCategories = computed(() => categories.value.flatMap(c => [c, ...c.children]));
const isLoading = ref(false);

const selectedCategories = computed(() => {
    return props.modelValue
        .map(id => flatCategories.value.find(cat => cat.id === id))
        .filter(Boolean);
});

const fetchCategories = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/categories');
        categories.value = response.data;
    } catch (error) {
        console.error('Error fetching categories:', error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchCategories();
});

const isSelected = (category) => {
    return props.modelValue.includes(category.id);
};

const onSelect = (category, event) => {
    if (event) {
        event.preventDefault();
    }

    const newSelectedIds = [...props.modelValue];
    const index = newSelectedIds.indexOf(category.id);

    if (index > -1) {
        newSelectedIds.splice(index, 1);
    } else {
        newSelectedIds.push(category.id);
    }
    emit('update:modelValue', newSelectedIds);
}

const toggleSelection = (category) => {
    onSelect(category, null)
};

const clearSelection = () => {
    emit('update:modelValue', []);
};

const filterFunction = (list, search) => {
  return list.filter(item => {
    const childMatch = item.children?.some(child => child.name.toLowerCase().includes(search.toLowerCase()));
    return item.name.toLowerCase().includes(search.toLowerCase()) || childMatch;
  });
};
</script> 