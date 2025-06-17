<template>
  <ReservationLayout :title="'Réservation du ' + reservation.start_date + ' au ' + reservation.end_date">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm p-6">
        <!-- Header Section -->
        <div class="flex justify-between items-start mb-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">
              Réservation du {{ reservation.start_date }} au {{ reservation.end_date }}
            </h1>
            <h2 class="text-xl font-semibold text-gray-600 mt-2">
              {{ reservation.from_organization.name }}
            </h2>
          </div>
          <Badge :class="`${reservation.status_color}`">
            {{ reservation.status_label }}
          </Badge>
        </div>

        <!-- Dates Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Récupération</h2>
            <p class="text-gray-600">
              <i class="fas fa-calendar-alt mr-2"></i>
              {{ reservation.start_date }}
            </p>
          </div>

          <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Retour</h2>
            <p class="text-gray-600">
              <i class="fas fa-calendar-alt mr-2"></i>
              {{ reservation.end_date }}
            </p>
          </div>
        </div>

        <!-- Equipment Section -->
        <div class="border-t pt-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Matériel réservé</h2>
            <div class="text-right">
              <p class="text-sm text-gray-600">Prix total</p>
              <p class="text-xl font-bold text-gray-900">{{ reservation.total_price }} €</p>
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="item in reservation.items" :key="item.id" 
                 class="bg-white border rounded-lg p-4 hover:shadow-md transition-shadow">
              <div class="flex items-center justify-between">
                <Badge variant="outline" class="text-sm font-medium">
                  {{ item.quantity }}
                </Badge>
                <span class="text-gray-600 font-medium">{{ item.price }} €/jour</span>
              </div>
              <h3 class="mt-2 text-gray-900 font-medium">{{ item.equipment.name }}</h3>
              <div class="mt-2 text-sm text-gray-500">
                <p>Dépôt: {{ item.depot?.name || 'Non spécifié' }}</p>
                <p>Caution: {{ item.deposit }} €</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </ReservationLayout>
</template>

<script setup>
import ReservationLayout from '@/pages/App/Organizations/Reservations/Layout.vue'
import { Badge } from '@/components/ui/badge'

defineProps({
  reservation: {
    type: Object,
    required: true
  }
})
</script> 