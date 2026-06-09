@extends('layouts.app')

@section('content')

{{-- ✅ CSS en premier --}}
<style>
    #map {
        z-index: 0 !important;
        position: relative !important;
    }
    .leaflet-pane,
    .leaflet-top,
    .leaflet-bottom {
        z-index: 0 !important;
    }
    .leaflet-control {
        z-index: 1 !important;
    }
</style>

{{-- Leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">

    {{-- EN-TÊTE --}}
    <div class="text-center mb-12">
        <h1 class="text-5xl font-extrabold text-[#F16522] uppercase tracking-widest font-serif">
            🛰️ Suivi en Temps Réel des Véhicules
        </h1>
        <p class="mt-4 text-lg text-[#346693] font-semibold">
            Visualisez la position actuelle des véhicules en service.
        </p>
    </div>

    {{-- CARTE --}}
    <div id="map" class="rounded-2xl shadow-2xl border-4 border-[#82D2F5]" style="height: 600px;"></div>

    {{-- LÉGENDE --}}
    <div class="mt-6 text-center text-[#346693]">
        <p class="font-semibold">
            🟢 Mise à jour automatique toutes les 3 secondes
        </p>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    var map = L.map('map').setView([33.5731, -7.5898], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    var markers = {};

    var busIcon = L.icon({
        iconUrl: "https://cdn-icons-png.flaticon.com/512/3448/3448339.png",
        iconSize: [45, 45],
        iconAnchor: [22, 22],
        popupAnchor: [0, -22]
    });

    // ✅ Markers réels depuis PHP
    var initialPositions = @json($positions);

    initialPositions.forEach(function(vehicle) {
        if (!vehicle.latitude || !vehicle.longitude) return;

        let lat = parseFloat(vehicle.latitude);
        let lng = parseFloat(vehicle.longitude);

        let marker = L.marker([lat, lng], { icon: busIcon }).addTo(map);

        marker.bindPopup(`
            <div style="font-size:14px; line-height:1.8;">
                <strong>🚍 Trajet :</strong> ${vehicle.trajet?.nom ?? 'N/A'}<br>
                <strong>👨‍✈️ Chauffeur :</strong> ${vehicle.trajet?.chauffeur?.nom ?? 'N/A'}<br>
                <strong>🕒 Mise à jour :</strong> ${new Date(vehicle.updated_at).toLocaleTimeString('fr-FR')}
            </div>
        `);

        markers[vehicle.trajet_id] = marker;
    });

    // 🎭 Markers DEMO pour présentation
    var demoPositions = [
        { trajet_id: 'demo1', latitude: 33.5892, longitude: -7.6031, nom_trajet: 'Ligne 1 - Hay Riad',    chauffeur: 'Mohammed A.' },
        { trajet_id: 'demo2', latitude: 33.5650, longitude: -7.5800, nom_trajet: 'Ligne 2 - Ain Chock',   chauffeur: 'Youssef B.'  },
        { trajet_id: 'demo3', latitude: 33.5500, longitude: -7.6200, nom_trajet: 'Ligne 3 - Sidi Maarouf', chauffeur: 'Khalid C.'  }
    ];

    demoPositions.forEach(function(vehicle) {
        let marker = L.marker([vehicle.latitude, vehicle.longitude], { icon: busIcon }).addTo(map);
        marker.bindPopup(`
            <div style="font-size:14px; line-height:1.8;">
                <strong>🚍 Trajet :</strong> ${vehicle.nom_trajet}<br>
                <strong>👨‍✈️ Chauffeur :</strong> ${vehicle.chauffeur}<br>
                <strong>🕒 Mise à jour :</strong> ${new Date().toLocaleTimeString('fr-FR')}
            </div>
        `);
    });

    // 🔥 Refresh dynamique
    async function loadPositions() {
        try {
            const response = await fetch('/api/admin/bus-positions');
            const data = await response.json();

            if (!data.length) return;

            data.forEach(vehicle => {
                if (!vehicle.latitude || !vehicle.longitude) return;

                let lat = parseFloat(vehicle.latitude);
                let lng = parseFloat(vehicle.longitude);

                if (markers[vehicle.trajet_id]) {
                    markers[vehicle.trajet_id].setLatLng([lat, lng]);
                } else {
                    let marker = L.marker([lat, lng], { icon: busIcon }).addTo(map);
                    marker.bindPopup(`
                        <div style="font-size:14px; line-height:1.8;">
                            <strong>🚍 Trajet :</strong> ${vehicle.trajet?.nom ?? 'N/A'}<br>
                            <strong>👨‍✈️ Chauffeur :</strong> ${vehicle.trajet?.chauffeur?.nom ?? 'N/A'}<br>
                            <strong>🕒 Mise à jour :</strong> ${new Date(vehicle.updated_at).toLocaleTimeString('fr-FR')}
                        </div>
                    `);
                    markers[vehicle.trajet_id] = marker;
                }
            });

            Object.keys(markers).forEach(trajetId => {
                const stillActive = data.find(v => v.trajet_id == trajetId);
                if (!stillActive) {
                    map.removeLayer(markers[trajetId]);
                    delete markers[trajetId];
                }
            });

        } catch (error) {
            console.log("Erreur chargement positions", error);
        }
    }

    setInterval(loadPositions, 3000);
    loadPositions();
});
</script>



@endsection