<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi_Bus_Scolaire</title>
    
    {{-- Utilisation d'une police moderne simulant Bahnschrift / Impact (ex: Oswald ou Roboto Condensed) --}}
    {{-- J'ajoute Oswald ici pour simuler le style condensé d'Impact/Bahnschrift --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Définition stricte des couleurs de la palette en variables CSS pour la cohérence */
        :root {
            --primary-blue: #346693;
            --secondary-cyan: #82D2F5;
            --accent-orange: #F16522;
            --background-light: #F0F2F5; /* Même fond que dans la vue précédente */
        }
        
        body {
            /* Utilise la police Oswald pour simuler l'effet condensé/impact */
            font-family: 'Oswald', sans-serif;
            background-color: var(--background-light);
        }
        
        /* Style des liens de navigation desktop */
        .nav-link {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            /* Utilise le cyan pour le fond au survol */
            background-color: transparent; 
        }
        
        /* Barre de survol inférieure (Couleur Orange) */
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px; /* Plus visible */
            background-color: var(--accent-orange);
            transform: scaleX(0);
            transform-origin: bottom right;
            transition: transform 0.3s ease-out;
        }
        
        /* Active l'animation au survol */
        .nav-link:hover::after, .nav-link.active::after {
            transform: scaleX(1);
            transform-origin: bottom left;
        }

        /* Fond Cyan au survol pour un effet moderne */
        .nav-link:hover {
            background-color: var(--secondary-cyan);
            color: var(--primary-blue);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="antialiased">

{{-- NAVIGATION : Fond Bleu Principal #346693 --}}
<nav class="bg-[#346693] shadow-2xl sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20"> {{-- Hauteur augmentée --}}
            
            {{-- Logo / titre --}}
            <div class="flex-shrink-0 text-white font-extrabold text-3xl tracking-widest uppercase">
                Admin <span class="text-[#82D2F5]">BUS</span>
            </div>

            {{-- Liens du menu (desktop) --}}
            <div class="hidden md:flex space-x-2 items-center">
                {{-- ✅ Dashboard sélectionné par défaut --}}
                <a href="{{ route('dashboard') }}" 
                   class="nav-link active text-white px-4 py-3 rounded-lg font-medium tracking-wider">
                     DASHBOARD
                </a>
                <a href="{{ route('admin.users.index') }}" 
                   class="nav-link text-white px-4 py-3 rounded-lg font-medium tracking-wider">
                     COMPTES
                </a>
                <a href="{{ route('admin.transports.index') }}" 
                   class="nav-link text-white px-4 py-3 rounded-lg font-medium tracking-wider">
                    TRANSPORTS
                </a>
                <a href="{{ route('admin.reports') }}" 
                   class="nav-link text-white px-4 py-3 rounded-lg font-medium tracking-wider">
                     RAPPORTS
                </a>

                {{-- Bouton Déconnexion : Couleur Orange #F16522 --}}
                <form method="POST" action="{{ route('logout') }}" class="ml-4">
                    @csrf
                    <button type="submit"
        class="bg-[#F16522] text-white hover:bg-white hover:text-[#F16522] font-bold py-2 px-6 rounded-full shadow-md transition-all duration-300 flex items-center justify-center">
    {{-- L'icône SVG de déconnexion --}}
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
        <path d="M7 6a7.75 7.75 0 1 0 10 0" />
        <line x1="12" y1="4" x2="12" y2="12" />
    </svg>
</button>
                </form>
            </div>

            {{-- Bouton mobile --}}
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-button" class="text-white hover:text-[#82D2F5] focus:outline-none">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Menu mobile : Fond Cyan #82D2F5 pour contraster --}}
    <div id="mobile-menu" class="hidden md:hidden bg-[#82D2F5] py-2 shadow-inner">
        <a href="{{ route('dashboard') }}" 
           class="block px-6 py-3 text-[#346693] font-semibold hover:bg-[#346693] hover:text-white transition-colors duration-200">🏠 Dashboard</a>
        <a href="{{ route('admin.users.index') }}" 
           class="block px-6 py-3 text-[#346693] font-semibold hover:bg-[#346693] hover:text-white transition-colors duration-200">👥 Gérer les Comptes</a>
        <a href="{{ route('admin.transports.index') }}" 
           class="block px-6 py-3 text-[#346693] font-semibold hover:bg-[#346693] hover:text-white transition-colors duration-200">🚌 Gérer les Transports</a>
        <a href="{{ route('admin.reports') }}" 
           class="block px-6 py-3 text-[#346693] font-semibold hover:bg-[#346693] hover:text-white transition-colors duration-200">📑 Voir les Rapports</a>
        
        {{-- Déconnexion (mobile) --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                class="w-full text-left px-6 py-3 text-white bg-[#F16522] hover:bg-[#346693] font-bold transition-colors duration-200 mt-2">
                 DÉCONNEXION
            </button>
        </form>
    </div>
</nav>

{{-- La partie principale de la page --}}
<main class="p-6">
    @yield('content')
</main>

<script>
    // Toggle menu mobile
    const btn = document.getElementById('mobile-menu-button');
    const menu = document.getElementById('mobile-menu');
    btn.addEventListener('click', () => {
        menu.classList.toggle('hidden');
    });
</script>
</body>
</html>
