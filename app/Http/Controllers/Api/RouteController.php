<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Arret;

class RouteController extends Controller
{
    /**
     * 🔹 Calculer une route simple entre deux points
     * Utilisé par exemple pour :
     * - afficher un trajet ponctuel
     * - tester OSRM
     */
    public function dynamicRoute(Request $request)
    {
        // ✅ Vérification des paramètres envoyés par le frontend
        $request->validate([
            'start_lat' => 'required|numeric',
            'start_lng' => 'required|numeric',
            'end_lat'   => 'required|numeric',
            'end_lng'   => 'required|numeric',
        ]);

        // 🔗 Format exigé par OSRM : lng,lat;lng,lat
        $coords = "{$request->start_lng},{$request->start_lat};{$request->end_lng},{$request->end_lat}";

        // 🌍 API OSRM (routing open-source)
        $url = "https://router.project-osrm.org/route/v1/driving/$coords?overview=full&geometries=polyline";

        // 📡 Appel HTTP vers OSRM
        $res = Http::get($url);

        // ❌ Sécurité : si OSRM ne répond pas ou ne renvoie pas de route
        if (!$res->ok() || empty($res['routes'])) {
            return response()->json([
                'message' => 'Erreur de calcul de route'
            ], 500);
        }

        // ✅ Retour de la polyline (ligne de navigation)
        return response()->json([
            'polyline' => $res['routes'][0]['geometry']
        ]);
    }

    /**
     * 🔹 Calculer la route complète d’un trajet
     * (relie TOUS les arrêts du trajet dans l’ordre)
     */
    public function routeForTrajet($trajetId)
    {
        // 📍 Récupérer les arrêts du trajet dans l’ordre
        $arrets = Arret::where('trajet_id', $trajetId)
            ->orderBy('order_number')
            ->get();

        // ❌ Un trajet doit avoir au moins 2 arrêts
        if ($arrets->count() < 2) {
            return response()->json(['message' => 'Pas assez de points'], 400);
        }

        // 🔗 Transformation des arrêts en format OSRM
        $coords = $arrets->map(fn ($a) =>
            "{$a->longitude},{$a->latitude}"
        )->implode(';');

        // 🌍 Appel OSRM
        $url = "https://router.project-osrm.org/route/v1/driving/$coords?overview=full&geometries=geojson";
        $res = Http::get($url);

        // ❌ Sécurité OSRM
        if (!$res->ok() || empty($res['routes'])) {
            return response()->json([
                'message' => 'Erreur de calcul de route'
            ], 500);
        }

        // ✅ Retour de la géométrie complète du trajet
        return response()->json([
            'geometry' => $res['routes'][0]['geometry']['coordinates']
        ]);
    }

    /**
     * 🔹 Route dynamique vers le PROCHAIN arrêt NON ATTEINT
     * 👉 Cette fonction est appelée à CHAQUE mise à jour GPS du chauffeur
     */
    public function routeToNextArret(Request $request, $trajetId)
    {
        // ✅ Vérification de la position GPS du chauffeur
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        // 📍 Récupérer le prochain arrêt non atteint
        $arret = Arret::where('trajet_id', $trajetId)
            ->where('is_reached', false)
            ->orderBy('order_number')
            ->first();

        // ✅ Tous les arrêts sont atteints → trajet terminé
        if (!$arret) {
            return response()->json(['finished' => true]);
        }

        // 📏 Calculer la distance chauffeur → arrêt
        $distance = $this->distance(
            $request->lat,
            $request->lng,
            $arret->latitude,
            $arret->longitude
        );

        // ✅ Si le chauffeur est à moins de 30 mètres
        if ($distance < 30) {
            // 🟢 Marquer l’arrêt comme atteint
            $arret->update(['is_reached' => true]);

            // 🔁 Charger le prochain arrêt
            $arret = Arret::where('trajet_id', $trajetId)
                ->where('is_reached', false)
                ->orderBy('order_number')
                ->first();

            // ✅ Fin du trajet
            if (!$arret) {
                return response()->json(['finished' => true]);
            }
        }

        // 🧭 Calculer la route GPS vers le prochain arrêt
        $coords = "{$request->lng},{$request->lat};{$arret->longitude},{$arret->latitude}";
        $url = "https://router.project-osrm.org/route/v1/driving/$coords?overview=full&geometries=geojson";
        $res = Http::get($url);

        // ❌ Sécurité OSRM
        if (!$res->ok() || empty($res['routes'])) {
            return response()->json([
                'message' => 'Erreur de calcul de route'
            ], 500);
        }

        // ✅ Retour :
        // - la ligne à afficher sur la carte
        // - les infos du prochain arrêt
        return response()->json([
            'geometry' => $res['routes'][0]['geometry']['coordinates'],
            'arret'    => $arret
        ]);
    }

    /**
     * 📐 Calcul de distance entre deux points GPS (formule Haversine)
     * Résultat en mètres
     */
    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Rayon de la Terre en mètres

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        return 2 * $earthRadius * atan2(sqrt($a), sqrt(1 - $a));
    }
  /**
 * Réordonner les arrêts d’un trajet selon la position de départ du chauffeur
 *
 * @param int   $trajetId  ID du trajet
 * @param float $startLat  Latitude de départ (chauffeur)
 * @param float $startLng  Longitude de départ (chauffeur)
 */
public function reorderArrets(Request $request, $trajetId)
{
    $request->validate([
        'lat' => 'required|numeric',
        'lng' => 'required|numeric',
    ]);

    $startLat = $request->lat;
    $startLng = $request->lng;

    $arrets = Arret::where('trajet_id', $trajetId)->get();

    if ($arrets->isEmpty()) return;

    $currentLat = $startLat;
    $currentLng = $startLng;
    $order = 1;

    while ($arrets->isNotEmpty()) {

        $nearest = null;
        $minDist = INF;

        foreach ($arrets as $arret) {

            $dist = $this->distance(
                $currentLat,
                $currentLng,
                $arret->latitude,
                $arret->longitude
            );

            if ($dist < $minDist) {
                $minDist = $dist;
                $nearest = $arret;
            }
        }

        $nearest->update([
            'order_number' => $order,
            'is_reached'   => false
        ]);

        $currentLat = $nearest->latitude;
        $currentLng = $nearest->longitude;

        $arrets = $arrets->reject(fn ($a) => $a->id === $nearest->id);
        $order++;
    }

    return response()->json(['message' => 'Arrets reordered']);
}

}
 