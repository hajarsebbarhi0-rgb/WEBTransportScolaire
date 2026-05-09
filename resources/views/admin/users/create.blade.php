@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">

    <div class="max-w-xl mx-auto rounded-xl shadow-lg p-8 sm:p-10 bg-white border border-gray-200">

        <h1 class="text-3xl sm:text-4xl font-extrabold mb-8 text-center text-[#F16522] uppercase tracking-wide">
            Ajouter un Compte
        </h1>

        {{-- ERREURS --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-700 rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            @php
                $labelClass = 'block text-base font-bold mb-2 text-[#346693]';
                $inputClass = 'w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#82D2F5]/50';
            @endphp

            {{-- INFOS USER --}}
            <div class="mb-5">
                <label class="{{ $labelClass }}">Nom</label>
                <input type="text" name="nom" value="{{ old('nom') }}" class="{{ $inputClass }}" required>
            </div>

            <div class="mb-5">
                <label class="{{ $labelClass }}">Prénom</label>
                <input type="text" name="prenom" value="{{ old('prenom') }}" class="{{ $inputClass }}" required>
            </div>

            <div class="mb-5">
                <label class="{{ $labelClass }}">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="{{ $inputClass }}" required>
            </div>

            <div class="mb-5">
                <label class="{{ $labelClass }}">Mot de passe</label>
                <input type="password" name="password" class="{{ $inputClass }}" required>
            </div>

            <div class="mb-5">
                <label class="{{ $labelClass }}">Confirmation</label>
                <input type="password" name="password_confirmation" class="{{ $inputClass }}" required>
            </div>

            <div class="mb-5">
                <label class="{{ $labelClass }}">Téléphone</label>
                <input type="text" name="telephone" value="{{ old('telephone') }}" class="{{ $inputClass }}">
            </div>

            <div class="mb-8">
                <label class="{{ $labelClass }}">Rôle</label>
                <select name="role" id="role" class="{{ $inputClass }}" required>
                    <option value="admin">Administrateur</option>
                    <option value="chauffeur">Chauffeur</option>
                    <option value="eleve">Élève</option>
                </select>
            </div>

            {{-- SECTION ÉLÈVE --}}
            <div id="eleve-fields" class="hidden p-6 rounded-lg border border-dashed border-[#82D2F5] bg-[#82D2F5]/10 mb-6">

                <h3 class="text-xl font-bold mb-4 text-[#346693]">
                    Détails Élève
                </h3>

                <div class="mb-5">
                    <label class="{{ $labelClass }}">École</label>
                    <input type="text" name="ecole" class="{{ $inputClass }}">
                </div>

                <div class="mb-5">
                    <label class="{{ $labelClass }}">Niveau</label>
                    <input type="text" name="niveau" class="{{ $inputClass }}">
                </div>

                <div class="mb-5">
                    <label class="{{ $labelClass }}">Code Trajet</label>
                    <input type="text" name="code_trajet" id="code_trajet" class="{{ $inputClass }}">
                </div>

                {{-- MAP --}}
                <div class="mb-5">
                    <label class="{{ $labelClass }}">
                        📍 Localisation du domicile
                    </label>

                    <div id="map-eleve"
                         class="w-full rounded-xl border-2 border-[#82D2F5]"
                         style="height: 300px;">
                    </div>

                    <p class="text-sm text-gray-500 mt-2">
                        Cliquez sur la carte pour choisir l’arrêt de l’élève
                    </p>

                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                </div>

            </div>

            {{-- SUBMIT --}}
            <div class="flex justify-center">
                <button type="submit"
                        class="px-8 py-3 bg-[#F16522] text-white font-bold rounded-full hover:bg-[#346693] transition">
                    ✅ Créer le compte
                </button>
            </div>

        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('admin.users.index') }}"
               class="text-[#346693] hover:text-[#82D2F5] font-semibold">
                ← Retour
            </a>
        </div>
    </div>
</div>

{{-- LEAFLET --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link
  rel="stylesheet"
  href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css"
/>

<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const roleSelect = document.getElementById('role');
    const eleveFields = document.getElementById('eleve-fields');
    let map = null;
    let marker = null;

    function initMap() {
        if (map) return;

        map = L.map('map-eleve').setView([33.5731, -7.5898], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // 🔍 BARRE DE RECHERCHE
        L.Control.geocoder({
            defaultMarkGeocode: false,
            placeholder: 'Rechercher une adresse ou un lieu...',
        })
        .on('markgeocode', function (e) {
            const latlng = e.geocode.center;

            map.setView(latlng, 16);

            if (marker) {
                marker.setLatLng(latlng);
            } else {
                marker = L.marker(latlng).addTo(map);
            }

            document.getElementById('latitude').value = latlng.lat;
            document.getElementById('longitude').value = latlng.lng;
        })
        .addTo(map);

        // 📍 CLIC SUR LA MAP
        map.on('click', function (e) {
            const { lat, lng } = e.latlng;

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        });
    }

    roleSelect.addEventListener('change', function () {
        const isEleve = this.value === 'eleve';
        eleveFields.classList.toggle('hidden', !isEleve);

        if (isEleve) {
            setTimeout(initMap, 300);
        }
    });

});
</script>

@endsection
