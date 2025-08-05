<template>
  <AppLayout 
    title="Location et prêt de matériel événementiel" 
    :description="`${appName} facilite la location de matériel entre associations. Trouvez et réservez en quelques clics le matériel dont vous avez besoin pour vos événements.`"
  >

      <!-- Hero Section -->
      <template v-if="!user">
        <section class="relative">
          <HeroSection />
        </section>
      </template>

      <!-- Search Section -->
      <section>
        <SearchSection
          :filters="filters"
          :stats="stats"
          @searching="isSearching = $event"
        />
      </section>

      <!-- Results Section -->
      <section 
        class="relative py-12 bg-gradient-to-b from-background via-background/50 to-background"
        :class="{ 'mt-8': equipments.data.length > 0 }"
      >
        <ResultsSection
          :equipments="equipments"
          :start-date="filters.start_date"
          :end-date="filters.end_date"
          :isSearching="isSearching"
        />
      </section>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import HeroSection from '@/components/Home/HeroSection.vue';
import SearchSection from '@/components/Home/SearchSection.vue';
import ResultsSection from '@/components/Home/ResultsSection.vue';

const { equipments, filters, stats } = defineProps({
  equipments: {
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

const isSearching = ref(false);
const page = usePage();
const appName = page.props.name;
const user = page.props.auth.user;
</script> 