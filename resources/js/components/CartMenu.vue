<template>
    <DropdownMenu>
        <DropdownMenuTrigger asChild>
            <Button variant="ghost" size="icon" class="relative">
                <Truck class="h-5 w-5" />
                <Badge v-if="totalItems > 0" variant="primary" class="absolute -top-2 -right-2 min-w-[20px] h-5">
                    {{ totalItems }}
                </Badge>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-[350px]">
            <DropdownMenuLabel class="flex items-center gap-2">
                <Truck class="h-4 w-4" />
                <span>Votre Camion</span>
                <Badge v-if="totalItems > 0" variant="secondary" class="ml-auto">
                    {{ totalItems }} article{{ totalItems > 1 ? 's' : '' }}
                </Badge>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            
            <div v-if="items.length === 0" class="p-6 flex flex-col items-center justify-center text-center space-y-4">
                <PackageIcon class="h-12 w-12 text-muted-foreground/30" />
                <div>
                    <p class="font-medium text-muted-foreground">Votre camion est vide</p>
                    <p class="text-sm text-muted-foreground/70">Ajoutez des équipements à votre camion pour commencer.</p>
                </div>

                <Button asChild variant="outline" class="w-full">
                    <Link :href="route('carts.show')">
                        Voir le camion
                    </Link>
                </Button>
            </div>
            
            <template v-else>
                <div class="max-h-[300px] overflow-y-auto py-1">
                    <div 
                        v-for="(item, index) in items" 
                        :key="index" 
                        class="group px-2 py-2 hover:bg-accent transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <Link 
                                    :href="route('equipments.show', item.equipment.id)" 
                                    class="text-sm font-medium hover:text-primary-600 truncate block"
                                >
                                    {{ item.equipment.name }}
                                </Link>
                                <div class="flex items-center gap-2 mt-1">
                                    <CalendarIcon class="h-3 w-3 text-muted-foreground/70" />
                                    <p class="text-xs text-muted-foreground">
                                        <span v-if="item.rental_start && item.rental_end">
                                            {{ formatDate(item.rental_start) }} - {{ formatDate(item.rental_end) }}
                                        </span>
                                        <span v-else class="text-yellow-600">Dates non définies</span>
                                    </p>
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <p class="text-xs text-muted-foreground">
                                        Qté : {{ item.quantity }}
                                    </p>
                                    <p class="text-xs font-medium">
                                        {{ formatPrice(calculateItemPrice(item)) }}
                                    </p>
                                </div>
                            </div>
                            <Button
                                variant="ghost"
                                size="icon"
                                class="h-8 w-8 text-muted-foreground/50 opacity-0 group-hover:opacity-100 transition-opacity"
                                @click="removeItem(index)"
                                :aria-label="`Retirer ${item.equipment.name}`"
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </div>
                
                <DropdownMenuSeparator />
                
                <div class="p-3 space-y-2 bg-accent/50">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-muted-foreground">Sous-total</span>
                        <span class="font-medium">{{ formatPrice(totalPrice) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-muted-foreground">Caution totale</span>
                        <span class="font-medium">{{ formatPrice(totalDeposit) }}</span>
                    </div>
                </div>
                
                <div class="p-3 space-y-2 bg-background">
                    <Button asChild variant="outline" class="w-full">
                        <Link :href="route('carts.show')">
                            Voir le détail
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
import { Truck, Trash2, CalendarIcon, PackageIcon } from 'lucide-vue-next';
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
    return new Date(date).toLocaleDateString('fr-FR', { 
        day: '2-digit', 
        month: 'short', 
        year: 'numeric' 
    });
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-FR', { 
        style: 'currency', 
        currency: 'EUR',
        minimumFractionDigits: 2
    }).format(price || 0);
};

const calculateItemPrice = (item) => {
    if (!item.rental_start || !item.rental_end) return 0;
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
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Article retiré du camion', {
                description: "L'article a été retiré de votre camion avec succès."
            });
        },
        onError: () => {
            toast.error('Erreur de suppression', {
                description: "Une erreur s'est produite lors de la suppression de l'article."
            });
        }
    });
};
</script> 