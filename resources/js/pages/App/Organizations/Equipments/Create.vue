<template>
  <AppLayout :title="'Ajouter du matériel - ' + organization.name">
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ajouter du matériel
          </h2>
        </div>
        <Link
          :href="route('app.organizations.equipments.index', organization)"
          class="inline-flex items-center bg-background hover:bg-accent hover:text-accent-foreground h-10 py-2 px-4 transition-colors"
        >
          <ArrowLeft class="w-4 h-4 mr-2" />
          Retour à la liste
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-card text-card-foreground rounded-lg border shadow-sm overflow-hidden">
          <!-- Progress bar -->
          <div class="w-full bg-muted/10">
            <div 
              class="h-1 bg-primary transition-all duration-300 ease-in-out"
              :style="{ width: ((currentStep + 1) / steps.length * 100) + '%' }"
            />
          </div>

          <!-- Stepper header -->
          <div class="px-2 py-3 sm:px-6 sm:py-4 bg-muted/5 border-b overflow-x-auto">
            <Stepper v-model="currentStep" class="w-full min-w-[720px]">
              <div class="flex items-center justify-between w-full">
                <StepperItem 
                  v-for="(step, index) in steps" 
                  :key="step.value" 
                  :step="index"
                  class="flex-1 px-2"
                >
                  <StepperTrigger 
                    class="w-full group"
                    @click="currentStep = step.value"
                  >
                    <div class="flex items-start gap-3">
                      <StepperIndicator 
                        :class="[
                          'mt-0.5 size-7 flex items-center justify-center rounded-full border transition-colors shrink-0',
                          currentStep === step.value ? 'bg-primary border-primary text-white' : 'bg-background border-muted-foreground/30'
                        ]"
                      >
                        <component :is="step.icon" class="w-4 h-4" />
                      </StepperIndicator>
                      <div class="flex flex-col items-start gap-0.5 min-w-0">
                        <StepperTitle 
                          :class="[
                            'text-sm font-medium transition-colors',
                            currentStep === step.value ? 'text-primary' : 'text-foreground/80'
                          ]"
                        >
                          {{ step.label }}
                        </StepperTitle>
                        <StepperDescription 
                          class="text-xs text-left text-muted-foreground/75"
                        >
                          {{ step.description }}
                        </StepperDescription>
                      </div>
                    </div>
                  </StepperTrigger>
                  <StepperSeparator 
                    v-if="index < steps.length - 1"
                    class="bg-muted-foreground/20" 
                  />
                </StepperItem>
              </div>
            </Stepper>
          </div>

          <form @submit.prevent="submit" class="p-4 sm:p-6">
            <p class="text-sm text-muted-foreground/75 mb-8">Les champs marqués d'un <span class="text-destructive">*</span> sont obligatoires</p>

            <!-- Étape 1: Informations générales -->
            <div 
              v-show="currentStepName === 'general'" 
              class="space-y-8 animate-in fade-in-50"
              role="tabpanel"
              id="step-general-content"
              :aria-labelledby="`step-${currentStepName}-trigger`"
            >             
                <div class="grid gap-8 sm:grid-cols-2">
                  <div>
                    <Label required class="text-base flex items-center gap-1">
                      Nom
                      <span class="text-destructive" aria-hidden="true">*</span>
                      <span class="sr-only">obligatoire</span>
                    </Label>
                    <Input 
                      v-model="form.name"
                      type="text" 
                      required 
                      autofocus
                      placeholder="Ex: Perceuse Bosch Professional"
                      class="mt-2"
                      aria-required="true"
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
                    <span class="sr-only">obligatoire</span>
                  </Label>
                  <Select v-model="form.category_id" required class="mt-2" aria-required="true">
                    <SelectTrigger>
                      <SelectValue placeholder="Sélectionnez une catégorie" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </SelectItem>
                    </SelectContent>
                  </Select>
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
            </div>

            <!-- Étape 2: État et stockage -->
            <div 
              v-show="currentStepName === 'storage'" 
              class="space-y-8 animate-in fade-in-50"
              role="tabpanel"
              id="step-storage-content"
              :aria-labelledby="`step-${currentStepName}-trigger`"
            >
                <div class="grid gap-8">
                  <div>
                    <Label required class="text-base flex items-center gap-1">
                      État
                      <span class="text-destructive" aria-hidden="true">*</span>
                      <span class="sr-only">obligatoire</span>
                    </Label>
                    <Select v-model="form.condition" class="mt-2" required aria-required="true">
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
                      <span class="sr-only">obligatoire</span>
                    </Label>
                    <Input
                      v-model="form.quantity"
                      type="number"
                      min="1"
                      required
                      placeholder="Ex: 1"
                      class="mt-2"
                      aria-required="true"
                    />
                    <p class="mt-1.5 text-xs text-muted-foreground/75">Nombre d'unités disponibles de ce matériel</p>
                    <p v-if="form.errors.quantity" class="mt-2 text-sm text-destructive">{{ form.errors.quantity }}</p>
                  </div>

                  <div>
                    <Label required class="text-base flex items-center gap-1">
                      Lieu de stockage
                      <span class="text-destructive" aria-hidden="true">*</span>
                      <span class="sr-only">obligatoire</span>
                    </Label>
                    <Select v-model="form.depot_id" required class="mt-2" aria-required="true">
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
            </div>

            <!-- Étape 3: Photos -->
            <div 
              v-show="currentStepName === 'images'" 
              class="space-y-8 animate-in fade-in-50"
              role="tabpanel"
              id="step-images-content"
              :aria-labelledby="`step-${currentStepName}-trigger`"
            >
              <div class="space-y-4">
                <div class="flex items-start p-4 text-sm bg-muted/20 rounded-lg border border-muted">
                  <Image class="w-5 h-5 mr-3 flex-shrink-0 text-muted-foreground/75" />
                  <div class="space-y-1">
                    <p class="text-muted-foreground/75">
                      Ajoutez jusqu'à 10 photos de votre matériel. La première photo sera l'image principale.
                    </p>
                    <ul class="text-xs text-muted-foreground/75 list-disc list-inside">
                      <li>Formats acceptés : JPG, JPEG, PNG</li>
                      <li>Taille maximale par image : 5 MB</li>
                      <li>Glissez-déposez pour réorganiser les photos</li>
                    </ul>
                  </div>
                </div>

                <file-pond
                  name="images"
                  ref="pond"
                  class-name="file-pond-custom"
                  :allow-multiple="true"
                  :allow-reorder="true"
                  :instant-upload="false"
                  :allow-image-preview="true"
                  :image-preview-height="170"
                  :label-idle="'Glissez vos photos ici ou <span class=\'filepond--label-action\'>parcourez</span>'"
                  :accepted-file-types="['image/png', 'image/jpeg', 'image/jpg']"
                  :max-files="10"
                  :files="form.images"
                  @init="handleFilePondInit"
                  @addfile="handleFilePondAddFile"
                  @removefile="handleFilePondRemoveFile"
                  @reorderfiles="handleFilePondReorderFiles"
                />
              </div>
            </div>

            <!-- Étape 4: Tarification -->
            <div 
              v-show="currentStepName === 'pricing'" 
              class="space-y-8 animate-in fade-in-50"
              role="tabpanel"
              id="step-pricing-content"
              :aria-labelledby="`step-${currentStepName}-trigger`"
            >
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
            </div>

            <!-- Navigation -->
            <div class="flex items-center justify-between gap-x-6 mt-8 pt-6 border-t">
              <Link
                :href="route('app.organizations.equipments.index', organization)"
                class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 rounded-sm"
              >
                Annuler
              </Link>

              <div class="flex items-center flex-wrap gap-x-2">
                <Button 
                  type="button" 
                  variant="outline"
                  :disabled="!canGoPrevious"
                  @click="previousStep"
                  v-if="!isFirstStep"
                  class="focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                >
                  <ArrowLeft class="w-4 h-4 mr-2" aria-hidden="true" />
                  Précédent
                </Button>
                <Button 
                  v-if="!isLastStep"
                  type="button"
                  :disabled="!canGoNext"
                  @click="nextStep"
                  class="focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                >
                  Suivant
                  <ArrowRight class="w-4 h-4 ml-2" aria-hidden="true" />
                </Button>
                <Button
                  v-else
                  type="submit"
                  :disabled="form.processing"
                  :class="{ 'opacity-25': form.processing }"
                  size="lg"
                  class="focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                >
                  <Plus v-if="!form.processing" class="w-4 h-4 mr-2" aria-hidden="true" />
                  <Spinner v-else class="w-4 h-4 mr-2" aria-hidden="true" />
                  {{ form.processing ? 'Création en cours...' : 'Ajouter le matériel' }}
                </Button>
              </div>
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
import { Switch } from '@/components/ui/switch'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import {
  Stepper,
  StepperDescription,
  StepperIndicator,
  StepperItem,
  StepperSeparator,
  StepperTitle,
  StepperTrigger,
} from '@/components/ui/stepper'
import { 
  ArrowLeft,
  Plus,
  Sparkles,
  CheckCircle,
  AlertCircle,
  XCircle,
  ImagePlus,
  ClipboardList,
  Settings,
  Euro,
  ArrowRight,
  Image
} from 'lucide-vue-next'
import Spinner from '@/components/ui/spinner.vue'
import { ref, computed } from 'vue'
import vueFilePond from 'vue-filepond'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.esm.js'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.esm.js'
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.esm.js'
import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css'

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

// Create component
const FilePond = vueFilePond(
  FilePondPluginFileValidateType,
  FilePondPluginImagePreview,
  FilePondPluginImageExifOrientation
)

const steps = [
  { 
    value: 0, 
    name: 'general',
    label: 'Informations générales', 
    icon: ClipboardList,
    description: 'Description du matériel'
  },
  { 
    value: 1, 
    name: 'storage',
    label: 'État et stockage', 
    icon: Settings,
    description: 'Quantité et lieu de stockage'
  },
  { 
    value: 2, 
    name: 'images',
    label: 'Photos', 
    icon: Image,
    description: 'Ajoutez des photos'
  },
  { 
    value: 3, 
    name: 'pricing',
    label: 'Tarification', 
    icon: Euro,
    description: 'Conditions de location'
  }
]

const currentStep = ref(0)
const currentStepName = computed(() => steps[currentStep.value].name)
const canGoPrevious = computed(() => currentStep.value > 0)
const canGoNext = computed(() => currentStep.value < steps.length - 1)
const isLastStep = computed(() => currentStep.value === steps.length - 1)
const isFirstStep = computed(() => currentStep.value === 0)

const previousStep = () => {
  if (canGoPrevious.value) {
    currentStep.value--
  }
}

const nextStep = () => {
  if (canGoNext.value) {
    currentStep.value++
  }
}

const form = useForm({
  name: '',
  description: '',
  brand: '',
  category_id: null,
  depot_id: null,
  condition: 'good',
  quantity: 1,
  purchase_price: 0.00,
  rental_price: 0.00,
  deposit_amount: 0.00,
  is_available: true,
  requires_deposit: true,
  is_rentable: true,
  specifications: {},
  images: [],
})

// FilePond options
const pondOptions = {
  allowMultiple: true,
  allowReorder: true,
  instantUpload: false,
  allowImagePreview: true,
  imagePreviewHeight: 170,
  labelIdle: 'Glissez vos photos ici ou <span class="filepond--label-action">parcourez</span>',
  labelFileProcessing: 'Chargement',
  labelFileProcessingComplete: 'Chargement terminé',
  labelFileProcessingAborted: 'Chargement annulé',
  labelFileProcessingError: 'Erreur lors du chargement',
  labelFileRemoveError: 'Erreur lors de la suppression',
  labelTapToCancel: 'Cliquez pour annuler',
  labelTapToRetry: 'Cliquez pour réessayer',
  labelTapToUndo: 'Cliquez pour annuler',
  acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],
  maxFiles: 10,
}

// Handle FilePond events
const handleFilePondInit = () => {
  console.log('FilePond initialized')
}

const handleFilePondAddFile = (error, file) => {
  if (error) {
    console.error('Error adding file:', error)
    return
  }
  form.images.push(file.file)
}

const handleFilePondRemoveFile = (error, file) => {
  if (error) {
    console.error('Error removing file:', error)
    return
  }
  const index = form.images.findIndex(f => f === file.file)
  if (index !== -1) {
    form.images.splice(index, 1)
  }
}

const handleFilePondReorderFiles = (files) => {
  form.images = files.map(file => file.file)
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
  // Send raw values to server, Laravel will handle the conversion
  form.post(route('app.organizations.equipments.store', props.organization))
}
</script>

<style>
.file-pond-custom {
  --filepond-bg-color: rgb(var(--background));
  --filepond-primary-color: rgb(var(--primary));
  --filepond-border-radius: 0.5rem;
}

.filepond--panel-root {
  background-color: rgb(var(--muted) / 0.1);
  border: 1px dashed rgb(var(--muted-foreground) / 0.3);
}

.filepond--drop-label {
  color: rgb(var(--muted-foreground));
}

.filepond--item-panel {
  background-color: rgb(var(--background));
}

.filepond--image-preview-wrapper {
  border-radius: 0.375rem;
}

.filepond--image-preview-overlay-success {
  color: rgb(var(--primary));
}

/* Hover states */
.filepond--drop-label.filepond--drop-label:hover {
  color: rgb(var(--foreground));
}

/* Active states */
.filepond--file-action-button:hover {
  background-color: rgb(var(--primary));
}

/* Focus states */
.filepond--file-action-button:focus {
  outline: 2px solid rgb(var(--ring));
  outline-offset: 2px;
}
</style> 