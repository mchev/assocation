<template>
  <form @submit.prevent="submit" class="space-y-6">
    <!-- Titre de la réservation -->
    <div>
      <Label for="title" required>Titre de la réservation</Label>
      <Input
        id="title"
        v-model="form.title"
        type="text"
        placeholder="Ex: Événement externe, Maintenance, etc."
        required
        class="mt-2"
      />
      <p v-if="form.errors.title" class="mt-2 text-sm text-destructive">{{ form.errors.title }}</p>
    </div>

    <!-- Dates -->
    <div class="grid gap-4 sm:grid-cols-2">
      <div>
        <Label for="start_date" required>Date de début</Label>
        <Input
          id="start_date"
          v-model="form.start_date"
          type="date"
          required
          class="mt-2"
        />
        <p v-if="form.errors.start_date" class="mt-2 text-sm text-destructive">{{ form.errors.start_date }}</p>
      </div>

      <div>
        <Label for="end_date" required>Date de fin</Label>
        <Input
          id="end_date"
          v-model="form.end_date"
          type="date"
          required
          class="mt-2"
        />
        <p v-if="form.errors.end_date" class="mt-2 text-sm text-destructive">{{ form.errors.end_date }}</p>
      </div>
    </div>

    <!-- Destinataire -->
    <div>
      <Label for="recipient">Destinataire (optionnel)</Label>
      <Input
        id="recipient"
        v-model="form.recipient"
        type="text"
        placeholder="Ex: Jean Dupont, Service technique, etc."
        class="mt-2"
      />
      <p v-if="form.errors.recipient" class="mt-2 text-sm text-destructive">{{ form.errors.recipient }}</p>
    </div>

    <!-- Notes -->
    <div>
      <Label for="notes">Notes (optionnel)</Label>
      <Textarea
        id="notes"
        v-model="form.notes"
        rows="3"
        placeholder="Informations supplémentaires sur cette réservation..."
        class="mt-2"
      />
      <p v-if="form.errors.notes" class="mt-2 text-sm text-destructive">{{ form.errors.notes }}</p>
    </div>

    <!-- Équipements -->
    <div>
      <Label required>Équipements à bloquer</Label>
      <p class="text-sm text-muted-foreground mt-1">Sélectionnez les équipements à bloquer pour cette période</p>
      
      <div class="mt-4 space-y-4">
        <div v-for="(item, index) in form.items" :key="index" class="p-4 border rounded-lg">
          <div class="flex items-center justify-between mb-3">
            <h4 class="font-medium">Équipement {{ index + 1 }}</h4>
            <Button
              type="button"
              variant="outline"
              size="sm"
              @click="removeItem(index)"
              v-if="form.items.length > 1"
            >
              <X class="w-4 h-4" />
            </Button>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <Label :for="`equipment_${index}`" required>Équipement</Label>
              <Select v-model="item.equipment_id" required class="mt-2">
                <SelectTrigger>
                  <SelectValue placeholder="Sélectionnez un équipement" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="equipment in availableEquipments" :key="equipment.id" :value="equipment.id">
                    {{ equipment.name }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div>
              <Label :for="`quantity_${index}`" required>Quantité</Label>
              <Input
                :id="`quantity_${index}`"
                v-model.number="item.quantity"
                type="number"
                min="1"
                required
                class="mt-2"
              />
            </div>
          </div>

          <div class="mt-3">
            <Label :for="`notes_${index}`">Notes pour cet équipement</Label>
            <Input
              :id="`notes_${index}`"
              v-model="item.notes"
              type="text"
              placeholder="Notes spécifiques..."
              class="mt-2"
            />
          </div>
        </div>
      </div>

      <Button
        type="button"
        variant="outline"
        @click="addItem"
        class="mt-3"
      >
        <Plus class="w-4 h-4 mr-2" />
        Ajouter un équipement
      </Button>
    </div>

    <!-- Actions -->
    <div class="flex justify-end gap-3">
      <Button type="button" variant="outline" @click="$emit('cancel')">
        Annuler
      </Button>
      <Button type="submit" :disabled="form.processing">
        <Spinner v-if="form.processing" class="w-4 h-4 mr-2" />
        {{ form.processing ? 'Création...' : 'Créer la réservation' }}
      </Button>
    </div>
  </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { Plus, X } from 'lucide-vue-next'
import Spinner from '@/components/ui/spinner.vue'

const props = defineProps({
  startDate: {
    type: String,
    required: true
  },
  endDate: {
    type: String,
    required: true
  },
  equipments: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['cancel'])

const form = useForm({
  title: '',
  start_date: props.startDate,
  end_date: props.endDate,
  recipient: '',
  notes: '',
  items: [
    {
      equipment_id: '',
      quantity: 1,
      notes: ''
    }
  ]
})

const availableEquipments = computed(() => {
  return props.equipments.filter(equipment => equipment.is_available)
})

const addItem = () => {
  form.items.push({
    equipment_id: '',
    quantity: 1,
    notes: ''
  })
}

const removeItem = (index) => {
  form.items.splice(index, 1)
}

const submit = () => {
  form.post(route('app.organizations.calendar.manual-reservation'), {
    onSuccess: () => {
      emit('cancel')
    }
  })
}
</script> 