@extends('layouts.app')

@section('content')

<div class="container mx-auto px-6 py-8">

    <!-- ============================= -->
    <!-- Titre -->
    <!-- ============================= -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-[#346693]">
            Gestion des Présences des Élèves
        </h1>
        <p class="text-gray-500 mt-2">
            Suivi des présences par période
        </p>
    </div>

    <!-- ============================= -->
    <!-- FORMULAIRE FILTRE (TOUT ENSEMBLE) -->
    <!-- ============================= -->
    <form method="GET" class="flex flex-wrap gap-3 mb-8 justify-center items-center">

        <!-- 🔍 Recherche élève -->
        <input
            type="text"
            name="search"
            value="{{ $search }}"
            placeholder="Nom ou prénom élève"
            class="border p-2 rounded focus:outline-none focus:ring-2 focus:ring-[#346693]"
        >

        <!-- 🚍 Filtre trajet -->
        <select name="trajet_id" class="border p-2 rounded focus:outline-none focus:ring-2 focus:ring-[#346693]">
            <option value="">Tous les trajets</option>
            @foreach($allTrajets as $t)
                <option value="{{ $t->id }}" {{ $trajet_id == $t->id ? 'selected' : '' }}>
                    {{ $t->nom }}
                </option>
            @endforeach
        </select>

        <!-- 📅 Filtre dates -->
        <input type="date" name="date_debut" value="{{ $date_debut }}" class="border p-2 rounded focus:outline-none focus:ring-2 focus:ring-[#346693]">
        <input type="date" name="date_fin" value="{{ $date_fin }}" class="border p-2 rounded focus:outline-none focus:ring-2 focus:ring-[#346693]">

        <button type="submit" class="bg-[#346693] text-white px-4 py-2 rounded hover:bg-[#295578] transition">
            Filtrer
        </button>

        <!-- 🔄 Réinitialiser -->
        <a href="{{ route('admin.presences') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">
            Réinitialiser
        </a>

    </form>

    <!-- ============================= -->
    <!-- TRAJETS -->
    <!-- ============================= -->
    @forelse($trajets as $trajet)

        @if($trajet->eleves->count() > 0)

        <div class="bg-white shadow-lg rounded-xl border mb-10">

            <!-- Header trajet -->
            <div class="bg-[#f4f8fb] border-b px-6 py-4">
                <h2 class="text-xl font-bold text-[#F16522]">
                    Trajet : {{ $trajet->nom ?? 'Trajet #' . $trajet->id }}
                </h2>
            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-center border-collapse">

                    <!-- HEADER -->
                    <thead>
                        <tr class="bg-[#82D2F5] text-[#346693]">
                            <th class="p-3 text-left">Élève</th>

                            @foreach($dates as $date)
                                <th colspan="2" class="p-3 border-l border-white">
                                    {{ \Carbon\Carbon::parse($date)->format('d/m') }}
                                </th>
                            @endforeach
                        </tr>

                        <tr class="bg-[#eaf6fd] text-gray-600 text-xs">
                            <th class="p-2"></th>

                            @foreach($dates as $date)
                                <th class="p-2">Matin</th>
                                <th class="p-2">Soir</th>
                            @endforeach
                        </tr>
                    </thead>

                    <!-- BODY -->
                    <tbody>
                        @foreach($trajet->eleves as $eleve)

                        <tr class="border-b hover:bg-gray-50">

                            <!-- Nom élève -->
                            <td class="text-left p-3 font-semibold text-gray-700 whitespace-nowrap">
                                {{ $eleve->prenom }} {{ $eleve->nom }}
                            </td>

                            <!-- DATES -->
                            @foreach($dates as $date)

                                @php
                                    $matin = $data[$trajet->id][$eleve->id][$date]['matin'] ?? ['presence' => null, 'absence' => null];
                                    $soir  = $data[$trajet->id][$eleve->id][$date]['soir']  ?? ['presence' => null, 'absence' => null];
                                @endphp

                                <!-- MATIN -->
                                <td class="p-2 border-l">
                                    @if($matin['presence'])
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700">
                                            Présent
                                        </span>
                                    @elseif($matin['absence'])
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700">
                                            Absence
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                <!-- SOIR -->
                                <td class="p-2">
                                    @if($soir['presence'])
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700">
                                            Présent
                                        </span>
                                    @elseif($soir['absence'])
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700">
                                            Absence
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                            @endforeach

                        </tr>

                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>

        @endif

    @empty
        <div class="text-center text-gray-400 py-16">
            <p class="text-xl">Aucun résultat trouvé.</p>
            <p class="text-sm mt-2">Essayez de modifier vos filtres.</p>
        </div>
    @endforelse

</div>

@endsection