<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bienvenue</title>
        
        {{-- J'ai conservé le lien de police générique pour la compatibilité avec votre environnement, mais les classes Tailwind utilisent une police sans-serif moderne --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        
        <style>
            :root {
                --primary-blue: #346693;
                --secondary-cyan: #82D2F5;
                --accent-orange: #F16522;
                --background-light: #F0F2F5;
            }
            
            body {
                background-color: var(--background-light);
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-30px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fadeIn {
                animation: fadeIn 1s ease-out;
            }
        </style>
    </head>
    
    {{-- Utilisation des couleurs de la palette --}}
    <body class="antialiased font-sans text-gray-800">
        <div class="relative min-h-screen flex items-center justify-center selection:bg-[#346693] selection:text-white">
            
            @if (Route::has('login'))
                {{-- LIENS D'AUTHENTIFICATION (Haut droite) --}}
                <div class="absolute top-0 right-0 p-6 z-20">
                    @auth
                        <a href="{{ url('/dashboard') }}" 
                           class="font-semibold text-[#346693] hover:text-[#F16522] transition-colors duration-300 px-3 py-2 rounded-lg bg-white shadow-md">
                            Tableau de bord 🏠
                        </a>
                    @else
                        {{-- Si non authentifié, le lien "Se connecter" sera répété sur le bouton principal, mais gardons cette structure pour les autres liens --}}
                        <a href="{{ route('login') }}" 
                           class="font-semibold text-[#346693] hover:text-[#F16522] transition-colors duration-300 px-3 py-2 rounded-lg bg-white shadow-md">
                            Se connecter
                        </a>
                    @endauth
                    
                    @if (Route::has('register'))
                         {{-- Si l'inscription est activée (non dans la palette, mais souvent présent) --}}
                         <a href="{{ route('register') }}" 
                            class="ml-4 font-semibold text-[#F16522] hover:text-[#346693] transition-colors duration-300 px-3 py-2 rounded-lg bg-white shadow-md">
                            S'inscrire
                        </a>
                    @endif
                </div>
            @endif

            {{-- CARTE DE BIENVENUE CENTRALE --}}
            <div class="relative z-10 p-10 bg-white rounded-2xl shadow-2xl max-w-lg w-full text-center animate-fadeIn 
                        border-t-8 border-[#82D2F5] transition duration-500 hover:shadow-cyan-400/50">
                
                <div class="flex justify-center mb-6">
                    <!-- Logo : Couleur Cyan #82D2F5 -->
                    <svg class="w-16 h-16 text-[#82D2F5]" fill="currentColor" viewBox="0 0 20 20">
                         {{-- Icône de Bus --}}
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm14 2a1 1 0 011 1v7a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1h14zm-4 7a1 1 0 100-2 1 1 0 000 2zM9 14a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                    </svg>
                </div>
                
                {{-- Titre : Couleur Bleu Principal #346693 --}}
                <h1 class="text-5xl font-extrabold tracking-tight sm:text-6xl text-[#346693] uppercase">
                    Bienvenue!
                </h1>
                
                <p class="mt-4 text-xl text-[#346693]/70 font-medium">
                    Suivez le trajet de vos transports scolaires en toute simplicité.
                </p>

                <div class="mt-10">
                    @auth
                        {{-- Bouton : Orange Accent #F16522 --}}
                        <a href="{{ url('/dashboard') }}" 
                           class="inline-block px-10 py-4 text-xl font-extrabold text-white bg-[#F16522] rounded-full shadow-lg hover:bg-[#346693] transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                            Aller au tableau de bord ➡️
                        </a>
                    @else
                        {{-- Bouton : Orange Accent #F16522 --}}
                        <a href="{{ route('login') }}" 
                           class="inline-block px-10 py-4 text-xl font-extrabold text-white bg-[#F16522] rounded-full shadow-lg hover:bg-[#346693] transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                            Se connecter 🔑
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </body>
</html>