<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Ajout de la police Oswald pour l'harmonisation --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Définition de la palette de couleurs de l'application */
        :root {
            --primary-blue: #346693; /* Primaire (Bleu foncé) */
            --secondary-cyan: #82D2F5; /* Secondaire (Cyan clair) */
            --accent-orange: #F16522; /* Accent (Orange) */
            --background-light: #F0F2F5; /* Fond léger */
        }
        body {
            /* Utilise la police Oswald pour les titres (Oswald est mieux géré par font-serif dans Tailwind par défaut) */
            font-family: 'Oswald', sans-serif;
            background-color: var(--background-light);
            /* Couleur de texte générale en bleu foncé (primaire) pour la cohérence */
            color: var(--primary-blue); 
        }
        /* Style spécifique du bouton submit pour le survol */
        .submit-btn:hover {
            background-color: #2a5275; /* Un bleu légèrement plus foncé pour le survol */
        }
    </style>
</head>
<body class="antialiased text-[var(--primary-blue)]">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        {{-- Conteneur principal : Ombre plus forte et bordure accentuée --}}
        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-2xl border-t-4 border-[var(--primary-blue)]">
            <div class="text-center">
                {{-- Titre : Utilise le bleu principal et le style Impact/Bahnschrift (simulé par font-extrabold et uppercase) --}}
                <h2 class="mt-6 text-4xl tracking-tight font-extrabold text-[var(--primary-blue)] uppercase">
                    🚌 Se connecter
                </h2>
                <p class="mt-4 text-base text-gray-600 font-sans">
                    Utilisez vos identifiants pour accéder à l'interface d'administration.
                </p>
            </div>
            
            <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    <label for="email" class="sr-only">Email</label>
                    <input id="email" name="email" type="email" autocomplete="username" required 
                        {{-- Champs de formulaire : Bordure secondaire (cyan) et focus principal (bleu) --}}
                        class="relative block w-full px-4 py-3 border border-[var(--secondary-cyan)] placeholder-gray-500 text-gray-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary-blue)] focus:border-[var(--primary-blue)] sm:text-sm bg-gray-50 shadow-sm"
                        placeholder="Adresse Email" value="{{ old('email') }}" autofocus />
                </div>
                {{-- J'ai conservé le composant d'erreur de Laravel/Blade pour la compatibilité --}}
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-[var(--accent-orange)] font-sans" />
                
                <div class="mt-4">
                    <label for="password" class="sr-only">Mot de passe</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                        class="relative block w-full px-4 py-3 border border-[var(--secondary-cyan)] placeholder-gray-500 text-gray-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary-blue)] focus:border-[var(--primary-blue)] sm:text-sm bg-gray-50 shadow-sm"
                        placeholder="Mot de passe" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-[var(--accent-orange)] font-sans" />

                <div class="flex items-center justify-between mt-6">
                    <div class="flex items-center">
                        {{-- Checkbox : utilise le bleu principal pour la couleur --}}
                        <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-[var(--primary-blue)] focus:ring-[var(--primary-blue)] border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm font-sans text-gray-600">
                            Se souvenir de moi
                        </label>
                    </div>
                    
                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-[var(--accent-orange)] hover:text-[var(--primary-blue)] font-sans transition-colors" href="{{ route('password.request') }}">
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>

                <div>
                    {{-- Bouton Connexion : Utilise le bleu principal pour le fond, avec un survol légèrement plus foncé (défini en CSS) --}}
                    <button type="submit" 
                        class="submit-btn group relative w-full flex justify-center py-3 px-4 text-lg font-bold uppercase rounded-xl text-white bg-[var(--primary-blue)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--secondary-cyan)] transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.01]">
                        Connexion
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>