<template>
  <AppLayout :title="'Réservation - ' + reservation.equipment.name">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Réservation de {{ reservation.equipment.name }}
        </h2>
        <div class="flex items-center space-x-4">
          <Link
            :href="route('organizations.reservations.index', organization)"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
          >
            Retour à la liste
          </Link>
          <Link
            v-if="can.update"
            :href="route('organizations.reservations.edit', [organization, reservation])"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            Modifier
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
              <!-- Informations principales -->
              <div class="lg:col-span-2 space-y-6">
                <!-- Statut -->
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-medium text-gray-900">Statut</h3>
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

                <!-- Période -->
                <div>
                  <h3 class="text-lg font-medium text-gray-900">Période</h3>
                  <p class="mt-2 text-gray-600">
                    Du {{ formatDate(reservation.start_date) }}
                    au {{ formatDate(reservation.end_date) }}
                  </p>
                </div>

                <!-- Motif -->
                <div>
                  <h3 class="text-lg font-medium text-gray-900">Motif</h3>
                  <p class="mt-2 text-gray-600">{{ reservation.purpose }}</p>
                </div>

                <!-- Notes -->
                <div v-if="reservation.notes">
                  <h3 class="text-lg font-medium text-gray-900">Notes</h3>
                  <p class="mt-2 text-gray-600">{{ reservation.notes }}</p>
                </div>

                <!-- Informations sur le matériel -->
                <div>
                  <h3 class="text-lg font-medium text-gray-900">Matériel</h3>
                  <dl class="mt-2 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Nom</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ reservation.equipment.name }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Catégorie</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ reservation.equipment.category }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Prix de location</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ reservation.equipment.rental_price ? `${reservation.equipment.rental_price} €` : 'Non spécifié' }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Caution requise</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ reservation.equipment.requires_deposit ? `${reservation.equipment.deposit_amount} €` : 'Non' }}
                      </dd>
                    </div>
                  </dl>
                </div>
              </div>

              <!-- Actions -->
              <div class="space-y-6">
                <!-- Informations sur le demandeur -->
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900">Demandeur</h3>
                  <dl class="mt-4 space-y-4">
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Nom</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ reservation.user.name }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Email</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ reservation.user.email }}
                      </dd>
                    </div>
                  </dl>
                </div>

                <!-- Actions disponibles -->
                <div v-if="can.update && reservation.status === 'pending'" class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                  <div class="mt-4 space-y-4">
                    <form @submit.prevent="approve" class="inline-block">
                      <Button type="submit">
                        Approuver
                      </Button>
                    </form>

                    <form @submit.prevent="reject" class="inline-block ml-4">
                      <Button type="submit">
                        Rejeter
                      </Button>
                    </form>
                  </div>
                </div>

                <div v-if="can.update && ['pending', 'approved'].includes(reservation.status)" class="bg-gray-50 p-6 rounded-lg">
                  <form @submit.prevent="cancel" class="inline-block">
                    <Button type="submit">
                      Annuler la réservation
                    </Button>
                  </form>
                </div>

                <div v-if="can.delete" class="bg-gray-50 p-6 rounded-lg">
                  <form @submit.prevent="destroy" class="inline-block">
                    <Button type="submit">
                      Supprimer la réservation
                    </Button>
                  </form>
                </div>
              </div>
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
import { Button } from '@/components/ui/button'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  organization: {
    type: Object,
    required: true
  },
  reservation: {
    type: Object,
    required: true
  },
  can: {
    type: Object,
    required: true
  }
})

const form = useForm({})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const approve = () => {
  form.post(route('organizations.reservations.approve', [props.organization, props.reservation]))
}

const reject = () => {
  form.post(route('organizations.reservations.reject', [props.organization, props.reservation]))
}

const cancel = () => {
  form.post(route('organizations.reservations.cancel', [props.organization, props.reservation]))
}

const destroy = () => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?')) {
    form.delete(route('organizations.reservations.destroy', [props.organization, props.reservation]))
  }
}
</script> 