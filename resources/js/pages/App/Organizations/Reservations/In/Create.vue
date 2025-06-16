<template>
  <AppLayout >
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold">Créer une réservation</h1>
        <p class="text-muted-foreground">
            La fonctionnalité de réservation n'est pas encore disponible. Vous pouvez d'ores et déjà créer vos matériels pour commencer votre inventaire.
        </p>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'

defineProps({
  organization: Object,
  equipment: Object,
})

</script> 