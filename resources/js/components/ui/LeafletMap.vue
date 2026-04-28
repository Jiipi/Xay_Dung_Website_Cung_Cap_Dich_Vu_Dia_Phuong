<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

import iconUrl from 'leaflet/dist/images/marker-icon.png';
import shadowUrl from 'leaflet/dist/images/marker-shadow.png';
import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';

const DefaultIcon = L.icon({
    iconUrl,
    iconRetinaUrl,
    shadowUrl,
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});
L.Marker.prototype.options.icon = DefaultIcon;

const props = defineProps<{
    services: Array<any>;
    center?: [number, number];
}>();

const mapContainer = ref<HTMLElement | null>(null);
let map: L.Map | null = null;
let markers: L.Marker[] = [];

onMounted(() => {
    map = L.map(mapContainer.value!).setView(props.center || [11.9404, 108.4583], 13); // Default to Dalat
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    updateMarkers();
});

watch(() => props.services, () => {
    updateMarkers();
}, { deep: true });

function updateMarkers() {
    if (!map) return;
    
    // Clear old markers
    markers.forEach(m => m.remove());
    markers = [];
    
    // Create bounds object to fit map to markers
    let bounds = L.latLngBounds([]);
    
    props.services.forEach(service => {
        // If the service doesn't have coordinates, we'll randomize near Dalat center
        const lat = service.lat ?? (11.9404 + (Math.random() - 0.5) * 0.05);
        const lng = service.lng ?? (108.4583 + (Math.random() - 0.5) * 0.05);
        
        const marker = L.marker([lat, lng]).addTo(map!);
        marker.bindPopup(`
            <div class="p-1">
                <img src="${service.image}" class="w-full h-24 object-cover mb-2 rounded" referrerpolicy="no-referrer" />
                <h3 class="font-bold text-sm text-stone-900">${service.title}</h3>
                <p class="text-xs text-brand font-bold mt-1">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(service.price)}</p>
            </div>
        `);
        markers.push(marker);
        bounds.extend([lat, lng]);
    });
    
    // Fit bounds if we have markers
    if (markers.length > 0 && map) {
        map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
    }
}

onUnmounted(() => {
    if (map) {
        map.remove();
        map = null;
    }
});
</script>

<template>
    <div class="h-full w-full z-0" ref="mapContainer"></div>
</template>

<style>
/* Leaflet customizations to match Dalat vibe */
.leaflet-popup-content-wrapper {
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}
.leaflet-popup-content {
    margin: 8px;
}
</style>
