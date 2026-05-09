@extends('layouts.app')

@section('content')

<div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">

{{-- EN-TÊTE --}}
<div class="text-center mb-10">
    <h1 class="text-5xl font-serif font-extrabold text-[#346693] uppercase tracking-wide">
        ✏️ Modifier le Trajet
    </h1>
    <p class="text-[#346693]/70 mt-2 text-lg font-sans">
        Mettez à jour les informations du trajet existant et ajustez ses arrêts si nécessaire.
    </p>
</div>

{{-- FORMULAIRE DE MODIFICATION --}}
<div class="max-w-3xl mx-auto bg-white shadow-xl rounded-2xl p-8 sm:p-10 border-t-4 border-[#346693]">
    <form action="{{ route('admin.trajets.update', $trajet) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- NOM DU TRAJET --}}
        <div class="mb-6">
            <label class="block text-base font-semibold mb-2 text-[#346693]">Nom du Trajet</label>
            <input type="text" name="nom" value="{{ old('nom', $trajet->nom) }}"
                   class="w-full rounded-full px-5 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition text-base font-sans" 
                   required>
        </div>

        {{-- DESCRIPTION --}}
        <div class="mb-6">
            <label class="block text-base font-semibold mb-2 text-[#346693]">Description</label>
            <textarea name="description" rows="3"
                      class="w-full rounded-2xl px-5 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition text-base font-sans">{{ old('description', $trajet->description) }}</textarea>
        </div>

        {{-- CODE DU TRAJET (MODIFIÉ) --}}
        <div class="mb-6">
            <label class="block text-base font-semibold mb-2 text-[#346693]">Code du Trajet</label>
            <input type="text" value="{{ $trajet->code_trajet }}"
                   class="w-full rounded-full px-5 py-3 border border-gray-300 bg-gray-100 cursor-not-allowed text-base font-sans" 
                   readonly> 
        </div>

        {{-- CHAUFFEUR --}}
        <div class="mb-6">
            <label class="block text-base font-semibold mb-2 text-[#346693]">Chauffeur</label>
            <select name="chauffeur_id"
                    class="w-full rounded-full px-5 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition text-base font-sans" required>
                @foreach($chauffeurs as $chauffeur)
                    <option value="{{ $chauffeur->id }}" {{ $trajet->chauffeur_id == $chauffeur->id ? 'selected' : '' }}>
                        {{ $chauffeur->nom }} {{ $chauffeur->prenom }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- TRANSPORT --}}
        <div class="mb-6">
            <label class="block text-base font-semibold mb-2 text-[#346693]">Transport Assigné</label>
            <select name="transport_id"
                    class="w-full rounded-full px-5 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition text-base font-sans" required>
                @foreach($transports as $transport)
                    <option value="{{ $transport->id }}" {{ $trajet->transport_id == $transport->id ? 'selected' : '' }}>
                        {{ $transport->marque }} - {{ $transport->modele }} ({{ $transport->plaque_immatriculation }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- HEURES --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="block text-base font-semibold mb-2 text-[#346693]">Heure de Début</label>
                <input type="time" name="debut" value="{{ old('debut', $trajet->debut) }}"
                        class="w-full rounded-full px-5 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition font-sans" required>
            </div>
            <div>
                <label class="block text-base font-semibold mb-2 text-[#346693]">Heure de Fin</label>
                <input type="time" name="fin" value="{{ old('fin', $trajet->fin) }}"
                        class="w-full rounded-full px-5 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition font-sans" required>
            </div>
        </div>

        {{-- ARRÊTS --}}
        <div class="mb-8">
            <label class="block text-base font-semibold mb-3 text-[#346693]">📍 Liste des Arrêts</label>
            <div id="arrets-container" class="space-y-2">
                @foreach($trajet->arrets as $index => $arret)
                    <input type="text" name="arrets[{{ $index }}][nom]" value="{{ $arret->nom }}"
                           class="w-full rounded-full px-5 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition text-base font-sans">
                @endforeach
            </div>
            <button type="button" onclick="addArret()" 
                    class="mt-3 px-6 py-2 bg-[#82D2F5] text-[#346693] font-semibold rounded-full hover:bg-[#346693] hover:text-white transition transform hover:scale-105">
                ➕ Ajouter un arrêt
            </button>
        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-between items-center mt-8">
            <a href="{{ route('admin.trajets.index') }}" 
               class="inline-flex items-center px-8 py-3 bg-gray-300 text-[#346693] font-semibold rounded-full hover:bg-gray-400 transition transform hover:-translate-y-1">
                ⬅️ Annuler
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-8 py-3 bg-[#82D2F5] text-[#346693] font-bold rounded-full shadow-lg hover:bg-[#346693] hover:text-white transition transform hover:scale-105">
                💾 Mettre à jour
            </button>
        </div>
    </form>
</div>

</div>

<script>
function addArret() {
    const container = document.getElementById('arrets-container');
    const index = container.children.length;
    const div = document.createElement('div');
    div.innerHTML = `
        <input type="text" name="arrets[${index}][nom]" placeholder="Nom de l'arrêt"
               class="w-full rounded-full px-5 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition text-base font-sans mb-2">
    `;
    container.appendChild(div);
}
</script>

@endsection