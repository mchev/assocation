<template>
  <PublicLayout title="Location et prêt de matériel événementiel" description="Assodépôt facilite la location de matériel entre associations. Trouvez et réservez en quelques clics le matériel dont vous avez besoin pour vos événements. ">
    <main class="min-h-screen bg-background">
      <!-- Hero Section -->
      <section class="relative pb-12">
        <HeroSection />
      </section>

      <!-- Search Section -->
      <section class="relative -mt-8">
        <SearchSection
          :filters="filters"
          :stats="stats"
          :is-searching="isSearching"
          @search="search"
        />
      </section>

      <!-- Results Section -->
      <section 
        class="relative py-12 bg-gradient-to-b from-background via-background/50 to-background"
        :class="{ 'mt-8': equipment.data.length > 0 }"
      >
        <ResultsSection
          :equipment="equipment"
          :start-date="form.start_date"
          :end-date="form.end_date"
          :is-searching="isSearching"
        />
      </section>
    </main>
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

const isSearching = ref(false);

const search = (filters) => {
  isSearching.value = true;
  router.get(route('home'), filters, {
    preserveState: true,
    preserveScroll: true,
    onFinish: () => {
      isSearching.value = false;
    }
  });
};
</script> 