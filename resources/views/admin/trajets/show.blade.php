@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-10">

    {{-- TITRE --}}
    <div class="mb-10 text-center">
        <h1 class="text-4xl font-extrabold text-[#346693] mb-2">
            🚍 Détails du Trajet
        </h1>
        <p class="text-gray-500">
            Code : <span class="font-semibold">{{ $trajet->code_trajet }}</span>
        </p>
    </div>

    {{-- INFOS GÉNÉRALES --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">

        <div class="bg-white shadow-lg rounded-xl p-6 border-l-4 border-[#346693]">
            <h2 class="text-xl font-bold text-[#346693] mb-4">📌 Informations</h2>
            <ul class="space-y-2 text-gray-700">
                <li><strong>Nom :</strong> {{ $trajet->nom }}</li>
                <li><strong>Description :</strong> {{ $trajet->description ?? '—' }}</li>
                <li><strong>Début :</strong> {{ $trajet->debut }}</li>
                <li><strong>Fin :</strong> {{ $trajet->fin }}</li>
            </ul>
        </div>

        <div class="bg-white shadow-lg rounded-xl p-6 border-l-4 border-[#F16522]">
            <h2 class="text-xl font-bold text-[#F16522] mb-4">🧑‍✈️ Affectation</h2>
            <ul class="space-y-2 text-gray-700">
                <li>
                    <strong>Chauffeur :</strong>
                    {{ $trajet->chauffeur->nom ?? '—' }}
                    {{ $trajet->chauffeur->prenom ?? '' }}
                </li>
                <li>
                    <strong>Transport :</strong>
                    {{ $trajet->transport->matricule ?? '—' }}
                </li>
            </ul>
        </div>

    </div>

    {{-- ARRÊTS --}}
    <div class="bg-white shadow-lg rounded-xl p-6 mb-10">
        <h2 class="text-2xl font-bold text-[#346693] mb-6">
            📍 Arrêts du trajet
        </h2>

        @if($trajet->arrets->count() > 0)
            <ol class="relative border-l border-gray-300 ml-4 space-y-6">
                @foreach($trajet->arrets as $arret)
                    <li class="ml-6">
                        <span class="absolute -left-3 flex items-center justify-center w-6 h-6 bg-[#346693] rounded-full text-white font-bold">
                            {{ $arret->order_number }}
                        </span>
                        <h3 class="font-semibold text-lg text-gray-800">
                            {{ $arret->nom }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            Lat : {{ $arret->latitude ?? '—' }},
                            Lng : {{ $arret->longitude ?? '—' }}
                        </p>
                    </li>
                @endforeach
            </ol>
        @else
            <p class="text-gray-500 italic">Aucun arrêt défini.</p>
        @endif
    </div>

    {{-- ÉLÈVES --}}
    <div class="bg-white shadow-lg rounded-xl p-6 mb-10">
        <h2 class="text-2xl font-bold text-[#346693] mb-6">
            👨‍🎓 Élèves affectés au trajet
        </h2>

        @if($trajet->eleves->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-[#346693] text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">Nom</th>
                            <th class="px-4 py-3 text-left">Prénom</th>
                            <th class="px-4 py-3 text-left">Niveau</th>
                            <th class="px-4 py-3 text-left">Adresse</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($trajet->eleves as $eleve)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-semibold">
                                    {{ $eleve->user->nom ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $eleve->user->prenom ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $eleve->niveau ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $eleve->adresse ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 italic">
                Aucun élève affecté à ce trajet.
            </p>
        @endif
    </div>

    {{-- BOUTONS --}}
    <div class="flex justify-center gap-4">
        <a href="{{ route('admin.trajets.index') }}"
           class="px-6 py-3 rounded-full bg-gray-200 text-gray-800 font-semibold hover:bg-gray-300 transition">
            ⬅ Retour
        </a>

        <a href="{{ route('admin.trajets.edit', $trajet) }}"
           class="px-6 py-3 rounded-full bg-[#F16522] text-white font-semibold hover:bg-[#346693] transition">
            ✏️ Modifier
        </a>
    </div>

</div>
@endsection
