<template>
  <div>
    <Select v-model="internalValue" @update:modelValue="onSelect">
      <SelectTrigger>
        <span v-if="selectedCategory">{{ selectedCategory.name }}</span>
        <span v-else class="text-muted-foreground">Sélectionner une catégorie</span>
      </SelectTrigger>
      <SelectContent class="w-full max-h-72 overflow-y-auto">
            <SelectGroup v-for="category in categories" :key="category.id">
              <SelectLabel class="uppercase font-bold">{{ category.name }}</SelectLabel>
              <SelectItem v-for="child in category.children" :key="child.id" :value="child.id" class="pl-8">
                {{ child.name }}
              </SelectItem>
            </SelectGroup>
      </SelectContent>
    </Select>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectGroup, SelectLabel } from '@/components/ui/select';
import axios from 'axios';

const props = defineProps({
  modelValue: {
    type: [String, Number, null],
    default: null,
  },
  required: {
    type: Boolean,
    default: false,
  },
});
const emit = defineEmits(['update:modelValue']);

const categories = ref([]);
const flatCategories = computed(() => categories.value.flatMap(c => [c, ...(c.children || [])]));

const selectedCategory = computed(() => {
  return flatCategories.value.find(cat => cat.id === internalValue.value) || null;
});

const internalValue = ref(props.modelValue);
watch(() => props.modelValue, v => { internalValue.value = v; });

const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/categories');
    categories.value = response.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

onMounted(fetchCategories);

const onSelect = (value) => {
  emit('update:modelValue', value);
};
</script>
