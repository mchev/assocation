<template>
  <template v-if="category.children?.length">
    <CommandItem 
      :value="category.name"
      @select="$emit('select', category)"
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
            modelValue === category.id ? 'pl-0 font-medium' : 'pl-6',
            'font-medium text-foreground'
          ]"
        >
          {{ category.name }}
        </span>
        <ChevronRight class="h-4 w-4 text-muted-foreground flex-shrink-0 opacity-75" />
      </div>
    </CommandItem>

    <div class="ml-3 pl-3 border-l border-border/50">
      <RecursiveCategoryItem
        v-for="child in category.children"
        :key="child.id"
        :category="child"
        :modelValue="modelValue"
        :searchQuery="searchQuery"
        @select="$emit('select', $event)"
      />
    </div>
  </template>

  <template v-else>
    <CommandItem 
      :value="category.name"
      @select="$emit('select', category)"
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
            'flex-grow text-sm text-muted-foreground',
            modelValue === category.id ? 'pl-0 font-medium text-foreground' : 'pl-6'
          ]"
        >
          {{ category.name }}
        </span>
      </div>
    </CommandItem>
  </template>
</template>

<script setup>
import { ChevronRight, Check } from 'lucide-vue-next';
import { CommandItem } from '@/components/ui/command';

defineProps({
  category: {
    type: Object,
    required: true
  },
  modelValue: {
    type: [String, Number],
    default: null
  },
  searchQuery: {
    type: String,
    default: ''
  }
});

defineEmits(['select']);
</script> 