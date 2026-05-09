<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Eleve;
use App\Models\Trajet;
use App\Models\Arret;
use App\Models\Absence;
use Carbon\Carbon;

class EleveController extends Controller
{
    /**
     * 🔵 TOUS les élèves du chauffeur
     * Utilisé pour : signaler présence / absence
     */
    public function elevesChauffeur(Request $request)
{
    $chauffeur = Auth::user();

    if ($chauffeur->role !== 'chauffeur') {
        return response()->json(['message' => 'Accès non autorisé'], 403);
    }

    $periode = $request->periode ?? 'matin';
    $today = Carbon::today();

    $eleves = Eleve::whereHas('trajet', function ($q) use ($chauffeur) {
            $q->where('chauffeur_id', $chauffeur->id);
        })
        ->with([
            'user',
            'absences' => function ($q) use ($periode, $today) {
                $q->whereDate('date_absence', $today)
                  ->where('periode', $periode)
                  ->where('status', 'active');
            }
        ])
        ->get()
        ->map(function ($eleve) {
            return [
                'id' => $eleve->id,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'email' => $eleve->user->email ?? null,
                'trajet_id' => $eleve->trajet_id,
                'absent_signale' => $eleve->absences->isNotEmpty(),
                'photo_url' => $eleve->photo
                    ? url('storage/' . $eleve->photo)
                    : null,
            ];
        });

    return response()->json($eleves);
}
// Nouvelle méthode uniquement pour la carte du chauffeur
public function elevesPresentsParTrajet(Request $request)
{
    $trajetId = $request->query('trajet_id');
    $periode  = $request->query('periode', 'matin');
    $today    = Carbon::today()->toDateString();

    $eleves = \App\Models\Eleve::where('trajet_id', $trajetId)
        ->whereDoesntHave('absences', function ($q) use ($today, $periode) {
            $q->where('date_absence', $today)
              ->where('periode', $periode)
              ->where('status', 'active');
        })
        ->get();

    return response()->json($eleves);
}

public function arretsPresents(Request $request)
{
    $trajetId = $request->query('trajet_id');
    $periode  = $request->query('periode', 'matin');
    $today    = Carbon::today()->toDateString();

    // Récupérer les arret_id des élèves présents uniquement
    $arretIds = Eleve::where('trajet_id', $trajetId)
        ->whereNotNull('arret_id')
        ->whereDoesntHave('absences', function ($q) use ($today, $periode) {
            $q->where('date_absence', $today)
              ->where('periode', $periode)
              ->where('status', 'active');
        })
        ->pluck('arret_id');

    $arrets = Arret::whereIn('id', $arretIds)->get();

    return response()->json($arrets);
}
    /**
     * 🔵 TOUS les élèves (admin)
     */
    public function index()
    {
        return response()->json(
            Eleve::with('user')->get(),
            200
        );
    }

    /**
     * 🔵 Élève par ID
     */
    public function show($id)
    {
        $eleve = Eleve::with('user')->find($id);

        if (!$eleve) {
            return response()->json(['message' => 'Élève non trouvé'], 404);
        }

        return response()->json($eleve, 200);
    }

    /**
     * 📍 Enregistrer domicile de l’élève
     */
    public function setHomeLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();
        $eleve = Eleve::where('user_id', $user->id)->first();

        if (!$eleve || !$eleve->trajet_id) {
            return response()->json(['message' => 'Trajet non associé'], 400);
        }

        if ($eleve->arret_id) {
            return response()->json(['message' => 'Domicile déjà enregistré'], 409);
        }

        $arret = Arret::create([
            'trajet_id' => $eleve->trajet_id,
            'nom' => 'Maison de ' . $eleve->prenom,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'order_number' => 0,
        ]);

        $adresse = $this->getAddressFromCoordinates(
            $request->latitude,
            $request->longitude
        );

        $eleve->update([
            'arret_id' => $arret->id,
            'adresse' => $adresse,
        ]);

        return response()->json([
            'message' => 'Lieu enregistré avec succès',
            'arret' => $arret,
            'adresse' => $adresse,
        ]);
    }

    /**
     * 🌍 Adresse depuis coordonnées GPS
     */
    private function getAddressFromCoordinates($latitude, $longitude)
    {
        $response = Http::withHeaders([
            'User-Agent' => 'nvTransportScolaire'
        ])->get('https://nominatim.openstreetmap.org/reverse', [
            'lat' => $latitude,
            'lon' => $longitude,
            'format' => 'json'
        ]);

        if ($response->successful() && isset($response['address'])) {
            $a = $response['address'];
            return trim(
                ($a['road'] ?? '') . ', ' .
                ($a['city'] ?? $a['town'] ?? $a['village'] ?? '') . ', ' .
                ($a['postcode'] ?? '') . ', ' .
                ($a['country'] ?? '')
            );
        }

        return null;
    }

    /**
     * 👤 Profil élève connecté
     */
    public function profile()
    {
        $user = Auth::user();
        $eleve = Eleve::with('trajet', 'arret')
            ->where('user_id', $user->id)
            ->first();

        if (!$eleve) {
            return response()->json(['message' => 'Élève non trouvé'], 404);
        }

        return response()->json([
            'id' => $eleve->id,
            'nom' => $eleve->nom,
            'prenom' => $eleve->prenom,
            'date_de_naissance' => $eleve->date_de_naissance,
            'genre' => $eleve->genre,
            'niveau' => $eleve->niveau,
            'photo' => $eleve->photo ? url("storage/$eleve->photo") : null,
            'email' => $user->email,
            'telephone' => $user->telephone,
            'trajet' => $eleve->trajet,
            'arret' => $eleve->arret,
            'adresse' => $eleve->adresse,
        ]);
    }

    /**
     * ✏️ Mise à jour profil
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $eleve = Eleve::where('user_id', $user->id)->first();

        if (!$eleve) {
            return response()->json(['message' => 'Élève non trouvé'], 404);
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_de_naissance' => 'required|date',
            'genre' => 'required|string|max:20',
            'niveau' => 'required|string|max:50',
            'email' => 'required|email',
            'telephone' => 'required|string|max:20',
        ]);

        $eleve->update($request->only([
            'nom', 'prenom', 'date_de_naissance', 'genre', 'niveau'
        ]));

        $user->update([
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'eleve' => $eleve->load('user'),
        ]);
    }

    /**
     * 📸 Upload photo
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();
        $eleve = Eleve::where('user_id', $user->id)->first();

        if (!$eleve) {
            return response()->json(['message' => 'Élève non trouvé'], 404);
        }

        if ($eleve->photo && Storage::disk('public')->exists($eleve->photo)) {
            Storage::disk('public')->delete($eleve->photo);
        }

        $path = $request->file('photo')->store('uploads/eleves', 'public');
        $eleve->update(['photo' => $path]);

        return response()->json([
    'message' => 'Profil mis à jour avec succès',
    'eleve' => [
        'id' => $eleve->id,
        'nom' => $eleve->nom,
        'prenom' => $eleve->prenom,
        'date_de_naissance' => $eleve->date_de_naissance,
        'genre' => $eleve->genre,
        'niveau' => $eleve->niveau,
        'photo' => $eleve->photo ? url("storage/$eleve->photo") : null,
        'email' => $eleve->user->email,
        'telephone' => $eleve->user->telephone,
    ],
]);
    }

    /**
     * 🚍 Trajets
     */
    public function trajets()
    {
        return response()->json(Trajet::all(), 200);
    }

    /**
     * 🚏 Arrêts par trajet
     */
    public function arrets($trajet_id)
    {
        $trajet = Trajet::with('arrets')->find($trajet_id);

        if (!$trajet) {
            return response()->json(['message' => 'Trajet non trouvé'], 404);
        }

        return response()->json($trajet->arrets, 200);
    }

    /**
     * 🟢 Élèves PRÉSENTS du chauffeur
     * Utilisé pour : liste chauffeur
     */
   public function elevesParTrajet(Request $request)
{
    $chauffeur = Auth::user();

    if ($chauffeur->role !== 'chauffeur') {
        return response()->json(['message' => 'Accès non autorisé'], 403);
    }
 $periode = $request->periode ?? 'matin';
    $today = Carbon::today();
    $trajets = Trajet::where('chauffeur_id', $chauffeur->id)
        ->with(['eleves.user'])
        ->get();

    if ($trajets->isEmpty()) {
        return response()->json(['message' => 'Aucun trajet trouvé'], 404);
    }

    $eleves = collect();

    foreach ($trajets as $trajet) {
      $eleves = $eleves->merge(
            $trajet->eleves()
                ->whereDoesntHave('absences', function ($q) use ($today, $periode) {
                    $q->whereDate('date_absence', $today)
                      ->where('periode', $periode)
                      ->where('status', 'active');
                })
                ->with('user')
                ->get()
        );
    }

    return response()->json($eleves->values(), 200);
}
}
