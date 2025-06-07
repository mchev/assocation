<template>
    <AppLayout title="Organisations">
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Organisations
          </h2>
          <Link
            :href="route('organizations.create')"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            Nouvelle organisation
          </Link>
        </div>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <div v-if="organizations.length === 0" class="text-center py-12">
                <h3 class="mt-2 text-sm font-semibold text-gray-900">Aucune organisation</h3>
                <p class="mt-1 text-sm text-gray-500">Commencez par créer une nouvelle organisation.</p>
                <div class="mt-6">
                  <Link
                    :href="route('organizations.create')"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                  >
                    <PlusIcon class="-ml-0.5 mr-1.5 h-5 w-5" aria-hidden="true" />
                    Nouvelle organisation
                  </Link>
                </div>
              </div>
  
              <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                  v-for="organization in organizations"
                  :key="organization.id"
                  class="relative flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white"
                >
                  <div class="flex flex-1 flex-col p-6">
                    <div class="flex items-center">
                      <div class="h-12 w-12 flex-shrink-0">
                        <img
                          :src="organization.logo_path || '/images/default-organization.png'"
                          :alt="organization.name"
                          class="h-12 w-12 rounded-full object-cover"
                        />
                      </div>
                      <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">
                          <Link :href="route('organizations.show', organization)" class="hover:underline">
                            {{ organization.name }}
                          </Link>
                        </h3>
                        <p class="text-sm text-gray-500">{{ organization.city }}</p>
                      </div>
                    </div>
                    <div class="mt-4 flex-1">
                      <p class="text-sm text-gray-500 line-clamp-3">{{ organization.description }}</p>
                    </div>
                    <div class="mt-6">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                          <span
                            class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20"
                          >
                            {{ organization.equipment_count }} équipements
                          </span>
                          <span
                            class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20"
                          >
                            {{ organization.users_count }} membres
                          </span>
                        </div>
                        <Link
                          :href="route('organizations.show', organization)"
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
    </AppLayout>
  </template>
  
  <script setup>
  import { Link } from '@inertiajs/vue3'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import { PlusIcon } from '@heroicons/vue/24/outline'
  
  defineProps({
    organizations: {
      type: Array,
      required: true
    }
  })
  </script>