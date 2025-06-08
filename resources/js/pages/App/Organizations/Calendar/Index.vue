<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import frLocale from '@fullcalendar/core/locales/fr'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { CalendarDays, User, Package, CreditCard, Building2, Phone, Mail, Plus } from 'lucide-vue-next'

const props = defineProps({
  reservations: {
    type: Array,
    required: true
  }
})

const organization = computed(() => usePage().props.auth.user.current_organization)

const selectedReservation = ref(null)
const isModalOpen = ref(false)
const isNewReservationModalOpen = ref(false)
const selectedDates = ref({ start: null, end: null })

const getStatusColors = (status) => {
  const colors = {
    'bg-yellow-100': '#fef9c3',
    'bg-green-100': '#dcfce7',
    'bg-red-100': '#fee2e2',
    'bg-gray-100': '#f3f4f6',
    'bg-blue-100': '#dbeafe',
    'text-yellow-800': '#854d0e',
    'text-green-800': '#166534',
    'text-red-800': '#991b1b',
    'text-gray-800': '#1f2937',
    'text-blue-800': '#1e3a8a'
  }
  
  const [bgClass, textClass] = status.color.split(' ')
  return {
    backgroundColor: colors[bgClass],
    textColor: colors[textClass]
  }
}

const calendarEvents = computed(() => {
  return props.reservations.map(reservation => {
    const colors = getStatusColors(reservation.status)
    return {
      id: reservation.id,
      title: reservation.title,
      start: reservation.start,
      end: reservation.end,
      allDay: true,
      backgroundColor: colors.backgroundColor,
      textColor: colors.textColor,
      borderColor: colors.backgroundColor,
      extendedProps: reservation
    }
  })
})

const handleEventClick = (info) => {
  selectedReservation.value = info.event.extendedProps
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  selectedReservation.value = null
}

const handleDateSelect = (selectInfo) => {
  selectedDates.value = {
    start: selectInfo.startStr,
    end: selectInfo.endStr
  }
  isNewReservationModalOpen.value = true
}

const createReservation = () => {
  router.visit(route('organizations.reservations.create', organization.id), {
    data: {
      start_date: selectedDates.value.start,
      end_date: selectedDates.value.end
    }
  })
}
</script>

<template>
  <AppLayout title="Calendrier">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl leading-tight">
          Calendrier des locations de {{ organization.name }}
        </h2>
        <Button
          as="a"
          :href="route('app.organizations.reservations.create', organization)"
          variant="default"
          size="default"
        >
          Nouvelle réservation
        </Button>
      </div>
    </template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 py-8">
      <div class="calendar-wrapper">
        <FullCalendar
          :options="{
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            headerToolbar: {
              left: 'prev,next today',
              center: 'title',
              right: 'dayGridMonth'
            },
            events: calendarEvents,
            eventClick: handleEventClick,
            locale: frLocale,
            height: 'auto',
            editable: false,
            selectable: true,
            select: handleDateSelect,
            selectMirror: true,
            nowIndicator: true,
            displayEventTime: false,
            dayMaxEvents: true,
            views: {
              dayGrid: {
                dayMaxEvents: 4
              }
            }
          }"
        />
      </div>

      <Dialog :open="isModalOpen" @update:open="closeModal">
        <DialogContent class="sm:max-w-[500px]">
          <DialogHeader>
            <DialogTitle>Détails de la location</DialogTitle>
          </DialogHeader>

          <div v-if="selectedReservation" class="space-y-6">
            <div class="flex items-start gap-4">
              <Badge :class="selectedReservation.status.color">
                {{ selectedReservation.status.label }}
              </Badge>
            </div>

            <div class="grid gap-4">
              <div class="flex items-center gap-2">
                <CalendarDays class="h-4 w-4" />
                <span>Du {{ new Date(selectedReservation.start).toLocaleDateString() }} au {{ new Date(selectedReservation.end).toLocaleDateString() }}</span>
              </div>

              <!-- Organisation locataire -->
              <div class="rounded-lg bg-muted p-4">
                <div class="flex items-center gap-2 mb-3">
                  <Building2 class="h-4 w-4" />
                  <span class="font-medium">{{ selectedReservation.borrower.name }}</span>
                </div>
                
                <div class="space-y-2 text-sm text-muted-foreground">
                  <div v-if="selectedReservation.borrower.phone" class="flex items-center gap-2">
                    <Phone class="h-4 w-4" />
                    <a :href="'tel:' + selectedReservation.borrower.phone" class="hover:underline">
                      {{ selectedReservation.borrower.phone }}
                    </a>
                  </div>
                  <div v-if="selectedReservation.borrower.email" class="flex items-center gap-2">
                    <Mail class="h-4 w-4" />
                    <a :href="'mailto:' + selectedReservation.borrower.email" class="hover:underline">
                      {{ selectedReservation.borrower.email }}
                    </a>
                  </div>
                </div>

                <!-- Contact principal -->
                <div class="mt-4 pt-4 border-t border-border">
                  <div class="flex items-center gap-2 mb-2">
                    <User class="h-4 w-4" />
                    <span class="font-medium">{{ selectedReservation.borrower.contact.name }}</span>
                  </div>
                  
                  <div class="space-y-2 text-sm text-muted-foreground">
                    <div v-if="selectedReservation.borrower.contact.phone" class="flex items-center gap-2">
                      <Phone class="h-4 w-4" />
                      <a :href="'tel:' + selectedReservation.borrower.contact.phone" class="hover:underline">
                        {{ selectedReservation.borrower.contact.phone }}
                      </a>
                    </div>
                    <div v-if="selectedReservation.borrower.contact.email" class="flex items-center gap-2">
                      <Mail class="h-4 w-4" />
                      <a :href="'mailto:' + selectedReservation.borrower.contact.email" class="hover:underline">
                        {{ selectedReservation.borrower.contact.email }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Équipements -->
              <div class="flex items-start gap-2">
                <Package class="h-4 w-4 mt-1" />
                <div class="flex-1">
                  <h4 class="font-medium mb-2">Équipements</h4>
                  <ul class="space-y-2">
                    <li v-for="item in selectedReservation.items" :key="item.equipment" class="flex justify-between">
                      <span>{{ item.equipment }} (x{{ item.quantity }})</span>
                      <Badge :class="item.status.color">{{ item.status.label }}</Badge>
                    </li>
                  </ul>
                </div>
              </div>

              <div class="flex items-center gap-2">
                <CreditCard class="h-4 w-4" />
                <span>Total: {{ selectedReservation.total }}</span>
              </div>
            </div>
          </div>
        </DialogContent>
      </Dialog>

      <!-- Modal nouvelle réservation -->
      <Dialog :open="isNewReservationModalOpen" @update:open="isNewReservationModalOpen = false">
        <DialogContent class="sm:max-w-[500px]">
          <DialogHeader>
            <DialogTitle>Nouvelle réservation</DialogTitle>
          </DialogHeader>

          <div class="space-y-6">
            <div class="flex items-center gap-2">
              <CalendarDays class="h-4 w-4" />
              <span>Du {{ new Date(selectedDates.start).toLocaleDateString() }} au {{ new Date(selectedDates.end).toLocaleDateString() }}</span>
            </div>

            <div class="flex justify-end gap-2">
              <Button variant="outline" @click="isNewReservationModalOpen = false">
                Annuler
              </Button>
              <Button @click="createReservation">
                Créer la location
              </Button>
            </div>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  </AppLayout>
</template>