<x-guest-layout>

<div class="min-h-screen flex items-center justify-center px-4 bg-[#F0F2F5]">

    <div class="w-full max-w-lg bg-white p-10 rounded-2xl shadow-lg border border-gray-200 relative">

        <!-- Bande bleue supérieure -->
        <div class="absolute top-0 left-0 w-full h-3 bg-[#346693] rounded-t-2xl"></div>

        <!-- Icône -->
        <div class="flex justify-center mt-4 mb-4">
            <div class="w-16 h-16 flex items-center justify-center rounded-full bg-[#346693] text-white text-2xl shadow-md">
                📧
            </div>
        </div>

        <!-- Titre -->
        <div class="text-center">
            <h2 class="text-3xl font-bold uppercase tracking-wide text-[#346693]">
                Vérification Email
            </h2>

            <p class="mt-3 text-gray-500 text-sm leading-relaxed">
                Merci pour votre inscription.<br>
                Veuillez vérifier votre adresse email en cliquant sur le lien envoyé.<br>
                Si vous ne l'avez pas reçu, vous pouvez demander un nouveau lien.
            </p>
        </div>

        <!-- Message succès modernisé -->
        @if (session('status') == 'verification-link-sent')
            <div class="mt-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm text-center shadow-sm">
                ✅ Un nouveau lien de vérification a été envoyé.
            </div>
        @endif

        <!-- Actions -->
        <div class="mt-8 space-y-4">

            <!-- Resend -->
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <button type="submit"
                        class="w-full py-3 rounded-xl bg-[#F16522] text-white font-bold uppercase tracking-wide
                               shadow-md hover:shadow-xl hover:bg-[#d9541c]
                               transition-all duration-300 transform hover:scale-[1.02]">
                    Renvoyer le lien
                </button>
            </form>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                        class="w-full text-center text-sm font-semibold text-[#346693]
                               hover:text-[#F16522] transition-colors duration-200">
                    Se déconnecter
                </button>
            </form>

        </div>

    </div>
</div>

</x-guest-layout>