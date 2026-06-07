@extends('layouts.app')

@section('content')

<div class="py-8 px-4 bg-gray-50 min-h-screen">

    <div class="max-w-7xl mx-auto">

        <h1 class="text-center text-5xl font-extrabold text-[#F16522] mb-8">
            📊 Rapports & Statistiques
        </h1>

        {{-- KPI --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">

            <div class="bg-white rounded-xl shadow-lg p-6 hover:scale-105 transition duration-300">
                <h3 class="text-gray-500">Chauffeurs</h3>
                <p class="text-4xl font-bold text-[#346693]">
                    {{ $chauffeursCount }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 hover:scale-105 transition duration-300">
                <h3 class="text-gray-500">Élèves</h3>
                <p class="text-4xl font-bold text-[#346693]">
                    {{ $elevesCount }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 hover:scale-105 transition duration-300">
                <h3 class="text-gray-500">Transports</h3>
                <p class="text-4xl font-bold text-[#346693]">
                    {{ $transportsCount }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 hover:scale-105 transition duration-300">
                <h3 class="text-gray-500">Trajets</h3>
                <p class="text-4xl font-bold text-[#346693]">
                    {{ $trajetsCount }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 hover:scale-105 transition duration-300">
                <h3 class="text-gray-500">Bus actifs</h3>
                <p class="text-4xl font-bold text-green-600">
                    {{ $busActifs }}
                </p>
            </div>

        </div>
 {{-- Filtres dates --}}
<form method="GET" action="{{ route('admin.reports') }}" 
      class="bg-white rounded-xl shadow-lg p-5 mb-8 flex flex-wrap gap-4 items-end">
    
    <div class="flex flex-col">
        <label class="text-gray-500 text-sm mb-1">Date début</label>
        <input type="date" name="date_debut" 
               value="{{ $date_debut }}"
               class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#346693]">
    </div>

    <div class="flex flex-col">
        <label class="text-gray-500 text-sm mb-1">Date fin</label>
        <input type="date" name="date_fin" 
               value="{{ $date_fin }}"
               class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#346693]">
    </div>

    <button type="submit" 
            class="bg-[#F16522] text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
        🔍 Filtrer
    </button>

    <a href="{{ route('admin.reports') }}" 
       class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
        🔄 Réinitialiser
    </a>

</form>
        {{-- Ligne 1 --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            <div class="bg-white rounded-xl shadow-lg p-5">
                <h2 class="font-bold text-xl mb-3">🚍 Élèves par trajet</h2>
                <canvas id="elevesTrajetChart"></canvas>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-5">
                <h2 class="font-bold text-xl mb-3">❌ Absences par trajet</h2>
                <canvas id="absencesChart"></canvas>
            </div>

        </div>

        {{-- Ligne 2 --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            <div class="bg-white rounded-xl shadow-lg p-5">
                <h2 class="font-bold text-xl mb-3">✅ Présences par trajet</h2>
                <canvas id="presencesChart"></canvas>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-5">
                <h2 class="font-bold text-xl mb-3"> Top élèves absents</h2>
                <canvas id="elevesAbsentsChart"></canvas>
            </div>

        </div>

        {{-- Ligne 3 --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            <div class="bg-white rounded-xl shadow-lg p-5">
                <h2 class="font-bold text-xl mb-3">📈 Présences par mois</h2>
                <canvas id="presencesMoisChart"></canvas>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-5">
                <h2 class="font-bold text-xl mb-3">🚌 Utilisation des transports</h2>
                <canvas id="transportChart"></canvas>
            </div>

        </div>

       

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

Chart.defaults.animation = {
    duration: 2500
};

// Élèves par trajet
new Chart(document.getElementById('elevesTrajetChart'), {
    type:'bar',
    data:{
        labels:@json($elevesParTrajet->pluck('nom')),
        datasets:[{
            label:'Élèves',
            data:@json($elevesParTrajet->pluck('eleves_count')),
            backgroundColor:'#346693'
        }]
    }
});

// Absences
new Chart(document.getElementById('absencesChart'), {
    type:'bar',
    data:{
        labels:@json($absencesByTrajet->pluck('nom')),
        datasets:[{
            label:'Absences',
            data:@json($absencesByTrajet->pluck('absences_count')),
            backgroundColor:'#F16522'
        }]
    }
});

// Présences
// Présences par trajet
new Chart(document.getElementById('presencesChart'), {
    type: 'doughnut',
    data: {
        labels: @json($presencesByTrajet->pluck('nom')),
        datasets: [{
            data: @json($presencesByTrajet->pluck('presences_count')),
            backgroundColor: [
                '#346693','#F16522','#10b981','#f59e0b',
                '#8b5cf6','#ec4899','#06b6d4','#84cc16'
            ]
        }]
    }
});

// Top absents
new Chart(document.getElementById('elevesAbsentsChart'), {
    type:'bar',
    data:{
        labels:@json($elevesAbsents->pluck('nom')),
        datasets:[{
            data:@json($elevesAbsents->pluck('absences_count')),
            backgroundColor:'#dc2626'
        }]
    },
    options:{
        indexAxis:'y'
    }
});

// Présences par mois
new Chart(document.getElementById('presencesMoisChart'), {
    type:'line',
    data:{
        labels:[
            'Jan','Fev','Mar','Avr','Mai','Jun',
            'Jul','Aou','Sep','Oct','Nov','Dec'
        ],
        datasets:[{
            label:'Présences',
            data:@json($presencesParMois),
            borderColor:'#346693',
            tension:0.4
        }]
    }
});

// Utilisation des transports
new Chart(document.getElementById('transportChart'), {
    type:'pie',
    data:{
        labels:@json($utilisationTransports->pluck('plaque_immatriculation')),
        datasets:[{
          data:@json($utilisationTransports->pluck('trajets_count'))
        }]
    }
});

// Répartition globale


</script>

@endsection