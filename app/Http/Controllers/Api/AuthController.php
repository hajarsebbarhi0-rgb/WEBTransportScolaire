<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Eleve;
use App\Models\Trajet;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur (élève ou chauffeur)
     */
    public function register(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:eleve,chauffeur',
            'telephone' => 'required|string|max:20',
            'date_de_naissance' => 'nullable|date',
            'genre' => 'nullable|string|max:10',
            'ecole' => 'nullable|string|max:255',
            'niveau' => 'nullable|string|max:50',
            'adresse' => 'nullable|string|max:500',

            // Nouveau champ : code du trajet obligatoire uniquement pour les élèves
            'code_trajet' => 'required_if:role,eleve|string|exists:trajets,code_trajet',
        ], [
    'email.unique' => 'Cet email est déjà utilisé.', // <-- Ici le message personnalisé
    'email.required' => 'Veuillez entrer votre email.',
    'email.email' => 'L’email n’est pas valide.',
     'code_trajet.exists' => 'Ce code de trajet n’existe pas. Vérifiez-le.',
]);

        // Création de l'utilisateur
        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'telephone' => $validated['telephone'],
        ]);

        // Si c’est un élève, on l’associe au trajet correspondant
        if ($user->role === 'eleve') {
            // Récupérer le trajet à partir du code
            $trajet = Trajet::where('code_trajet', $validated['code_trajet'])->first();

            if (!$trajet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code du trajet invalide.',
                ], 400);
            }

            // Créer le profil élève lié à ce trajet
            Eleve::create([
                'user_id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'date_de_naissance' => $validated['date_de_naissance'] ?? null,
                'genre' => $validated['genre'] ?? null,
                'ecole' => $validated['ecole'] ?? null,
                'niveau' => $validated['niveau'] ?? null,
                'trajet_id' => $trajet->id, // liaison avec le trajet
                'arret_id' => null, 
                'adresse' => $validated['adresse'] ?? null,
            ]);
        }

        // Création du token d’authentification
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur inscrit avec succès',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Connexion d'un utilisateur
     */
   public function login(Request $request)
{
    // Validation
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // 1️⃣ Chercher l'utilisateur par email
    $user = User::where('email', $credentials['email'])->first();

    // 2️⃣ Vérifier mot de passe
    if (!$user || !Hash::check($credentials['password'], $user->password)) {
        return response()->json(['message' => 'Identifiants invalides'], 401);
    }

    // 3️⃣ Créer un token sanctum
    $token = $user->createToken('authToken')->plainTextToken;

    // 4️⃣ Retourner les infos + token
    return response()->json([
        'token' => $token,
        'user' => $user,
    ]);
}


    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnecté avec succès',
        ]);
    }
}
