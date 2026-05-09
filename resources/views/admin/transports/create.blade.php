@extends('layouts.app')

@section('content')
{{-- Conteneur principal : Fond gris clair et texte principal en Bleu Principal --}}
<div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 text-[#346693] min-h-screen">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-5xl font-extrabold text-center mb-12 uppercase tracking-tight font-serif text-[#346693]">
            ➕ Ajouter un Transport
        </h1>

        <div class="bg-white rounded-2xl p-8 max-w-2xl mx-auto shadow-xl border border-gray-200 border-t-4 border-t-[#346693]">
            <form action="{{ route('admin.transports.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="marque" class="block text-sm font-semibold mb-1 text-[#346693]">🚘 Marque</label>
                    <input type="text" id="marque" name="marque" 
                            class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition duration-200 placeholder-gray-500"
                            placeholder="Ex : Mercedes, Renault..." required>
                </div>

                <div>
                    <label for="modele" class="block text-sm font-semibold mb-1 text-[#346693]">📌 Modèle</label>
                    <input type="text" id="modele" name="modele" 
                            class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition duration-200 placeholder-gray-500"
                            placeholder="Ex : Sprinter, Clio..." required>
                </div>

                <div>
                    <label for="plaque_immatriculation" class="block text-sm font-semibold mb-1 text-[#346693]">🔖 Plaque d'immatriculation</label>
                    <input type="text" id="plaque_immatriculation" name="plaque_immatriculation" 
                            class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition duration-200 placeholder-gray-500"
                            placeholder="Ex : 1234-AB-56" required>
                </div>

                <div>
                    <label for="capacite_passagers" class="block text-sm font-semibold mb-1 text-[#346693]">👥 Capacité des passagers</label>
                    <input type="number" id="capacite_passagers" name="capacite_passagers" 
                            class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition duration-200 placeholder-gray-500"
                            placeholder="Ex : 50" required>
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold mb-1 text-[#346693]">⚙️ État</label>
                    <select name="status" id="status" 
                            class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:ring-4 focus:ring-[#82D2F5]/50 transition duration-200 appearance-none bg-white"
                            required>
                        <option value="disponible">✅ Disponible</option>
                        <option value="en_service">🚍 En service</option>
                        <option value="en_maintenance">🛠️ En maintenance</option>
                    </select>
                </div>

                <div class="flex justify-between items-center pt-6">
                    {{-- Annuler : Texte en Bleu Principal, hover en Cyan Secondaire --}}
                    <a href="{{ route('admin.transports.index') }}" 
                        class="px-6 py-3 rounded-full font-semibold border border-[#346693] text-[#346693] hover:bg-[#82D2F5] hover:text-[#346693] transition duration-300 shadow-md">
                        ❌ Annuler
                    </a>
                    
                    {{-- Enregistrer : Fond Bleu Principal, hover en Cyan Secondaire --}}
                    <button type="submit" 
                            class="px-6 py-3 rounded-full font-semibold bg-[#346693] text-white hover:bg-[#82D2F5] hover:text-[#346693] transition duration-300 shadow-lg transform hover:scale-105">
                        💾 Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection