<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Textarea } from '@/components/ui/textarea'
import AppLayout from '@/layouts/AppLayout.vue'
import { ref, computed } from 'vue'

const props = defineProps({
  organizations: Array,
  equipment: Array,
  discountTypes: Object,
})

const form = useForm({
  to_organization_id: '',
  start_date: '',
  end_date: '',
  notes: '',
  items: [],
  discount_type: null,
  discount_value: null,
  discount_reason: null,
})

const selectedEquipment = ref([])

const addEquipment = () => {
  selectedEquipment.value.push({
    equipment_id: '',
    depot_id: '',
    quantity: 1,
    price: 0,
    notes: '',
  })
}

const removeEquipment = (index) => {
  selectedEquipment.value.splice(index, 1)
}

const getEquipmentDepot = (equipmentId) => {
  const equipment = props.equipment.find(e => e.id === equipmentId)
  return equipment?.depot
}

const subtotal = computed(() => {
  return selectedEquipment.value.reduce((total, item) => {
    return total + (item.price * item.quantity)
  }, 0)
})

const discountAmount = computed(() => {
  if (!form.discount_type || !form.discount_value) return 0
  
  if (form.discount_type === 'percentage') {
    return Math.round(subtotal.value * (form.discount_value / 100))
  }
  
  return form.discount_value
})

const total = computed(() => {
  return Math.max(0, subtotal.value - discountAmount.value)
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
  }).format(price / 100)
}

const submit = () => {
  form.items = selectedEquipment.value
  form.post(route('app.reservations.store'))
}
</script>

<template>
  <AppLayout>
    <Head title="Nouvelle réservation" />

    <div class="max-w-7xl mx-auto py-6 space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold">Nouvelle réservation</h1>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <Card>
          <CardHeader>
            <CardTitle>Informations générales</CardTitle>
            <CardDescription>Informations de base de la réservation</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <Label for="to_organization_id">Organisation emprunteuse</Label>
              <Select v-model="form.to_organization_id">
                <SelectTrigger>
                  <SelectValue placeholder="Sélectionner une organisation" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="org in organizations" :key="org.id" :value="org.id">
                    {{ org.name }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <div v-if="form.errors.to_organization_id" class="text-sm text-red-600">
                {{ form.errors.to_organization_id }}
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label for="start_date">Date de début</Label>
                <Input
                  type="datetime-local"
                  v-model="form.start_date"
                  :min="new Date().toISOString().slice(0, 16)"
                />
                <div v-if="form.errors.start_date" class="text-sm text-red-600">
                  {{ form.errors.start_date }}
                </div>
              </div>

              <div>
                <Label for="end_date">Date de fin</Label>
                <Input
                  type="datetime-local"
                  v-model="form.end_date"
                  :min="form.start_date"
                />
                <div v-if="form.errors.end_date" class="text-sm text-red-600">
                  {{ form.errors.end_date }}
                </div>
              </div>
            </div>

            <div>
              <Label for="notes">Notes</Label>
              <Textarea v-model="form.notes" rows="3" />
              <div v-if="form.errors.notes" class="text-sm text-red-600">
                {{ form.errors.notes }}
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Équipements</CardTitle>
            <CardDescription>Sélectionnez les équipements à réserver</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div v-for="(item, index) in selectedEquipment" :key="index" class="border-b pb-4">
              <div class="flex justify-between items-start mb-4">
                <h4 class="text-lg font-medium">Équipement {{ index + 1 }}</h4>
                <Button
                  type="button"
                  variant="destructive"
                  size="sm"
                  @click="removeEquipment(index)"
                >
                  Supprimer
                </Button>
              </div>

              <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                  <Label>Équipement</Label>
                  <Select v-model="item.equipment_id">
                    <SelectTrigger>
                      <SelectValue placeholder="Sélectionner un équipement" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem v-for="eq in equipment" :key="eq.id" :value="eq.id">
                        {{ eq.name }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div>
                  <Label>Dépôt</Label>
                  <Input
                    type="text"
                    :value="getEquipmentDepot(item.equipment_id)?.name"
                    readonly
                    disabled
                  />
                  <input type="hidden" v-model="item.depot_id" :value="getEquipmentDepot(item.equipment_id)?.id">
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                  <Label>Quantité</Label>
                  <Input type="number" v-model="item.quantity" min="1" />
                </div>

                <div>
                  <Label>Prix unitaire (en centimes)</Label>
                  <Input type="number" v-model="item.price" min="0" />
                </div>
              </div>

              <div>
                <Label>Notes</Label>
                <Textarea v-model="item.notes" rows="2" />
              </div>
            </div>

            <Button type="button" variant="outline" @click="addEquipment">
              Ajouter un équipement
            </Button>

            <div v-if="form.errors.items" class="text-sm text-red-600">
              {{ form.errors.items }}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Remise</CardTitle>
            <CardDescription>Appliquer une remise à la réservation</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label>Type de remise</Label>
                <Select v-model="form.discount_type">
                  <SelectTrigger>
                    <SelectValue placeholder="Sélectionner un type de remise" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="">Aucune remise</SelectItem>
                    <SelectItem
                      v-for="(label, value) in discountTypes"
                      :key="value"
                      :value="value"
                    >
                      {{ label }}
                    </SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div v-if="form.discount_type">
                <Label>
                  {{ form.discount_type === 'percentage' ? 'Pourcentage' : 'Montant (en centimes)' }}
                </Label>
                <Input
                  type="number"
                  v-model="form.discount_value"
                  :min="0"
                  :max="form.discount_type === 'percentage' ? 100 : null"
                />
              </div>
            </div>

            <div v-if="form.discount_type">
              <Label>Raison de la remise</Label>
              <Textarea v-model="form.discount_reason" rows="2" />
            </div>

            <div class="mt-4 space-y-2">
              <div class="flex justify-between text-sm">
                <span>Sous-total:</span>
                <span>{{ formatPrice(subtotal) }}</span>
              </div>
              <div v-if="discountAmount" class="flex justify-between text-sm text-gray-600">
                <span>Remise:</span>
                <span>-{{ formatPrice(discountAmount) }}</span>
              </div>
              <div class="flex justify-between font-medium">
                <span>Total:</span>
                <span>{{ formatPrice(total) }}</span>
              </div>
            </div>
          </CardContent>
        </Card>

        <div class="flex justify-end space-x-4">
          <Button
            type="button"
            variant="outline"
            :href="route('app.reservations.index')"
          >
            Annuler
          </Button>
          <Button
            type="submit"
            :disabled="form.processing || !selectedEquipment.length"
          >
            Créer la réservation
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template> 