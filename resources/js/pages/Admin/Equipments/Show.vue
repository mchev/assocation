<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">{{ equipment.name }}</h1>
          <p class="text-muted-foreground">
            {{ equipment.organization?.name }} · {{ equipment.category?.name }}
          </p>
        </div>
        <Button variant="outline" as-child>
          <Link :href="route('admin.equipments.index')">
            <ArrowLeft class="h-4 w-4 mr-2" />
            Retour au listing
          </Link>
        </Button>
      </div>

      <!-- Success / Error messages -->
      <div v-if="$page.props.flash?.success" class="rounded-lg bg-green-50 dark:bg-green-950/30 text-green-800 dark:text-green-200 px-4 py-3">
        {{ $page.props.flash.success }}
      </div>
      <div v-if="uploadForm.errors?.image" class="rounded-lg bg-destructive/10 text-destructive px-4 py-3">
        {{ uploadForm.errors.image }}
      </div>
      <div v-if="generateError" class="rounded-lg bg-destructive/10 text-destructive px-4 py-3">
        {{ generateError }}
      </div>

      <div class="grid gap-6 lg:grid-cols-2">
        <!-- Infos + Photos actuelles -->
        <Card>
          <CardHeader>
            <CardTitle>Photos actuelles</CardTitle>
            <CardDescription>
              {{ equipment.images?.length || 0 }} photo(s) pour ce matériel
            </CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div v-if="equipment.images?.length" class="flex flex-wrap gap-3">
              <div
                v-for="img in equipment.images"
                :key="img.id"
                class="relative w-24 h-24 rounded-lg overflow-hidden border bg-muted group"
              >
                <img
                  :src="img.url"
                  :alt="equipment.name"
                  class="w-full h-full object-cover"
                />
                <Button
                  type="button"
                  variant="destructive"
                  size="icon"
                  class="absolute top-1 right-1 h-7 w-7 opacity-0 group-hover:opacity-100 transition-opacity"
                  :disabled="deleteImageId === img.id"
                  @click="confirmDeleteImage(img.id)"
                >
                  <Trash2 v-if="deleteImageId !== img.id" class="h-3.5 w-3.5" />
                  <Loader2 v-else class="h-3.5 w-3.5 animate-spin" />
                </Button>
              </div>
            </div>
            <div v-else class="flex flex-col items-center justify-center py-8 rounded-lg border border-dashed bg-muted/30">
              <ImageOff class="h-10 w-10 text-muted-foreground mb-2" />
              <p class="text-sm text-muted-foreground">Aucune photo</p>
            </div>

            <!-- Upload manuel -->
            <div class="pt-4 border-t">
              <Label class="text-sm font-medium">Ajouter une photo (upload)</Label>
              <form
                class="mt-2 flex items-end gap-2"
                @submit.prevent="submitUpload"
              >
                <Input
                  ref="fileInputRef"
                  type="file"
                  accept="image/jpeg,image/jpg,image/png"
                  class="max-w-xs"
                  @change="onFileSelect"
                />
                <Button type="submit" :disabled="uploadForm.processing || !selectedFile">
                  {{ uploadForm.processing ? 'Envoi…' : 'Envoyer' }}
                </Button>
              </form>
            </div>

            <!-- Trouver une photo sur le web -->
            <div class="pt-4 border-t">
              <Label class="text-sm font-medium">Trouver une photo sur le web</Label>
              <p class="text-xs text-muted-foreground mt-1 mb-2">
                L’IA génère une requête de recherche, trouve une image pertinente via Google Images (SerpApi) et l’enregistre pour ce matériel.
              </p>

              <!-- Étapes de progression -->
              <ul class="mb-3 space-y-1.5 text-sm">
                <li
                  v-for="(step, index) in progressSteps"
                  :key="index"
                  class="flex items-center gap-2"
                >
                  <Loader2
                    v-if="step.status === 'pending' && generateLoading"
                    class="h-4 w-4 shrink-0 text-muted-foreground animate-spin"
                  />
                  <Check
                    v-else-if="step.status === 'ok'"
                    class="h-4 w-4 shrink-0 text-green-600 dark:text-green-400"
                  />
                  <span
                    v-else
                    class="h-4 w-4 shrink-0 rounded-full border-2 border-muted-foreground/30"
                  />
                  <span
                    :class="[
                      step.status === 'ok' ? 'text-foreground' : step.status === 'pending' && generateLoading ? 'text-muted-foreground' : 'text-muted-foreground/70',
                    ]"
                  >
                    {{ step.label }}
                  </span>
                </li>
              </ul>

              <Button
                type="button"
                variant="secondary"
                :disabled="generateLoading"
                @click="generateWithAi"
              >
                <Sparkles v-if="!generateLoading" class="h-4 w-4 mr-2" />
                <Loader2 v-else class="h-4 w-4 mr-2 animate-spin" />
                {{ generateLoading ? 'Recherche en cours…' : 'Trouver une photo sur le web' }}
              </Button>
            </div>
          </CardContent>
        </Card>

        <!-- Détails matériel -->
        <Card>
          <CardHeader>
            <CardTitle>Détails</CardTitle>
            <CardDescription>
              Informations du matériel
            </CardDescription>
          </CardHeader>
          <CardContent class="space-y-3">
            <div v-if="equipment.description">
              <p class="text-sm font-medium text-muted-foreground">Description</p>
              <p class="text-sm">{{ equipment.description }}</p>
            </div>
            <div v-if="equipment.brand">
              <p class="text-sm font-medium text-muted-foreground">Marque</p>
              <p class="text-sm">{{ equipment.brand }}</p>
            </div>
            <div>
              <p class="text-sm font-medium text-muted-foreground">État</p>
              <p class="text-sm">{{ equipment.condition }}</p>
            </div>
            <div>
              <p class="text-sm font-medium text-muted-foreground">Quantité</p>
              <p class="text-sm">{{ equipment.quantity }}</p>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, router, useForm, usePage } from '@inertiajs/vue3'
import { ArrowLeft, ImageOff, Sparkles, Loader2, Trash2, Check } from 'lucide-vue-next'
import { ref, computed } from 'vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const props = defineProps({
  equipment: {
    type: Object,
    required: true,
  },
})

const page = usePage()
const fileInputRef = ref(null)
const selectedFile = ref(null)
const generateLoading = ref(false)
const generateError = ref(null)
const deleteImageId = ref(null)

const defaultSteps = [
  { label: 'Génération de la requête de recherche', status: 'pending' },
  { label: "Recherche d'image sur le web", status: 'pending' },
  { label: 'Téléchargement et enregistrement', status: 'pending' },
]

const progressSteps = computed(() => {
  const flashSteps = page.props.flash?.generateSteps
  if (flashSteps?.length) {
    return flashSteps
  }
  if (generateLoading.value) {
    return defaultSteps
  }
  return defaultSteps
})

const uploadForm = useForm({
  image: null,
})

const onFileSelect = (e) => {
  const file = e.target.files?.[0]
  selectedFile.value = file || null
  uploadForm.image = file || null
}

const submitUpload = () => {
  if (!selectedFile.value) return
  generateError.value = null
  uploadForm.clearErrors()
  uploadForm.post(route('admin.equipments.images.store', props.equipment.id), {
    forceFormData: true,
    onSuccess: () => {
      selectedFile.value = null
      uploadForm.reset()
      if (fileInputRef.value) {
        fileInputRef.value.value = ''
      }
    },
  })
}

const generateWithAi = () => {
  generateError.value = null
  generateLoading.value = true
  router.post(route('admin.equipments.generate-image', props.equipment.id), {}, {
    preserveScroll: true,
    onFinish: () => {
      generateLoading.value = false
    },
    onSuccess: () => {
      // Steps are in flash.generateSteps, progressSteps will show them
    },
    onError: (errors) => {
      generateError.value = errors.generate ?? 'Une erreur est survenue.'
    },
  })
}

const confirmDeleteImage = (imageId) => {
  if (!window.confirm('Supprimer cette photo ?')) return
  deleteImageId.value = imageId
  router.delete(route('admin.equipments.images.destroy', { equipment: props.equipment.id, image: imageId }), {
    preserveScroll: true,
    onFinish: () => {
      deleteImageId.value = null
    },
  })
}
</script>
