<template>
  <AppLayout :title="'Ajouter du matériel - ' + organization.name">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Ajouter du matériel
        </h2>
        <Link
          :href="route('app.organizations.equipments.index', organization)"
          class="inline-flex items-center px-4 py-2 bg-background hover:bg-accent hover:text-accent-foreground h-10 py-2 px-4 transition-colors"
        >
          <ArrowLeft class="w-4 h-4 mr-2" />
          Retour à la liste
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <Card>
          <CardContent class="p-8">
            <form @submit.prevent="submit">
              <!-- Informations générales -->
              <div class="space-y-6">
                <CardHeader class="px-0 pt-0">
                  <CardTitle>Informations générales</CardTitle>
                  <CardDescription>Informations de base du matériel</CardDescription>
                </CardHeader>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <!-- Nom -->
                  <div class="space-y-2">
                    <Label required>
                      Nom <span class="text-destructive">*</span>
                    </Label>
                    <Input 
                      v-model="form.name"
                      type="text" 
                      required 
                      autofocus
                      placeholder="Ex: Perceuse Bosch Professional"
                    />
                    <p class="text-xs text-muted-foreground">Nom complet du matériel avec sa marque si possible</p>
                    <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
                  </div>

                  <!-- Catégorie -->
                  <div class="space-y-2">
                    <Label>
                      Catégorie <span class="text-destructive">*</span>
                    </Label>
                    <Select v-model="form.category_id" required>
                      <SelectTrigger>
                        <SelectValue placeholder="Sélectionnez une catégorie" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem v-for="category in categories" :key="category.id" :value="category.id">
                          {{ category.name }}
                        </SelectItem>
                      </SelectContent>
                    </Select>
                    <p v-if="form.errors.category_id" class="text-sm text-destructive">{{ form.errors.category_id }}</p>
                  </div>

                  <!-- Description -->
                  <div class="sm:col-span-2 space-y-2">
                    <Label>
                      Description
                    </Label>
                    <Textarea 
                      v-model="form.description"
                      rows="3"
                      placeholder="Décrivez les caractéristiques principales du matériel..."
                    />
                    <p class="text-xs text-muted-foreground">Une description détaillée aidera les utilisateurs à mieux comprendre le matériel</p>
                    <p v-if="form.errors.description" class="text-sm text-destructive">{{ form.errors.description }}</p>
                  </div>

                  <!-- Message photos -->
                  <div class="sm:col-span-2 flex items-start p-4 text-sm text-muted-foreground bg-muted/50 rounded-lg">
                    <ImagePlus class="w-5 h-5 mr-3 flex-shrink-0" />
                    <p>
                      Vous pourrez ajouter des photos du matériel après sa création. Les photos permettront aux utilisateurs de mieux visualiser le matériel disponible.
                    </p>
                  </div>
                </div>
              </div>

              <!-- État et stockage -->
              <div class="space-y-6 mt-8">
                <CardHeader class="px-0">
                  <CardTitle>État et stockage</CardTitle>
                  <CardDescription>Informations sur l'état et le lieu de stockage</CardDescription>
                </CardHeader>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <!-- État -->
                  <div class="space-y-2">
                    <Label>
                      État <span class="text-destructive">*</span>
                    </Label>
                    <Select v-model="form.condition">
                      <SelectTrigger>
                        <SelectValue placeholder="Sélectionnez l'état" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="new">
                          <div class="flex items-center">
                            <Sparkles class="w-4 h-4 mr-2 text-green-500" />
                            <span>Neuf</span>
                          </div>
                        </SelectItem>
                        <SelectItem value="good">
                          <div class="flex items-center">
                            <CheckCircle class="w-4 h-4 mr-2 text-blue-500" />
                            <span>Bon</span>
                          </div>
                        </SelectItem>
                        <SelectItem value="fair">
                          <div class="flex items-center">
                            <AlertCircle class="w-4 h-4 mr-2 text-yellow-500" />
                            <span>Moyen</span>
                          </div>
                        </SelectItem>
                        <SelectItem value="poor">
                          <div class="flex items-center">
                            <XCircle class="w-4 h-4 mr-2 text-red-500" />
                            <span>Mauvais</span>
                          </div>
                        </SelectItem>
                      </SelectContent>
                    </Select>
                    <p v-if="form.errors.condition" class="text-sm text-destructive">{{ form.errors.condition }}</p>
                  </div>

                  <!-- Lieu de stockage -->
                  <div class="space-y-2">
                    <Label>
                      Lieu de stockage <span class="text-destructive">*</span>
                    </Label>
                    <Select v-model="form.depot_id" required>
                      <SelectTrigger>
                        <SelectValue placeholder="Sélectionnez un lieu de stockage" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem v-for="depot in depots" :key="depot.id" :value="depot.id">
                          {{ depot.name }}
                        </SelectItem>
                      </SelectContent>
                    </Select>
                    <p v-if="form.errors.depot_id" class="text-sm text-destructive">{{ form.errors.depot_id }}</p>
                  </div>
                </div>
              </div>

              <!-- Tarification -->
              <div class="space-y-6 mt-8">
                <CardHeader class="px-0">
                  <CardTitle>Tarification</CardTitle>
                  <CardDescription>Définissez les prix et conditions de location</CardDescription>
                </CardHeader>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                  <!-- Prix d'achat -->
                  <div class="space-y-2">
                    <Label>
                      Prix d'achat
                    </Label>
                    <div class="relative">
                      <Input
                        v-model="form.purchase_price"
                        type="text"
                        inputmode="decimal"
                        class="pl-7"
                        @input="e => handlePriceInput(e, 'purchase_price')"
                      />
                      <span class="absolute left-2 top-1/2 -translate-y-1/2 text-muted-foreground">€</span>
                    </div>
                    <p v-if="form.errors.purchase_price" class="text-sm text-destructive">{{ form.errors.purchase_price }}</p>
                  </div>

                  <!-- Prix de location -->
                  <div class="space-y-2">
                    <Label>
                      Prix de location
                    </Label>
                    <div class="relative">
                      <Input
                        v-model="form.rental_price"
                        type="text"
                        inputmode="decimal"
                        class="pl-7"
                        @input="e => handlePriceInput(e, 'rental_price')"
                      />
                      <span class="absolute left-2 top-1/2 -translate-y-1/2 text-muted-foreground">€</span>
                    </div>
                    <p class="text-xs text-muted-foreground">Prix par jour</p>
                    <p v-if="form.errors.rental_price" class="text-sm text-destructive">{{ form.errors.rental_price }}</p>
                  </div>

                  <!-- Montant de la caution -->
                  <div class="space-y-2">
                    <Label>
                      Montant de la caution
                    </Label>
                    <div class="relative">
                      <Input
                        v-model="form.deposit_amount"
                        type="text"
                        inputmode="decimal"
                        class="pl-7"
                        :disabled="!form.requires_deposit"
                        @input="e => handlePriceInput(e, 'deposit_amount')"
                      />
                      <span class="absolute left-2 top-1/2 -translate-y-1/2 text-muted-foreground">€</span>
                    </div>
                    <p v-if="form.errors.deposit_amount" class="text-sm text-destructive">{{ form.errors.deposit_amount }}</p>
                  </div>
                </div>

                <!-- Options -->
                <div class="space-y-4 mt-4">
                  <label class="flex items-center space-x-2">
                    <Switch v-model="form.is_available" />
                    <div>
                      <span class="text-sm font-medium leading-none">Disponible</span>
                      <p class="text-xs text-muted-foreground">Le matériel peut être réservé</p>
                    </div>
                  </label>

                  <label class="flex items-center space-x-2">
                    <Switch v-model="form.requires_deposit" />
                    <div>
                      <span class="text-sm font-medium leading-none">Nécessite une caution</span>
                      <p class="text-xs text-muted-foreground">Une caution sera demandée pour la location</p>
                    </div>
                  </label>

                  <label class="flex items-center space-x-2">
                    <Switch v-model="form.is_rentable" />
                    <div>
                      <span class="text-sm font-medium leading-none">Louable</span>
                      <p class="text-xs text-muted-foreground">Le matériel peut être loué</p>
                    </div>
                  </label>
                </div>
              </div>

              <!-- Maintenance -->
              <div class="space-y-6 mt-8">
                <CardHeader class="px-0">
                  <CardTitle>Maintenance</CardTitle>
                  <CardDescription>Suivi des maintenances du matériel</CardDescription>
                </CardHeader>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <!-- Dernière maintenance -->
                  <div class="space-y-2">
                    <Label>
                      Dernière maintenance
                    </Label>
                    <Input
                      v-model="form.last_maintenance_date"
                      type="date"
                    />
                    <p class="text-xs text-muted-foreground">Date de la dernière maintenance effectuée</p>
                    <p v-if="form.errors.last_maintenance_date" class="text-sm text-destructive">{{ form.errors.last_maintenance_date }}</p>
                  </div>

                  <!-- Prochaine maintenance -->
                  <div class="space-y-2">
                    <Label>
                      Prochaine maintenance
                    </Label>
                    <Input
                      v-model="form.next_maintenance_date"
                      type="date"
                    />
                    <p class="text-xs text-muted-foreground">Date de la prochaine maintenance prévue</p>
                    <p v-if="form.errors.next_maintenance_date" class="text-sm text-destructive">{{ form.errors.next_maintenance_date }}</p>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="mt-8 flex items-center justify-end gap-x-6 border-t pt-6">
                <Link
                  :href="route('app.organizations.equipments.index', organization)"
                  class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700"
                >
                  Annuler
                </Link>
                <Button
                  type="submit"
                  :disabled="form.processing"
                  :class="{ 'opacity-25': form.processing }"
                >
                  <Plus v-if="!form.processing" class="w-4 h-4 mr-2" />
                  <Spinner v-else class="w-4 h-4 mr-2" />
                  {{ form.processing ? 'Création en cours...' : 'Ajouter le matériel' }}
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Switch } from '@/components/ui/switch'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { 
  ArrowLeft,
  Plus,
  Sparkles,
  CheckCircle,
  AlertCircle,
  XCircle,
  ImagePlus
} from 'lucide-vue-next'
import Spinner from '@/components/ui/spinner.vue'
import { ref, computed } from 'vue'

const props = defineProps({
  organization: {
    type: Object,
    required: true
  },
  categories: {
    type: Array,
    required: true
  },
  depots: {
    type: Array,
    required: true
  }
})

const form = useForm({
  name: '',
  description: '',
  category_id: null,
  depot_id: null,
  condition: 'good',
  purchase_price: '0,00',
  rental_price: '0,00',
  deposit_amount: '0,00',
  is_available: true,
  requires_deposit: true,
  is_rentable: true,
  specifications: {},
  images: [],
  last_maintenance_date: null,
  next_maintenance_date: null,
})

const handlePriceInput = (e, field) => {
  let value = e.target.value
  
  // Replace commas with dots for standardization
  value = value.replace(',', '.')
  
  // Remove any non-numeric characters except dots
  value = value.replace(/[^\d.]/g, '')
  
  // Ensure only one decimal point
  const parts = value.split('.')
  if (parts.length > 2) {
    value = parts[0] + '.' + parts.slice(1).join('')
  }
  
  // Format the display value with comma
  const displayValue = value ? value.replace('.', ',') : '0,00'
  
  // Store the raw value
  form[field] = value || '0.00'
  e.target.value = displayValue
}

const submit = () => {
  // Send raw values to server, Laravel will handle the conversion
  form.post(route('app.organizations.equipments.store', props.organization))
}
</script> 