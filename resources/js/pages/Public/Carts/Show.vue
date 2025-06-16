<template>
    <PublicLayout>
        <div class="container py-6">
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
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <img 
                                        v-if="group.organization.logo" 
                                        :src="group.organization.logo" 
                                        :alt="group.organization.name"
                                        class="w-8 h-8 rounded-full object-cover"
                                    />
                                    <span>{{ group.organization.name }}</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <Card v-for="(item, index) in group.items" :key="item.equipment.id" class="group">
                                    <CardContent class="p-6">
                                        <div class="flex gap-6">
                                            <!-- Item image -->
                                            <div class="relative">
                                                <img
                                                    v-if="item.image"
                                                    :src="item.image"
                                                    :alt="item.equipment.name"
                                                    class="w-32 h-32 object-cover rounded-lg"
                                                />
                                                <div v-else class="w-32 h-32 bg-muted rounded-lg flex items-center justify-center">
                                                    <Camera class="h-10 w-10 text-muted-foreground" />
                                                </div>
                                            </div>

                                            <!-- Item details -->
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold mb-2">{{ item.equipment.name }}</h3>
                                                <div class="space-y-2 text-sm text-muted-foreground">
                                                    <p>
                                                        <span class="font-medium">Période de location:</span><br>
                                                        {{ formatDate(item.rental_start) }} - {{ formatDate(item.rental_end) }}
                                                    </p>
                                                    <div class="space-y-1">
                                                        <span class="font-medium">Quantité:</span>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <Button
                                                                variant="outline"
                                                                size="icon"
                                                                @click="updateQuantity(index, item.quantity - 1)"
                                                                :disabled="item.quantity <= 1"
                                                                class="transition-opacity"
                                                            >
                                                                <Minus class="h-4 w-4" />
                                                            </Button>
                                                            <span class="w-8 text-center">{{ item.quantity }}</span>
                                                            <Button
                                                                variant="outline"
                                                                size="icon"
                                                                @click="updateQuantity(index, item.quantity + 1)"
                                                                class="transition-opacity"
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
                                            <div class="text-right">
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
                                                    size="icon"
                                                    class="mt-4 opacity-0 group-hover:opacity-100 transition-opacity"
                                                    @click="removeItem(index)"
                                                >
                                                    <Trash class="h-4 w-4" />
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

                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Info class="h-5 w-5 text-primary" />
                                Informations importantes
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-destructive/10 flex items-center justify-center text-destructive">
                                        <AlertTriangle class="h-4 w-4" />
                                    </div>
                                    <p class="text-sm text-orange-500">Les demandes de réservation en attente depuis plus d'une semaine seront automatiquement annulées.</p>
                                </div>
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                        <User class="h-4 w-4" />
                                    </div>
                                    <p class="text-sm">Vous pouvez consulter vos réservations en cours dans votre espace personnel.</p>
                                </div>
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                        <CreditCard class="h-4 w-4" />
                                    </div>
                                    <p class="text-sm">Les paiements et arrangements financiers se font directement avec les propriétaires des équipements, en dehors de la plateforme.</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Order summary -->
                <div class="lg:col-span-1">
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
                                                <p>Vous notifiez les propriétaires des équipements sélectionnés en cliquant sur le bouton "Confirmer ma demande".</p>
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
                                    asChild 
                                    class="w-full"
                                    :disabled="isProcessing"
                                >
                                    <Link :href="route('checkout.index')">
                                        Confirmer ma demande
                                    </Link>
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
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { Truck, Minus, Plus, Trash, AlertTriangle, User, CreditCard, Camera } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { toast } from 'vue-sonner';
import PublicLayout from '@/layouts/PublicLayout.vue';

const isProcessing = ref(false);
const items = computed(() => usePage().props.items);

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

const updateQuantity = (index, quantity) => {
    if (quantity < 1) return;

    handleAction(() => 
        router.put(route('cart.update', index), { quantity }, {
            onSuccess: () => {
                toast('Camion mis à jour', {
                    description: 'La quantité a été mise à jour avec succès.'
                });
            },
            onError: () => {
                toast('Erreur', {
                    description: "Une erreur s'est produite lors de la mise à jour de la quantité.",
                    variant: 'destructive'
                });
            }
        })
    );
};

const removeItem = (index) => {
    handleAction(() =>
        router.delete(route('cart.remove', index), {
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
    handleAction(() =>
        router.delete(route('cart.clear'), {
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