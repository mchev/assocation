<template>
  <PublicLayout>
    <div>
      <HeroSection />
      <SearchSection
        :filters="filters"
        :stats="stats"
        @search="search"
      />
      <ResultsSection
        :equipment="equipment"
        :start-date="form.start_date"
        :end-date="form.end_date"
      />
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import HeroSection from '@/components/Home/HeroSection.vue';
import SearchSection from '@/components/Home/SearchSection.vue';
import ResultsSection from '@/components/Home/ResultsSection.vue';

const props = defineProps({
  equipment: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  stats: {
    type: Object,
    default: () => ({
      min_price: 0,
      max_price: 1000,
      categories: []
    })
  }
});

const form = ref({
  search: props.filters.search || '',
  location: props.filters.location || '',
  radius: props.filters.radius || 10,
  category: props.filters.category || '',
  available: props.filters.available || false,
  min_price: props.filters.min_price || props.stats.min_price,
  max_price: props.filters.max_price || props.stats.max_price,
  start_date: props.filters.start_date || '',
  end_date: props.filters.end_date || ''
});

const search = (searchForm) => {
  router.get(route('home'), searchForm, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  });
};
</script> 