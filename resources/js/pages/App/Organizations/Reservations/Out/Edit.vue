<template>
  <ReservationLayout :title="'Réservation du ' + reservation.start_date + ' au ' + reservation.end_date">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm p-6">
        <!-- Back Button -->
        <div class="mb-6">
          <Button 
            variant="ghost" 
            class="gap-2"
            @click="router.visit(route('app.organizations.reservations.out.index'))"
          >
            <ArrowLeftIcon class="h-4 w-4" />
            Retour aux réservations
          </Button>
        </div>

        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-6">
          <div class="flex-1">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
              Réservation du {{ reservation.start_date }} au {{ reservation.end_date }}
            </h1>
            <div class="flex items-center gap-2 mt-2">
              <BuildingIcon class="h-4 w-4 text-gray-500" />
              <h2 class="text-lg sm:text-xl font-semibold text-gray-600">
                {{ reservation.to_organization.name }}
              </h2>
            </div>
          </div>
          <div class="flex flex-col items-start sm:items-end gap-2">
            <Badge :class="`${reservation.status_color}`">
              {{ reservation.status_label }}
            </Badge>
          </div>
        </div>

        <!-- Status Management Section -->
        <Card class="mb-6">
          <CardHeader>
            <CardTitle class="text-lg">Gestion de la réservation</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <!-- Pending Status Actions -->
              <div v-if="reservation.status === 'pending'" class="space-y-4">
                <div class="flex items-center gap-3 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                  <ClockIcon class="h-5 w-5 text-yellow-600" />
                  <div class="flex-1">
                    <h3 class="font-medium text-yellow-800">Réservation en attente</h3>
                    <p class="text-sm text-yellow-700">Cette réservation attend votre validation</p>
                    <p class="text-sm text-yellow-700">Vous avez {{ reservation.deadline_for_human }} pour valider ou refuser la réservation. Au delà de ce délai, la réservation sera automatiquement annulée.</p>
                  </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                  <Button 
                    @click="confirmReservation"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white"
                    :disabled="isProcessing"
                  >
                    <CheckIcon class="h-4 w-4 mr-2" />
                    <span v-if="isProcessing">Traitement...</span>
                    <span v-else>Accepter la réservation</span>
                  </Button>
                  
                  <Button 
                    @click="rejectReservation"
                    variant="destructive"
                    class="flex-1"
                    :disabled="isProcessing"
                  >
                    <XIcon class="h-4 w-4 mr-2" />
                    <span v-if="isProcessing">Traitement...</span>
                    <span v-else>Refuser la réservation</span>
                  </Button>
                </div>
              </div>

              <!-- Confirmed Status Actions -->
              <div v-else-if="reservation.status === 'confirmed'" class="space-y-4">
                <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                  <CheckCircleIcon class="h-5 w-5 text-green-600" />
                  <div class="flex-1">
                    <h3 class="font-medium text-green-800">Réservation confirmée</h3>
                    <p class="text-sm text-green-700">Cette réservation a été acceptée</p>
                  </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                  <Button 
                    @click="completeReservation"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white"
                    :disabled="isProcessing"
                  >
                    <CheckCircleIcon class="h-4 w-4 mr-2" />
                    <span v-if="isProcessing">Traitement...</span>
                    <span v-else>Marquer comme terminée</span>
                  </Button>
                  
                  <Button 
                    @click="cancelReservation"
                    variant="outline"
                    class="flex-1 border-red-300 text-red-700 hover:bg-red-50"
                    :disabled="isProcessing"
                  >
                    <XIcon class="h-4 w-4 mr-2" />
                    <span v-if="isProcessing">Traitement...</span>
                    <span v-else>Annuler la réservation</span>
                  </Button>
                </div>
              </div>

              <!-- Completed Status -->
              <div v-else-if="reservation.status === 'completed'" class="space-y-4">
                <div class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                  <CheckCircleIcon class="h-5 w-5 text-blue-600" />
                  <div class="flex-1">
                    <h3 class="font-medium text-blue-800">Réservation terminée</h3>
                    <p class="text-sm text-blue-700">Cette réservation a été complétée avec succès</p>
                  </div>
                </div>
              </div>

              <!-- Cancelled Status -->
              <div v-else-if="reservation.status === 'cancelled'" class="space-y-4">
                <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                  <XCircleIcon class="h-5 w-5 text-red-600" />
                  <div class="flex-1">
                    <h3 class="font-medium text-red-800">Réservation annulée</h3>
                    <p class="text-sm text-red-700">Cette réservation a été annulée</p>
                  </div>
                </div>
              </div>

              <!-- Rejected Status -->
              <div v-else-if="reservation.status === 'rejected'" class="space-y-4">
                <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                  <XCircleIcon class="h-5 w-5 text-red-600" />
                  <div class="flex-1">
                    <h3 class="font-medium text-red-800">Réservation refusée</h3>
                    <p class="text-sm text-red-700">Cette réservation a été refusée</p>
                  </div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Dates Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <Card>
            <CardHeader>
              <CardTitle class="text-lg">Détails de la réservation</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-start gap-3">
                  <CalendarIcon class="h-5 w-5 text-gray-500 mt-0.5" />
                  <div>
                    <h3 class="text-sm font-medium text-gray-500">Récupération</h3>
                    <p class="text-gray-900">{{ reservation.start_date }}</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <CalendarIcon class="h-5 w-5 text-gray-500 mt-0.5" />
                  <div>
                    <h3 class="text-sm font-medium text-gray-500">Retour</h3>
                    <p class="text-gray-900">{{ reservation.end_date }}</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <ClockIcon class="h-5 w-5 text-gray-500 mt-0.5" />
                  <div>
                    <h3 class="text-sm font-medium text-gray-500">Durée</h3>
                    <p class="text-gray-900">{{ reservation.duration }} {{ reservation.duration > 1 ? 'jours' : 'jour' }}</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <BuildingIcon class="h-5 w-5 text-gray-500 mt-0.5" />
                  <div>
                    <h3 class="text-sm font-medium text-gray-500">Organisation prêteuse</h3>
                    <p class="text-gray-900">{{ reservation.from_organization.name }}</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <PlusIcon class="h-5 w-5 text-gray-500 mt-0.5" />
                  <div>
                    <h3 class="text-sm font-medium text-gray-500">Créée le</h3>
                    <p class="text-gray-900">{{ reservation.created_at }}</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <PencilIcon class="h-5 w-5 text-gray-500 mt-0.5" />
                  <div>
                    <h3 class="text-sm font-medium text-gray-500">Dernière mise à jour</h3>
                    <p class="text-gray-900">{{ reservation.updated_at }}</p>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Contact Section -->
          <Card>
            <CardHeader>
              <CardTitle class="text-lg">Contact emprunteur</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="grid grid-cols-1 gap-6">
                <div class="flex items-start gap-3">
                  <UserIcon class="h-5 w-5 text-gray-500 mt-0.5" />
                  <div>
                    <h3 class="text-sm font-medium text-gray-500">Nom</h3>
                    <p class="text-gray-900">{{ reservation.user.name }}</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <MailIcon class="h-5 w-5 text-gray-500 mt-0.5" />
                  <div>
                    <h3 class="text-sm font-medium text-gray-500">Email</h3>
                    <p class="text-gray-900">{{ reservation.user.email }}</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <PhoneIcon class="h-5 w-5 text-gray-500 mt-0.5" />
                  <div>
                    <h3 class="text-sm font-medium text-gray-500">Téléphone</h3>
                    <p class="text-gray-900">{{ reservation.user.phone || 'Non renseigné' }}</p>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Equipment Section -->
        <Card>
          <CardHeader>
            <div class="flex justify-between items-center">
              <CardTitle class="text-lg">Matériel réservé</CardTitle>
              <div class="text-right">
                <p class="text-sm text-gray-600">Prix total</p>
                <p class="text-xl font-bold text-gray-900">{{ reservation.total }} €</p>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Équipement</TableHead>
                  <TableHead>Quantité</TableHead>
                  <TableHead>Prix/jour</TableHead>
                  <TableHead>Caution</TableHead>
                  <TableHead>Emplacement</TableHead>
                  <TableHead v-if="reservation.status === 'pending'" class="w-[100px]"></TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="item in reservation.items" :key="item.id">
                  <TableCell class="font-medium">{{ item.equipment.name }}</TableCell>
                  <TableCell>
                    <Badge variant="outline" class="text-sm font-medium">
                      {{ item.quantity }}
                    </Badge>
                  </TableCell>
                  <TableCell>{{ item.price }} €</TableCell>
                  <TableCell>{{ item.equipment.deposit_amount }} €</TableCell>
                  <TableCell>{{ item.city }}</TableCell>
                  <TableCell v-if="reservation.status === 'pending'">
                    <Button 
                      variant="ghost" 
                      size="icon"
                      class="text-destructive hover:text-destructive/90"
                      @click="removeItem(item.id)"
                    >
                      <Trash2Icon class="h-4 w-4" />
                    </Button>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </CardContent>
        </Card>
      </div>
    </div>

    <!-- Confirmation Dialog -->
    <Dialog v-model:open="showConfirmDialog">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle class="flex items-center gap-2">
            <CheckIcon class="h-5 w-5 text-green-600" />
            Confirmer la réservation
          </DialogTitle>
          <DialogDescription>
            Vous êtes sur le point d'accepter cette réservation. Voici ce qui va se passer :
          </DialogDescription>
        </DialogHeader>
        
        <div class="space-y-4">
          <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h4 class="font-medium text-green-800 mb-2">Prochaines étapes :</h4>
            <ul class="text-sm text-green-700 space-y-1">
              <li>• Un email de confirmation sera envoyé à l'emprunteur</li>
              <li>• Vous devez prendre contact pour organiser les retraits</li>
              <li>• Coordonner les modalités de paiement et de caution</li>
              <li>• Définir les conditions de retour du matériel</li>
            </ul>
          </div>
          
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="font-medium text-blue-800 mb-2">Informations importantes :</h4>
            <p class="text-sm text-blue-700">
              La réservation sera marquée comme "confirmée" et vous pourrez ensuite la marquer comme "terminée" une fois le matériel retourné.
            </p>
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" @click="showConfirmDialog = false" :disabled="isProcessing">
            Annuler
          </Button>
          <Button @click="handleConfirmReservation" :disabled="isProcessing" class="bg-green-600 hover:bg-green-700">
            <CheckIcon class="h-4 w-4 mr-2" />
            <span v-if="isProcessing">Traitement...</span>
            <span v-else>Confirmer la réservation</span>
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Rejection Dialog -->
    <Dialog v-model:open="showRejectDialog">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle class="flex items-center gap-2">
            <XIcon class="h-5 w-5 text-red-600" />
            Refuser la réservation
          </DialogTitle>
          <DialogDescription>
            Vous êtes sur le point de refuser cette réservation. Voici ce qui va se passer :
          </DialogDescription>
        </DialogHeader>
        
        <div class="space-y-4">
          <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <h4 class="font-medium text-red-800 mb-2">Conséquences :</h4>
            <ul class="text-sm text-red-700 space-y-1">
              <li>• Un email de refus sera envoyé à l'emprunteur</li>
              <li>• La réservation sera marquée comme "refusée"</li>
              <li>• L'emprunteur pourra chercher d'autres options</li>
            </ul>
          </div>
          
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h4 class="font-medium text-yellow-800 mb-2">Recommandation :</h4>
            <p class="text-sm text-yellow-700">
              Si possible, contactez l'emprunteur pour expliquer les raisons du refus et proposer des alternatives.
            </p>
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" @click="showRejectDialog = false" :disabled="isProcessing">
            Annuler
          </Button>
          <Button @click="handleRejectReservation" variant="destructive" :disabled="isProcessing">
            <XIcon class="h-4 w-4 mr-2" />
            <span v-if="isProcessing">Traitement...</span>
            <span v-else>Refuser la réservation</span>
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Completion Dialog -->
    <Dialog v-model:open="showCompleteDialog">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle class="flex items-center gap-2">
            <CheckCircleIcon class="h-5 w-5 text-blue-600" />
            Marquer comme terminée
          </DialogTitle>
          <DialogDescription>
            Vous êtes sur le point de marquer cette réservation comme terminée. Voici ce qui va se passer :
          </DialogDescription>
        </DialogHeader>
        
        <div class="space-y-4">
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="font-medium text-blue-800 mb-2">Confirmation :</h4>
            <ul class="text-sm text-blue-700 space-y-1">
              <li>• Le matériel a été retourné en bon état</li>
              <li>• Les paiements ont été finalisés</li>
              <li>• Un email de confirmation sera envoyé aux deux parties</li>
              <li>• La réservation sera archivée</li>
            </ul>
          </div>
          
          <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h4 class="font-medium text-green-800 mb-2">Important :</h4>
            <p class="text-sm text-green-700">
              Cette action ne peut être annulée. Assurez-vous que tous les aspects de la réservation sont bien finalisés.
            </p>
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" @click="showCompleteDialog = false" :disabled="isProcessing">
            Annuler
          </Button>
          <Button @click="handleCompleteReservation" :disabled="isProcessing" class="bg-blue-600 hover:bg-blue-700">
            <CheckCircleIcon class="h-4 w-4 mr-2" />
            <span v-if="isProcessing">Traitement...</span>
            <span v-else>Marquer comme terminée</span>
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Cancellation Dialog -->
    <Dialog v-model:open="showCancelDialog">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle class="flex items-center gap-2">
            <XCircleIcon class="h-5 w-5 text-red-600" />
            Annuler la réservation
          </DialogTitle>
          <DialogDescription>
            Vous êtes sur le point d'annuler cette réservation. Voici ce qui va se passer :
          </DialogDescription>
        </DialogHeader>
        
        <div class="space-y-4">
          <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <h4 class="font-medium text-red-800 mb-2">Conséquences :</h4>
            <ul class="text-sm text-red-700 space-y-1">
              <li>• Un email d'annulation sera envoyé à l'emprunteur</li>
              <li>• La réservation sera marquée comme "annulée"</li>
              <li>• Le matériel sera remis à disposition</li>
            </ul>
          </div>
          
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h4 class="font-medium text-yellow-800 mb-2">Recommandation :</h4>
            <p class="text-sm text-yellow-700">
              Contactez l'emprunteur pour expliquer les raisons de l'annulation et proposer des solutions alternatives si possible.
            </p>
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" @click="showCancelDialog = false" :disabled="isProcessing">
            Garder la réservation
          </Button>
          <Button @click="handleCancelReservation" variant="destructive" :disabled="isProcessing">
            <XCircleIcon class="h-4 w-4 mr-2" />
            <span v-if="isProcessing">Traitement...</span>
            <span v-else>Annuler la réservation</span>
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </ReservationLayout>
</template>

<script setup>
import ReservationLayout from '@/pages/App/Organizations/Reservations/Layout.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { CalendarIcon, BuildingIcon, ClockIcon, XIcon, ArrowLeftIcon, PlusIcon, PencilIcon, UserIcon, MailIcon, PhoneIcon, CheckIcon, CheckCircleIcon, XCircleIcon } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'
import { Trash2Icon } from 'lucide-vue-next'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { ref } from 'vue'

const props = defineProps({
  reservation: {
    type: Object,
    required: true
  }
})

const isProcessing = ref(false)
const showConfirmDialog = ref(false)
const showRejectDialog = ref(false)
const showCompleteDialog = ref(false)
const showCancelDialog = ref(false)

const confirmReservation = () => {
  showConfirmDialog.value = true
}

const rejectReservation = () => {
  showRejectDialog.value = true
}

const completeReservation = () => {
  showCompleteDialog.value = true
}

const cancelReservation = () => {
  showCancelDialog.value = true
}

const handleConfirmReservation = () => {
  isProcessing.value = true
  showConfirmDialog.value = false
  router.put(route('app.organizations.reservations.out.confirm', { reservation: props.reservation.id }), {}, {
    onFinish: () => {
      isProcessing.value = false
    }
  })
}

const handleRejectReservation = () => {
  isProcessing.value = true
  showRejectDialog.value = false
  router.put(route('app.organizations.reservations.out.reject', { reservation: props.reservation.id }), {}, {
    onFinish: () => {
      isProcessing.value = false
    }
  })
}

const handleCompleteReservation = () => {
  isProcessing.value = true
  showCompleteDialog.value = false
  router.put(route('app.organizations.reservations.out.complete', { reservation: props.reservation.id }), {}, {
    onFinish: () => {
      isProcessing.value = false
    }
  })
}

const handleCancelReservation = () => {
  isProcessing.value = true
  showCancelDialog.value = false
  router.delete(route('app.organizations.reservations.out.destroy', { reservation: props.reservation.id }), {
    onFinish: () => {
      isProcessing.value = false
    }
  })
}

const removeItem = (itemId) => {
  if(!confirm('Voulez-vous vraiment retirer cet équipement de la réservation ?')) return;
  router.delete(route('app.organizations.reservations.out.items.destroy', { 
    reservation: props.reservation.id,
    item: itemId
  }))
}

</script> 