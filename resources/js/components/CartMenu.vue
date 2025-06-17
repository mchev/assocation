<template>
    <DropdownMenu>
        <DropdownMenuTrigger asChild>
            <Button variant="ghost" size="icon" class="relative hover:bg-primary/10 transition-colors">
                <Truck class="size-5" />
                <Badge v-if="totalItems > 0" variant="primary" class="absolute -top-2 -right-2 min-w-[20px] h-5 animate-in fade-in slide-in-from-top-2">
                    {{ totalItems }}
                </Badge>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-[350px] animate-in fade-in slide-in-from-top-2">
            <DropdownMenuLabel class="flex items-center gap-2">
                <Truck class="h-4 w-4" />
                <span>Votre Camion</span>
                <Badge v-if="totalItems > 0" variant="secondary" class="ml-auto">
                    {{ totalItems }} article{{ totalItems > 1 ? 's' : '' }}
                </Badge>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            
            <div v-if="items.length === 0" class="p-6 flex flex-col items-center justify-center text-center space-y-4">
                <div class="relative">
                    <PackageIcon class="h-16 w-16 text-muted-foreground/30 animate-bounce" />
                    <Truck class="h-8 w-8 text-primary absolute -bottom-2 -right-2" />
                </div>
                <div class="space-y-2">
                    <p class="font-medium text-lg">Votre camion est vide</p>
                    <p class="text-sm text-muted-foreground/70">Ajoutez des équipements à votre camion pour commencer.</p>
                </div>

                <Button asChild variant="outline" class="w-full hover:bg-primary/10 transition-colors">
                    <Link :href="route('carts.show')">
                        Voir le camion
                    </Link>
                </Button>
            </div>
            
            <template v-else>
                <div class="max-h-[300px] overflow-y-auto py-1">
                    <TransitionGroup 
                        name="list" 
                        tag="div"
                        class="divide-y divide-border"
                    >
                        <div 
                            v-for="(item, index) in items" 
                            :key="index" 
                            class="group px-3 py-3 hover:bg-muted/50 transition-colors"
                        >
                            <div class="flex items-center gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <Link 
                                            :href="route('equipments.show', item.equipment.id)" 
                                            class="text-sm font-medium hover:text-primary-600 truncate block transition-colors"
                                        >
                                            {{ item.equipment.name }}
                                        </Link>
                                        <TooltipProvider>
                                            <Tooltip>
                                                <TooltipTrigger asChild>
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        class="h-7 w-7 opacity-0 group-hover:opacity-100 transition-opacity -mr-1"
                                                        @click="removeItem(item)"
                                                        :disabled="isRemoving"
                                                        :aria-label="`Retirer ${item.equipment.name}`"
                                                    >
                                                        <Trash class="h-4 w-4 text-destructive" />
                                                    </Button>
                                                </TooltipTrigger>
                                                <TooltipContent>
                                                    <p>Retirer du camion</p>
                                                </TooltipContent>
                                            </Tooltip>
                                        </TooltipProvider>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <CalendarIcon class="h-3 w-3 text-muted-foreground/70" />
                                        <p class="text-xs text-muted-foreground">
                                            <span v-if="item.rental_start && item.rental_end">
                                                {{ formatDate(item.rental_start) }} - {{ formatDate(item.rental_end) }} ({{ item.days }} jours)
                                            </span>
                                            <span v-else class="text-yellow-600 font-medium">Dates non définies</span>
                                        </p>
                                    </div>
                                    <div class="flex items-center justify-between mt-1.5">
                                        <Badge variant="secondary" class="text-xs">
                                            Qté : {{ item.quantity }}
                                        </Badge>
                                        <div class="text-xs font-medium">
                                            <p class="text-muted-foreground/70">
                                                {{ formatPrice(item.equipment.rental_price) }} × {{ item.quantity }} × {{ item.days }}j
                                            </p>
                                            <p class="text-right font-semibold">
                                                {{ formatPrice(item.total_price) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TransitionGroup>
                </div>
                
                <div class="border-t">
                    <div class="px-3 py-2 space-y-1.5 bg-muted/50">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-muted-foreground">Sous-total</span>
                            <span class="font-medium">{{ formatPrice(totalPrice) }}</span>
                        </div>
                    </div>
                    
                    <div class="p-3 pt-2 space-y-2">
                        <div class="flex gap-2">
                            <Button asChild class="flex-1 h-9">
                                <Link :href="route('carts.show')">
                                    Ouvrir le camion
                                </Link>
                            </Button>
                            <Button 
                                variant="outline" 
                                class="h-9"
                                @click="clearCart"
                                :disabled="isClearing"
                            >
                                <Loader2 v-if="isClearing" class="h-4 w-4 animate-spin" />
                                <Trash v-else class="h-4 w-4 text-destructive" />
                            </Button>
                        </div>
                    </div>
                </div>
            </template>
        </DropdownMenuContent>
    </DropdownMenu>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { Truck, Trash, CalendarIcon, PackageIcon, Loader2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { toast } from 'vue-sonner';

const items = computed(() => usePage().props.cart);
const isRemoving = ref(false);
const isClearing = ref(false);

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

const totalItems = computed(() => {
    return items.value?.reduce((total, item) => total + item.quantity, 0) ?? 0;
});

const totalPrice = computed(() => {
    return items.value?.reduce((total, item) => total + item.total_price, 0) ?? 0;
});

const removeItem = (item) => {
    if(!confirm('Êtes-vous sûr de vouloir retirer cet article du camion ?')) return;
    
    isRemoving.value = true;
    router.delete(route('carts.remove', item.equipment.id), {
        data: {
            rental_start: item.rental_start,
            rental_end: item.rental_end,
        },
        preserveScroll: true,
        only: ['cart'],
        onSuccess: () => {
            toast.success('Article retiré du camion', {
                description: "L'article a été retiré de votre camion avec succès."
            });
        },
        onError: () => {
            toast.error('Erreur de suppression', {
                description: "Une erreur s'est produite lors de la suppression de l'article."
            });
        },
        onFinish: () => {
            isRemoving.value = false;
        }
    });
};

const clearCart = () => {
    isClearing.value = true;
    router.delete(route('carts.clear'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Camion vidé', {
                description: "Votre camion a été vidé avec succès."
            });
        },
        onError: () => {
            toast.error('Erreur', {
                description: "Une erreur s'est produite lors du vidage du camion."
            });
        },
        onFinish: () => {
            isClearing.value = false;
        }
    });
};
</script>

<style scoped>
.list-enter-active,
.list-leave-active {
    transition: all 0.3s ease;
}
.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}
</style> 