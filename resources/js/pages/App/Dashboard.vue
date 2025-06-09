<template>
  <AppLayout>
    <Head title="Tableau de bord" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 py-8">
      
      <!-- Quick Actions -->
      <div class="flex flex-wrap gap-4">
        <Button  asChild variant="default" class="group">
          <Link :href="route('app.organizations.equipments.create')" class="flex items-center">
            <PlusCircle class="i-lucide-plus-circle mr-2 h-4 w-4 transition-transform group-hover:scale-125" />
            Ajouter du matériel
          </Link>
        </Button>
        <Button  asChild variant="outline" class="group">
          <Link :href="route('app.organizations.reservations.create')" class="flex items-center">
            <CalendarPlus class="i-lucide-calendar-plus mr-2 h-4 w-4 transition-transform group-hover:scale-125" />
            Créer une réservation
          </Link>
        </Button>
        <Button  asChild variant="outline" class="group">
          <Link :href="route('app.organizations.settings.edit')" class="flex items-center">
            <Settings class="i-lucide-settings mr-2 h-4 w-4 transition-transform group-hover:scale-125" />
            Gérer l'organisation
          </Link>
        </Button>
      </div>

      <!-- Stats Overview -->
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Équipements</CardTitle>
            <i class="i-lucide-box h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ safeStats.equipment_count }}</div>
            <p class="text-xs text-muted-foreground">
              Total des équipements disponibles
            </p>
          </CardContent>
        </Card>

        <Card class="border-blue-200">
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Emprunts</CardTitle>
            <i class="i-lucide-arrow-down-left h-4 w-4 text-blue-500" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-blue-700">{{ safeStats.total_borrowed_count }}</div>
            <p class="text-xs text-muted-foreground">
              Total des emprunts effectués
            </p>
          </CardContent>
        </Card>

        <Card class="border-orange-200">
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Prêts</CardTitle>
            <i class="i-lucide-arrow-up-right h-4 w-4 text-orange-500" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-orange-700">{{ safeStats.total_lent_count }}</div>
            <p class="text-xs text-muted-foreground">
              Total des prêts effectués
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Lieux de stockage</CardTitle>
            <i class="i-lucide-warehouse h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ safeStats.depots_count }}</div>
            <p class="text-xs text-muted-foreground">
              Nombre total de lieux de stockage
            </p>
          </CardContent>
        </Card>
      </div>

      <!-- Main Content -->
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <!-- Emprunts en cours -->
        <Card class="col-span-1 border-orange-200">
          <CardHeader class="flex flex-row items-center justify-between space-y-0">
            <div class="space-y-1">
              <CardTitle>Équipements prêtés</CardTitle>
              <CardDescription>Équipements que vous prêtez actuellement</CardDescription>
            </div>
            <i class="i-lucide-arrow-up-right h-5 w-5 text-orange-500" />
          </CardHeader>
          <CardContent>
            <ScrollArea class="h-[300px] pr-4">
              <div v-if="lentReservations?.length" class="space-y-6">
                <div v-for="reservation in lentReservations" :key="reservation.id" 
                     class="flex flex-col space-y-2 rounded-lg border border-orange-200 p-4 transition-colors hover:bg-orange-50/50">
                  <div class="flex items-center justify-between">
                    <div class="font-medium">{{ reservation.borrower_organization?.name }}</div>
                    <Badge :class="reservation.status_color">
                      {{ reservation.status_label }}
                    </Badge>
                  </div>
                  <div class="text-sm text-muted-foreground">
                    {{ formatDateRange(reservation.start_date, reservation.end_date) }}
                  </div>
                  <div class="space-y-1">
                    <div v-for="item in reservation.items" :key="item.id" 
                         class="flex items-center justify-between text-sm">
                      <div class="flex items-center gap-2">
                        <Badge variant="outline" class="shrink-0">
                          {{ item.quantity }}
                        </Badge>
                        <span>{{ item.equipment.name }}</span>
                      </div>
                      <Badge :class="item.status_color">
                        {{ item.status_label }}
                      </Badge>
                    </div>
                  </div>
                  <div class="mt-2 flex items-center justify-between border-t pt-2 text-sm">
                    <div class="flex items-center gap-2 text-muted-foreground">
                      <i class="i-lucide-wallet h-4 w-4" />
                      <span>{{ formatPrice(reservation.subtotal / 100) }}</span>
                      <span v-if="reservation.discount_amount" class="text-orange-600">
                        (-{{ formatPrice(reservation.discount_amount / 100) }})
                      </span>
                    </div>
                    <div class="flex items-center gap-2 text-muted-foreground">
                      <i class="i-lucide-shield h-4 w-4" />
                      <span class="font-bold">Total : {{ formatPrice(reservation.total / 100) }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="flex h-[200px] items-center justify-center">
                <div class="text-center">
                  <i class="i-lucide-package-x mb-2 h-10 w-10 text-muted-foreground" />
                  <p class="text-sm text-muted-foreground">Aucun équipement prêté</p>
                </div>
              </div>
            </ScrollArea>
          </CardContent>
        </Card>

        <!-- Prêts en cours -->
        <Card class="col-span-1 border-blue-200">
          <CardHeader class="flex flex-row items-center justify-between space-y-0">
            <div class="space-y-1">
              <CardTitle>Équipements empruntés</CardTitle>
              <CardDescription>Équipements que vous empruntez actuellement</CardDescription>
            </div>
            <i class="i-lucide-arrow-down-left h-5 w-5 text-blue-500" />
          </CardHeader>
          <CardContent>
            <ScrollArea class="h-[300px] pr-4">
              <div v-if="borrowedReservations?.length" class="space-y-6">
                <div v-for="reservation in borrowedReservations" :key="reservation.id" 
                     class="flex flex-col space-y-2 rounded-lg border border-blue-200 p-4 transition-colors hover:bg-blue-50/50">
                  <div class="flex items-center justify-between">
                    <div class="font-medium">{{ reservation.lender_organization?.name }}</div>
                    <Badge :class="reservation.status_color">
                      {{ reservation.status_label }}
                    </Badge>
                  </div>
                  <div class="text-sm text-muted-foreground">
                    {{ formatDateRange(reservation.start_date, reservation.end_date) }}
                  </div>
                  <div class="space-y-1">
                    <div v-for="item in reservation.items" :key="item.id" 
                         class="flex items-center justify-between text-sm">
                      <div class="flex items-center gap-2">
                        <Badge variant="outline" class="shrink-0">
                          {{ item.quantity }}
                        </Badge>
                        <span>{{ item.equipment.name }}</span>
                      </div>
                      <Badge :class="item.status_color">
                        {{ item.status_label }}
                      </Badge>
                    </div>
                  </div>
                  <div class="mt-2 flex items-center justify-between border-t pt-2 text-sm">
                    <div class="flex items-center gap-2 text-muted-foreground">
                      <i class="i-lucide-wallet h-4 w-4" />
                      <span>{{ formatPrice(reservation.subtotal / 100) }}</span>
                      <span v-if="reservation.discount_amount" class="text-blue-600">
                        (-{{ formatPrice(reservation.discount_amount / 100) }})
                      </span>
                    </div>
                    <div class="flex items-center gap-2 text-muted-foreground">
                      <i class="i-lucide-shield h-4 w-4" />
                      <span class="font-bold">Total : {{ formatPrice(reservation.total / 100) }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="flex h-[200px] items-center justify-center">
                <div class="text-center">
                  <i class="i-lucide-package-x mb-2 h-10 w-10 text-muted-foreground" />
                  <p class="text-sm text-muted-foreground">Aucun équipement emprunté</p>
                </div>
              </div>
            </ScrollArea>
          </CardContent>
        </Card>

        <!-- Dépôts -->
        <Card class="col-span-1">
          <CardHeader class="flex flex-row items-center justify-between space-y-0">
            <div class="space-y-1">
              <CardTitle>Lieux de stockage</CardTitle>
              <CardDescription>Vue d'ensemble des lieux de stockage</CardDescription>
            </div>
            <i class="i-lucide-warehouse h-5 w-5 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <ScrollArea class="h-[300px] pr-4">
              <div v-if="depots?.length" class="space-y-4">
                <div v-for="depot in depots" :key="depot.id" 
                     class="flex items-center justify-between rounded-lg border p-4 transition-colors hover:bg-muted/50">
                  <div class="space-y-1">
                    <div class="font-medium">{{ depot?.name ?? 'Dépôt sans nom' }}</div>
                    <div class="flex items-center gap-4 text-sm text-muted-foreground">
                      <span class="flex items-center gap-1">
                        <i class="i-lucide-box h-4 w-4" />
                        {{ depot?.equipment_count ?? 0 }} équipements
                      </span>
                    </div>
                  </div>
                  <Button variant="ghost" size="icon">
                    <i class="i-lucide-chevron-right h-4 w-4" />
                  </Button>
                </div>
              </div>
              <div v-else class="flex h-[200px] items-center justify-center">
                <div class="text-center">
                  <i class="i-lucide-warehouse-off mb-2 h-10 w-10 text-muted-foreground" />
                  <p class="text-sm text-muted-foreground">Aucun dépôt</p>
                </div>
              </div>
            </ScrollArea>
          </CardContent>
        </Card>

        <!-- Équipements Récents -->
        <Card class="col-span-1">
          <CardHeader class="flex flex-row items-center justify-between space-y-0">
            <div class="space-y-1">
              <CardTitle>Équipements Récents</CardTitle>
              <CardDescription>Derniers équipements ajoutés</CardDescription>
            </div>
            <i class="i-lucide-box h-5 w-5 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <ScrollArea class="h-[300px] pr-4">
              <div v-if="recentEquipment?.length" class="space-y-4">
                <div v-for="equipment in recentEquipment" :key="equipment.id" 
                     class="flex items-center justify-between rounded-lg border p-4 transition-colors hover:bg-muted/50">
                  <div class="space-y-1">
                    <div class="font-medium">{{ equipment?.name ?? 'Équipement sans nom' }}</div>
                    <div class="flex items-center gap-1 text-sm text-muted-foreground">
                      <i class="i-lucide-warehouse h-4 w-4" />
                      {{ equipment?.depot?.name ?? 'Dépôt inconnu' }}
                    </div>
                  </div>
                  <Button variant="ghost" size="icon">
                    <i class="i-lucide-chevron-right h-4 w-4" />
                  </Button>
                </div>
              </div>
              <div v-else class="flex h-[200px] items-center justify-center">
                <div class="text-center">
                  <i class="i-lucide-package-x mb-2 h-10 w-10 text-muted-foreground" />
                  <p class="text-sm text-muted-foreground">Aucun équipement</p>
                </div>
              </div>
            </ScrollArea>
          </CardContent>
        </Card>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { ScrollArea } from '@/components/ui/scroll-area'
import { Separator } from '@/components/ui/separator'
import AppLayout from '@/layouts/AppLayout.vue'
import { format, formatDistance } from 'date-fns'
import { fr } from 'date-fns/locale'
import { onMounted } from 'vue'
import { PlusCircle, CalendarPlus, Settings } from 'lucide-vue-next'

const user = usePage().props.auth.user

const props = defineProps({
  organization: {
    type: Object,
    default: () => ({})
  },
  stats: {
    type: Object,
    default: () => ({
      equipment_count: 0,
      total_borrowed_count: 0,
      total_lent_count: 0,
      depots_count: 0
    })
  },
  lentReservations: {
    type: Array,
    required: true
  },
  borrowedReservations: {
    type: Array,
    required: true
  },
  recentEquipment: {
    type: Array,
    default: () => []
  },
  depots: {
    type: Array,
    default: () => []
  },
  upcomingReservations: {
    type: Array,
    default: () => []
  }
})

const formatDateTime = (date) => {
  if (!date) return '---'
  try {
    return format(new Date(date), 'PPP à p', { locale: fr })
  } catch (error) {
    console.error('Error formatting date:', error)
    return '---'
  }
}

const formatDateRange = (startDate, endDate) => {
  if (!startDate || !endDate) return '---'
  try {
    const start = format(new Date(startDate), 'dd/MM/yyyy', { locale: fr })
    const end = format(new Date(endDate), 'dd/MM/yyyy', { locale: fr })
    return `${start} → ${end}`
  } catch (error) {
    console.error('Error formatting date range:', error)
    return '---'
  }
}

const getReservationItemsSummary = (items) => {
  if (!items?.length) return 'Aucun équipement'
  if (items.length === 1) return items[0].equipment.name
  return `${items[0].equipment.name} + ${items.length - 1} autre${items.length > 2 ? 's' : ''}`
}

const formatPrice = (price) => {
  if (!price) return '0,00 €'
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}

const safeStats = {
  equipment_count: props.stats?.equipment_count ?? 0,
  total_borrowed_count: props.stats?.total_borrowed_count ?? 0,
  total_lent_count: props.stats?.total_lent_count ?? 0,
  depots_count: props.stats?.depots_count ?? 0
}
</script> 