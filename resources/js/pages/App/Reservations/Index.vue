<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import AppLayout from '@/layouts/AppLayout.vue'
import { format } from 'date-fns'
import { fr } from 'date-fns/locale'

const props = defineProps({
  reservations: Object,
})

const formatDateTime = (date) => {
  if (!date) return '---'
  try {
    return format(new Date(date), 'PPP à p', { locale: fr })
  } catch (error) {
    console.error('Error formatting date:', error)
    return '---'
  }
}

const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    cancelled: 'bg-gray-100 text-gray-800',
    completed: 'bg-blue-100 text-blue-800',
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
  const texts = {
    pending: 'En attente',
    confirmed: 'Confirmée',
    rejected: 'Refusée',
    cancelled: 'Annulée',
    completed: 'Terminée',
  }
  return texts[status] || status
}

const getReservationItemsSummary = (items) => {
  if (!items?.length) return 'Aucun équipement'
  return items.map(item => item?.equipment?.name).join(', ')
}
</script>

<template>
  <AppLayout>
    <Head title="Réservations" />

    <div class="max-w-7xl mx-auto py-6 space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold">Réservations</h1>
        <Link :href="route('app.reservations.create')">
          <Button>Nouvelle réservation</Button>
        </Link>
      </div>

      <Card v-if="reservations.data.length">
        <CardContent class="p-0">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Équipements
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Organisation
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Dates
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Total
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Statut
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="reservation in reservations.data" :key="reservation.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">
                    {{ getReservationItemsSummary(reservation.items) }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ reservation.items?.length }} équipement(s)
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">
                    De: {{ reservation.from_organization?.name }}
                  </div>
                  <div class="text-sm text-gray-500">
                    À: {{ reservation.to_organization?.name }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">
                    {{ formatDateTime(reservation.start_date) }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ formatDateTime(reservation.end_date) }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">
                    {{ reservation.formatted_total }}
                  </div>
                  <div v-if="reservation.discount_amount > 0" class="text-sm text-gray-500">
                    Remise: {{ reservation.discount_text }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <Badge :class="getStatusColor(reservation.status)">
                    {{ getStatusText(reservation.status) }}
                  </Badge>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <Link :href="route('app.reservations.show', reservation.id)" class="text-primary-600 hover:text-primary-900">
                    Voir
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </CardContent>
        <CardFooter class="flex items-center justify-between border-t">
          <div class="text-sm text-gray-500">
            {{ reservations.total }} réservation(s)
          </div>
          <!-- Pagination -->
          <div class="flex items-center space-x-2" v-if="reservations.links.length > 3">
            <Link
              v-for="link in reservations.links"
              :key="link.label"
              :href="link.url"
              class="px-3 py-1 rounded"
              :class="{
                'bg-primary-50 text-primary-600': link.active,
                'text-gray-500 hover:text-gray-700': !link.active,
                'opacity-50 cursor-not-allowed': !link.url
              }"
              v-html="link.label"
            />
          </div>
        </CardFooter>
      </Card>

      <Card v-else>
        <CardContent class="text-center py-12">
          <div class="text-gray-500">Aucune réservation pour le moment</div>
          <Link :href="route('app.reservations.create')" class="mt-4 inline-block">
            <Button>Créer une réservation</Button>
          </Link>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template> 