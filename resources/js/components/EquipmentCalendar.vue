<template>
    <div class="space-y-4 availability-calendar-container">
        <FullCalendar
          :options="calendarOptions"
        />

        <div class="flex justify-evenly gap-2 flex-wrap text-sm text-muted-foreground">
            <div v-for="(status, index) in availabilityStatuses" 
                 :key="index" 
                 class="flex items-center gap-1">
                <div :class="`size-2 rounded-full ${status.color}`"></div>
                <span>{{ status.label }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import frLocale from '@fullcalendar/core/locales/fr'
import axios from 'axios';

const props = defineProps({
    equipment: {
        type: Object,
        required: true
    }
});

const loading = ref(false);
const availabilities = ref([]);

const availabilityStatuses = [
    { label: 'Disponible', color: 'bg-green-500' },
    { label: 'Option posée', color: 'bg-orange-500' },
    { label: 'Réservé', color: 'bg-red-500' }
];

const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin],
    initialView: 'dayGridMonth',
    datesSet: (dateInfo) => {
        const year = dateInfo.view.currentStart.getFullYear();
        const month = dateInfo.view.currentStart.getMonth();
        fetchAvailabilities(year, month);
    },
    headerToolbar: {
        left: 'prev',
        center: 'title',
        right: 'next'
    },
    events: availabilities.value,
    locale: frLocale,
    height: 'auto',
    editable: false,
    selectable: false,
    selectMirror: false,
    nowIndicator: false,
    eventDisplay: 'list-item',
    displayEventTime: false,
    displayEventEnd: false,
    eventClassNames: 'availibilityEvent',
    showNonCurrentDates: true,
    // validRange: {
    //     start: new Date().toISOString().split('T')[0],
    // }
}));

const fetchAvailabilities = async (year, month) => {
    loading.value = true;
    try {
        const response = await axios.get(route('api.equipments.reservations-dates-by-month', {
            equipment: props.equipment.id,
            month: month + 1,
            year: year
        }));

        const apiEvents = response.data.map(event => ({
            start: event.date,
            display: 'list-item',
            backgroundColor: event.color,
            borderColor: event.color,
            classNames: 'availibilityEvent'
        }));

        const eventDates = new Map(apiEvents.map(event => [event.start, event]));
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        const allDates = Array.from({ length: daysInMonth }, (_, day) => {
            const d = new Date(Date.UTC(year, month, day + 1));
            const dateStr = d.toISOString().split('T')[0];
            
            return eventDates.has(dateStr) 
                ? eventDates.get(dateStr)
                : {
                    start: dateStr,
                    display: 'list-item',
                    backgroundColor: '#22c55e', // Default green for available dates
                    borderColor: '#22c55e',
                    classNames: 'availibilityEvent'
                };
        });

        availabilities.value = allDates;
    } catch (error) {
        console.error('Erreur lors du chargement des disponibilités:', error);
    } finally {
        loading.value = false;
    }
};
</script>

<style>
.availability-calendar-container .fc {
    border-radius: 1rem;
    border: 1px solid var(--border);
    overflow: hidden;
}

.availability-calendar-container .fc .fc-daygrid-body-natural .fc-daygrid-day-events {
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: auto;
}

.availability-calendar-container .fc .fc-daygrid-day {
    border: none !important;
}

.availability-calendar-container .fc .fc-button {
    border: none !important;
    background: none !important;
    box-shadow: none !important;
    color: inherit;
    padding: 0.25rem 0.5rem;
    min-width: 0;
}

.availability-calendar-container .fc .fc-toolbar-title {
    font-size: 1rem !important;
}

.availability-calendar-container .fc .fc-col-header-cell {
    background: none !important;
    padding: 0.25rem 0 !important;
    height: 1.5rem !important;
    border: none !important;
}

.availability-calendar-container .fc .fc-col-header {
    background: none !important;
}

.availability-calendar-container .fc .fc-daygrid-day-frame {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
}

.availability-calendar-container .fc .fc-daygrid-day-events {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 0;
    margin: 0;
}

.availability-calendar-container .availibilityEvent {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 !important;
    padding: 0 !important;
    min-height: 0 !important;
    min-width: 0 !important;
}

.availability-calendar-container .availibilityEvent .fc-event-title {
    display: none;
}
</style>