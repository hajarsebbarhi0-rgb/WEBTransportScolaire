<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusPosition;
use App\Models\Eleve;
use App\Models\Trajet;
use App\Models\Arret;

class LocationController extends Controller
{
    // 🚍 Chauffeur envoie la position


public function store(Request $request)
{
    $request->validate([
        'trajet_id' => 'required|exists:trajets,id',
        'latitude'  => 'required|numeric',
        'longitude' => 'required|numeric',
    ]);

    BusPosition::where('trajet_id', $request->trajet_id)
        ->update(['is_active' => 0]);

    $position = BusPosition::create([
        'trajet_id' => $request->trajet_id,
        'latitude'  => $request->latitude,
        'longitude' => $request->longitude,
        'is_active' => 1,
    ]);

    // ✅ CHECK ARRÊT ATTEINT (ICI)
    $arret = Arret::where('trajet_id', $request->trajet_id)
        ->where('is_reached', false)
        ->orderBy('order_number')
        ->first();

    if ($arret) {
        $dist = $this->distance(
            $request->latitude,
            $request->longitude,
            $arret->latitude,
            $arret->longitude
        );

        if ($dist < 30) {
            $arret->update(['is_reached' => true]);
        }
    }

    // ✅ UN SEUL RETURN
    return response()->json([
        'message' => 'Position enregistrée',
        'data' => $position
    ], 201);
}

    // 📍 Position pour élève
  public function getLocation($trajetId)
{
    $position = BusPosition::where('trajet_id', $trajetId)
        ->where('is_active', 1)
        
        ->latest()
        ->first();

    if (!$position) {
        return response()->json([
            'status' => 'waiting'
        ]);
    }

    return response()->json([
        'latitude' => $position->latitude,
        'longitude' => $position->longitude,
    ]);
}

    // ⛔ Arrêter le trajet
    public function stopTrajet(Request $request)
{
    $request->validate([
        'trajet_id' => 'required|exists:trajets,id'
    ]);

    $trajet = Trajet::where('id', $request->trajet_id)
        ->where('chauffeur_id', $request->user()->id)
        ->first();

    if (!$trajet) {
        return response()->json(['message' => 'Trajet non autorisé'], 403);
    }

    BusPosition::where('trajet_id', $trajet->id)
        ->update(['is_active' => 0]);

    return response()->json(['message' => 'Trajet arrêté']);
}
private function distance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371000;

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) ** 2 +
        cos(deg2rad($lat1)) *
        cos(deg2rad($lat2)) *
        sin($dLon / 2) ** 2;

    return 2 * $earthRadius * atan2(sqrt($a), sqrt(1 - $a));
}
}
