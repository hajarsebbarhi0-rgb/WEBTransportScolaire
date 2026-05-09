@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
    
    {{-- EN-TÊTE ET BOUTON AJOUTER --}}
    <div class="flex justify-between items-center mb-8">
        {{-- Titre : Utilise Impact (simulé par font-serif et extra-bold) --}}
        <h1 class="text-5xl tracking-tight font-serif font-extrabold text-[#F16522] uppercase">
             Gestion des Transports
        </h1>
        
        {{-- Bouton : Utilise la couleur secondaire claire #82D2F5 --}}
        <a href="{{ route('admin.transports.create') }}" 
            class="inline-flex items-center bg-[#82D2F5] text-[#346693] font-semibold py-3 px-6 rounded-full shadow-lg hover:bg-[#F16522] hover:text-white transition duration-300 transform hover:scale-105">
            <span class="text-xl mr-2">➕</span> Ajouter un Transport
        </a>
    </div>

    {{-- MESSAGE DE SUCCÈS --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-8 shadow-md" role="alert">
            <p class="font-sans font-semibold">{{ session('success') }}</p>
        </div>
    @endif
    
    {{-- BARRE DE RECHERCHE ET FILTRES --}}
    <div class="bg-white p-6 shadow-xl rounded-2xl mb-8 border-t-4 border-[#346693]">
        <div class="flex flex-col sm:flex-row gap-4 items-center">
            
            {{-- Champ de Recherche --}}
            <div class="relative flex-grow w-full sm:w-auto">
                <input type="text" name="searchInput" id="searchInput" placeholder="🔍 Rechercher (Marque, Modèle, Plaque)..."
                    class="w-full rounded-full pl-5 pr-4 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition duration-200 text-base placeholder-[#346693]/70 font-sans">
            </div>

            {{-- Filtre de Statut --}}
            <div class="w-full sm:w-52">
                <select name="statusFilter" id="statusFilter" 
                        class="w-full rounded-full px-5 py-3 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition duration-200 text-base appearance-none bg-white text-[#346693] font-sans font-semibold">
                    <option value="" selected>-- Tous les États --</option>
                    <option value="disponible">✅ Disponible</option>
                    <option value="en_service">🚍 En service</option>
                    <option value="en_maintenance">🛠️ Maintenance</option>
                </select>
            </div>
            
        </div>
    </div>

    {{-- TABLEAU DES TRANSPORTS --}}
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                {{-- En-tête de Tableau : Utilise la couleur primaire #346693 --}}
                <thead>
                    <tr class="bg-[#346693] text-white uppercase text-sm font-semibold tracking-wider">
                        <th class="py-4 px-6 text-left">ID</th>
                        <th class="py-4 px-6 text-left">Marque</th>
                        <th class="py-4 px-6 text-left">Modèle</th>
                        <th class="py-4 px-6 text-left">Immatriculation</th>
                        <th class="py-4 px-6 text-left">Capacité</th>
                        <th class="py-4 px-6 text-left">État</th>
                        <th class="py-4 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="transportTableBody" class="text-[#346693] text-base font-sans divide-y divide-gray-100">
                    {{-- Le contenu est pré-rempli par Laravel pour le chargement initial --}}
                    @forelse($transports as $transport)
                    <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                        <td class="py-4 px-6">{{ $transport->id }}</td>
                        <td class="py-4 px-6 font-semibold">{{ $transport->marque }}</td>
                        <td class="py-4 px-6">{{ $transport->modele }}</td>
                        <td class="py-4 px-6">{{ $transport->plaque_immatriculation }}</td>
                        <td class="py-4 px-6">{{ $transport->capacite_passagers }}</td>
                        <td class="py-4 px-6">
                            @if($transport->status == 'disponible')
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800">✅ Disponible</span>
                            @elseif($transport->status == 'en_service')
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-[#82D2F5]/30 text-[#346693]">🚍 En service</span>
                            @elseif($transport->status == 'en_maintenance')
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-[#F16522]/30 text-[#F16522]">🛠️ Maintenance</span>
                            @else
                                {{ $transport->status }}
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            {{-- Liens d'action en couleur #F16522 --}}
                            <a href="{{ route('admin.transports.show', $transport->id) }}" 
                               class="text-[#F16522] font-bold hover:text-[#346693] transition-colors duration-200">
                                👁️ Voir
                            </a>
                            <a href="{{ route('admin.transports.edit', $transport->id) }}" 
                               class="ml-4 text-[#F16522] font-bold hover:text-[#346693] transition-colors duration-200">
                                ✏️ Modifier
                            </a>
                            <form action="{{ route('admin.transports.destroy', $transport->id) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce transport ?')">
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
                    <tr>
                        <td colspan="7" class="py-10 px-6 text-center italic text-[#346693]/70">
                            Aucun transport trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Pagination -->
<div class="mt-4 flex justify-center">
    {{ $transports->appends(request()->query())->links() }}
</div>
        </div>
    </div>
</div>

{{-- SCRIPT JAVASCRIPT AJAX --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const tableBody = document.getElementById('transportTableBody');
    const searchUrl = "{{ route('admin.transports.search') }}"; 
    let debounceTimer;

    // Fonction pour générer le badge HTML correct
    const getStatusBadge = (status) => {
        switch(status) {
            case 'disponible':
                return '<span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800">✅ Disponible</span>';
            case 'en_service':
                // Couleur secondaire claire #82D2F5 avec texte Bleu Principal #346693
                return '<span class="px-3 py-1 text-xs font-bold rounded-full bg-[#82D2F5]/30 text-[#346693]">🚍 En service</span>';
            case 'en_maintenance':
                // Couleur accent #F16522
                return '<span class="px-3 py-1 text-xs font-bold rounded-full bg-[#F16522]/30 text-[#F16522]">🛠️ Maintenance</span>';
            default:
                return status;
        }
    };

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
        const status = statusFilter.value;
        const csrfToken = '{{ csrf_token() }}'; // Utilisé pour la suppression dynamique

        // Requête GET avec les deux paramètres (recherche et filtre)
        fetch(`${searchUrl}?query=${encodeURIComponent(query)}&status_filter=${encodeURIComponent(status)}`)
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = ''; // Vide la table

                if (data.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="py-10 px-6 text-center italic text-[#346693]/70">
                                Aucun transport trouvé
                            </td>
                        </tr>`;
                    return;
                }
                
                data.forEach(transport => {
                    // Construction dynamique des URLs
                    const showUrl = `/admin/transports/${transport.id}`;
                    const editUrl = `/admin/transports/${transport.id}/edit`;
                    const deleteUrl = `/admin/transports/${transport.id}`;
                    const deleteConfirmText = "Êtes-vous sûr de vouloir supprimer le transport " + transport.plaque_immatriculation + " ?";
                    const statusHtml = getStatusBadge(transport.status);

                    tableBody.innerHTML += `
                        <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                            <td class="py-4 px-6">${transport.id}</td>
                            <td class="py-4 px-6 font-semibold">${transport.marque}</td>
                            <td class="py-4 px-6">${transport.modele}</td>
                            <td class="py-4 px-6">${transport.plaque_immatriculation}</td>
                            <td class="py-4 px-6">${transport.capacite_passagers}</td>
                            <td class="py-4 px-6">${statusHtml}</td>
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                <a href="${showUrl}" 
                                   class="text-[#F16522] font-bold hover:text-[#346693] transition-colors duration-200">
                                    👁️ Voir
                                </a>
                                <a href="${editUrl}" 
                                   class="ml-4 text-[#F16522] font-bold hover:text-[#346693] transition-colors duration-200">
                                    ✏️ Modifier
                                </a>
                                <form action="${deleteUrl}" method="POST" class="inline-block ml-4" onsubmit="return confirm('${deleteConfirmText}')">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" 
                                            class="text-gray-400 font-bold hover:text-[#F16522] transition-colors duration-200 focus:outline-none">
                                        🗑️ Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>`;
                });
            })
            .catch(err => console.error('Erreur AJAX:', err));
    };

    // Attache les écouteurs d'événements
    // La recherche se déclenche après 300ms de pause dans la frappe
    searchInput.addEventListener('input', debounce(performSearch, 300));
    // Le filtre se déclenche immédiatement au changement de sélection
    statusFilter.addEventListener('change', performSearch);
});
</script>
@endsection