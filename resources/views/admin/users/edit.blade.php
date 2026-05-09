@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl mx-auto rounded-xl shadow-lg p-8 sm:p-10 bg-white border border-gray-200">

        <h1 class="text-3xl sm:text-4xl font-extrabold mb-8 text-center text-[#346693] uppercase tracking-wide">
            ✏️ Modifier un Compte
        </h1>

        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- ================= USER ================= --}}

            <div class="mb-5">
                <label class="block font-bold mb-2 text-[#346693]">Nom</label>
                <input type="text" name="nom"
                       value="{{ old('nom', $user->nom) }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300" required>
            </div>

            <div class="mb-5">
                <label class="block font-bold mb-2 text-[#346693]">Prénom</label>
                <input type="text" name="prenom"
                       value="{{ old('prenom', $user->prenom) }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300" required>
            </div>

            <div class="mb-5">
                <label class="block font-bold mb-2 text-[#346693]">Email</label>
                <input type="email" name="email"
                       value="{{ old('email', $user->email) }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300" required>
            </div>

            <div class="mb-5">
                <label class="block font-bold mb-2 text-[#346693]">Téléphone</label>
                <input type="text" name="telephone"
                       value="{{ old('telephone', $user->telephone) }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300">
            </div>

            <div class="mb-8">
                <label class="block font-bold mb-2 text-[#346693]">Rôle</label>
                <select name="role" id="role"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white" required>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="chauffeur" {{ $user->role == 'chauffeur' ? 'selected' : '' }}>Chauffeur</option>
                    <option value="eleve" {{ $user->role == 'eleve' ? 'selected' : '' }}>Élève</option>
                </select>
            </div>

            {{-- ================= INFOS ÉLÈVE ================= --}}
            @if($user->role === 'eleve')

            <hr class="my-8">

            <h2 class="text-2xl font-bold mb-6 text-center text-[#346693]">
                🎓 Informations Élève
            </h2>

            <div class="mb-5">
                <label class="block font-bold mb-2 text-[#346693]">Date de naissance</label>
                <input type="date" name="date_de_naissance"
                       value="{{ old('date_de_naissance', $user->eleve->date_de_naissance ?? '') }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300">
            </div>

            <div class="mb-5">
                <label class="block font-bold mb-2 text-[#346693]">Genre</label>
                <select name="genre" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                    <option value="">-- Choisir --</option>
                    <option value="garçon" {{ old('genre', $user->eleve->genre ?? '') == 'garçon' ? 'selected' : '' }}>Garçon</option>
                    <option value="fille" {{ old('genre', $user->eleve->genre ?? '') == 'fille' ? 'selected' : '' }}>Fille</option>
                </select>
            </div>

            <div class="mb-5">
                <label class="block font-bold mb-2 text-[#346693]">École</label>
                <input type="text" name="ecole"
                       value="{{ old('ecole', $user->eleve->ecole ?? '') }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300">
            </div>

            <div class="mb-5">
                <label class="block font-bold mb-2 text-[#346693]">Niveau</label>
                <input type="text" name="niveau"
                       value="{{ old('niveau', $user->eleve->niveau ?? '') }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300">
            </div>

            <div class="mb-5">
                <label class="block font-bold mb-2 text-[#346693]">Adresse</label>
                <input type="text" name="adresse"
                       value="{{ old('adresse', $user->eleve->adresse ?? '') }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300">
            </div>

            <div class="mb-8">
                <label class="block font-bold mb-2 text-[#346693]">Code Trajet</label>
                <input type="text" name="code_trajet"
                       value="{{ old('code_trajet', $user->eleve->trajet->code_trajet ?? '') }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300">
            </div>

            @endif

            {{-- ================= SUBMIT ================= --}}
            <div class="flex justify-center">
                <button type="submit"
                        class="bg-[#F16522] text-white font-bold py-3 px-8 rounded-full hover:bg-[#346693] transition">
                    💾 Enregistrer
                </button>
            </div>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('admin.users.index') }}"
               class="text-[#346693] hover:text-[#82D2F5] font-semibold">
                ← Retour à la liste
            </a>
        </div>
    </div>
</div>
@endsection
