<template>
    <PublicLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-3xl font-bold mb-8">Checkout</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Contact Information -->
                    <div class="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Contact Information</CardTitle>
                                <CardDescription>Please provide your contact details for the reservation.</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <form @submit.prevent="handleSubmit" class="space-y-4">
                                    <div class="space-y-2">
                                        <Label for="contact_name">Full Name</Label>
                                        <Input
                                            id="contact_name"
                                            v-model="form.contact_name"
                                            :class="{ 'border-red-500': errors.contact_name }"
                                            required
                                        />
                                        <p v-if="errors.contact_name" class="text-sm text-red-500">
                                            {{ errors.contact_name }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="contact_email">Email</Label>
                                        <Input
                                            id="contact_email"
                                            type="email"
                                            v-model="form.contact_email"
                                            :class="{ 'border-red-500': errors.contact_email }"
                                            required
                                        />
                                        <p v-if="errors.contact_email" class="text-sm text-red-500">
                                            {{ errors.contact_email }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="contact_phone">Phone Number</Label>
                                        <Input
                                            id="contact_phone"
                                            type="tel"
                                            v-model="form.contact_phone"
                                            :class="{ 'border-red-500': errors.contact_phone }"
                                            required
                                        />
                                        <p v-if="errors.contact_phone" class="text-sm text-red-500">
                                            {{ errors.contact_phone }}
                                        </p>
                                    </div>

                                    <Button type="submit" class="w-full" :disabled="isSubmitting">
                                        <Loader2 v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
                                        {{ isSubmitting ? 'Processing...' : 'Complete Reservation' }}
                                    </Button>
                                </form>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Order Summary -->
                    <div class="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Order Summary</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="space-y-4">
                                    <div v-for="item in cartStore.items" :key="item.id" class="flex gap-4">
                                        <img
                                            :src="item.main_image"
                                            :alt="item.name"
                                            class="w-20 h-20 object-cover rounded-lg"
                                        />
                                        <div class="flex-1">
                                            <h3 class="font-medium">{{ item.name }}</h3>
                                            <p class="text-sm text-muted-foreground">
                                                {{ formatDate(item.start_date) }} - {{ formatDate(item.end_date) }}
                                            </p>
                                            <p class="text-sm text-muted-foreground">
                                                Quantity: {{ item.quantity }}
                                            </p>
                                            <p class="text-sm font-medium">
                                                {{ formatPrice(calculateItemPrice(item)) }}
                                            </p>
                                        </div>
                                    </div>

                                    <Separator />

                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span>Subtotal</span>
                                            <span>{{ formatPrice(cartStore.totalPrice) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Total Deposit</span>
                                            <span>{{ formatPrice(cartStore.totalDeposit) }}</span>
                                        </div>
                                        <div class="flex justify-between font-medium">
                                            <span>Total</span>
                                            <span>{{ formatPrice(cartStore.totalPrice + cartStore.totalDeposit) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useCartStore } from '@/stores/cart';
import { toast } from 'sonner';
import { Loader2 } from 'lucide-vue-next';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';

const cartStore = useCartStore();

const form = useForm({
    contact_name: '',
    contact_email: '',
    contact_phone: '',
    items: cartStore.items.map(item => ({
        equipment_id: item.id,
        start_date: item.start_date,
        end_date: item.end_date,
        quantity: item.quantity,
        notes: item.notes
    }))
});

const isSubmitting = ref(false);
const errors = ref({});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(price);
};

const calculateItemPrice = (item) => {
    const startDate = new Date(item.start_date);
    const endDate = new Date(item.end_date);
    const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
    return item.rental_price * days * item.quantity;
};

const handleSubmit = async () => {
    isSubmitting.value = true;
    errors.value = {};

    try {
        await form.post(route('checkout.store'), {
            onSuccess: () => {
                cartStore.clearCart();
                toast.success('Reservation completed successfully!');
            },
            onError: (err) => {
                errors.value = err;
                toast.error('There was an error processing your reservation.');
            }
        });
    } catch (error) {
        toast.error('An unexpected error occurred.');
    } finally {
        isSubmitting.value = false;
    }
};
</script> 