<x-guest-layout>

<div class="min-h-screen flex items-center justify-center px-4 bg-[#F0F2F5]">

    <div class="w-full max-w-lg bg-white p-10 rounded-2xl shadow-lg border border-gray-200 relative">

        <!-- Bande bleue supérieure -->
        <div class="absolute top-0 left-0 w-full h-3 bg-[#346693] rounded-t-2xl"></div>

        <!-- Icône centrale -->
        <div class="flex justify-center mt-4 mb-4">
            <div class="w-16 h-16 flex items-center justify-center rounded-full bg-[#346693] text-white text-2xl shadow-md">
                🔒
            </div>
        </div>

        <!-- Titre -->
        <div class="text-center">
            <h2 class="text-3xl font-bold uppercase tracking-wide text-[#346693]">
                Confirmation requise
            </h2>

            <p class="mt-2 text-gray-500 text-sm leading-relaxed">
                Cette section est sécurisée.<br>
                Veuillez confirmer votre mot de passe pour continuer.
            </p>
        </div>

        <!-- Formulaire -->
        <form method="POST" action="{{ route('password.confirm') }}" class="mt-8 space-y-6">
            @csrf

            <!-- Mot de passe -->
            <div>
                <label class="block text-sm font-semibold text-[#346693]">
                    Mot de passe
                </label>

                <input type="password"
                       name="password"
                       required
                       autocomplete="current-password"
                       class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-50
                              focus:outline-none focus:ring-2 focus:ring-[#346693]
                              focus:border-[#346693] transition-all duration-200">

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Bouton ADMIN BUS -->
            <div class="pt-4">
                <button type="submit"
                        class="w-full py-3 rounded-xl bg-[#F16522] text-white font-bold uppercase tracking-wide
                               shadow-md hover:shadow-xl hover:bg-[#d9541c]
                               transition-all duration-300 transform hover:scale-[1.02]">
                    Confirmer
                </button>
            </div>
        </form>

    </div>
</div>

</x-guest-layout>