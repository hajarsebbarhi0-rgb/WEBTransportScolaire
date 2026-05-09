<x-guest-layout>

<div class="min-h-screen flex items-center justify-center px-4 bg-[#F0F2F5]">

    <div class="w-full max-w-lg bg-white p-10 rounded-2xl shadow-lg border border-gray-200 relative">

        <!-- Bande bleue supérieure comme dashboard -->
        <div class="absolute top-0 left-0 w-full h-3 bg-[#346693] rounded-t-2xl"></div>

        <!-- Icône -->
        <div class="flex justify-center mb-4 mt-4">
            <div class="w-16 h-16 flex items-center justify-center rounded-full bg-[#346693] text-white text-2xl shadow-md">
                🔐
            </div>
        </div>

        <!-- Titre -->
        <div class="text-center">
            <h2 class="text-3xl font-bold uppercase tracking-wide text-[#346693]">
                Réinitialisation
            </h2>
            <p class="mt-2 text-gray-500 text-sm">
                Définissez votre nouveau mot de passe sécurisé.
            </p>
        </div>

        <!-- Formulaire -->
        <form method="POST" action="{{ route('password.store') }}" class="mt-8 space-y-5">
            @csrf

            <!-- Token -->
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold text-[#346693]">
                    Adresse Email
                </label>
                <input type="email"
                       name="email"
                       value="{{ old('email', $request->email) }}"
                       required
                       class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-50
                       focus:outline-none focus:ring-2 focus:ring-[#346693] focus:border-[#346693] transition">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Nouveau mot de passe -->
            <div>
                <label class="block text-sm font-semibold text-[#346693]">
                    Nouveau mot de passe
                </label>
                <input type="password"
                       name="password"
                       required
                       class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-50
                       focus:outline-none focus:ring-2 focus:ring-[#346693] focus:border-[#346693] transition">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirmation -->
            <div>
                <label class="block text-sm font-semibold text-[#346693]">
                    Confirmer le mot de passe
                </label>
                <input type="password"
                       name="password_confirmation"
                       required
                       class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-50
                       focus:outline-none focus:ring-2 focus:ring-[#346693] transition">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Bouton style ADMIN BUS -->
            <div class="pt-4">
                <button type="submit"
                        class="w-full py-3 rounded-xl bg-[#F16522] text-white font-bold uppercase tracking-wide
                        shadow-md hover:shadow-xl hover:bg-[#d9541c] transition-all duration-300">
                    Réinitialiser le mot de passe
                </button>
            </div>

        </form>
    </div>
</div>

</x-guest-layout>