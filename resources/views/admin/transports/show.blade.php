@extends('layouts.app')

@section('content')
{{-- Conteneur principal : Fond gris clair et texte principal en Bleu Principal --}}
<div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 text-[#346693] min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <h1 class="text-5xl font-extrabold text-center mb-12 uppercase tracking-tight font-serif text-[#346693]">
            🚍 Détails du Transport
        </h1>

        <div class="max-w-lg mx-auto bg-white rounded-2xl p-8 shadow-xl border border-gray-200 border-t-4 border-t-[#346693]">

            <div class="p-0 space-y-4 text-lg text-gray-700">
                
                {{-- Nom/Modèle du transport en haut --}}
                <div class="border-b pb-4 mb-4 text-2xl font-bold text-[#346693] text-center border-b-[#346693]/30">
                    {{ $transport->marque }} {{ $transport->modele }}
                </div>

                {{-- Détails --}}
                <div class="flex justify-between border-b border-gray-100 py-3">
                    <span class="font-semibold text-[#346693]">Marque :</span>
                    <span class="font-medium">{{ $transport->marque }}</span>
                </div>
                
                <div class="flex justify-between border-b border-gray-100 py-3">
                    <span class="font-semibold text-[#346693]">Modèle :</span>
                    <span class="font-medium">{{ $transport->modele }}</span>
                </div>
                
                <div class="flex justify-between border-b border-gray-100 py-3">
                    <span class="font-semibold text-[#346693]">Plaque d'immatriculation :</span>
                    {{-- Utilisation de l'Orange Accent (#F16522) --}}
                    <span class="px-3 py-1 rounded-full text-sm font-bold bg-[#F16522] text-white shadow-sm">
                        {{ $transport->plaque_immatriculation }}
                    </span>
                </div>
                
                <div class="flex justify-between py-3">
                    <span class="font-semibold text-[#346693]">Capacité des passagers :</span>
                    {{-- Utilisation du Bleu Principal (#346693) --}}
                    <span class="px-3 py-1 rounded-full text-sm font-bold bg-[#346693] text-white shadow-sm">
                        {{ $transport->capacite_passagers }}
                    </span>
                </div>
            </div>
        </div>

        <div class="max-w-lg mx-auto flex justify-between mt-8">
            {{-- Retour : Texte en Bleu Principal, border en Bleu Principal, hover en Cyan Secondaire --}}
            <a href="{{ route('admin.transports.index') }}" 
                class="px-6 py-3 rounded-full font-semibold border border-[#346693] text-[#346693] hover:bg-[#82D2F5] hover:text-[#346693] transition duration-300 shadow-md">
                ⬅ Retour à la liste
            </a>

            {{-- Modifier : Fond Bleu Principal, hover en Cyan Secondaire --}}
            <a href="{{ route('admin.transports.edit', $transport->id) }}" 
                class="px-6 py-3 rounded-full font-semibold bg-[#346693] text-white hover:bg-[#82D2F5] hover:text-[#346693] transition duration-300 shadow-lg transform hover:scale-105">
                ✏ Modifier
            </a>
        </div>
    </div>
</div>
@endsection