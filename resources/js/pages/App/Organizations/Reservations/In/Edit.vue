<template>
  <ReservationLayout :title="'Réservation du ' + reservation.start_date + ' au ' + reservation.end_date">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm p-6">
        <!-- Back Button -->
        <div class="mb-6">
          <Button 
            variant="ghost" 
            class="gap-2"
            @click="router.visit(route('app.organizations.reservations.in.index'))"
          >
            <ArrowLeftIcon class="h-4 w-4" />
            Retour aux réservations
          </Button>
        </div>

        <!-- Header Section -->
        <div class="flex justify-between items-start mb-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">
              Réservation du {{ reservation.start_date }} au {{ reservation.end_date }}
            </h1>
            <div class="flex items-center gap-2 mt-2">
              <BuildingIcon class="h-4 w-4 text-gray-500" />
              <h2 class="text-xl font-semibold text-gray-600">
                {{ reservation.from_organization.name }}
              </h2>
            </div>
          </div>
          <div class="flex flex-col items-end gap-2">
            <Badge :class="`${reservation.status_color}`">
              {{ reservation.status_label }}
            </Badge>
            <div class="flex gap-2">
              <Button 
                v-if="['pending', 'confirmed'].includes(reservation.status)"
                variant="destructive" 
                size="sm" 
                @click="cancelReservation"
              >
                <XIcon class="h-4 w-4" />
                Annuler
              </Button>
            </div>
          </div>
        </div>

        <!-- Dates Section -->
        <Card class="mb-6">
          <CardHeader>
            <CardTitle class="text-lg">Détails de la réservation</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="flex items-start gap-3">
                <CalendarIcon class="h-5 w-5 text-gray-500 mt-0.5" />
                <div>
                  <h3 class="text-sm font-medium text-gray-500">Récupération</h3>
                  <p class="text-gray-900">{{ reservation.start_date }}</p>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <CalendarIcon class="h-5 w-5 text-gray-500 mt-0.5" />
                <div>
                  <h3 class="text-sm font-medium text-gray-500">Retour</h3>
                  <p class="text-gray-900">{{ reservation.end_date }}</p>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <ClockIcon class="h-5 w-5 text-gray-500 mt-0.5" />
                <div>
                  <h3 class="text-sm font-medium text-gray-500">Durée</h3>
                  <p class="text-gray-900">{{ reservation.duration }} jours</p>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <BuildingIcon class="h-5 w-5 text-gray-500 mt-0.5" />
                <div>
                  <h3 class="text-sm font-medium text-gray-500">Organisation emprunteuse</h3>
                  <p class="text-gray-900">{{ reservation.to_organization.name }}</p>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <PlusIcon class="h-5 w-5 text-gray-500 mt-0.5" />
                <div>
                  <h3 class="text-sm font-medium text-gray-500">Créée le</h3>
                  <p class="text-gray-900">{{ reservation.created_at }}</p>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <PencilIcon class="h-5 w-5 text-gray-500 mt-0.5" />
                <div>
                  <h3 class="text-sm font-medium text-gray-500">Dernière mise à jour</h3>
                  <p class="text-gray-900">{{ reservation.updated_at }}</p>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <Alert variant="warning" class="mb-6">
          <AlertCircleIcon class="h-4 w-4" />
          <AlertTitle>Information importante</AlertTitle>
          <AlertDescription>
            Pour garantir la sécurité du matériel, les informations de localisation ne sont pas visibles sur cette page.<br>
            Merci de contacter directement l'organisation pour organiser la récupération.
          </AlertDescription>
        </Alert>

        <!-- Equipment Section -->
        <Card>
          <CardHeader>
            <div class="flex justify-between items-center">
              <CardTitle class="text-lg">Matériel réservé</CardTitle>
              <div class="text-right">
                <p class="text-sm text-gray-600">Prix total</p>
                <p class="text-xl font-bold text-gray-900">{{ reservation.total }} €</p>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Équipement</TableHead>
                  <TableHead>Quantité</TableHead>
                  <TableHead>Prix/jour</TableHead>
                  <TableHead>Caution</TableHead>
                  <TableHead>Emplacement</TableHead>
                  <TableHead v-if="reservation.status === 'pending'" class="w-[100px]"></TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="item in reservation.items" :key="item.id">
                  <TableCell class="font-medium">{{ item.equipment.name }}</TableCell>
                  <TableCell>
                    <Badge variant="outline" class="text-sm font-medium">
                      {{ item.quantity }}
                    </Badge>
                  </TableCell>
                  <TableCell>{{ item.price }} €</TableCell>
                  <TableCell>{{ item.equipment.deposit_amount }} €</TableCell>
                  <TableCell>{{ item.city }}</TableCell>
                  <TableCell v-if="reservation.status === 'pending'">
                    <Button 
                      variant="ghost" 
                      size="icon"
                      class="text-destructive hover:text-destructive/90"
                      @click="removeItem(item.id)"
                    >
                      <Trash2Icon class="h-4 w-4" />
                    </Button>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </CardContent>
        </Card>
      </div>
    </div>
  </ReservationLayout>
</template>

<script setup>
import ReservationLayout from '@/pages/App/Organizations/Reservations/Layout.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { AlertCircleIcon, CalendarIcon, BuildingIcon, ClockIcon, XIcon, ArrowLeftIcon, PlusIcon, PencilIcon } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'
import { Trash2Icon } from 'lucide-vue-next'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'

const props = defineProps({
  reservation: {
    type: Object,
    required: true
  }
})

const cancelReservation = () => {
  if(!confirm('Voulez-vous vraiment annuler cette réservation ?')) return;
  router.delete(route('organizations.reservations.in.destroy', { reservation: props.reservation.id }))
}

const removeItem = (itemId) => {
  if(!confirm('Voulez-vous vraiment retirer cet équipement de la réservation ?')) return;
  router.delete(route('organizations.reservations.in.items.destroy', { 
    reservation: props.reservation.id,
    item: itemId
  }))
}

</script> 