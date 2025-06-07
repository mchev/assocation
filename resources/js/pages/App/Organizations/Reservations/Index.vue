<template>
  <AppLayout :title="'Réservations - ' + organization.name">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Réservations
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filtres -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <form @submit.prevent="filter" class="space-y-4">
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div>
                  <InputLabel for="search" value="Rechercher" />
                  <TextInput
                    id="search"
                    v-model="filters.search"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="Matériel, utilisateur..."
                  />
                </div>

                <div>
                  <InputLabel for="status" value="Statut" />
                  <SelectInput
                    id="status"
                    v-model="filters.status"
                    class="mt-1 block w-full"
                  >
                    <option value="">Tous les statuts</option>
                    <option value="pending">En attente</option>
                    <option value="approved">Approuvée</option>
                    <option value="rejected">Rejetée</option>
                    <option value="cancelled">Annulée</option>
                  </SelectInput>
                </div>

                <div>
                  <InputLabel for="start_date" value="Date de début" />
                  <TextInput
                    id="start_date"
                    v-model="filters.start_date"
                    type="date"
                    class="mt-1 block w-full"
                  />
                </div>

                <div>
                  <InputLabel for="end_date" value="Date de fin" />
                  <TextInput
                    id="end_date"
                    v-model="filters.end_date"
                    type="date"
                    class="mt-1 block w-full"
                  />
                </div>
              </div>

              <div class="flex justify-end">
                <PrimaryButton type="submit">
                  Filtrer
                </PrimaryButton>
              </div>
            </form>
          </div>
        </div>

        <!-- Liste des réservations -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div v-if="reservations.data.length === 0" class="text-center py-12">
              <p class="text-gray-500 text-lg">
                Aucune réservation trouvée
              </p>
            </div>

            <div v-else class="space-y-6">
              <div
                v-for="reservation in reservations.data"
                :key="reservation.id"
                class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200"
              >
                <div class="p-6">
                  <div class="flex items-center justify-between">
                    <div>
                      <h3 class="text-lg font-medium text-gray-900">
                        {{ reservation.equipment.name }}
                      </h3>
                      <p class="mt-1 text-sm text-gray-500">
                        Réservé par {{ reservation.user.name }}
                      </p>
                    </div>
                    <span
                      :class="{
                        'bg-yellow-100 text-yellow-800': reservation.status === 'pending',
                        'bg-green-100 text-green-800': reservation.status === 'approved',
                        'bg-red-100 text-red-800': reservation.status === 'rejected',
                        'bg-gray-100 text-gray-800': reservation.status === 'cancelled'
                      }"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    >
                      {{ {
                        pending: 'En attente',
                        approved: 'Approuvée',
                        rejected: 'Rejetée',
                        cancelled: 'Annulée'
                      }[reservation.status] }}
                    </span>
                  </div>

                  <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                      <p class="text-sm text-gray-500">Période</p>
                      <p class="mt-1 text-sm text-gray-900">
                        Du {{ formatDate(reservation.start_date) }}
                        au {{ formatDate(reservation.end_date) }}
                      </p>
                    </div>

                    <div>
                      <p class="text-sm text-gray-500">Motif</p>
                      <p class="mt-1 text-sm text-gray-900 line-clamp-2">
                        {{ reservation.purpose }}
                      </p>
                    </div>
                  </div>

                  <div class="mt-4 flex items-center justify-end space-x-4">
                    <Link
                      :href="route('organizations.reservations.show', [organization, reservation])"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      Voir les détails
                    </Link>

                    <Link
                      v-if="can.update"
                      :href="route('organizations.reservations.edit', [organization, reservation])"
                      class="text-gray-600 hover:text-gray-900"
                    >
                      Modifier
                    </Link>
                  </div>
                </div>
              </div>

              <!-- Pagination -->
              <Pagination
                v-if="reservations.data.length > 0"
                :links="reservations.links"
                class="mt-6"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import InputError from '@/components/InputError.vue'
import InputLabel from '@/components/InputLabel.vue'
import PrimaryButton from '@/components/PrimaryButton.vue'
import TextInput from '@/components/TextInput.vue'
import SelectInput from '@/components/SelectInput.vue'
import Pagination from '@/components/Pagination.vue'
import { Link } from '@inertiajs/vue3'

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
  search: props.filters.search || '',
  status: props.filters.status || '',
  start_date: props.filters.start_date || '',
  end_date: props.filters.end_date || ''
})

const filter = () => {
  filters.get(route('organizations.reservations.index', props.organization), {
    preserveState: true,
    preserveScroll: true
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script> 