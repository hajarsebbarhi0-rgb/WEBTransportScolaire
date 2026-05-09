<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\Absence;
use Carbon\Carbon;

class PresenceController extends Controller
{
   public function store(Request $request)
{
    $request->validate([
        'eleve_id' => 'required|exists:eleves,id',
        'trajet_id' => 'required|exists:trajets,id',
        'date_presence' => 'required|date',
        'periode' => 'required|in:matin,soir',
    ]);

    // annuler ancienne présence
    Presence::where([
        'eleve_id' => $request->eleve_id,
        'trajet_id' => $request->trajet_id,
        'date_presence' => $request->date_presence,
        'periode' => $request->periode,
        'status' => 'active'
    ])->update([
        'status' => 'annulee'
    ]);

    // annuler absence active
    Absence::where([
        'eleve_id' => $request->eleve_id,
        'trajet_id' => $request->trajet_id,
        'date_absence' => $request->date_presence,
        'periode' => $request->periode,
        'status' => 'active'
    ])->update([
        'status' => 'annulee'
    ]);

    // créer nouvelle présence
    Presence::create([
        'eleve_id' => $request->eleve_id,
        'trajet_id' => $request->trajet_id,
        'date_presence' => $request->date_presence,
        'periode' => $request->periode,
        'status' => 'active'
    ]);

    return response()->json([
        'message' => 'Présence enregistrée ✅'
    ]);
}


}
