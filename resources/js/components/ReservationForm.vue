<template>
    <Card>
        <CardHeader>
            <CardTitle>Faire une réservation</CardTitle>
        </CardHeader>
        <CardContent>
            <form id="reservation-form" @submit.prevent="handleReservation">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <Label>Période de location</Label>
                        <DateRangePicker
                            v-model="dateRange"
                            :disabled="false"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="quantity">Quantité</Label>
                        <Input 
                            id="quantity" 
                            type="number" 
                            v-model="form.quantity"
                            :min="1"
                            :disabled="false"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="notes">Notes</Label>
                        <Textarea 
                            id="notes" 
                            v-model="form.notes"
                            placeholder="Ajoutez des besoins spécifiques ou des questions..."
                            :disabled="false"
                        />
                    </div>
                </div>
            </form>
        </CardContent>
        <CardFooter>
            <Button 
                type="submit"
                form="reservation-form"
                :disabled="!isReservationValid"
                @click="handleReservation"
            >
                <Truck class="mr-2 h-4 w-4" />
                Charger dans mon camion
            </Button>
        </CardFooter>
    </Card>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Truck } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent, CardFooter } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import DateRangePicker from '@/components/DateRangePicker.vue';
import { CalendarDate, getLocalTimeZone } from '@internationalized/date';

const props = defineProps({
    equipment: {
        type: Object,
        required: true
    }
});

const dateRange = ref({
    from: null,
    to: null
});

const form = useForm({
    rental_start: null,
    rental_end: null,
    quantity: 1,
    notes: ''
});

// Update form values when dateRange changes
const updateFormDates = (newValue) => {
    form.rental_start = newValue.from instanceof CalendarDate 
        ? newValue.from.toDate(getLocalTimeZone())
        : newValue.from;
    form.rental_end = newValue.to instanceof CalendarDate 
        ? newValue.to.toDate(getLocalTimeZone())
        : newValue.to;
};

const isReservationValid = computed(() => {
    if (!dateRange.value.from || !dateRange.value.to) {
        return false;
    }
    
    const start = dateRange.value.from instanceof CalendarDate 
        ? dateRange.value.from.toDate(getLocalTimeZone())
        : new Date(dateRange.value.from);
    const end = dateRange.value.to instanceof CalendarDate 
        ? dateRange.value.to.toDate(getLocalTimeZone())
        : new Date(dateRange.value.to);
    const today = new Date();
    
    // Reset time components for all dates to compare only dates
    start.setHours(0, 0, 0, 0);
    end.setHours(0, 0, 0, 0);
    today.setHours(0, 0, 0, 0);
    
    // Validate that:
    // 1. Start date is not in the past
    // 2. End date is after start date
    // 3. Quantity is positive
    const isValid = start >= today && end > start && form.quantity > 0;
    
    return isValid;
});

// Watch for changes in dateRange and update form values
watch(dateRange, updateFormDates, { deep: true });

const handleReservation = () => {
    if (!isReservationValid.value) return;

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