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
                        Parcourir le matériel
                    </Link>
                </Button>
            </div>

            <!-- Cart items -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Items list -->
                <div class="lg:col-span-2 space-y-6">
                    <Card v-for="(item, index) in items" :key="item.equipment.id" class="group">
                        <CardContent class="p-6">
                            <div class="flex gap-6">
                                <!-- Item image -->
                                <div class="relative">
                                    <img
                                        :src="item.equipment.image"
                                        :alt="item.equipment.name"
                                        class="w-32 h-32 object-cover rounded-lg"
                                    />
                                    <Badge 
                                        class="absolute top-2 right-2"
                                        :variant="item.equipment.is_available ? 'success' : 'destructive'"
                                    >
                                        {{ item.equipment.is_available ? 'Disponible' : 'Non disponible' }}
                                    </Badge>
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
                                        {{ formatPrice(calculateItemPrice(item)) }}
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        Caution: {{ formatPrice(item.equipment.deposit * item.quantity) }}
                                    </p>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="mt-4 opacity-0 group-hover:opacity-100 transition-opacity"
                                        @click="removeItem(index)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Order summary -->
                <div class="lg:col-span-1">
                    <Card>
                        <CardHeader>
                            <CardTitle>Récapitulatif de la commande</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span>Sous-total</span>
                                    <span>{{ formatPrice(totalPrice) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Caution totale</span>
                                    <span>{{ formatPrice(totalDeposit) }}</span>
                                </div>
                                <Separator />
                                <div class="flex justify-between font-semibold">
                                    <span>Total</span>
                                    <span>{{ formatPrice(totalPrice + totalDeposit) }}</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Button 
                                    variant="outline" 
                                    @click="clearCart" 
                                    class="w-full"
                                    :disabled="isProcessing"
                                >
                                    Vider le camion
                                </Button>
                                <Button 
                                    asChild 
                                    class="w-full"
                                    :disabled="isProcessing"
                                >
                                    <Link :href="route('checkout.index')">
                                        Passer la commande
                                    </Link>
                                </Button>
                            </div>
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
import { Truck, Minus, Plus, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Badge } from '@/components/ui/badge';
import { toast } from 'vue-sonner';
import PublicLayout from '@/layouts/PublicLayout.vue';

const isProcessing = ref(false);
const items = computed(() => usePage().props.cart);

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

const calculateItemPrice = (item) => {
    const startDate = new Date(item.rental_start);
    const endDate = new Date(item.rental_end);
    const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
    return item.equipment.price * days * item.quantity;
};

const totalPrice = computed(() => {
    return items.value?.reduce((total, item) => total + calculateItemPrice(item), 0) ?? 0;
});

const totalDeposit = computed(() => {
    return items.value?.reduce((total, item) => {
        return total + (item.equipment.deposit * item.quantity);
    }, 0) ?? 0;
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