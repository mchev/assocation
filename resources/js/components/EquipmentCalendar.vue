<template>
    <div class="space-y-4">
        <h4 class="font-medium">Disponibilités</h4>

        <FullCalendar
          :options="{
            plugins: [dayGridPlugin],
            initialView: 'dayGridMonth',
            datesSet: (dateInfo) => {
                const currentDate = new Date(dateInfo.view.currentStart);
                fetchAvailabilities(currentDate);
            },
            headerToolbar: {
              left: 'prev',
              center: 'title',
              right: 'next'
            },
            events: availabilities,
            locale: frLocale,
            height: 'auto',
            editable: false,
            selectable: false,
            selectMirror: false,
            nowIndicator: false,
            displayEventTime: false,
          }"
        />

        <div class="flex justify-evenly gap-2 flex-wrap text-sm text-muted-foreground">
            <div class="flex items-center gap-1">
                <div class="size-2 rounded-full bg-green-500"></div>
                <span>Disponible</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="size-2 rounded-full bg-orange-500"></div>
                <span>Option posée</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="size-2 rounded-full bg-red-500"></div>
                <span>Réservé</span>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import frLocale from '@fullcalendar/core/locales/fr'
import axios from 'axios';

const props = defineProps({
    equipment: {
        type: Object,
        required: true
    }
})

const currentDate = ref(new Date());
const loading = ref(false);
const availabilities = ref([]);

onMounted(() => {
    fetchAvailabilities(currentDate.value);
});

const fetchAvailabilities = async (date) => {
    loading.value = true;
    try {
        await axios.get(route('api.equipments.reservations-dates-by-month', {
            equipment: props.equipment.id,
            month: date.getMonth() + 1,
            year: date.getFullYear()
        })).then(response => {
            availabilities.value = response.data;
        });
    } catch (error) {
        console.error('Erreur lors du chargement des disponibilités:', error);
    } finally {
        loading.value = false;
    }
};

</script>