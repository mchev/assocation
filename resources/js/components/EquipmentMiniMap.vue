<template>
    <div class="w-full h-60 rounded-lg overflow-hidden">
        <div ref="mapContainer" class="w-full h-full"></div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
    city: {
        type: String,
        required: true
    },
});

const mapContainer = ref(null);
let map = null;
let circle = null;

// Function to get coordinates for a city (you might want to use a geocoding service)
const getCityCoordinates = async (city) => {
    // This is a placeholder - you should implement proper geocoding
    // For example, using OpenStreetMap Nominatim API
    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(city)}`);
    const data = await response.json();
    if (data && data[0]) {
        return [parseFloat(data[0].lat), parseFloat(data[0].lon)];
    }
    // Default to Paris if geocoding fails
    return [48.8566, 2.3522];
};

const initializeMap = async () => {
    if (!mapContainer.value) return;

    const coordinates = await getCityCoordinates(props.city);
    
    // Initialize map
    map = L.map(mapContainer.value).setView(coordinates, 13);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    // Add circle
    const radius = 1000; // 1km radius
    circle = L.circle(coordinates, {
        radius: radius,
        color: 'rgb(147, 51, 234)',
        fillColor: 'rgb(147, 51, 234)',
        fillOpacity: 0.2,
        weight: 2
    }).addTo(map);
};

// Watch for city changes
watch(() => props.city, async (newCity) => {
    if (map) {
        const coordinates = await getCityCoordinates(newCity);
        map.setView(coordinates, 13);
        if (circle) {
            circle.setLatLng(coordinates);
        }
    }
});

onMounted(() => {
    initializeMap();
});
</script>

<style>
.leaflet-container {
    width: 100%;
    height: 100%;
}
</style>