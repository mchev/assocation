<template>
    <DropdownMenu>
        <DropdownMenuTrigger asChild>
            <Button variant="ghost" size="icon" class="relative">
                <Truck class="h-5 w-5" />
                <Badge v-if="totalItems > 0" class="absolute -top-2 -right-2">
                    {{ totalItems }}
                </Badge>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-80">
            <DropdownMenuLabel>Votre Camion</DropdownMenuLabel>
            <DropdownMenuSeparator />
            
            <div v-if="items.length === 0" class="p-4 text-center text-sm text-muted-foreground">
                Votre camion est vide

                <Button asChild class="w-full mt-4">
                    <Link :href="route('carts.show')">
                        Ouvrir mon camion
                    </Link>
                </Button>
            </div>
            
            <template v-else>
                <div class="max-h-[300px] overflow-y-auto">
                    <div v-for="(item, index) in items" :key="index" class="p-2 hover:bg-accent">
                        <div class="flex items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <Link :href="route('equipments.show', item.equipment.id)" class="text-sm font-medium truncate">
                                    {{ item.equipment.name }}
                                </Link>
                                <p class="text-xs text-muted-foreground">
                                    <span v-if="item.rental_start && item.rental_end">
                                        {{ formatDate(item.rental_start) }} - {{ formatDate(item.rental_end) }}
                                    </span>
                                    <span v-else>Dates non définies</span>
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    Qté : {{ item.quantity }}
                                </p>
                            </div>
                            <Button
                                variant="ghost"
                                size="icon"
                                class="h-8 w-8 text-destructive"
                                @click="removeItem(index)"
                                :aria-label="`Retirer ${item.equipment.name}`"
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </div>
                
                <DropdownMenuSeparator />
                
                <div class="p-2 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span>Sous-total</span>
                        <span>{{ formatPrice(totalPrice) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Caution totale</span>
                        <span>{{ formatPrice(totalDeposit) }}</span>
                    </div>
                </div>
                
                <DropdownMenuSeparator />
                
                <div class="p-2 space-y-2">
                    <Button asChild class="w-full">
                        <Link :href="route('carts.show')">
                            Voir le camion
                        </Link>
                    </Button>
                    <Button asChild class="w-full">
                        <Link :href="route('checkout.index')">
                            Passer la commande
                        </Link>
                    </Button>
                </div>
            </template>
        </DropdownMenuContent>
    </DropdownMenu>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { Truck, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { toast } from 'vue-sonner';

const items = computed(() => usePage().props.cart);

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
};

const formatPrice = (price) => {
    return `${Number(price || 0).toFixed(2)} €`;
};

const calculateItemPrice = (item) => {
    const startDate = new Date(item.rental_start);
    const endDate = new Date(item.rental_end);
    const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
    return item.equipment.price * days * item.quantity;
};

const totalItems = computed(() => {
    return items.value?.reduce((total, item) => total + item.quantity, 0) ?? 0;
});

const totalPrice = computed(() => {
    return items.value?.reduce((total, item) => total + calculateItemPrice(item), 0) ?? 0;
});

const totalDeposit = computed(() => {
    return items.value?.reduce((total, item) => {
        return total + (item.equipment.deposit * item.quantity);
    }, 0) ?? 0;
});

const removeItem = (index) => {
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
    });
};
</script> 