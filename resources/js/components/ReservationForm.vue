<template>
    <Card>
        <CardHeader>
            <CardTitle>Faire une demande de réservation</CardTitle>
        </CardHeader>
        <CardContent>
            <form id="reservation-form" @submit.prevent="handleReservation">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <Label>Période de location</Label>
                        <DateRangePicker
                            v-model="dateRange"
                            start-day="1"
                            :min-value="today(getLocalTimeZone()).add({ days: 2 })"
                            locale="fr-FR"
                            :disabled="false"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="quantity">Quantité</Label>
                        <Input 
                            id="quantity" 
                            type="number" 
                            v-model="form.quantity"
                            :min="0"
                            :max="availableQuantity"
                            :disabled="disableQuantityField"
                        />
                        <p v-if="availableQuantity > 0" class="text-xs text-gray-500">
                            {{ availableQuantity }} disponible{{ availableQuantity > 1 ? 's' : '' }} pour la période sélectionnée.
                        </p>
                        <p v-if="availableQuantity === 0 && dateRange.from && dateRange.to" class="text-xs text-red-500">
                            Aucune disponibilité pour la période sélectionnée.
                        </p>
                    </div>
                </div>
            </form>
        </CardContent>
        <CardFooter>
            <Button 
                type="submit"
                form="reservation-form"
                :disabled="!form.quantity || !form.rental_start || !form.rental_end || form.quantity <= 0"
                @click="handleReservation"
            >
                <Truck class="mr-2 h-4 w-4" />
                Charger dans mon camion
            </Button>
        </CardFooter>
    </Card>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Truck } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent, CardFooter } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import DateRangePicker from '@/components/DateRangePicker.vue';
import { CalendarDate, getLocalTimeZone, today } from '@internationalized/date';

const props = defineProps({
    equipment: {
        type: Object,
        required: true
    }
});

const disableQuantityField = ref(true);
const availableQuantity = ref(0);

const dateRange = ref({
    from: null,
    to: null
});

const form = useForm({
    rental_start: null,
    rental_end: null,
    quantity: 1,
});

watch(dateRange, (newValue) => {

    disableQuantityField.value = true;

    if (!newValue.from || !newValue.to) return;

    let start = newValue.from instanceof CalendarDate ? newValue.from.toDate(getLocalTimeZone()) : newValue.from;
    let end = newValue.to instanceof CalendarDate ? newValue.to.toDate(getLocalTimeZone()) : newValue.to;

    axios.get(route('api.equipments.available-quantity', {
        equipment: props.equipment.id,
        start: start,
        end: end
    })).then(response => {
        availableQuantity.value = response.data;
        if (availableQuantity.value > 0) {
            disableQuantityField.value = false;
            form.quantity = 1;
        }
        if (availableQuantity.value === 0) {
            form.quantity = 0;
        }
    });

    form.rental_start = start;
    form.rental_end = end;

}, { deep: true });

const handleReservation = () => {
    form.post(route('carts.add', props.equipment.id), {
        onSuccess: () => {
            toast.success('Article ajouté au camion');
            form.reset();
            dateRange.value = { from: null, to: null };
        },
        onError: (errors) => {
            toast.error('Erreur lors de l\'ajout au camion', {
                description: Object.values(errors).join(', ')
            });
        }
    });
};
</script> 