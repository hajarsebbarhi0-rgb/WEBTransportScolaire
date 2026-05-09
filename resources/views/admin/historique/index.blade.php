@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
    
    {{-- EN-TÊTE : Titre en Bleu Principal #346693 --}}
    <div class="mb-10">
        <h1 class="text-4xl font-extrabold text-[#F16522] sm:text-5xl uppercase tracking-wider font-serif">
            📜 Historique des Opérations
        </h1>
        <p class="mt-2 text-lg text-gray-500 font-sans">
            Journal de toutes les actions effectuées par les administrateurs et utilisateurs.
        </p>
    </div>

    {{-- TABLEAU PRINCIPAL --}}
    <div class="bg-white shadow-2xl rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                {{-- En-tête de Tableau : Utilise la couleur principale #346693 --}}
                <thead>
                    <tr class="bg-[#346693] text-white uppercase text-sm font-semibold tracking-wider shadow-md">
                        <th class="py-4 px-6 text-left">Utilisateur</th>
                        <th class="py-4 px-6 text-center">Action</th>
                        <th class="py-4 px-6 text-left">Modèle Ciblé</th>
                        <th class="py-4 px-6 text-left">Description</th>
                        <th class="py-4 px-6 text-left">Date & Heure</th>
                    </tr>
                </thead>
                <tbody class="text-[#346693] text-base font-sans divide-y divide-gray-100">
                    @forelse($historiques as $h)
                    {{-- Effet de survol Cyan #82D2F5 --}}
                    <tr class="hover:bg-[#82D2F5]/10 transition-colors duration-200">
                        <td class="px-6 py-3 font-semibold text-[#346693]">{{ $h->user->nom }} {{ $h->user->prenom }}</td>
                        
                        {{-- Utilisation des couleurs pour catégoriser l'action --}}
                        <td class="px-6 py-3 text-center">
                            @php
                                $action = strtolower($h->action);
                                $badgeClass = '';
                                if (str_contains($action, 'création') || str_contains($action, 'créer')) {
                                    // Action Positive -> Cyan Secondaire
                                    $badgeClass = 'bg-[#82D2F5]/30 text-[#346693] border-2 border-[#82D2F5]';
                                } elseif (str_contains($action, 'modification') || str_contains($action, 'éditer')) {
                                    // Action Neutre -> Bleu Principal
                                    $badgeClass = 'bg-[#346693]/20 text-[#346693] border-2 border-[#346693]/50';
                                } elseif (str_contains($action, 'suppression') || str_contains($action, 'supprimer')) {
                                    // Action Critique -> Orange Accent
                                    $badgeClass = 'bg-[#F16522]/30 text-[#F16522] border-2 border-[#F16522]';
                                } else {
                                    // Autre
                                    $badgeClass = 'bg-gray-200 text-gray-700';
                                }
                            @endphp
                            
                            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $badgeClass }} uppercase tracking-wider">
                                {{ ucfirst($h->action) }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-3">{{ $h->model_type }}</td>
                        <td class="px-6 py-3 text-gray-600 italic">{{ $h->description }}</td>
                        <td class="px-6 py-3 text-sm font-mono text-gray-500">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center italic text-[#346693]/70 bg-gray-50">
                            Aucune opération enregistrée dans l'historique pour le moment.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-8">
        {{ $historiques->links('pagination::tailwind') }} 
        {{-- Utilisation du style de pagination Tailwind par défaut (si Laravel le supporte) --}}
    </div>
</div>
@endsection