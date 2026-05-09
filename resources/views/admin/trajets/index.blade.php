@extends('layouts.app')

@section('content')

<div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">

{{-- EN-TÊTE ET BOUTON AJOUTER --}}
<div class="flex justify-between items-center mb-8">
    <h1 class="text-5xl tracking-tight font-serif font-extrabold text-[#F16522] uppercase">
        Gestion des Trajets
    </h1>
    
    <a href="{{ route('admin.trajets.create') }}" 
       class="inline-flex items-center bg-[#82D2F5] text-[#346693] font-semibold py-3 px-6 rounded-full shadow-lg hover:bg-[#346693] hover:text-white transition duration-300 transform hover:scale-105">
        <span class="text-xl mr-2">➕</span> Ajouter un Trajet
    </a>
</div>

{{-- MESSAGE DE SUCCÈS --}}
@if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-8 shadow-md" role="alert">
        <p class="font-sans font-semibold">{{ session('success') }}</p>
    </div>
@endif

{{-- BARRE DE RECHERCHE --}}
<div class="bg-white p-6 shadow-xl rounded-2xl mb-8 border-t-4 border-[#346693]">
    <div class="flex flex-col sm:flex-row gap-4 items-center">
        <div class="relative flex-grow w-full sm:w-auto">
            <input type="text" id="searchInput" placeholder="🔍 Rechercher un trajet (Nom, Code ou Chauffeur)..."
                class="w-full rounded-full pl-5 pr-4 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition duration-200 text-base placeholder-[#346693]/70 font-sans">
        </div>
    </div>
</div>

{{-- TABLEAU DES TRAJETS --}}
<div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="bg-[#346693] text-white uppercase text-sm font-semibold tracking-wider">
                    <th class="py-4 px-6 text-left">Nom</th>
                    <th class="py-4 px-6 text-left">Code</th>
                    <th class="py-4 px-6 text-left">Chauffeur</th>
                    <th class="py-4 px-6 text-left">Début / Fin</th>
                    <th class="py-4 px-6 text-left">Arrêts</th>
                    <th class="py-4 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="trajetTableBody" class="text-[#346693] text-base font-sans divide-y divide-gray-100">
                @forelse($trajets as $trajet)
                <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                    <td class="py-4 px-6 font-semibold">{{ $trajet->nom }}</td>
                    <td class="py-4 px-6">{{ $trajet->code_trajet }}</td>
                    {{-- MODIFICATION 1 : Affichage initial du Nom + Prénom --}}
                    <td class="py-4 px-6">
                        {{ $trajet->chauffeur ? "{$trajet->chauffeur->nom} {$trajet->chauffeur->prenom}" : 'Non affecté' }}
                    </td>
                    <td class="py-4 px-6">{{ $trajet->debut }} - {{ $trajet->fin }}</td>
                    <td class="py-4 px-6">{{ $trajet->arrets->count() }} arrêts</td>
                    <td class="py-4 px-6 text-center whitespace-nowrap">
                    <a href="{{ route('admin.trajets.show', $trajet) }}"
   class="text-blue-600 hover:underline font-semibold">
   👁 Voir
</a>

                        <a href="{{ route('admin.trajets.edit', $trajet) }}" 
                           class="text-[#F16522] font-bold hover:text-[#346693] transition-colors duration-200">
                            ✏️ Modifier
                        </a>
                        
                        <form action="{{ route('admin.trajets.destroy', $trajet) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Supprimer ce trajet ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-gray-400 font-bold hover:text-[#F16522] transition-colors duration-200 focus:outline-none">
                                🗑️ Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr id="noResultsRow">
                    <td colspan="6" class="py-10 px-6 text-center italic text-[#346693]/70">
                        Aucun trajet trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


</div>

{{-- SCRIPT DE RECHERCHE LIVE --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('trajetTableBody');
    const searchUrl = "{{ route('admin.trajets.search') }}"; 
    let debounceTimer;

    const debounce = (callback, delay) => {
        return function(...args) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                callback.apply(this, args);
            }, delay);
        };
    };

    const performSearch = () => {
        const query = searchInput.value.trim();
        const csrfToken = '{{ csrf_token() }}';

        fetch(`${searchUrl}?query=${encodeURIComponent(query)}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error('Erreur de réseau ou de serveur: ' + res.status);
                }
                return res.json();
            })
            .then(data => {
                tableBody.innerHTML = ''; // Vide la table

                if (data.length === 0) {
                    tableBody.innerHTML = 
                        `<tr>
                            <td colspan="6" class="py-10 px-6 text-center italic text-[#346693]/70">
                                Aucun trajet trouvé
                            </td>
                        </tr>`;
                    return;
                }

                data.forEach(trajet => {
                    const editUrl = `/admin/trajets/${trajet.id}/edit`;
                    const deleteUrl = `/admin/trajets/${trajet.id}`;
                    
                    // MODIFICATION 2 : Gestion robuste du Nom + Prénom dans la réponse AJAX
                    // Utilise Nom et Prénom s'ils existent, sinon 'Non affecté'
                    const chauffeurNom = trajet.chauffeur 
                        ? `${trajet.chauffeur.nom || ''} ${trajet.chauffeur.prenom || ''}`.trim()
                        : 'Non affecté';
                    
                    tableBody.innerHTML += 
                        `<tr class="hover:bg-blue-50/50 transition-colors duration-200">
                            <td class="py-4 px-6 font-semibold">${trajet.nom}</td>
                            <td class="py-4 px-6">${trajet.code_trajet}</td>
                            <td class="py-4 px-6">${chauffeurNom}</td>
                            <td class="py-4 px-6">${trajet.debut} - ${trajet.fin}</td>
                            <td class="py-4 px-6">${trajet.arrets ? trajet.arrets.length : 0} arrêts</td>
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                <a href="${editUrl}" class="text-[#F16522] font-bold hover:text-[#346693] transition-colors duration-200">✏️ Modifier</a>
                                <form action="${deleteUrl}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Supprimer ce trajet ?')">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="text-gray-400 font-bold hover:text-[#F16522] transition-colors duration-200 focus:outline-none">🗑️ Supprimer</button>
                                </form>
                            </td>
                        </tr>`;
                });
            })
            .catch(err => console.error('Erreur lors de la recherche AJAX ou du parsing:', err));
    };

    searchInput.addEventListener('input', debounce(performSearch, 300));
});
</script>

@endsection