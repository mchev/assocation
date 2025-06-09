<template>
  <AppLayout :title="equipment.name">

    <template #header>
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-4">
          <div class="flex-shrink-0">
            <div v-if="equipment.images && equipment.images[0]" class="h-16 w-16 rounded-lg overflow-hidden">
              <img :src="equipment.images[0].url" :alt="equipment.name" class="h-full w-full object-cover" />
            </div>
            <div v-else class="h-16 w-16 rounded-lg bg-muted flex items-center justify-center">
              <Package class="h-8 w-8 text-muted-foreground" />
            </div>
          </div>
          <div>
            <h2 class="font-semibold text-xl leading-tight">
              {{ equipment.name }}
            </h2>
            <p class="text-sm text-muted-foreground mt-1">{{ equipment.category.name }}</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <TooltipProvider>
            <Tooltip>
              <TooltipTrigger asChild>
                <Link
                  :href="route('app.organizations.equipments.index')"
                  class="inline-flex items-center justify-center h-9 w-9 rounded-md border bg-background hover:bg-accent"
                >
                  <ArrowLeft class="h-4 w-4" />
                </Link>
              </TooltipTrigger>
              <TooltipContent>Retour à la liste</TooltipContent>
            </Tooltip>

            <Tooltip v-if="can.update">
              <TooltipTrigger asChild>
                <Link
                  :href="route('app.organizations.equipments.edit', equipment)"
                  class="inline-flex items-center justify-center h-9 px-4 rounded-md bg-primary text-primary-foreground hover:bg-primary/90"
                >
                  <Pencil class="h-4 w-4 mr-2" />
                  Modifier
                </Link>
              </TooltipTrigger>
              <TooltipContent>Modifier l'équipement</TooltipContent>
            </Tooltip>

            <Tooltip v-if="can.delete">
              <TooltipTrigger asChild>
                <Button
                  variant="destructive"
                  class="h-9"
                  @click="deleteEquipment"
                >
                  <Trash2 class="h-4 w-4 mr-2" />
                  Supprimer
                </Button>
              </TooltipTrigger>
              <TooltipContent>Supprimer l'équipement</TooltipContent>
            </Tooltip>
          </TooltipProvider>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <!-- Main Information Card -->
          <Card class="lg:col-span-2">
            <CardHeader>
              <CardTitle class="flex items-center gap-2">
                <FileText class="h-5 w-5" />
                Description
              </CardTitle>
              <CardDescription class="mt-2 text-base">{{ equipment.description }}</CardDescription>
            </CardHeader>
            <CardContent>
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="space-y-1">
                  <CardTitle class="text-sm font-medium text-muted-foreground">Catégorie</CardTitle>
                  <CardDescription class="text-base font-medium">{{ equipment.category.name }}</CardDescription>
                </div>

                <div class="space-y-1">
                  <CardTitle class="text-sm font-medium text-muted-foreground">État</CardTitle>
                  <Badge
                    :variant="equipment.condition === 'new' ? 'blue' : 
                             equipment.condition === 'good' ? 'green' :
                             equipment.condition === 'fair' ? 'yellow' : 'destructive'"
                  >
                    {{ {
                      new: 'Neuf',
                      good: 'Bon',
                      fair: 'Moyen',
                      poor: 'Mauvais'
                    }[equipment.condition] }}
                  </Badge>
                </div>

                <div class="space-y-1">
                  <CardTitle class="text-sm font-medium text-muted-foreground">Prix d'achat</CardTitle>
                  <CardDescription class="text-base font-medium">
                    {{ equipment.purchase_price ? `${equipment.purchase_price} €` : '—' }}
                  </CardDescription>
                </div>

                <div class="space-y-1">
                  <CardTitle class="text-sm font-medium text-muted-foreground">Prix de location</CardTitle>
                  <CardDescription class="text-base font-medium">
                    {{ equipment.rental_price ? `${equipment.rental_price} €` : '—' }}
                  </CardDescription>
                </div>

                <div class="space-y-1">
                  <CardTitle class="text-sm font-medium text-muted-foreground">Caution</CardTitle>
                  <CardDescription class="text-base font-medium">
                    {{ equipment.deposit_amount ? `${equipment.deposit_amount} €` : '—' }}
                  </CardDescription>
                </div>

                <div class="space-y-1">
                  <CardTitle class="text-sm font-medium text-muted-foreground">Dernière maintenance</CardTitle>
                  <CardDescription class="text-base font-medium">
                    {{ equipment.last_maintenance_date || '—' }}
                  </CardDescription>
                </div>

                <div class="space-y-1">
                  <CardTitle class="text-sm font-medium text-muted-foreground">Prochaine maintenance</CardTitle>
                  <CardDescription class="text-base font-medium">
                    {{ equipment.next_maintenance_date || '—' }}
                  </CardDescription>
                </div>
              </div>

              <Separator class="my-6" />

              <div v-if="equipment.specifications && Object.keys(equipment.specifications).length > 0">
                <CardTitle class="flex items-center gap-2 mb-4">
                  <Settings2 class="h-5 w-5" />
                  Spécifications
                </CardTitle>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                  <div v-for="(value, key) in equipment.specifications" :key="key" class="space-y-1">
                    <dt class="text-sm font-medium text-muted-foreground">{{ key }}</dt>
                    <dd class="text-base font-medium">{{ value }}</dd>
                  </div>
                </dl>
              </div>

              <Separator v-if="equipment.images && equipment.images.length > 0" class="my-6" />

              <div v-if="equipment.images && equipment.images.length > 0">
                <CardTitle class="flex items-center gap-2 mb-4">
                  <Image class="h-5 w-5" />
                  Photos
                </CardTitle>
                <ImageGallery :images="equipment.images" />
              </div>
            </CardContent>
          </Card>

          <!-- Status and Actions Column -->
          <div class="space-y-6">
            <!-- Status Card -->
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center gap-2">
                  <Activity class="h-5 w-5" />
                  Statut
                </CardTitle>
              </CardHeader>
              <CardContent class="space-y-4">
                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-muted transition-colors">
                  <span class="text-sm font-medium">Disponibilité</span>
                  <Badge :variant="equipment.is_available ? 'success' : 'destructive'">
                    {{ equipment.is_available ? 'Disponible' : 'Non disponible' }}
                  </Badge>
                </div>

                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-muted transition-colors">
                  <span class="text-sm font-medium">Location</span>
                  <Badge :variant="equipment.is_rentable ? 'secondary' : 'outline'">
                    {{ equipment.is_rentable ? 'Louable' : 'Non louable' }}
                  </Badge>
                </div>

                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-muted transition-colors">
                  <span class="text-sm font-medium">Caution requise</span>
                  <Badge :variant="equipment.requires_deposit ? 'warning' : 'outline'">
                    {{ equipment.requires_deposit ? 'Oui' : 'Non' }}
                  </Badge>
                </div>
              </CardContent>
            </Card>

            <!-- Location Card -->
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center gap-2">
                  <MapPin class="h-5 w-5" />
                  Emplacement actuel
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div v-if="equipment.depot" class="p-3 rounded-lg border hover:bg-muted transition-colors">
                  <p class="text-base font-medium">{{ equipment.depot.name }}</p>
                  <p v-if="equipment.depot.address" class="text-sm text-muted-foreground mt-1">
                    {{ equipment.depot.address }}<br>
                    {{ equipment.depot.city }} {{ equipment.depot.zip_code }}
                  </p>
                </div>
                <p v-else class="text-sm text-muted-foreground italic">
                  Aucun emplacement spécifié
                </p>
              </CardContent>
            </Card>

            <!-- Reservations Card -->
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center gap-2">
                  <Calendar class="h-5 w-5" />
                  Réservations
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div v-if="equipment.current_rental" class="mb-4">
                  <h4 class="text-sm font-medium mb-2 flex items-center gap-2">
                    <Clock class="h-4 w-4" />
                    Location en cours
                  </h4>
                  <div class="p-3 rounded-lg border hover:bg-muted transition-colors">
                    <p class="text-base font-medium">
                      {{ equipment.current_rental.renter.name }}
                    </p>
                    <p class="text-sm text-muted-foreground mt-1">
                      Du {{ equipment.current_rental.start_date }} au {{ equipment.current_rental.end_date }}
                    </p>
                  </div>
                </div>

                <div v-if="equipment.upcoming_rentals && equipment.upcoming_rentals.length > 0">
                  <h4 class="text-sm font-medium mb-2 flex items-center gap-2">
                    <CalendarClock class="h-4 w-4" />
                    Prochaines réservations
                  </h4>
                  <div class="space-y-2">
                    <div
                      v-for="rental in equipment.upcoming_rentals"
                      :key="rental.id"
                      class="p-3 rounded-lg border hover:bg-muted transition-colors"
                    >
                      <p class="text-base font-medium">
                        {{ rental.renter.name }}
                      </p>
                      <p class="text-sm text-muted-foreground mt-1">
                        Du {{ rental.start_date }} au {{ rental.end_date }}
                      </p>
                    </div>
                  </div>
                </div>

                <p
                  v-if="(!equipment.current_rental && (!equipment.upcoming_rentals || equipment.upcoming_rentals.length === 0))"
                  class="text-sm text-muted-foreground italic"
                >
                  Aucune réservation en cours ou à venir
                </p>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Breadcrumb, BreadcrumbItem } from '@/components/ui/breadcrumb'
import { Badge } from '@/components/ui/badge'
import { Separator } from '@/components/ui/separator'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import ImageGallery from '@/components/ImageGallery.vue'
import {
  Package,
  ArrowLeft,
  Pencil,
  Trash2,
  FileText,
  Settings2,
  Activity,
  MapPin,
  Calendar,
  Clock,
  CalendarClock,
  Image
} from 'lucide-vue-next'

const props = defineProps({
  organization: {
    type: Object,
    required: true
  },
  equipment: {
    type: Object,
    required: true
  },
  can: {
    type: Object,
    required: true
  }
})

const deleteEquipment = () => {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce matériel ?')) {
    router.delete(route('app.organizations.equipments.destroy', [props.organization, props.equipment]))
  }
}
</script> 