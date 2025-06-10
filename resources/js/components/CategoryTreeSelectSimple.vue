<template>
  <div class="space-y-1.5">
    <label :for="id" class="text-sm font-medium text-gray-700 dark:text-white">
      {{ label }}
    </label>

    <div class="relative">
      <select
        :id="id"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        class="w-full rounded-md border border-input bg-white px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-800"
      >
        <option value="all">{{ allItemsLabel }}</option>
        <template v-for="category in categories" :key="category.id">
          <option :value="category.id">{{ category.name }}</option>
          <template v-if="category.children?.length">
            <option 
              v-for="child in category.children" 
              :key="child.id"
              :value="child.id"
            >
              — {{ child.name }}
            </option>
          </template>
        </template>
      </select>
    </div>
  </div>
</template>

<script setup>
defineProps({
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
  }
});

defineEmits(['update:modelValue']);
</script> 