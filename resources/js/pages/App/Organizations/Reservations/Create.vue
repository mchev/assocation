<template>
  <AppLayout :title="'Réserver ' + equipment.name">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Réserver {{ equipment.name }}
        </h2>
        <Link
          :href="route('organizations.equipment.show', [organization, equipment])"
          class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
        >
          Annuler
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <!-- Dates de réservation -->
              <div>
                <InputLabel for="start_date" value="Date de début" />
                <TextInput
                  id="start_date"
                  v-model="form.start_date"
                  type="date"
                  class="mt-1 block w-full"
                  required
                  :min="minDate"
                />
                <InputError :message="form.errors.start_date" class="mt-2" />
              </div>

              <div>
                <InputLabel for="end_date" value="Date de fin" />
                <TextInput
                  id="end_date"
                  v-model="form.end_date"
                  type="date"
                  class="mt-1 block w-full"
                  required
                  :min="form.start_date || minDate"
                />
                <InputError :message="form.errors.end_date" class="mt-2" />
              </div>

              <!-- Motif de la réservation -->
              <div class="sm:col-span-2">
                <InputLabel for="purpose" value="Motif de la réservation" />
                <TextArea
                  id="purpose"
                  v-model="form.purpose"
                  class="mt-1 block w-full"
                  rows="3"
                  required
                  placeholder="Décrivez l'utilisation prévue du matériel..."
                />
                <InputError :message="form.errors.purpose" class="mt-2" />
              </div>

              <!-- Notes supplémentaires -->
              <div class="sm:col-span-2">
                <InputLabel for="notes" value="Notes supplémentaires" />
                <TextArea
                  id="notes"
                  v-model="form.notes"
                  class="mt-1 block w-full"
                  rows="2"
                  placeholder="Informations complémentaires (optionnel)..."
                />
                <InputError :message="form.errors.notes" class="mt-2" />
              </div>

              <!-- Informations sur le matériel -->
              <div class="sm:col-span-2 bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations sur le matériel</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Prix de location</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ equipment.rental_price ? `${equipment.rental_price} €` : 'Non spécifié' }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Caution requise</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ equipment.requires_deposit ? `${equipment.deposit_amount} €` : 'Non' }}
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
              <Link
                :href="route('organizations.equipment.show', [organization, equipment])"
                class="text-sm font-semibold leading-6 text-gray-900"
              >
                Annuler
              </Link>
              <PrimaryButton
                type="submit"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                Soumettre la réservation
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import InputError from '@/components/InputError.vue'
import InputLabel from '@/components/InputLabel.vue'
import PrimaryButton from '@/components/PrimaryButton.vue'
import TextInput from '@/components/TextInput.vue'
import TextArea from '@/components/TextArea.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  organization: {
    type: Object,
    required: true
  },
  equipment: {
    type: Object,
    required: true
  }
})

const minDate = computed(() => {
  const today = new Date()
  return today.toISOString().split('T')[0]
})

const form = useForm({
  start_date: '',
  end_date: '',
  purpose: '',
  notes: ''
})

const submit = () => {
  form.post(route('organizations.reservations.store', [props.organization, props.equipment]))
}
</script> 