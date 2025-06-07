<template>
  <AppLayout >
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold">Créer une réservation</h1>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps({
  organization: Object,
  equipment: Object,
})

</script> 