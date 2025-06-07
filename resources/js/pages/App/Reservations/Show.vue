<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import AppLayout from '@/layouts/AppLayout.vue'
import { format } from 'date-fns'
import { fr } from 'date-fns/locale'

const props = defineProps({
  reservation: Object,
  availableStatuses: Object,
  availableItemStatuses: Object,
})

const statusForm = useForm({
  status: props.reservation.status,
})

const itemStatusForm = useForm({})

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

const getItemStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800',
    picked_up: 'bg-green-100 text-green-800',
    returned: 'bg-blue-100 text-blue-800',
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getItemStatusText = (status) => {
  const texts = {
    pending: 'En attente',
    picked_up: 'Récupéré',
    returned: 'Retourné',
  }
  return texts[status] || status
}

const updateStatus = () => {
  statusForm.put(route('app.reservations.update-status', props.reservation.id))
}

const updateItemStatus = (itemId, status) => {
  itemStatusForm.put(route('app.reservations.items.update-status', [props.reservation.id, itemId]), {
    status: status
  })
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
  }).format(price / 100)
}
</script>

<template>
  <AppLayout>
    <Head :title="`Réservation #${reservation.id}`" />

    <div class="max-w-7xl mx-auto py-6 space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold">Réservation #{{ reservation.id }}</h1>
        <div class="flex space-x-4">
          <Link :href="route('app.reservations.index')">
            <Button variant="outline">Retour à la liste</Button>
          </Link>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Card>
          <CardHeader>
            <CardTitle>Informations générales</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <div class="text-sm text-gray-500">Statut</div>
              <div class="flex items-center space-x-4">
                <Badge :class="getStatusColor(reservation.status)">
                  {{ getStatusText(reservation.status) }}
                </Badge>
                <Select
                  v-if="reservation.status === 'pending'"
                  v-model="statusForm.status"
                  @update:modelValue="updateStatus"
                >
                  <SelectTrigger class="w-[180px]">
                    <SelectValue placeholder="Changer le statut" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem
                      v-for="(label, value) in availableStatuses"
                      :key="value"
                      :value="value"
                    >
                      {{ label }}
                    </SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>

            <div>
              <div class="text-sm text-gray-500">Organisation emprunteuse</div>
              <div class="font-medium">{{ reservation.to_organization?.name }}</div>
            </div>

            <div>
              <div class="text-sm text-gray-500">Période</div>
              <div class="font-medium">
                Du {{ formatDateTime(reservation.start_date) }}
                <br>
                Au {{ formatDateTime(reservation.end_date) }}
              </div>
            </div>

            <div v-if="reservation.notes">
              <div class="text-sm text-gray-500">Notes</div>
              <div class="font-medium">{{ reservation.notes }}</div>
            </div>
          </CardContent>
        </Card>

        <Card class="md:col-span-2">
          <CardHeader>
            <CardTitle>Équipements</CardTitle>
          </CardHeader>
          <CardContent>
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Équipement</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dépôt</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Prix</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="item in reservation.items" :key="item.id">
                  <td class="px-4 py-2">
                    <div class="font-medium">{{ item.equipment?.name }}</div>
                    <div v-if="item.notes" class="text-sm text-gray-500">{{ item.notes }}</div>
                  </td>
                  <td class="px-4 py-2">{{ item.depot?.name }}</td>
                  <td class="px-4 py-2">{{ item.quantity }}</td>
                  <td class="px-4 py-2">
                    <div>{{ formatPrice(item.price) }} / unité</div>
                    <div class="text-sm text-gray-500">
                      Total: {{ formatPrice(item.price * item.quantity) }}
                    </div>
                  </td>
                  <td class="px-4 py-2">
                    <div class="flex items-center space-x-2">
                      <Badge :class="getItemStatusColor(item.status)">
                        {{ getItemStatusText(item.status) }}
                      </Badge>
                      <Select
                        v-if="reservation.status === 'confirmed'"
                        v-model="item.status"
                        @update:modelValue="(status) => updateItemStatus(item.id, status)"
                      >
                        <SelectTrigger class="w-[140px]">
                          <SelectValue placeholder="Changer" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem
                            v-for="(label, value) in availableItemStatuses"
                            :key="value"
                            :value="value"
                          >
                            {{ label }}
                          </SelectItem>
                        </SelectContent>
                      </Select>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </CardContent>
          <CardFooter class="border-t">
            <div class="w-full space-y-1">
              <div class="flex justify-between text-sm">
                <span>Sous-total:</span>
                <span>{{ formatPrice(reservation.subtotal) }}</span>
              </div>
              <div v-if="reservation.discount_amount > 0" class="flex justify-between text-sm text-gray-600">
                <span>
                  Remise
                  <span v-if="reservation.discount_type === 'percentage'">
                    ({{ reservation.discount_value }}%)
                  </span>:
                </span>
                <span>-{{ formatPrice(reservation.discount_amount) }}</span>
              </div>
              <div v-if="reservation.discount_reason" class="text-sm text-gray-500">
                Raison: {{ reservation.discount_reason }}
              </div>
              <div class="flex justify-between font-medium pt-2 border-t">
                <span>Total:</span>
                <span>{{ formatPrice(reservation.total) }}</span>
              </div>
            </div>
          </CardFooter>
        </Card>
      </div>
    </div>
  </AppLayout>
</template> 