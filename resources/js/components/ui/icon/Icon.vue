<template>
  <component
    :is="iconComponent"
    :class="[
      'flex-shrink-0',
      sizeClasses[size],
      colorClasses[color]
    ]"
    aria-hidden="true"
  />
</template>

<script setup>
import { computed } from 'vue';
import {
  MapPinIcon,
  TagIcon,
  CurrencyEuroIcon,
  CalendarIcon,
  MagnifyingGlassIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  name: {
    type: String,
    required: true
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
  },
  color: {
    type: String,
    default: 'gray',
    validator: (value) => ['gray', 'primary', 'success', 'error', 'warning'].includes(value)
  }
});

const iconMap = {
  'map-pin': MapPinIcon,
  'tag': TagIcon,
  'euro': CurrencyEuroIcon,
  'calendar': CalendarIcon,
  'search': MagnifyingGlassIcon
};

const iconComponent = computed(() => iconMap[props.name]);

const sizeClasses = {
  sm: 'h-4 w-4',
  md: 'h-5 w-5',
  lg: 'h-6 w-6',
  xl: 'h-8 w-8'
};

const colorClasses = {
  gray: 'text-gray-400',
  primary: 'text-primary',
  success: 'text-green-500',
  error: 'text-red-500',
  warning: 'text-yellow-500'
};
</script> 