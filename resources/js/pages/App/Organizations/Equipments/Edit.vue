<template>
  <AppLayout :title="'Modifier ' + equipment.name">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Modifier {{ equipment.name }}
        </h2>
        <div class="flex items-center space-x-4">
          <Link
            :href="route('organizations.equipment.show', [organization, equipment])"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
          >
            Annuler
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <!-- Nom -->
              <div>
                <InputLabel for="name" value="Nom" />
                <TextInput
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full"
                  required
                  autofocus
                />
                <InputError :message="form.errors.name" class="mt-2" />
              </div>

              <!-- Catégorie -->
              <div>
                <InputLabel for="category" value="Catégorie" />
                <TextInput
                  id="category"
                  v-model="form.category"
                  type="text"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.category" class="mt-2" />
              </div>

              <!-- Description -->
              <div class="sm:col-span-2">
                <InputLabel for="description" value="Description" />
                <TextArea
                  id="description"
                  v-model="form.description"
                  class="mt-1 block w-full"
                  rows="3"
                />
                <InputError :message="form.errors.description" class="mt-2" />
              </div>

              <!-- État -->
              <div>
                <InputLabel for="condition" value="État" />
                <SelectInput
                  id="condition"
                  v-model="form.condition"
                  class="mt-1 block w-full"
                  required
                >
                  <option value="new">Neuf</option>
                  <option value="good">Bon</option>
                  <option value="fair">Moyen</option>
                  <option value="poor">Mauvais</option>
                </SelectInput>
                <InputError :message="form.errors.condition" class="mt-2" />
              </div>

              <!-- Prix d'achat -->
              <div>
                <InputLabel for="purchase_price" value="Prix d'achat" />
                <TextInput
                  id="purchase_price"
                  v-model="form.purchase_price"
                  type="number"
                  step="0.01"
                  min="0"
                  class="mt-1 block w-full"
                />
                <InputError :message="form.errors.purchase_price" class="mt-2" />
              </div>

              <!-- Prix de location -->
              <div>
                <InputLabel for="rental_price" value="Prix de location" />
                <TextInput
                  id="rental_price"
                  v-model="form.rental_price"
                  type="number"
                  step="0.01"
                  min="0"
                  class="mt-1 block w-full"
                />
                <InputError :message="form.errors.rental_price" class="mt-2" />
              </div>

              <!-- Montant de la caution -->
              <div>
                <InputLabel for="deposit_amount" value="Montant de la caution" />
                <TextInput
                  id="deposit_amount"
                  v-model="form.deposit_amount"
                  type="number"
                  step="0.01"
                  min="0"
                  class="mt-1 block w-full"
                />
                <InputError :message="form.errors.deposit_amount" class="mt-2" />
              </div>

              <!-- Options -->
              <div class="sm:col-span-2">
                <div class="flex items-center space-x-6">
                  <label class="flex items-center">
                    <Checkbox
                      v-model:checked="form.is_available"
                      name="is_available"
                    />
                    <span class="ml-2 text-sm text-gray-600">Disponible</span>
                  </label>

                  <label class="flex items-center">
                    <Checkbox
                      v-model:checked="form.requires_deposit"
                      name="requires_deposit"
                    />
                    <span class="ml-2 text-sm text-gray-600">Nécessite une caution</span>
                  </label>

                  <label class="flex items-center">
                    <Checkbox
                      v-model:checked="form.is_rentable"
                      name="is_rentable"
                    />
                    <span class="ml-2 text-sm text-gray-600">Louable</span>
                  </label>
                </div>
              </div>

              <!-- Dernière maintenance -->
              <div>
                <InputLabel for="last_maintenance_date" value="Dernière maintenance" />
                <TextInput
                  id="last_maintenance_date"
                  v-model="form.last_maintenance_date"
                  type="date"
                  class="mt-1 block w-full"
                />
                <InputError :message="form.errors.last_maintenance_date" class="mt-2" />
              </div>

              <!-- Prochaine maintenance -->
              <div>
                <InputLabel for="next_maintenance_date" value="Prochaine maintenance" />
                <TextInput
                  id="next_maintenance_date"
                  v-model="form.next_maintenance_date"
                  type="date"
                  class="mt-1 block w-full"
                />
                <InputError :message="form.errors.next_maintenance_date" class="mt-2" />
              </div>

              <!-- Spécifications -->
              <div class="sm:col-span-2">
                <InputLabel value="Spécifications" />
                <div class="mt-2 space-y-4">
                  <div
                    v-for="(value, key) in form.specifications"
                    :key="key"
                    class="flex items-center space-x-4"
                  >
                    <TextInput
                      v-model="form.specifications[key]"
                      type="text"
                      class="block w-full"
                      :placeholder="key"
                    />
                    <button
                      type="button"
                      @click="removeSpecification(key)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Supprimer
                    </button>
                  </div>
                  <div class="flex items-center space-x-4">
                    <TextInput
                      v-model="newSpecificationKey"
                      type="text"
                      class="block w-full"
                      placeholder="Nouvelle spécification"
                    />
                    <button
                      type="button"
                      @click="addSpecification"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      Ajouter
                    </button>
                  </div>
                </div>
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
                Enregistrer les modifications
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import InputError from '@/components/InputError.vue'
import InputLabel from '@/components/InputLabel.vue'
import PrimaryButton from '@/components/PrimaryButton.vue'
import TextInput from '@/components/TextInput.vue'
import TextArea from '@/components/TextArea.vue'
import SelectInput from '@/components/SelectInput.vue'
import Checkbox from '@/components/Checkbox.vue'
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

const newSpecificationKey = ref('')

const form = useForm({
  name: props.equipment.name,
  description: props.equipment.description,
  category: props.equipment.category,
  condition: props.equipment.condition,
  purchase_price: props.equipment.purchase_price,
  rental_price: props.equipment.rental_price,
  deposit_amount: props.equipment.deposit_amount,
  is_available: props.equipment.is_available,
  requires_deposit: props.equipment.requires_deposit,
  is_rentable: props.equipment.is_rentable,
  specifications: { ...props.equipment.specifications },
  last_maintenance_date: props.equipment.last_maintenance_date,
  next_maintenance_date: props.equipment.next_maintenance_date
})

const addSpecification = () => {
  if (newSpecificationKey.value && !form.specifications[newSpecificationKey.value]) {
    form.specifications[newSpecificationKey.value] = ''
    newSpecificationKey.value = ''
  }
}

const removeSpecification = (key) => {
  const { [key]: removed, ...rest } = form.specifications
  form.specifications = rest
}

const submit = () => {
  form.put(route('organizations.equipment.update', [props.organization, props.equipment]))
}
</script> 