<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Eleve;
use App\Models\Transport;
use App\Models\Trajet;

class AdminRapportController extends Controller
{
    public function index(Request $request)
    {
        // 🔹 Valeurs par défaut pour Absences
        $mois_absence  = $request->get('mois_absence', now()->month);
        $annee_absence = $request->get('annee_absence', now()->year);

        // 🔹 Valeurs par défaut pour Présences
        $mois_presence  = $request->get('mois_presence', now()->month);
        $annee_presence = $request->get('annee_presence', now()->year);

        // 🔹 Statistiques générales
        $chauffeursCount = User::where('role', 'chauffeur')->count();
        $elevesCount     = Eleve::count();
        $transportsCount = Transport::count();
        $trajetsCount    = Trajet::count();

        // 🔹 Absences filtrées
        $absencesByTrajet = Trajet::with(['absences' => function($q) use($mois_absence, $annee_absence) {
            $q->where('status', 'active')
              ->whereMonth('date_absence', $mois_absence)
              ->whereYear('date_absence', $annee_absence);
        }])->get()->map(function($trajet) {
            return [
                'id' => $trajet->id,
                'nom' => $trajet->nom,
                'absences_count' => $trajet->absences->count()
            ];
        });

        // 🔹 Présences filtrées
        $presencesByTrajet = Trajet::with(['presences' => function($q) use($mois_presence, $annee_presence) {
            $q->where('status', 'active')
              ->whereMonth('date_presence', $mois_presence)
              ->whereYear('date_presence', $annee_presence);
        }])->get()->map(function($trajet) {
            return [
                'id' => $trajet->id,
                'nom' => $trajet->nom,
                'presences_count' => $trajet->presences->count()
            ];
        });

        // 🔹 Envoi des données à la vue
        return view('admin.rapports', compact(
            'chauffeursCount',
            'elevesCount',
            'transportsCount',
            'trajetsCount',
            'absencesByTrajet',
            'presencesByTrajet',
            'mois_absence',
            'annee_absence',
            'mois_presence',
            'annee_presence'
        ));
    }
}