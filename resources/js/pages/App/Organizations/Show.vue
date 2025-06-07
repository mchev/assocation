<template>
  <AppLayout :title="organization.name">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ organization.name }}
        </h2>
        <div class="flex items-center space-x-4">
          <Link
            v-if="isAdmin"
            :href="route('organizations.edit', organization)"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
          >
            Modifier
          </Link>
          <Link
            :href="route('organizations.equipment.create', organization)"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            Ajouter du matériel
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <!-- Informations de l'organisation -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <div>
                <h3 class="text-lg font-medium text-gray-900">Informations</h3>
                <dl class="mt-4 space-y-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ organization.description || 'Non renseignée' }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Adresse</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ organization.address }}<br>
                      {{ organization.postal_code }} {{ organization.city }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Contact</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      <div v-if="organization.phone">Tél: {{ organization.phone }}</div>
                      <div v-if="organization.email">Email: {{ organization.email }}</div>
                      <div v-if="organization.website">
                        Site web: <a :href="organization.website" target="_blank" class="text-indigo-600 hover:text-indigo-500">{{ organization.website }}</a>
                      </div>
                    </dd>
                  </div>
                </dl>
              </div>

              <!-- Statistiques -->
              <div>
                <h3 class="text-lg font-medium text-gray-900">Statistiques</h3>
                <dl class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2">
                  <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Équipements</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ organization.equipment_count }}</dd>
                  </div>
                  <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Membres</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ organization.users_count }}</dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Liste du matériel -->
            <div class="mt-8">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Matériel</h3>
                <Link
                  :href="route('organizations.equipment.create', organization)"
                  class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >
                  <PlusIcon class="-ml-0.5 mr-1.5 h-5 w-5" aria-hidden="true" />
                  Ajouter du matériel
                </Link>
              </div>

              <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                  v-for="equipment in organization.equipment"
                  :key="equipment.id"
                  class="relative flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white"
                >
                  <div class="flex flex-1 flex-col p-6">
                    <div class="flex items-center">
                      <div class="h-12 w-12 flex-shrink-0">
                        <img
                          :src="equipment.images?.[0] || '/images/default-equipment.png'"
                          :alt="equipment.name"
                          class="h-12 w-12 rounded-lg object-cover"
                        />
                      </div>
                      <div class="ml-4">
                        <h4 class="text-sm font-medium text-gray-900">
                          <Link :href="route('organizations.equipment.show', [organization, equipment])" class="hover:underline">
                            {{ equipment.name }}
                          </Link>
                        </h4>
                        <p class="text-sm text-gray-500">{{ equipment.category }}</p>
                      </div>
                    </div>
                    <div class="mt-4 flex-1">
                      <p class="text-sm text-gray-500 line-clamp-2">{{ equipment.description }}</p>
                    </div>
                    <div class="mt-6">
                      <div class="flex items-center justify-between">
                        <span
                          :class="{
                            'bg-green-50 text-green-700 ring-green-600/20': equipment.is_available,
                            'bg-red-50 text-red-700 ring-red-600/20': !equipment.is_available
                          }"
                          class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset"
                        >
                          {{ equipment.is_available ? 'Disponible' : 'Indisponible' }}
                        </span>
                        <Link
                          :href="route('organizations.equipment.show', [organization, equipment])"
                          class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                        >
                          Voir les détails
                          <span aria-hidden="true"> &rarr;</span>
                        </Link>
                      </div>
                    </div>
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
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { PlusIcon } from '@heroicons/vue/24/outline'

defineProps({
  organization: {
    type: Object,
    required: true
  },
  isAdmin: {
    type: Boolean,
    required: true
  }
})
</script> 