<template>
  <ReservationsLayout :title="'Réservations - ' + organization.name">
    <Head title="Réservations" />
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold text-xl leading-tight">
            Réservations ({{ reservations.total }})
          </h2>
          <p class="text-sm text-muted-foreground">Matériel que vous louez ou prêtez à d'autres organisations</p>
        </div>
        <Button
          v-if="can.create"
          as="a"
          :href="route('app.organizations.reservations.in.create', organization)"
          variant="default"
          size="default"
        >
          Nouvelle réservation
        </Button>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Liste des réservations -->
        <Card>
          <CardContent>
            <div v-if="reservations.data.length === 0" class="text-center py-12">
              <p class="text-muted-foreground text-lg">
                Aucune réservation trouvée
              </p>
              <Button
                as="a"
                :href="route('home')"
                variant="default"
                class="mt-4"
              >
                Parcourir le matériel disponible
              </Button>
            </div>

            <div v-else>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>
                      <div class="flex items-center space-x-2">
                        <span>Provenance</span>
                      </div>
                    </TableHead>
                    <TableHead>
                      <div class="flex items-center space-x-2">
                        <span>Contact</span>
                      </div>
                    </TableHead>
                    <TableHead>
                      <div class="flex items-center space-x-2">
                        <span>Période</span>
                      </div>
                    </TableHead>
                    <TableHead>
                      <div class="flex items-center space-x-2">
                        <span>Statut</span>
                      </div>
                    </TableHead>
                    <TableHead class="text-right">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow v-for="reservation in reservations.data" :key="reservation.id">
                    <TableCell class="font-medium">
                      <Link :href="route('app.organizations.reservations.in.edit', reservation)">
                        {{ reservation.lender_organization.name }}
                      </Link>
                    </TableCell>
                    <TableCell>
                      {{ reservation.user.name }}<br>
                      <span class="text-xs text-gray-500">
                        {{ reservation.user.phone }}
                      </span>
                    </TableCell>
                    <TableCell>
                      Du {{ formatDate(reservation.start_date) }}<br>
                      au {{ formatDate(reservation.end_date) }}
                    </TableCell>
                    <TableCell>
                      <span :class="[
                        reservation.status_color,
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium'
                      ]">
                        {{ reservation.status_label }}
                      </span>
                    </TableCell>
                    <TableCell class="text-right">
                      <div class="flex items-center justify-end space-x-2">
                        <Button asChild>
                          <Link :href="route('app.organizations.reservations.in.edit',reservation)">
                            Gérer
                          </Link>
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>

              <!-- Pagination -->
              <div class="mt-4">
                <Pagination :links="reservations.links" />
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </ReservationsLayout>
</template>

<script setup>
import { Link, Head } from '@inertiajs/vue3'
import ReservationsLayout from '@/pages/App/Organizations/Reservations/Layout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import Pagination from '@/components/Pagination.vue'

defineProps({
  organization: {
    type: Object,
    required: true
  },
  reservations: {
    type: Object,
    required: true
  },
  can: {
    type: Object,
    required: true
  }
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script> 