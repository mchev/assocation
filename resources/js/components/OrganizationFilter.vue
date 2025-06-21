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
            {{ selectedOrganizations.length > 0 ? selectedOrganizations.map(org => org.name).join(', ') : "Sélectionner une ou plusieurs organisations" }}
          </span>
          <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
        </Button>
      </PopoverTrigger>
      <PopoverContent class="w-[--radix-popover-trigger-width] p-0">
        <Command :filter-function="(list) => list">
          <div class="flex items-center justify-between border-b p-2">
            <p class="text-sm text-muted-foreground">
              {{ selectedOrganizations.length }} sélectionnée(s)
            </p>
            <Button
              v-if="selectedOrganizations.length > 0"
              variant="ghost"
              size="sm"
              @click="clearSelection"
            >
              Effacer
            </Button>
          </div>
          <CommandInput
            v-model="searchQuery"
            placeholder="Rechercher une organisation..."
            @update:model-value="searchOrganizations"
          />
          <CommandEmpty>
            <span v-if="isLoading">Chargement...</span>
            <span v-else>Aucune organisation trouvée.</span>
          </CommandEmpty>
          <CommandGroup>
            <CommandItem
              v-for="organization in searchedOrganizations"
              :key="organization.id"
              :value="organization.name"
              @select="(event) => onSelect(organization, event)"
            >
              <Check
                :class="[
                  'mr-2 h-4 w-4',
                  isSelected(organization) ? 'opacity-100' : 'opacity-0',
                ]"
              />
              {{ organization.name }}
            </CommandItem>
          </CommandGroup>
        </Command>
      </PopoverContent>
    </Popover>
    <div v-if="selectedOrganizations.length > 0" class="mt-2 flex flex-wrap gap-1">
      <Badge
        v-for="organization in selectedOrganizations"
        :key="`selected-${organization.id}`"
        variant="secondary"
      >
        {{ organization.name }}
        <button
          type="button"
          class="ml-1 rounded-full outline-none ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2"
          @click="toggleSelection(organization)"
        >
          <X class="h-3 w-3 text-muted-foreground hover:text-foreground" />
        </button>
      </Badge>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem } from '@/components/ui/command';
import { Badge } from '@/components/ui/badge';
import { Check, ChevronsUpDown, X } from 'lucide-vue-next';
import { debounce } from 'lodash-es';
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
const searchedOrganizations = ref([]);
const allKnownOrganizations = ref([]);
const isLoading = ref(false);

const selectedOrganizations = computed(() => {
    return props.modelValue
        .map(id => allKnownOrganizations.value.find(org => org.id === id))
        .filter(Boolean);
});

const fetchOrganizationsByIds = async (ids) => {
    if (ids.length === 0) return;
    const unknownIds = ids.filter(id => !allKnownOrganizations.value.some(org => org.id === id));
    if (unknownIds.length === 0) return;

    try {
        const response = await axios.get(route('api.organizations.index', { 'ids[]': unknownIds }));
        allKnownOrganizations.value.push(...response.data.data);
    } catch (error) {
        console.error('Error fetching organizations by IDs:', error);
    }
};

onMounted(() => {
    searchOrganizations();
    fetchOrganizationsByIds(props.modelValue);
});

watch(() => props.modelValue, (newIds) => {
    fetchOrganizationsByIds(newIds);
}, { deep: true });

const isSelected = (organization) => {
    return props.modelValue.includes(organization.id);
};

const onSelect = (organization, event) => {
    if (event) {
        event.preventDefault();
    }

    const newSelectedIds = [...props.modelValue];
    const index = newSelectedIds.indexOf(organization.id);

    if (index > -1) {
        newSelectedIds.splice(index, 1);
    } else {
        newSelectedIds.push(organization.id);
    }
    emit('update:modelValue', newSelectedIds);
}

const toggleSelection = (organization) => {
    onSelect(organization, null)
};

const clearSelection = () => {
    emit('update:modelValue', []);
};

const searchOrganizations = debounce(async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(route('api.organizations.index', { search: searchQuery.value }));
        searchedOrganizations.value = response.data.data;

        // Add newly found organizations to our list of all known organizations
        response.data.data.forEach(org => {
            if (!allKnownOrganizations.value.some(o => o.id === org.id)) {
                allKnownOrganizations.value.push(org);
            }
        });
    } catch (error) {
        console.error('Error searching organizations:', error);
    } finally {
        isLoading.value = false;
    }
}, 300);
</script> 