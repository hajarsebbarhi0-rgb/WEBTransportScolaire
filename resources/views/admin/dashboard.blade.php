@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
    
    {{-- EN-TÊTE : Utilise le Bleu Principal (#346693) --}}
    <div class="text-center mb-12">
        <h1 class="text-4xl font-extrabold text-[#346693] sm:text-5xl uppercase tracking-wide">
            📊 Tableau de Bord du Système
        </h1>
        <p class="mt-3 text-lg text-gray-600">
            Accès rapide aux fonctionnalités clés de l'administration.
        </p>
    </div>

    {{-- GRILLE DE CARTES --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-8 justify-items-center">

        {{-- Définition des cartes (avec la nouvelle clé 'icon_key') --}}
        @php
            $cards = [
                // Ajout de 'icon_key' pour une association fiable
                ['title' => ' Gestion des Comptes', 'link' => route('admin.users.index'), 'text' => 'Gérer les comptes', 'icon_key' => 'comptes'],
                ['title' => ' Gestion des transports', 'link' => route('admin.transports.index'), 'text' => 'Gérer les véhicules', 'icon_key' => 'transports'],
                ['title' => ' Planification Trajets', 'link' => route('admin.trajets.index'), 'text' => 'Gérer les itinéraires', 'icon_key' => 'trajets'],
                ['title' => ' Rapports & Statistiques', 'link' => route('admin.reports'), 'text' => 'Consulter les rapports', 'icon_key' => 'rapports'],
                ['title' => ' Logs & Historique', 'link' => route('admin.historique.index'), 'text' => 'Consulter les historiques', 'icon_key' => 'historique'],
                ['title' => ' Suivi en Temps Réel', 'link' => route('admin.suivi'), 'text' => 'Ouvrir la carte', 'icon_key' => 'suivi'],
                [ 'title' => ' Gestion des Présences', 'link' => route('admin.presences'), 'text' => 'Voir les présences', 'icon_key' => 'presences'],

            ];

            // Mapping des icônes SVG utilisant les clés textuelles
            $iconMap = [
                // Icône Comptes
                'comptes' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.12a3 3 0 0 0 0 4.76" /><path d="M21 8.24a3 3 0 0 0 0 4.76" /></svg>',
                
                // Icône Transport
                'transports' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="7" cy="17" r="2" /><circle cx="17" cy="17" r="2" /><path d="M5 17h-2v-6l2 -5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0h-6m-6 -6h15m-6 0v-4m0 4h4" /></svg>',
                
                // Icône Trajets
                'trajets' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7l6 -3l6 3l6 -3v13l-6 3l-6 -3l-6 3v-13" /><path d="M9 4v13" /><path d="M15 7v13" /></svg>',
                
                // Icône Rapports
                'rapports' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><line x1="10" y1="13" x2="14" y2="17" /><polyline points="10 17 14 17 14 13" /></svg>',
                
                // Icône Historique
                'historique' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /><line x1="3" y1="6" x2="3" y2="19" /><line x1="12" y1="6" x2="12" y2="19" /><line x1="21" y1="6" x2="21" y2="19" /></svg>',
                // Icône SUIVI
                'suivi' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a7 7 0 00-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 00-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>',
                //icone presence 
                'presences' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
          viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4M7 7h10M7 11h10M7 15h6"/></svg>',

            ];
        @endphp

        @foreach ($cards as $card)
        {{-- CARTE : Design minimaliste, fond blanc, ombre légère, sans scale trop forte --}}
        <div class="bg-white shadow-md rounded-xl p-8 border border-gray-100 
                     transition duration-300 hover:shadow-xl hover:border-[#82D2F5]
                     flex flex-col items-center w-full max-w-sm">
            
            {{-- Icône circulaire : Utilise le Bleu Principal (#346693) pour le fond --}}
            <div class="flex items-center justify-center h-14 w-14 bg-[#346693] 
                         text-white rounded-full mb-6 shadow-lg">
                {{-- Utilisation de la nouvelle clé 'icon_key' --}}
                {!! $iconMap[$card['icon_key']] ?? $iconMap['comptes'] !!}
            </div>

            {{-- Titre en Bleu Principal (#346693) --}}
            <h2 class="text-xl font-bold text-[#346693] text-center mb-4">{{ $card['title'] }}</h2>
            
            {{-- Bouton : Utilise le Cyan Secondaire (#82D2F5). Survol vers le Bleu Principal (#346693) --}}
            <a href="{{ $card['link'] }}" 
               class="mt-4 inline-block bg-[#82D2F5] text-[#346693] font-bold px-6 py-3 rounded-full 
                      transition-colors duration-300 hover:bg-[#F16522] hover:text-white shadow-md">
                {{ $card['text'] }}
            </a>
        </div>
        @endforeach

    </div>
    
</div>
@endsection