<template>
  <AppLayout :title="equipment.name">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ equipment.name }}
        </h2>
        <div class="flex items-center space-x-4">
          <Link
            :href="route('app.organizations.equipments.index')"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
          >
            Retour à la liste
          </Link>
          <Link
            v-if="can.update"
            :href="route('app.organizations.equipments.edit', equipment)"
            class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary/90 focus:bg-primary/90 active:bg-primary/95 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            Modifier
          </Link>
          <Button
            v-if="can.delete"
            type="button"
            @click="deleteEquipment"
            variant="destructive"
          >
            Supprimer
          </Button>
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
                <div>
                  <h3 class="text-lg font-medium text-gray-900">Description</h3>
                  <p class="mt-2 text-gray-600">{{ equipment.description }}</p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <div>
                    <h3 class="text-lg font-medium text-gray-900">Catégorie</h3>
                    <p class="mt-2 text-gray-600">{{ equipment.category.name }}</p>
                  </div>

                  <div>
                    <h3 class="text-lg font-medium text-gray-900">État</h3>
                    <p class="mt-2">
                      <span
                        :class="{
                          'bg-blue-100 text-blue-800': equipment.condition === 'new',
                          'bg-green-100 text-green-800': equipment.condition === 'good',
                          'bg-yellow-100 text-yellow-800': equipment.condition === 'fair',
                          'bg-red-100 text-red-800': equipment.condition === 'poor'
                        }"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      >
                        {{ {
                          new: 'Neuf',
                          good: 'Bon',
                          fair: 'Moyen',
                          poor: 'Mauvais'
                        }[equipment.condition] }}
                      </span>
                    </p>
                  </div>

                  <div>
                    <h3 class="text-lg font-medium text-gray-900">Prix d'achat</h3>
                    <p class="mt-2 text-gray-600">
                      {{ equipment.purchase_price ? `${equipment.purchase_price} €` : 'Non spécifié' }}
                    </p>
                  </div>

                  <div>
                    <h3 class="text-lg font-medium text-gray-900">Prix de location</h3>
                    <p class="mt-2 text-gray-600">
                      {{ equipment.rental_price ? `${equipment.rental_price} €` : 'Non spécifié' }}
                    </p>
                  </div>

                  <div>
                    <h3 class="text-lg font-medium text-gray-900">Caution</h3>
                    <p class="mt-2 text-gray-600">
                      {{ equipment.deposit_amount ? `${equipment.deposit_amount} €` : 'Non spécifié' }}
                    </p>
                  </div>

                  <div>
                    <h3 class="text-lg font-medium text-gray-900">Dernière maintenance</h3>
                    <p class="mt-2 text-gray-600">
                      {{ equipment.last_maintenance_date || 'Non spécifiée' }}
                    </p>
                  </div>

                  <div>
                    <h3 class="text-lg font-medium text-gray-900">Prochaine maintenance</h3>
                    <p class="mt-2 text-gray-600">
                      {{ equipment.next_maintenance_date || 'Non spécifiée' }}
                    </p>
                  </div>
                </div>

                <div v-if="equipment.specifications && Object.keys(equipment.specifications).length > 0">
                  <h3 class="text-lg font-medium text-gray-900">Spécifications</h3>
                  <dl class="mt-2 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div v-for="(value, key) in equipment.specifications" :key="key">
                      <dt class="text-sm font-medium text-gray-500">{{ key }}</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ value }}</dd>
                    </div>
                  </dl>
                </div>
              </div>

              <!-- Actions et statut -->
              <div class="space-y-6">
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900">Statut</h3>
                  <div class="mt-4 space-y-4">
                    <div class="flex items-center justify-between">
                      <span class="text-sm text-gray-500">Disponibilité</span>
                      <span
                        :class="{
                          'bg-green-100 text-green-800': equipment.is_available,
                          'bg-red-100 text-red-800': !equipment.is_available
                        }"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      >
                        {{ equipment.is_available ? 'Disponible' : 'Non disponible' }}
                      </span>
                    </div>

                    <div class="flex items-center justify-between">
                      <span class="text-sm text-gray-500">Location</span>
                      <span
                        :class="{
                          'bg-purple-100 text-purple-800': equipment.is_rentable,
                          'bg-gray-100 text-gray-800': !equipment.is_rentable
                        }"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      >
                        {{ equipment.is_rentable ? 'Louable' : 'Non louable' }}
                      </span>
                    </div>

                    <div class="flex items-center justify-between">
                      <span class="text-sm text-gray-500">Caution requise</span>
                      <span
                        :class="{
                          'bg-yellow-100 text-yellow-800': equipment.requires_deposit,
                          'bg-gray-100 text-gray-800': !equipment.requires_deposit
                        }"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      >
                        {{ equipment.requires_deposit ? 'Oui' : 'Non' }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Emplacement actuel -->
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900">Emplacement actuel</h3>
                  <div class="mt-4">
                    <div v-if="equipment.depot" class="space-y-2">
                      <p class="text-sm text-gray-900 font-medium">{{ equipment.depot.name }}</p>
                      <p v-if="equipment.depot.address" class="text-sm text-gray-600">
                        {{ equipment.depot.address }}<br>
                        {{ equipment.depot.city }} {{ equipment.depot.zip_code }}
                      </p>
                    </div>
                    <p v-else class="text-sm text-gray-500 italic">
                      Aucun emplacement spécifié
                    </p>
                  </div>
                </div>

                <!-- Réservations -->
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900">Réservations</h3>
                  <div class="mt-4">
                    <div v-if="equipment.current_rental" class="mb-4">
                      <h4 class="text-sm font-medium text-gray-900">Location en cours</h4>
                      <div class="mt-2 p-3 bg-white rounded border border-gray-200">
                        <p class="text-sm text-gray-600">
                          {{ equipment.current_rental.renter.name }}
                        </p>
                        <p class="text-xs text-gray-500">
                          Du {{ equipment.current_rental.start_date }} au {{ equipment.current_rental.end_date }}
                        </p>
                      </div>
                    </div>

                    <div v-if="equipment.upcoming_rentals && equipment.upcoming_rentals.length > 0">
                      <h4 class="text-sm font-medium text-gray-900">Prochaines réservations</h4>
                      <div class="mt-2 space-y-2">
                        <div
                          v-for="rental in equipment.upcoming_rentals"
                          :key="rental.id"
                          class="p-3 bg-white rounded border border-gray-200"
                        >
                          <p class="text-sm text-gray-600">
                            {{ rental.renter.name }}
                          </p>
                          <p class="text-xs text-gray-500">
                            Du {{ rental.start_date }} au {{ rental.end_date }}
                          </p>
                        </div>
                      </div>
                    </div>

                    <p
                      v-if="(!equipment.current_rental && (!equipment.upcoming_rentals || equipment.upcoming_rentals.length === 0))"
                      class="text-sm text-gray-500 italic"
                    >
                      Aucune réservation en cours ou à venir
                    </p>
                  </div>
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
import AppLayout from '@/layouts/AppLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'

const props = defineProps({
  organization: {
    type: Object,
    required: true
  },
  equipment: {
    type: Object,
    required: true
  },
  can: {
    type: Object,
    required: true
  }
})

const deleteEquipment = () => {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce matériel ?')) {
    router.delete(route('app.organizations.equipments.destroy', [props.organization, props.equipment]))
  }
}
</script> 