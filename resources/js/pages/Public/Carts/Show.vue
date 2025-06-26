<template>
    <PublicLayout title="Mon camion" description="Vérifier le chargement avant de confirmer votre demande">
        <template #header>
            <div class="max-w-7xl mx-auto">
                <h1 class="text-xl font-semibold">Mon camion</h1>
                <p class="text-sm text-muted-foreground">
                    Vérifiez bien les dates et les quantités avant de confirmer votre demande.
                </p>
            </div>
        </template>
        <div class="max-w-7xl mx-auto py-6">
            <!-- Empty cart state -->
            <div v-if="items.length === 0" class="flex flex-col items-center justify-center py-12">
                <Truck class="h-16 w-16 text-muted-foreground mb-4" />
                <h2 class="text-2xl font-semibold mb-2">Votre camion est vide</h2>
                <p class="text-muted-foreground mb-6">Ajoutez du matériel à votre camion pour commencer</p>
                <Button asChild>
                    <Link :href="route('home')">
                        Parcourir le matériel disponible
                    </Link>
                </Button>
            </div>

            <!-- Cart items -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Items list -->
                <div class="lg:col-span-2 space-y-6">
                    <div v-for="group in items" :key="group.organization.id" class="space-y-4">
                        <Card class="bg-secondary/50">
                            <CardHeader>
                                <CardTitle>
                                    <span class="uppercase font-medium">{{ group.organization.name }}</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <Card v-for="item in group.items" :key="item.equipment.id" class="group">
                                    <CardContent class="px-4">
                                        <div class="flex flex-wrap gap-6">
                                            <!-- Item image -->
                                            <div class="relative">
                                                <img
                                                    v-if="item.image"
                                                    :src="item.image"
                                                    :alt="item.equipment.name"
                                                    class="w-12 h-12 lg:w-32 lg:h-32 object-cover rounded-lg"
                                                />
                                                <div v-else class="w-12 h-12 lg:w-32 lg:h-32 bg-muted rounded-lg flex items-center justify-center">
                                                    <Camera class="h-10 w-10 text-muted-foreground" />
                                                </div>
                                            </div>

                                            <!-- Item details -->
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold mb-2">{{ item.equipment.name }}</h3>
                                                <div class="space-y-2 text-sm text-muted-foreground">
                                                    <p>
                                                        du {{ formatDate(item.rental_start) }} au {{ formatDate(item.rental_end) }}
                                                    </p>
                                                    <div class="space-y-1">
                                                        <span class="font-medium">Quantité:</span>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <Button
                                                                variant="outline"
                                                                size="icon"
                                                                @click="updateQuantity(item, item.quantity - 1)"
                                                                :disabled="item.quantity <= 1"
                                                                class="transition-opacity"
                                                            >
                                                                <Minus class="h-4 w-4" />
                                                            </Button>
                                                            <span class="w-8 text-center">{{ item.quantity }}</span>
                                                            <Button
                                                                variant="outline"
                                                                size="icon"
                                                                @click="updateQuantity(item, item.quantity + 1)"
                                                                class="transition-opacity"
                                                                :disabled="item.quantity >= item.quantityAvailable"
                                                            >
                                                                <Plus class="h-4 w-4" />
                                                            </Button>
                                                        </div>
                                                    </div>
                                                    <p v-if="item.notes">
                                                        <span class="font-medium">Notes:</span><br>
                                                        {{ item.notes }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Item price and actions -->
                                            <div class="lg:text-right space-y-2">
                                                <p class="text-lg font-semibold">
                                                    {{ formatPrice(item.total) }}
                                                </p>
                                                <p class="text-sm">
                                                    {{ formatPrice(item.price) }} x {{ item.days }} jours
                                                </p>
                                                <p class="text-sm text-muted-foreground" v-if="item.equipment.requires_deposit">
                                                    Caution: {{ formatPrice(item.equipment.deposit_amount * item.quantity) }}
                                                </p>
                                                <Button
                                                    variant="ghost"
                                                    class="text-destructive"
                                                    size="sm"
                                                    @click="removeItem(item)"
                                                >
                                                    <Trash class="h-4 w-4" /> Retirer
                                                </Button>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>

                                <div class="flex justify-between pt-4 border-t">
                                    <span class="font-medium">Total pour {{ group.organization.name }}</span>
                                    <div class="text-right">
                                        <p class="font-semibold">{{ formatPrice(group.total) }}</p>
                                        <p class="text-sm text-muted-foreground" v-if="group.deposit > 0">
                                            Caution: {{ formatPrice(group.deposit) }}
                                        </p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <!-- Order summary -->
                <div class="lg:col-span-1 space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Récapitulatif de la réservation</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span>Coût de location</span>
                                    <span>{{ formatPrice(totalPrice) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Caution totale</span>
                                    <span>{{ formatPrice(totalDeposit) }}</span>
                                </div>
                                <Separator />
                                <p class="text-sm text-muted-foreground">
                                    La caution sera à régler directement avec le propriétaire lors de la remise du matériel.
                                </p>
                            </div>

                            <div class="space-y-2">

                                <div class="mt-4 p-4 bg-muted/50 rounded-lg border border-border">
                                        <h4 class="font-medium mb-3 flex items-center gap-2">
                                            Comment fonctionne la réservation ?
                                        </h4>
                                        <div class="space-y-3 text-sm">
                                            <div class="flex gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-primary font-medium">1</div>
                                                <p>Vous notifiez les organisations propriétaires des équipements sélectionnés en cliquant sur le bouton "Confirmer ma demande".</p>
                                            </div>
                                            <div class="flex gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-primary font-medium">2</div>
                                                <p>Les propriétaires valident ou refusent votre demande. Laissez-leur un peu de temps pour répondre.</p>
                                            </div>
                                            <div class="flex gap-3">
                                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-primary font-medium">3</div>
                                                <p>Vous recevez un email de chaque propriétaire avec leurs coordonnées pour finaliser la réservation si elle est acceptée. Vous recevez également un email si la réservation est refusée.</p>
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            <Button 
                                    class="w-full"
                                    :disabled="isProcessing"
                                    @click="showConfirmationModal = true"
                                >
                                    Confirmer ma demande
                                </Button>

                                <Button 
                                    variant="destructive" 
                                    @click="clearCart" 
                                    class="w-full"
                                    :disabled="isProcessing"
                                >
                                    Vider le camion
                                </Button>

                        </CardContent>
                    </Card>

                    <Dialog v-model:open="showConfirmationModal">
                        <DialogContent class="sm:max-w-[600px]">
                            <DialogHeader>
                                <DialogTitle>Confirmation de votre demande</DialogTitle>
                                <DialogDescription>
                                    Veuillez prendre connaissance des informations importantes avant de confirmer votre demande.
                                </DialogDescription>
                            </DialogHeader>
                            <div class="space-y-4 py-4">
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-destructive/10 flex items-center justify-center text-destructive">
                                        <AlertTriangle class="h-4 w-4" />
                                    </div>
                                    <p class="text-sm text-destructive">Les demandes de réservation de plus d'une semaine restées sans réponse seront automatiquement annulées.</p>
                                </div>
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                        <Users class="h-4 w-4" />
                                    </div>
                                    <p class="text-sm">Chaque demande faite à une organisation différente est traitée comme une réservation distincte.</p>
                                </div>
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                        <Shield class="h-4 w-4" />
                                    </div>
                                    <p class="text-sm">La plateforme agit uniquement comme intermédiaire et ne peut être tenue responsable des litiges entre les parties.</p>
                                </div>
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                        <FileText class="h-4 w-4" />
                                    </div>
                                    <p class="text-sm">Il est de la responsabilité des utilisateurs de vérifier l'état et la conformité du matériel avant la location.</p>
                                </div>
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                        <AlertCircle class="h-4 w-4" />
                                    </div>
                                    <p class="text-sm">La plateforme ne garantit pas la disponibilité des équipements et ne peut être tenue responsable des annulations.</p>
                                </div>
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                        <CreditCard class="h-4 w-4" />
                                    </div>
                                    <p class="text-sm">Les paiements et arrangements financiers se font directement avec les propriétaires des équipements, en dehors de la plateforme.</p>
                                </div>
                            </div>
                            <form class="space-y-2" id="reservation-message-form" @submit.prevent="confirmReservation">
                                <p class="text-sm font-bold">Laissez un message pour présenter votre projet :</p>
                                <Textarea v-model="form.message" rows="6" placeholder="Présentez-vous, donnez du contexte, etc." :error="form.errors.message" required />
                            </form>
                            <DialogFooter>
                                <Button variant="outline" @click="showConfirmationModal = false">
                                    Annuler
                                </Button>
                                <Button type="submit" form="reservation-message-form" :disabled="isProcessing">
                                    Je confirme ma demande
                                </Button>
                            </DialogFooter>
                        </DialogContent>
                    </Dialog>

                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage, useForm } from '@inertiajs/vue3';
import { Truck, Minus, Plus, Trash, AlertTriangle, CreditCard, Camera, Users, Shield, FileText, AlertCircle } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { toast } from 'vue-sonner';
import PublicLayout from '@/layouts/PublicLayout.vue';

const isProcessing = ref(false);
const showConfirmationModal = ref(false);
const items = computed(() => usePage().props.items);

const form = useForm({
    message: ''
});

const confirmReservation = () => {
    form.post(route('app.organizations.reservations.in.store'), {
        onSuccess: () => {
            showConfirmationModal.value = false;
        }
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(price);
};

const totalPrice = computed(() => {
    return items.value?.reduce((total, item) => total + item.total, 0) ?? 0;
});

const totalDeposit = computed(() => {
    return items.value?.reduce((total, item) => total + item.deposit, 0) ?? 0;
});

const handleAction = async (action) => {
    if (isProcessing.value) return;
    
    isProcessing.value = true;
    try {
        await action();
    } finally {
        isProcessing.value = false;
    }
};

const updateQuantity = (item, quantity) => {
    if (quantity < 1) return;

    handleAction(() => 
        router.put(route('carts.update', item.equipment.id), {
            quantity: quantity,
            rental_start: item.rental_start,
            rental_end: item.rental_end,
        }, {
            preserveScroll: true,
            only: ['items'],
            onError: () => {
                toast('Erreur', {
                    description: "Une erreur s'est produite lors de la mise à jour de la quantité.",
                    variant: 'destructive'
                });
            }
        })
    );
};

const removeItem = (item) => {

    if(!confirm('Voulez-vous vraiment retirer cet article du camion ?')) return;
    
    handleAction(() =>
        router.delete(route('carts.remove', item.equipment.id), {
            data: {
                rental_start: item.rental_start,
                rental_end: item.rental_end,
            }
        }, {
            preserveScroll: true,
            onSuccess: () => {
                toast('Article supprimé', {
                    description: "L'article a été retiré de votre camion."
                });
            },
            onError: () => {
                toast('Erreur', {
                    description: "Une erreur s'est produite lors de la suppression de l'article.",
                    variant: 'destructive'
                });
            }
        })
    );
};

const clearCart = () => {

    if(!confirm('Voulez-vous vraiment vider votre camion ?')) return;
    
    handleAction(() =>
        router.delete(route('carts.clear'), {
            onSuccess: () => {
                toast('Camion vidé', {
                    description: 'Tous les articles ont été retirés de votre camion.'
                });
            },
            onError: () => {
                toast('Erreur', {
                    description: "Une erreur s'est produite lors du vidage du camion.",
                    variant: 'destructive'
                });
            }
        })
    );
};

</script> 