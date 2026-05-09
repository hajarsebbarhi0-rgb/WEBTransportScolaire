@extends('layouts.app')

@section('content')

<div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">

    {{-- EN-TÊTE --}}
    <div class="text-center mb-10">
        <h1 class="text-5xl font-serif font-extrabold text-[#346693] uppercase tracking-wide">
            ➕ Nouveau Trajet
        </h1>
        <p class="text-[#346693]/70 mt-2 text-lg font-sans">
            Créez un trajet en ajoutant ses informations et les arrêts. 
        </p>
    </div>

    {{-- FORMULAIRE PRINCIPAL --}}
    <div class="max-w-3xl mx-auto bg-white shadow-xl rounded-2xl p-8 sm:p-10 border-t-4 border-[#346693]">
        <form action="{{ route('admin.trajets.store') }}" method="POST">
            @csrf

            {{-- NOM DU TRAJET --}}
            <div class="mb-6">
                <label class="block text-base font-semibold mb-2 text-[#346693]">Nom du Trajet</label>
                <input type="text" name="nom" 
                       placeholder="Ex: Trajet Centre Ville - École"
                       class="w-full rounded-full px-5 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition duration-200 text-base placeholder-[#346693]/60 font-sans" 
                       required>
            </div>

            {{-- DESCRIPTION --}}
            <div class="mb-6">
                <label class="block text-base font-semibold mb-2 text-[#346693]">Description</label>
                <textarea name="description" rows="3" 
                          placeholder="Décrivez le trajet ou les zones desservies..."
                          class="w-full rounded-2xl px-5 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition duration-200 text-base placeholder-[#346693]/60 font-sans"></textarea>
            </div>
            
            {{-- CHAUFFEUR --}}
            <div class="mb-6">
                <label class="block text-base font-semibold mb-2 text-[#346693]">Chauffeur</label>
                <select name="chauffeur_id"
                        class="w-full rounded-full px-5 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition duration-200 text-base font-sans" required>
                    <option value="" disabled selected>-- Sélectionnez un chauffeur --</option>
                    @foreach($chauffeurs as $chauffeur)
                        <option value="{{ $chauffeur->id }}">{{ $chauffeur->nom }} {{ $chauffeur->prenom }}</option>
                    @endforeach
                </select>
            </div>

            {{-- TRANSPORT --}}
            <div class="mb-6">
                <label class="block text-base font-semibold mb-2 text-[#346693]">Transport assigné</label>
                <select name="transport_id"
                        class="w-full rounded-full px-5 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition duration-200 text-base font-sans" required>
                    <option value="" disabled selected>-- Choisissez un véhicule --</option>
                    @foreach($transports as $transport)
                        <option value="{{ $transport->id }}">
                            {{ $transport->marque }} - {{ $transport->modele }} ({{ $transport->plaque_immatriculation }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- HEURES DE DÉBUT ET FIN --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-base font-semibold mb-2 text-[#346693]">Heure de Début</label>
                    <input type="time" name="debut" 
                           class="w-full rounded-full px-5 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition duration-200 font-sans" required>
                </div>
                <div>
                    <label class="block text-base font-semibold mb-2 text-[#346693]">Heure de Fin</label>
                    <input type="time" name="fin" 
                           class="w-full rounded-full px-5 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition duration-200 font-sans" required>
                </div>
            </div>

            {{-- CARTE POUR AJOUTER DES ARRÊTS --}}
            <div class="mb-8">
                <label class="block text-base font-semibold mb-3 text-[#346693]">
                    📍 Arrêts (Recherchez une adresse puis cliquez sur la carte ou directement sur la carte)
                </label>
                <div id="map" class="rounded-2xl border-2 border-[#82D2F5] shadow-sm" style="height: 400px; margin-bottom: 20px;"></div>
                <div id="arrets-container" class="space-y-2"></div>
            </div>

            {{-- BOUTON DE VALIDATION --}}
            <div class="flex justify-end">
                <button type="submit" 
                        class="inline-flex items-center px-8 py-3 bg-[#82D2F5] text-[#346693] font-bold rounded-full shadow-lg hover:bg-[#346693] hover:text-white transition transform hover:scale-105">
                    ✅ Créer le Trajet
                </button>
            </div>
        </form>
    </div>

</div>

{{-- Dépendances Leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

{{-- Dépendances Geocoder --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('map').setView([34.020882, -6.84165], 12); // Rabat
    var markers = L.layerGroup().addTo(map); // Groupe pour stocker les marqueurs

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let arretIndex = 0;

    // --- Fonction d'ajout d'arrêt (inchangée) ---
    function addArret(lat, lng, name = "Arrêt N°" + (arretIndex + 1)) {
        const container = document.getElementById('arrets-container');
        const div = document.createElement('div');
        div.classList.add('flex', 'gap-3', 'items-center', 'bg-[#F0F8FF]', 'p-3', 'rounded-xl', 'border', 'border-[#82D2F5]/40');
        div.innerHTML = `
            <input type="text" name="arrets[${arretIndex}][nom]" placeholder="Nom de l'arrêt" value="${name}"
                    class="flex-1 rounded-full px-4 py-2 border border-gray-300 focus:ring-2 focus:ring-[#82D2F5]/50 transition" required>
            <input type="hidden" name="arrets[${arretIndex}][latitude]" value="${lat}">
            <input type="hidden" name="arrets[${arretIndex}][longitude]" value="${lng}">
            <span class="text-sm text-[#346693]/70 font-semibold">📍 ${lat.toFixed(5)}, ${lng.toFixed(5)}</span>
        `;
        container.appendChild(div);
        
        // Ajouter un marqueur pour visualiser l'arrêt
        L.marker([lat, lng]).addTo(markers);

        arretIndex++;
    }
    // ----------------------------------------------

    // --- Initialisation du Géocodeur ---
    L.Control.geocoder({
        defaultMarkGeocode: false, // Ne pas marquer automatiquement le résultat
        placeholder: "Rechercher une adresse...",
        errorMessage: "Adresse non trouvée.",
        position: 'topleft',
        collapsed: true
    })
    .on('markgeocode', function(e) {
        // En cas de résultat de recherche, centrer la carte sur le résultat
        var bbox = e.geocode.bbox;
        var poly = L.polygon([
            [bbox.getSouthEast().lat, bbox.getSouthEast().lng],
            [bbox.getNorthEast().lat, bbox.getNorthEast().lng],
            [bbox.getNorthWest().lat, bbox.getNorthWest().lng],
            [bbox.getSouthWest().lat, bbox.getSouthWest().lng]
        ]).addTo(map);
        map.fitBounds(poly.getBounds());
        map.removeLayer(poly);
        
        // Optionnel : Ajouter l'arrêt automatiquement après la recherche et le zoom
        // addArret(e.geocode.center.lat, e.geocode.center.lng, e.geocode.name);
    })
    .addTo(map);

    // --- Événement de clic sur la carte (pour l'ajout manuel) ---
    map.on('click', function(e) {
        addArret(e.latlng.lat, e.latlng.lng);
    });
});
</script>

@endsection