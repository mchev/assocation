<template>
  <AppLayout :title="'Modifier ' + equipment.name">
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold text-xl leading-tight">
            {{ equipment.name }}
          </h2>
        </div>
        <div class="flex items-center gap-3">
          <Link
            :href="route('app.organizations.equipments.index', organization)"
            class="inline-flex items-center bg-background hover:bg-accent hover:text-accent-foreground h-10 py-2 px-4 transition-colors"
          >
            <ArrowLeft class="w-4 h-4 mr-2" />
            Retour à la liste
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Floating Navigation -->
        <div class="hidden lg:block sticky top-4 z-30 float-right w-60 ml-8 bg-card rounded-lg border shadow-sm">
          <nav class="p-4">
            <p class="text-sm font-medium mb-3">Navigation rapide</p>
            <ul class="space-y-2 text-sm">
              <li>
                <a href="#general" class="flex items-center text-muted-foreground hover:text-foreground">
                  <ClipboardList class="w-4 h-4 mr-2" />
                  Informations
                </a>
              </li>
              <li>
                <a href="#storage" class="flex items-center text-muted-foreground hover:text-foreground">
                  <Settings class="w-4 h-4 mr-2" />
                  État et stockage
                </a>
              </li>
              <li>
                <a href="#images" class="flex items-center text-muted-foreground hover:text-foreground">
                  <Image class="w-4 h-4 mr-2" />
                  Photos
                </a>
              </li>
              <li>
                <a href="#pricing" class="flex items-center text-muted-foreground hover:text-foreground">
                  <Euro class="w-4 h-4 mr-2" />
                  Tarification
                </a>
              </li>
            </ul>

            <div class="mt-6 pt-6 border-t">
              <p class="text-sm font-medium mb-3">Actions</p>
              <ul class="space-y-4 text-sm">
                <li>
                  <Link 
                    :href="route('equipments.show', equipment.id)"
                    class="flex items-center text-muted-foreground hover:text-foreground group"
                    target="_blank"
                  >
                    <ExternalLink class="w-4 h-4 mr-2" />
                    Voir la fiche publique
                    <ArrowUpRight class="w-3 h-3 ml-1 opacity-0 -translate-y-1 translate-x-1 group-hover:opacity-100 group-hover:translate-y-0 group-hover:translate-x-0 transition-all" />
                  </Link>
                </li>
                <li>
                  <Link 
                    :href="route('app.organizations.reservations.out.create', {equipment:equipment.id})"
                    class="flex items-center text-muted-foreground hover:text-foreground group"
                  >
                    <CalendarPlus class="w-4 h-4 mr-2" />
                    Créer une réservation
                  </Link>
                </li>
                <li>
                  <Button
                    type="submit"
                    form="equipment-form"
                    :disabled="form.processing"
                    :class="{ 'opacity-25': form.processing }"
                    class="focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                  >
                    <Spinner v-if="form.processing" class="w-4 h-4 mr-2" aria-hidden="true" />
                    {{ form.processing ? 'Modification en cours...' : 'Mettre à jour' }}
                  </Button>
                </li>
              </ul>
            </div>
          </nav>
        </div>

        <div class="bg-card text-card-foreground rounded-lg border shadow-sm overflow-hidden">
          <form @submit.prevent="submit" class="divide-y" id="equipment-form">
            <p class="p-6 text-sm text-muted-foreground/75">Les champs marqués d'un <span class="text-destructive">*</span> sont obligatoires</p>

            <!-- General Information -->
            <section id="general" class="p-6 space-y-8">
              <div class="flex items-center gap-4 pb-4 border-b">
                <ClipboardList class="w-5 h-5 text-muted-foreground" />
                <h3 class="text-lg font-medium">Informations générales</h3>
              </div>

              <div class="grid gap-8 sm:grid-cols-2">
                <div>
                  <Label required class="text-base flex items-center gap-1">
                    Nom
                    <span class="text-destructive" aria-hidden="true">*</span>
                  </Label>
                  <Input 
                    v-model="form.name"
                    type="text" 
                    required 
                    autofocus
                    placeholder="Ex: Perceuse Bosch Professional"
                    class="mt-2"
                  />
                  <p v-if="form.errors.name" class="mt-2 text-sm text-destructive">{{ form.errors.name }}</p>
                </div>

                <div>
                  <Label required class="text-base flex items-center gap-1">
                    Marque
                  </Label>
                  <Input 
                    v-model="form.brand"
                    type="text" 
                    placeholder="Ex: Bosch"
                    class="mt-2"
                  />
                  <p v-if="form.errors.brand" class="mt-2 text-sm text-destructive">{{ form.errors.brand }}</p>
                </div>
              </div>

              <div>
                <Label required class="text-base flex items-center gap-1">
                  Catégorie
                  <span class="text-destructive" aria-hidden="true">*</span>
                </Label>
                <CategorySelect
                  v-model="form.category_id"
                  required
                />
                <p v-if="form.errors.category_id" class="mt-2 text-sm text-destructive">{{ form.errors.category_id }}</p>
              </div>

              <div>
                <Label class="text-base">Description</Label>
                <Textarea 
                  v-model="form.description"
                  rows="3"
                  placeholder="Décrivez les caractéristiques principales du matériel..."
                  class="mt-2"
                />
                <p class="mt-1.5 text-xs text-muted-foreground/75">Une description détaillée aidera les utilisateurs à mieux comprendre le matériel</p>
                <p v-if="form.errors.description" class="mt-2 text-sm text-destructive">{{ form.errors.description }}</p>
              </div>
            </section>

            <!-- Storage & Condition -->
            <section id="storage" class="p-6 space-y-8">
              <div class="flex items-center gap-4 pb-4 border-b">
                <Settings class="w-5 h-5 text-muted-foreground" />
                <h3 class="text-lg font-medium">État et stockage</h3>
              </div>

              <div class="grid gap-8">
                <div>
                  <Label required class="text-base flex items-center gap-1">
                    État
                    <span class="text-destructive" aria-hidden="true">*</span>
                  </Label>
                  <Select v-model="form.condition" class="mt-2" required>
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
                  <p v-if="form.errors.condition" class="mt-2 text-sm text-destructive">{{ form.errors.condition }}</p>
                </div>

                <div>
                  <Label required class="text-base flex items-center gap-1">
                    Quantité disponible
                    <span class="text-destructive" aria-hidden="true">*</span>
                  </Label>
                  <Input
                    v-model="form.quantity"
                    type="number"
                    min="1"
                    required
                    placeholder="Ex: 1"
                    class="mt-2"
                  />
                  <p class="mt-1.5 text-xs text-muted-foreground/75">Nombre d'unités disponibles de ce matériel</p>
                  <p v-if="form.errors.quantity" class="mt-2 text-sm text-destructive">{{ form.errors.quantity }}</p>
                </div>

                <div>
                  <Label required class="text-base flex items-center gap-1">
                    Lieu de stockage
                    <span class="text-destructive" aria-hidden="true">*</span>
                  </Label>
                  <Select v-model="form.depot_id" required class="mt-2">
                    <SelectTrigger>
                      <SelectValue placeholder="Sélectionnez un lieu de stockage" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem v-for="depot in depots" :key="depot.id" :value="depot.id">
                        {{ depot.name }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
                  <Link
                    :href="route('app.organizations.depots.index', organization)"
                    class="inline-flex items-center text-xs text-primary/80 hover:text-primary mt-2"
                  >
                    <Plus class="w-3 h-3 mr-1" />
                    Ajouter un lieu de stockage
                  </Link>
                  <p v-if="form.errors.depot_id" class="mt-2 text-sm text-destructive">{{ form.errors.depot_id }}</p>
                </div>
              </div>
            </section>

            <!-- Images -->
            <section id="images" class="p-6 space-y-8">
              <div class="flex items-center gap-4 pb-4 border-b">
                <Image class="w-5 h-5 text-muted-foreground" />
                <h3 class="text-lg font-medium">Photos</h3>
              </div>

              <div class="space-y-6">
                <div class="flex items-start p-4 text-sm bg-muted/20 rounded-lg border border-muted">
                  <Image class="w-5 h-5 mr-3 flex-shrink-0 text-muted-foreground/75" />
                  <div class="space-y-1">
                    <p class="text-muted-foreground/75">
                      Ajoutez jusqu'à 3 photos de votre matériel.
                    </p>
                    <ul class="text-xs text-muted-foreground/75 list-disc list-inside">
                      <li>Formats acceptés : JPG, JPEG, PNG</li>
                      <li>Taille maximale par image : 5 MB</li>
                    </ul>
                  </div>
                </div>

                <!-- Existing Images -->
                <div v-if="displayedExistingImages.length" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                  <div v-for="image in displayedExistingImages" :key="image.id" class="relative group aspect-square">
                    <img :src="image.url" :alt="image.original_name" class="w-full h-full object-cover rounded-lg" />
                    <button 
                      type="button"
                      @click="removeExistingImage(image.id)"
                      class="absolute top-2 right-2 p-1 bg-destructive/90 hover:bg-destructive text-destructive-foreground rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                      <X class="w-4 h-4" />
                    </button>
                  </div>
                </div>

                <!-- New Images Preview -->
                <div v-if="imagePreviewUrls.length" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                  <div v-for="(url, index) in imagePreviewUrls" :key="index" class="relative group aspect-square">
                    <img :src="url" class="w-full h-full object-cover rounded-lg" />
                    <button 
                      type="button"
                      @click="removeNewImage(index)"
                      class="absolute top-2 right-2 p-1 bg-destructive/90 hover:bg-destructive text-destructive-foreground rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                      <X class="w-4 h-4" />
                    </button>
                  </div>
                </div>

                <!-- Upload Button -->
                <div class="flex items-center justify-center w-full">
                  <label 
                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer bg-muted/20 border-muted hover:bg-muted/30 transition-colors"
                    :class="{ 'opacity-50 cursor-not-allowed': isUploadDisabled }"
                  >
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                      <Upload class="w-8 h-8 mb-3 text-muted-foreground" />
                      <p class="mb-2 text-sm text-muted-foreground">
                        <span class="font-medium">Cliquez pour ajouter</span> vos photos ici
                      </p>
                    </div>
                    <input 
                      type="file" 
                      class="hidden" 
                      accept="image/png,image/jpeg,image/jpg"
                      multiple
                      @change="handleImageUpload"
                      :disabled="isUploadDisabled"
                    />
                  </label>
                </div>
              </div>
            </section>

            <!-- Pricing -->
            <section id="pricing" class="p-6 space-y-8">
              <div class="flex items-center gap-4 pb-4 border-b">
                <Euro class="w-5 h-5 text-muted-foreground" />
                <h3 class="text-lg font-medium">Tarification</h3>
              </div>

              <div class="space-y-6">
                <div class="grid gap-8 sm:grid-cols-3">
                  <div>
                    <Label class="text-base">Prix d'achat</Label>
                    <div class="relative mt-2">
                      <Input
                        v-model="form.purchase_price"
                        type="text"
                        inputmode="decimal"
                        class="pl-7"
                        @input="e => handlePriceInput(e, 'purchase_price')"
                      />
                      <span class="absolute left-2 top-1/2 -translate-y-1/2 text-muted-foreground">€</span>
                    </div>
                    <p v-if="form.errors.purchase_price" class="mt-2 text-sm text-destructive">{{ form.errors.purchase_price }}</p>
                  </div>

                  <div>
                    <Label class="text-base">Prix de location</Label>
                    <div class="relative mt-2">
                      <Input
                        v-model="form.rental_price"
                        type="text"
                        inputmode="decimal"
                        class="pl-7"
                        @input="e => handlePriceInput(e, 'rental_price')"
                      />
                      <span class="absolute left-2 top-1/2 -translate-y-1/2 text-muted-foreground">€</span>
                    </div>
                    <p class="mt-1.5 text-xs text-muted-foreground/75">Prix par jour</p>
                    <p v-if="form.errors.rental_price" class="mt-2 text-sm text-destructive">{{ form.errors.rental_price }}</p>
                  </div>

                  <div>
                    <Label class="text-base">Montant de la caution</Label>
                    <div class="relative mt-2">
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
                    <p class="mt-1.5 text-xs text-muted-foreground/75">0 = pas de caution</p>
                    <p v-if="form.errors.deposit_amount" class="mt-2 text-sm text-destructive">{{ form.errors.deposit_amount }}</p>
                  </div>
                </div>

                <div class="grid gap-4 pt-4">
                  <label class="flex items-center p-4 bg-muted/20 rounded-lg border border-muted cursor-pointer hover:bg-muted/30 transition-colors">
                    <Switch v-model="form.is_available" class="data-[state=checked]:bg-primary" />
                    <div class="ml-3">
                      <span class="text-sm font-medium">Disponible immédiatement</span>
                      <p class="text-xs text-muted-foreground/75 mt-0.5">Le matériel peut être réservé dès maintenant</p>
                    </div>
                  </label>

                  <label class="flex items-center p-4 bg-muted/20 rounded-lg border border-muted cursor-pointer hover:bg-muted/30 transition-colors">
                    <Switch v-model="form.requires_deposit" class="data-[state=checked]:bg-primary" />
                    <div class="ml-3">
                      <span class="text-sm font-medium">Caution requise</span>
                      <p class="text-xs text-muted-foreground/75 mt-0.5">Une caution sera demandée pour la location</p>
                    </div>
                  </label>

                  <label class="flex items-center p-4 bg-muted/20 rounded-lg border border-muted cursor-pointer hover:bg-muted/30 transition-colors">
                    <Switch v-model="form.is_rentable" class="data-[state=checked]:bg-primary" />
                    <div class="ml-3">
                      <span class="text-sm font-medium">Disponible à la location</span>
                      <p class="text-xs text-muted-foreground/75 mt-0.5">Le matériel peut être loué par d'autres utilisateurs</p>
                    </div>
                  </label>
                </div>
              </div>
            </section>

            <!-- Form Actions -->
            <div class="flex items-center justify-between gap-x-6 p-6">
              <Link
                :href="route('app.organizations.equipments.index', organization)"
                class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 rounded-sm"
              >
                Annuler
              </Link>

              <Button
                type="submit"
                :disabled="form.processing"
                :class="{ 'opacity-25': form.processing }"
                size="lg"
                class="focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
              >
                <Spinner v-if="form.processing" class="w-4 h-4 mr-2" aria-hidden="true" />
                {{ form.processing ? 'Modification en cours...' : 'Enregistrer les modifications' }}
              </Button>
            </div>
          </form>
        </div>
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
import CategorySelect from '@/components/CategorySelect.vue'
import { Switch } from '@/components/ui/switch'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { 
  ArrowLeft,
  Plus,
  Sparkles,
  CheckCircle,
  AlertCircle,
  XCircle,
  ClipboardList,
  Settings,
  Euro,
  Image,
  ExternalLink,
  ArrowUpRight,
  CalendarPlus,
  Upload,
  X
} from 'lucide-vue-next'
import Spinner from '@/components/ui/spinner.vue'
import { ref, computed } from 'vue'

const props = defineProps({
  organization: {
    type: Object,
    required: true
  },
  equipment: {
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
  _method: 'PUT',
  name: props.equipment.name,
  brand: props.equipment.brand,
  description: props.equipment.description,
  category_id: props.equipment.category_id,
  condition: props.equipment.condition,
  quantity: props.equipment.quantity ?? 1,
  depot_id: props.equipment.depot_id,
  purchase_price: props.equipment.purchase_price?.toString().replace('.', ',') || '0,00',
  rental_price: props.equipment.rental_price?.toString().replace('.', ',') || '0,00',
  deposit_amount: props.equipment.deposit_amount?.toString().replace('.', ',') || '0,00',
  is_available: props.equipment.is_available,
  requires_deposit: props.equipment.requires_deposit,
  is_rentable: props.equipment.is_rentable,
  last_maintenance_date: props.equipment.last_maintenance_date,
  next_maintenance_date: props.equipment.next_maintenance_date,
  images: [],
  removed_images: [],
})

const imagePreviewUrls = ref([])
const displayedExistingImages = computed(() => {
  return props.equipment.images?.filter(image => !form.removed_images.includes(image.id)) || []
})

const isUploadDisabled = computed(() => {
  const totalImages = displayedExistingImages.value.length + form.images.length
  return totalImages >= 10
})

const handleImageUpload = (e) => {
  const files = Array.from(e.target.files || [])
  const remainingSlots = 10 - (displayedExistingImages.value.length + form.images.length)
  
  // Only process up to the remaining slots
  const filesToProcess = files.slice(0, remainingSlots)
  
  filesToProcess.forEach(file => {
    // Create preview URL
    const url = URL.createObjectURL(file)
    imagePreviewUrls.value.push(url)
    
    // Add to form
    form.images.push(file)
  })
  
  // Reset input
  e.target.value = null
}

const removeNewImage = (index) => {
  // Remove preview URL
  URL.revokeObjectURL(imagePreviewUrls.value[index])
  imagePreviewUrls.value.splice(index, 1)
  
  // Remove from form
  form.images.splice(index, 1)
}

const removeExistingImage = (imageId) => {
  form.removed_images.push(imageId)
}

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
  form.post(route('app.organizations.equipments.update', props.equipment.id), {
    preserveScroll: true,
    forceFormData: true,
  })
}
</script>