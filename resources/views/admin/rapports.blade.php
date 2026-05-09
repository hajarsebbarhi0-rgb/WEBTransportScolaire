@extends('layouts.app')

@section('content')
<div class="py-10 px-4 sm:px-6 lg:px-8 bg-gray-50 text-[#346693] min-h-screen">
    <div class="max-w-7xl mx-auto">

        {{-- TITRE --}}
        <div class="text-center mb-10">
            <h1 class="text-5xl font-extrabold uppercase tracking-tight font-serif text-[#F16522]">
                📊 Rapports et Statistiques
            </h1>
        </div>

        <hr class="border-[#346693]/20 my-6">

        {{-- STATISTIQUES GÉNÉRALES --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-xl p-5 border border-[#346693]/20 border-t-4 border-t-[#346693]">
                <h2 class="text-xl font-bold mb-3 text-[#346693]">Statistiques générales</h2>
                <div class="space-y-3">
                    <p class="flex justify-between text-lg"><span>Chauffeurs</span><span class="font-extrabold text-2xl text-[#F16522]">{{ $chauffeursCount }}</span></p>
                    <p class="flex justify-between text-lg"><span>Élèves</span><span class="font-extrabold text-2xl text-[#F16522]">{{ $elevesCount }}</span></p>
                    <p class="flex justify-between text-lg"><span>Transports</span><span class="font-extrabold text-2xl text-[#F16522]">{{ $transportsCount }}</span></p>
                    <p class="flex justify-between text-lg"><span>Trajets</span><span class="font-extrabold text-2xl text-[#F16522]">{{ $trajetsCount }}</span></p>
                </div>
            </div>
        </div>

        {{-- GRAPHIQUE ABSENCES --}}
        <div class="bg-white rounded-xl shadow-xl p-5 border border-[#346693]/20 border-t-4 border-t-[#346693] mb-6">
            <h2 class="text-xl font-bold mb-3 text-[#346693]">Absences par trajet</h2>
            <canvas id="absencesChart" class="w-full h-40"></canvas>
        </div>

        {{-- GRAPHIQUE PRÉSENCES --}}
        <div class="bg-white rounded-xl shadow-xl p-5 border border-[#346693]/20 border-t-4 border-t-[#346693] mb-6">
            <h2 class="text-xl font-bold mb-3 text-[#346693]">Présences par trajet</h2>
            <canvas id="presencesChart" class="w-full h-40"></canvas>
        </div>

    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const absencesCtx = document.getElementById('absencesChart').getContext('2d');
new Chart(absencesCtx, {
    type: 'bar',
    data: {
        labels: @json($absencesByTrajet->pluck('nom')),
        datasets: [{ label: 'Nombre d\'absences', data: @json($absencesByTrajet->pluck('absences_count')), backgroundColor: '#F16522' }]
    },
    options: { responsive:true, scales:{y:{beginAtZero:true}}, plugins:{legend:{display:false}} }
});

const presencesCtx = document.getElementById('presencesChart').getContext('2d');
new Chart(presencesCtx, {
    type: 'bar',
    data: {
        labels: @json($presencesByTrajet->pluck('nom')),
        datasets: [{ label: 'Nombre de présences', data: @json($presencesByTrajet->pluck('presences_count')), backgroundColor: '#82D2F5' }]
    },
    options: { responsive:true, scales:{y:{beginAtZero:true}}, plugins:{legend:{display:false}} }
});
</script>
@endsection