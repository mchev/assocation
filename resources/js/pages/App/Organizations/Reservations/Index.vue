<template>
  <AppLayout :title="'Réservations - ' + organization.name">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Réservations de {{ organization.name }} ({{ reservations.total }})
        </h2>
        <Button
          v-if="can.create"
          as="a"
          :href="route('organizations.reservations.create', organization)"
          variant="default"
          size="default"
        >
          Nouvelle réservation
        </Button>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filtres -->
        <Card class="mb-6">
          <CardContent class="p-4">
            <form @submit.prevent="filter">
              <div class="flex flex-wrap items-end gap-6">
                <div>
                  <Label for="search" class="text-sm">Rechercher</Label>
                  <Input
                    id="search"
                    v-model="filters.search"
                    type="text"
                    placeholder="Matériel, utilisateur..."
                    @input="filter"
                  />
                </div>

                <div>
                  <Label for="status" class="text-sm">Statut</Label>
                  <Select v-model="filters.status" @update:modelValue="filter">
                    <SelectTrigger class="h-9">
                      <SelectValue placeholder="Tous" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem :value="null">Tous les statuts</SelectItem>
                      <SelectItem value="pending">En attente</SelectItem>
                      <SelectItem value="approved">Approuvée</SelectItem>
                      <SelectItem value="rejected">Rejetée</SelectItem>
                      <SelectItem value="cancelled">Annulée</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div>
                  <Label for="start_date" class="text-sm">Date de début</Label>
                  <Input
                    id="start_date"
                    v-model="filters.start_date"
                    type="date"
                    class="h-9"
                  />
                </div>

                <div>
                  <Label for="end_date" class="text-sm">Date de fin</Label>
                  <Input
                    id="end_date"
                    v-model="filters.end_date"
                    type="date"
                    class="h-9"
                  />
                </div>

                <div class="flex gap-2 ml-auto">
                  <Button 
                    type="button" 
                    variant="outline" 
                    @click="resetFilters"
                    size="sm"
                    class="h-9"
                  >
                    Réinitialiser
                  </Button>
                  <Button 
                    type="submit" 
                    variant="default"
                    size="sm"
                    class="h-9"
                  >
                    Filtrer
                  </Button>
                </div>
              </div>
            </form>
          </CardContent>
        </Card>

        <!-- Liste des réservations -->
        <Card>
          <CardContent class="p-6">
            <div v-if="reservations.data.length === 0" class="text-center py-12">
              <p class="text-muted-foreground text-lg">
                Aucune réservation trouvée
              </p>
              <Button
                v-if="can.create"
                as="a"
                :href="route('organizations.reservations.create', organization)"
                variant="default"
                class="mt-4"
              >
                Nouvelle réservation
              </Button>
            </div>

            <div v-else>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead 
                      class="cursor-pointer hover:bg-muted/50"
                      @click="sort('equipment.name')"
                    >
                      <div class="flex items-center space-x-2">
                        <span>Matériel</span>
                        <component
                          :is="getSortIcon('equipment.name')"
                          class="w-4 h-4"
                        />
                      </div>
                    </TableHead>
                    <TableHead 
                      class="cursor-pointer hover:bg-muted/50"
                      @click="sort('user.name')"
                    >
                      <div class="flex items-center space-x-2">
                        <span>Utilisateur</span>
                        <component
                          :is="getSortIcon('user.name')"
                          class="w-4 h-4"
                        />
                      </div>
                    </TableHead>
                    <TableHead 
                      class="cursor-pointer hover:bg-muted/50"
                      @click="sort('start_date')"
                    >
                      <div class="flex items-center space-x-2">
                        <span>Période</span>
                        <component
                          :is="getSortIcon('start_date')"
                          class="w-4 h-4"
                        />
                      </div>
                    </TableHead>
                    <TableHead 
                      class="cursor-pointer hover:bg-muted/50"
                      @click="sort('status')"
                    >
                      <div class="flex items-center space-x-2">
                        <span>Statut</span>
                        <component
                          :is="getSortIcon('status')"
                          class="w-4 h-4"
                        />
                      </div>
                    </TableHead>
                    <TableHead class="text-right">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow v-for="reservation in reservations.data" :key="reservation.id">
                    <TableCell class="font-medium">
                      <Link :href="route('organizations.reservations.show', [organization, reservation])">
                        {{ reservation.equipment.name }}
                      </Link>
                    </TableCell>
                    <TableCell>{{ reservation.user.name }}</TableCell>
                    <TableCell>
                      Du {{ formatDate(reservation.start_date) }}<br>
                      au {{ formatDate(reservation.end_date) }}
                    </TableCell>
                    <TableCell>
                      <Badge
                        :variant="{
                          pending: 'warning',
                          approved: 'success',
                          rejected: 'destructive',
                          cancelled: 'secondary'
                        }[reservation.status]"
                      >
                        {{ {
                          pending: 'En attente',
                          approved: 'Approuvée',
                          rejected: 'Rejetée',
                          cancelled: 'Annulée'
                        }[reservation.status] }}
                      </Badge>
                    </TableCell>
                    <TableCell class="text-right">
                      <div class="flex items-center justify-end space-x-2">
                        <Button
                          as="a"
                          :href="route('organizations.reservations.show', [organization, reservation])"
                          size="sm"
                        >
                          Voir
                        </Button>

                        <Button
                          v-if="can.update"
                          as="a"
                          :href="route('organizations.reservations.edit', [organization, reservation])"
                          size="sm"
                        >
                          Modifier
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>

              <!-- Pagination -->
              <div class="mt-4">
                <Pagination
                  v-if="reservations.links.length > 3"
                  :links="reservations.links"
                />
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { ArrowUpDown, ArrowUp, ArrowDown } from 'lucide-vue-next'
import Pagination from '@/components/Pagination.vue'

const props = defineProps({
  organization: {
    type: Object,
    required: true
  },
  reservations: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    required: true
  },
  can: {
    type: Object,
    required: true
  }
})

const filters = useForm({
  search: props.filters?.search ?? '',
  status: props.filters?.status ?? null,
  start_date: props.filters?.start_date ?? '',
  end_date: props.filters?.end_date ?? '',
  sort: props.filters?.sort ?? 'start_date',
  direction: props.filters?.direction ?? 'desc'
})

const filter = () => {
  filters.get(route('organizations.reservations.index', props.organization), {
    preserveState: true,
    preserveScroll: true
  })
}

const resetFilters = () => {
  filters.search = ''
  filters.status = null
  filters.start_date = ''
  filters.end_date = ''
  filters.sort = 'start_date'
  filters.direction = 'desc'
  filter()
}

const sort = (column) => {
  if (filters.sort === column) {
    filters.direction = filters.direction === 'asc' ? 'desc' : 'asc'
  } else {
    filters.sort = column
    filters.direction = 'asc'
  }
  filter()
}

const getSortIcon = (column) => {
  if (filters.sort !== column) return ArrowUpDown
  return filters.direction === 'asc' ? ArrowUp : ArrowDown
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script> 