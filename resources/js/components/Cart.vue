<template>
    <Sheet v-model:open="isOpen">
        <SheetTrigger asChild>
            <Button variant="outline" size="icon" class="fixed bottom-4 right-4 z-50">
                <ShoppingCart class="h-4 w-4" />
                <Badge v-if="totalItems > 0" class="absolute -top-2 -right-2">
                    {{ totalItems }}
                </Badge>
            </Button>
        </SheetTrigger>
        <SheetContent class="w-full sm:max-w-lg">
            <SheetHeader>
                <SheetTitle>Reservation Cart</SheetTitle>
                <SheetDescription>
                    Review your equipment reservations
                </SheetDescription>
            </SheetHeader>

            <div v-if="items.length === 0" class="flex flex-col items-center justify-center h-[calc(100vh-8rem)]">
                <ShoppingCart class="h-12 w-12 text-muted-foreground mb-4" />
                <p class="text-lg text-muted-foreground mb-4">Your cart is empty</p>
                <Button asChild>
                    <Link :href="route('equipments.index')">
                        Browse Equipment
                    </Link>
                </Button>
            </div>

            <div v-else class="space-y-6">
                <div v-for="(item, index) in items" :key="index" class="flex gap-4">
                    <img
                        :src="item.equipment.main_image"
                        :alt="item.equipment.name"
                        class="w-20 h-20 object-cover rounded-lg"
                    />
                    <div class="flex-1">
                        <h3 class="font-medium">{{ item.equipment.name }}</h3>
                        <p class="text-sm text-muted-foreground">
                            {{ formatDate(item.start_date) }} - {{ formatDate(item.end_date) }}
                        </p>
                        <div class="flex items-center gap-4 mt-2">
                            <div class="flex items-center gap-2">
                                <Button
                                    variant="outline"
                                    size="icon"
                                    @click="updateQuantity(index, item.quantity - 1)"
                                    :disabled="item.quantity <= 1"
                                >
                                    <Minus class="h-4 w-4" />
                                </Button>
                                <span class="w-8 text-center">{{ item.quantity }}</span>
                                <Button
                                    variant="outline"
                                    size="icon"
                                    @click="updateQuantity(index, item.quantity + 1)"
                                >
                                    <Plus class="h-4 w-4" />
                                </Button>
                            </div>
                            <Button
                                variant="ghost"
                                size="icon"
                                @click="removeItem(index)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-medium">
                            {{ formatPrice(calculateItemPrice(item)) }}
                        </p>
                        <p class="text-sm text-muted-foreground">
                            Deposit: {{ formatPrice(item.equipment.deposit_amount * item.quantity) }}
                        </p>
                    </div>
                </div>

                <Separator />

                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>{{ formatPrice(totalPrice) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Deposit</span>
                        <span>{{ formatPrice(totalDeposit) }}</span>
                    </div>
                    <Separator />
                    <div class="flex justify-between font-medium">
                        <span>Total</span>
                        <span>{{ formatPrice(totalPrice + totalDeposit) }}</span>
                    </div>
                </div>

                <div class="flex gap-4">
                    <Button variant="outline" @click="clearCart" class="flex-1">
                        Clear Cart
                    </Button>
                    <Button asChild class="flex-1">
                        <Link :href="route('checkout.index')">
                            Checkout
                        </Link>
                    </Button>
                </div>
            </div>
        </SheetContent>
    </Sheet>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ShoppingCart, Minus, Plus, Trash2 } from 'lucide-vue-next';
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { toast } from 'vue-sonner';

const items = computed(() => usePage().props.cart);

const isOpen = ref(false);

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
    const startDate = new Date(item.start_date);
    const endDate = new Date(item.end_date);
    const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
    return item.equipment.rental_price * days * item.quantity;
};

const totalItems = computed(() => {
    return items.value?.reduce((total, item) => total + item.quantity, 0) ?? 0;
});

const totalPrice = computed(() => {
    return items.value?.reduce((total, item) => total + calculateItemPrice(item), 0) ?? 0;
});

const totalDeposit = computed(() => {
    return items.value?.reduce((total, item) => {
        return total + (item.equipment.deposit_amount * item.quantity);
    }, 0) ?? 0;
});

const updateQuantity = (index, quantity) => {
    if (quantity < 1) return;

    router.put(route('cart.update', index), { quantity }, {
        onSuccess: () => {
            toast('Cart updated', {
                description: 'The quantity has been updated successfully.'
            });
        },
        onError: () => {
            toast('Failed to update cart', {
                description: 'There was an error updating the quantity.',
                variant: 'destructive'
            });
        }
    });
};

const removeItem = (index) => {
    router.delete(route('cart.remove', index), {
        onSuccess: () => {
            toast('Item removed', {
                description: 'The item has been removed from your cart.'
            });
        },
        onError: () => {
            toast('Failed to remove item', {
                description: 'There was an error removing the item.',
                variant: 'destructive'
            });
        }
    });
};

const clearCart = () => {
    router.delete(route('cart.clear'), {
        onSuccess: () => {
            toast('Cart cleared', {
                description: 'All items have been removed from your cart.'
            });
            isOpen.value = false;
        },
        onError: () => {
            toast('Failed to clear cart', {
                description: 'There was an error clearing your cart.',
                variant: 'destructive'
            });
        }
    });
};
</script>