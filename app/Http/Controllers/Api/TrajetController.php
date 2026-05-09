<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trajet;
use Illuminate\Http\Request;
use App\Models\Arret;


class TrajetController extends Controller
{
    // Récupérer tous les trajets
    public function index()
    {
        $trajets = Trajet::with('arrets')->get(); // inclut les arrêts
        return response()->json($trajets, 200);
    }
    public function getTrajetsChauffeur(Request $request)
{
    // 1️⃣ Vérifier que l'utilisateur est un chauffeur
    $chauffeur = $request->user();
    if ($chauffeur->role !== 'chauffeur') {
        return response()->json(['message' => 'Accès refusé. Non chauffeur.'], 403);
    }

    // 2️⃣ Récupérer tous les trajets assignés à ce chauffeur avec leurs arrêts
    $trajets = Trajet::where('chauffeur_id', $chauffeur->id)
                     ->with('arrets') // facultatif : si tu veux inclure les arrêts
                     ->get();

    // 3️⃣ Retourner les trajets
    return response()->json($trajets, 200);
}

    // Récupérer les arrêts d’un trajet spécifique
    public function arrets($id)
    {
        $trajet = Trajet::with('arrets')->find($id);
        if (!$trajet) {
            return response()->json(['message' => 'Trajet non trouvé'], 404);
        }
        return response()->json($trajet->arrets, 200);
    }
 
     private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2)**2 +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLon/2)**2;

        return 2 * $earthRadius * atan2(sqrt($a), sqrt(1 - $a));
    }
}
