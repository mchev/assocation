<template>
    <PublicLayout :title="equipment.name" :description="equipment.description">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Equipment Header -->
                    <Card>
                        <CardHeader>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <Badge variant="outline">{{ equipment.category?.name }}</Badge>
                                    <Badge :variant="getConditionVariant(equipment.condition)">
                                        {{ equipment.condition }}
                                    </Badge>
                                </div>
                                <CardTitle class="text-3xl">{{ equipment.name }}</CardTitle>
                                <CardDescription class="text-lg">{{ equipment.description }}</CardDescription>
                            </div>
                        </CardHeader>
                    </Card>

                    <!-- Image Gallery -->
                    <Card>
                        <CardContent class="p-0">
                            <div class="relative aspect-[16/9] w-full bg-muted">
                                <ImageGallery v-if="equipment.images && equipment.images.length > 0" :images="equipment.images" />
                                <template v-else>
                                    <div class="w-full h-full flex items-center justify-center rounded-t-lg">
                                        <div class="text-center space-y-2">
                                            <Truck class="w-12 h-12 mx-auto text-muted-foreground" />
                                            <p class="text-muted-foreground font-medium">{{ equipment.name }}</p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Equipment Details -->
                    <Card>
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
                                <span class="text-2xl font-bold">{{ formatPrice(equipment.rental_price) }} €</span>
                            </div>
                            <div v-if="equipment.requires_deposit" class="flex items-center justify-between">
                                <span class="text-muted-foreground">Caution requise</span>
                                <span class="font-medium">{{ formatPrice(equipment.deposit_amount) }} €</span>
                            </div>
                            <Separator />
                            <div class="flex items-center justify-between">
                                <span class="text-muted-foreground">Disponibilité</span>
                                <Badge :variant="equipment.is_available ? 'success' : 'destructive'">
                                    {{ equipment.is_available ? 'Disponible' : 'Indisponible' }}
                                </Badge>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Reservation Card -->
                    <div class="lg:col-span-1">
                        <ReservationForm :equipment="equipment" />
                    </div>

                    <!-- Location Card -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Localisation</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <p class="font-medium">{{ equipment.depot?.name }}</p>
                                <p class="text-muted-foreground">
                                    {{ equipment.depot?.city }}
                                </p>
                            </div>
                            <Separator />
                            <div class="space-y-2">
                                <h4 class="font-medium">Informations de contact</h4>
                                <p class="text-muted-foreground">{{ equipment.depot?.phone }}</p>
                                <p class="text-muted-foreground">{{ equipment.depot?.email }}</p>
                            </div>
                            <Separator />
                            <div class="space-y-4">
                                <h4 class="font-medium">Disponibilités</h4>
                                <div v-if="loading" class="flex justify-center py-4">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                                </div>
                                <CalendarComponent
                                    v-else
                                    :disabled="true"
                                    :modifiers="{ today: false }"
                                    :month="currentDate"
                                    @update:month="handleMonthChange"
                                    class="rounded-md border"
                                />
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <div class="flex items-center gap-1">
                                        <div class="size-2 rounded-full bg-primary"></div>
                                        <span>Disponible</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <div class="size-2 rounded-full bg-destructive"></div>
                                        <span>Indisponible</span>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/layouts/PublicLayout.vue';
import { ref, computed, onMounted } from 'vue';
import { toast } from 'vue-sonner';
import { ShoppingCart, Truck, ArrowLeftIcon } from 'lucide-vue-next';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Separator } from '@/components/ui/separator';
import { useForm, router } from '@inertiajs/vue3';
import Cart from '@/components/Cart.vue';
import ImageGallery from '@/components/ImageGallery.vue';
import ReservationForm from '@/Components/ReservationForm.vue';
import { MapPin, Phone, Mail, Globe, Calendar, Clock, Users, Tag } from 'lucide-vue-next';
import { Calendar as CalendarComponent } from '@/components/ui/calendar';
import { addDays, format, isSameDay, isWithinInterval, startOfMonth, endOfMonth, eachDayOfInterval } from 'date-fns';
import { fr } from 'date-fns/locale';
import axios from 'axios';

const props = defineProps({
    equipment: {
        type: Object,
        required: true
    }
});

const currentDate = ref(new Date());
const availabilities = ref({});
const loading = ref(false);

const getConditionVariant = (condition) => {
    const variants = {
        'new': 'success',
        'good': 'default',
        'fair': 'warning',
        'poor': 'destructive'
    };
    return variants[condition] || 'default';
};

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

const formatDay = (day) => {
    const days = {
        'monday': 'Lundi',
        'tuesday': 'Mardi',
        'wednesday': 'Mercredi',
        'thursday': 'Jeudi',
        'friday': 'Vendredi',
        'saturday': 'Samedi',
        'sunday': 'Dimanche'
    };
    return days[day] || day;
};

const formatSpecificationKey = (key) => {
    return key.split('_').map(word => 
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ');
};

const isDateUnavailable = (date) => {
    const dateString = format(date, 'yyyy-MM-dd');
    return availabilities.value[dateString] === false;
};

const isDateToday = (date) => {
    return isSameDay(date, new Date());
};

const fetchAvailabilities = async (date) => {
    loading.value = true;
    try {
        const response = await axios.get(`/api/equipments/${props.equipment.id}/availability`, {
            params: {
                month: date.getMonth() + 1,
                year: date.getFullYear()
            }
        });
        availabilities.value = response.data.availabilities;
    } catch (error) {
        console.error('Erreur lors du chargement des disponibilités:', error);
    } finally {
        loading.value = false;
    }
};

const handleMonthChange = (date) => {
    currentDate.value = date;
    fetchAvailabilities(date);
};

onMounted(() => {
    fetchAvailabilities(currentDate.value);
});
</script>