<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mot de passe oublié</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Typos Impact / Franklin Gothic / Bahnschrift alternatives -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;700&family=Montserrat:wght@300;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #346693;
            --secondary-cyan: #82D2F5;
            --accent-orange: #F16522;
            --background-light: #F0F2F5;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--background-light);
            color: var(--primary-blue);
        }

        .title-impact {
            font-family: Impact, 'Oswald', sans-serif;
            letter-spacing: 1px;
        }

        .submit-btn:hover {
            background-color: #2b5273;
        }
    </style>
</head>

<body class="antialiased">

    <!-- Conteneur 100% centré -->
    <div class="min-h-screen flex items-center justify-center px-4">

        <!-- CARD -->
        <div class="w-full max-w-md bg-white p-10 rounded-3xl shadow-xl border border-gray-200 relative">

            <!-- Ligne bleue en haut -->
            <div class="absolute top-0 left-0 w-full h-2 bg-[var(--primary-blue)] rounded-t-3xl"></div>

            <!-- Titre -->
            <div class="text-center mt-4">
                <h2 class="text-3xl font-bold title-impact text-[var(--primary-blue)] uppercase drop-shadow-sm">
                    🔑 Mot de passe oublié
                </h2>

                <p class="mt-3 text-gray-600 text-sm">
                    Entrez votre email pour recevoir un lien sécurisé.
                </p>
            </div>

            <!-- Validation Errors -->
            <x-validation-errors class="mb-4 mt-4" />

            <!-- Formulaire -->
            <form class="mt-6 space-y-5" method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="text-sm font-medium text-[var(--primary-blue)]">Adresse Email</label>
                    <input id="email" type="email" name="email"
                        class="mt-2 block w-full px-4 py-3 border border-[var(--secondary-cyan)] rounded-xl text-gray-900 bg-gray-50 shadow-sm
                        focus:ring-2 focus:ring-[var(--primary-blue)] focus:border-[var(--primary-blue)] transition-all"
                        placeholder="exemple@domain.com" required value="{{ old('email') }}">
                </div>

                <!-- Bouton -->
                <button type="submit"
                    class="submit-btn w-full py-3 px-4 text-lg font-bold rounded-xl text-white bg-[var(--primary-blue)]
                    shadow-md hover:shadow-xl transition transform hover:scale-[1.02] uppercase tracking-wide">
                    Envoyer le lien
                </button>

                <!-- Retour -->
                <div class="text-center pt-2">
                    <a href="{{ route('login') }}"
                       class="text-sm font-semibold text-[var(--accent-orange)] hover:text-[var(--primary-blue)] transition-colors">
                        ← Retour à la connexion
                    </a>
                </div>

            </form>

        </div>
    </div>

</body>
</html>
