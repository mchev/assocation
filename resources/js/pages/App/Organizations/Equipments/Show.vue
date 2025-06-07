<template>
  <AppLayout :title="equipment.name">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ equipment.name }}
        </h2>
        <div class="flex items-center space-x-4">
          <Link
            :href="route('organizations.equipment.index', organization)"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
          >
            Retour à la liste
          </Link>
          <Link
            v-if="can.update"
            :href="route('organizations.equipment.edit', [organization, equipment])"
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
                <div>
                  <h3 class="text-lg font-medium text-gray-900">Description</h3>
                  <p class="mt-2 text-gray-600">{{ equipment.description }}</p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <div>
                    <h3 class="text-lg font-medium text-gray-900">Catégorie</h3>
                    <p class="mt-2 text-gray-600">{{ equipment.category }}</p>
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

                <div v-if="equipment.is_rentable && equipment.is_available" class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                  <div class="mt-4 space-y-4">
                    <Link
                      :href="route('organizations.equipment.reserve', [organization, equipment])"
                      class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                      Réserver
                    </Link>
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
import { Link } from '@inertiajs/vue3'

defineProps({
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
</script> 