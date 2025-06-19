<template>
    <AppLayout :title="equipment.name" :description="equipment.description" :image="equipment.images[0].url">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Equipment Header -->
                    <Card>
                        <CardHeader>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <Badge variant="primary">
                                        <User class="w-4 h-4" />
                                        {{ equipment.organization?.name }}
                                    </Badge>
                                    <Badge variant="outline">{{ equipment.category?.name }}</Badge>
                                </div>
                                <CardTitle class="text-3xl">{{ equipment.name }}</CardTitle>
                                <CardDescription class="text-lg">{{ equipment.description }}</CardDescription>
                            </div>
                        </CardHeader>
                    </Card>

                    <ImageGallery v-if="equipment.images && equipment.images.length > 0" :images="equipment.images" />

                    <!-- Equipment Details -->
                    <Card v-if="equipment.specifications || equipment.last_maintenance_date || equipment.next_maintenance_date">
                        <CardContent class="space-y-6">

                            <!-- Specifications -->
                            <div v-if="equipment.specifications">
                                <h3 class="text-lg font-semibold mb-2">Spécifications</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div v-for="(value, key) in equipment.specifications" :key="key">
                                        <h4 class="text-sm font-medium text-muted-foreground">{{ formatSpecificationKey(key) }}</h4>
                                        <p class="mt-1">{{ value }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Maintenance Info -->
                            <div v-if="equipment.last_maintenance_date || equipment.next_maintenance_date">
                                <h3 class="text-lg font-semibold mb-2">Informations de maintenance</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div v-if="equipment.last_maintenance_date">
                                        <h4 class="text-sm font-medium text-muted-foreground">Dernière maintenance</h4>
                                        <p class="mt-1">{{ formatDate(equipment.last_maintenance_date) }}</p>
                                    </div>
                                    <div v-if="equipment.next_maintenance_date">
                                        <h4 class="text-sm font-medium text-muted-foreground">Prochaine maintenance</h4>
                                        <p class="mt-1">{{ formatDate(equipment.next_maintenance_date) }}</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                    <EquipmentMiniMap :city="equipment.depot?.city" />
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Pricing Card -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Tarification</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-muted-foreground">Prix de location</span>
                                <span class="text-2xl font-bold">{{ formatPrice(equipment.rental_price) }} € / jour</span>
                            </div>
                            <div v-if="equipment.requires_deposit" class="flex items-center justify-between">
                                <span class="text-muted-foreground">Caution requise</span>
                                <span class="font-medium">{{ formatPrice(equipment.deposit_amount) }} €</span>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Reservation Card -->
                    <div class="lg:col-span-1">
                        <ReservationForm :equipment="equipment" />
                    </div>

                    <!-- Availability Calendar Card -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Disponibilités</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <EquipmentCalendar :equipment="equipment" />
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import ImageGallery from '@/components/ImageGallery.vue';
import ReservationForm from '@/components/ReservationForm.vue';
import EquipmentMiniMap from '@/components/EquipmentMiniMap.vue';
import EquipmentCalendar from '@/components/EquipmentCalendar.vue';
import { User } from 'lucide-vue-next';

defineProps({
    equipment: {
        type: Object,
        required: true
    }
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatSpecificationKey = (key) => {
    return key.split('_').map(word => 
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ');
};


</script>