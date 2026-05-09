<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trajet;
use Illuminate\Http\Request;

class TrajetEleveController extends Controller
{
    public function associerTrajet(Request $request)
{
    $request->validate([
        'code_trajet' => 'required|string'
    ]);

    $user = $request->user();
    $eleve = $user->eleve;

    if (!$eleve) {
        return response()->json(['message' => 'Élève introuvable'], 404);
    }

    if ($user->role !== 'eleve') {
        return response()->json(['message' => 'Accès refusé'], 403);
    }

    if ($eleve->trajet_id) {
        return response()->json([
            'message' => 'Un trajet est déjà associé à cet élève'
        ], 409);
    }

    if ($eleve->arret_id) {
        return response()->json([
            'message' => 'Domicile déjà défini. Impossible de changer le trajet.'
        ], 409);
    }

    $trajet = Trajet::where('code_trajet', $request->code_trajet)->first();

    if (!$trajet) {
        return response()->json(['message' => 'Code trajet invalide'], 404);
    }

    $eleve->trajet_id = $trajet->id;
    $eleve->save();

    return response()->json(
        $trajet->eleves()->select(
            'id',
            'nom',
            'prenom',
            'trajet_id' // 🔥 OBLIGATOIRE
        )->get()
    );
}

}
