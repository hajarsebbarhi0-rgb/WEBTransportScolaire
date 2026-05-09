<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Absence;
use App\Models\Eleve;
use Carbon\Carbon;
use App\Models\Presence;

class AbsenceController extends Controller
{
    // 👨‍🎓 Absence signalée par l’élève
  public function signalerAbsence(Request $request)
{
    $user = Auth::guard('sanctum')->user();

    if (!$user) {
        return response()->json(['message' => 'Utilisateur non authentifié'], 401);
    }

    $eleve = Eleve::where('user_id', $user->id)->first();

    if (!$eleve) {
        return response()->json(['message' => 'Élève non trouvé'], 404);
    }

    $request->validate([
        'raison' => 'required|string|max:255',
        'dates' => 'required|array',
        'dates.*' => 'date',
        'periodes' => 'required|array',
        'periodes.*' => 'in:matin,soir'
    ]);
     #empêcher l’élève de signaler une absence dans le passé
     $today = Carbon::today();

foreach ($request->dates as $date) {

    if (Carbon::parse($date)->lt($today)) {
        return response()->json([
            'message' => 'Impossible de signaler une absence dans le passé'
        ], 422);
    }
}
/* supprimer les absences non sélectionnées */

Absence::where('eleve_id', $eleve->id)
->where('status','active') 
    ->whereNotIn('date_absence', $request->dates)
    ->whereDate('date_absence', '>=', $today)
    ->update([
        'status' => 'annulee'
    ]);
  foreach ($request->dates as $date) {

  // 🧹 annuler les anciennes absences pour cette date
    Absence::where('eleve_id', $eleve->id)
        ->where('date_absence', $date)
        ->where('status', 'active')
        ->update([
            'status' => 'annulee'
        ]);
    foreach ($request->periodes as $periode) {
          
        $exists = Absence::where([
            'eleve_id' => $eleve->id,
            'trajet_id' => $eleve->trajet_id,
            'date_absence' => $date,
            'periode' => $periode,
            'status' => 'active'
        ])->exists();

        if (!$exists) {
            Absence::create([
                'eleve_id' => $eleve->id,
                'trajet_id' => $eleve->trajet_id,
                'date_absence' => $date,
                'periode' => $periode,
                'raison' => $request->raison,
                'status' => 'active'
            ]);
        }

    }

}


    return response()->json([
        'message' => 'Absences enregistrées ✅'
    ]);
}

    // 🚍 Absence signalée par le chauffeur
  public function absenceParChauffeur(Request $request)
{
    $request->validate([
        'eleve_id' => 'required|exists:eleves,id',
        'trajet_id' => 'required|exists:trajets,id',
        'date_absence' => 'required|date',
        'periode' => 'required|in:matin,soir',
     
    ]);

    $exists = Absence::where([
        'eleve_id' => $request->eleve_id,
        'trajet_id' => $request->trajet_id,
        'date_absence' => $request->date_absence,
        'periode' => $request->periode,
        'status' => 'active'
    ])->exists();

   if ($exists) {
    Absence::where([
        'eleve_id' => $request->eleve_id,
        'trajet_id' => $request->trajet_id,
        'date_absence' => $request->date_absence,
        'periode' => $request->periode,
        'status' => 'active'
    ])->update([
        'raison' => 'Confirmée par le chauffeur'
    ]);

    return response()->json([
        'message' => 'Absence déjà existante confirmée'
    ]);
}
// annuler présence existante
Presence::where([
    'eleve_id' => $request->eleve_id,
    'trajet_id' => $request->trajet_id,
    'date_presence' => $request->date_absence,
    'periode' => $request->periode,
])->delete();
    Absence::create([
        'eleve_id' => $request->eleve_id,
        'trajet_id' => $request->trajet_id,
        'date_absence' => $request->date_absence,
        'periode' => $request->periode,
        'raison' => 'Signalée par le chauffeur',
        'status' => 'active'
    ]);

    return response()->json(['message' => 'Absence enregistrée ✅']);
}

}
