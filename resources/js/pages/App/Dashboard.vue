<template>
  <AppLayout>
    <Head title="Dashboard" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
              <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Total Equipment
                  </dt>
                  <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ stats.equipment_count }}
                  </dd>
                </div>
              </div>

              <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Active Reservations
                  </dt>
                  <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ stats.reservations_count }}
                  </dd>
                </div>
              </div>

              <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Depots
                  </dt>
                  <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ stats.depots_count }}
                  </dd>
                </div>
              </div>
            </div>

            <div class="mt-8">
              <h2 class="text-lg font-medium text-gray-900">Recent Activity</h2>
              <!-- Add your recent activity content here -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps({
  stats: {
    type: Object,
    required: true
  }
});
</script> 