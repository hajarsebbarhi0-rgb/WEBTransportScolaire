<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transport;
use App\Models\Eleve; 
use App\Models\Trajet;
use App\Models\Absence;
use App\Models\Presence;
use App\Models\Incident;
use Carbon\Carbon;
use App\Models\BusPosition;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $usersCount      = User::count();
        $transportsCount = Transport::count();
        
        // Bien que nous ayons supprimé la table ici, nous gardons la variable 
        // au cas où d'autres parties du code en auraient besoin, ou pour les stats.
        $vehiclesInService = Transport::where('status', 'en_service')->get();
        
        // Passage de la variable (même si elle est désormais utilisée uniquement pour les stats)
        return view('admin.dashboard', compact('usersCount', 'transportsCount', 'vehiclesInService'));
    }

   public function reports()
{
    $chauffeursCount = User::where('role', 'chauffeur')->count();
    $elevesCount     = Eleve::count();
    $transportsCount = Transport::count();
    $trajetsCount    = Trajet::count();

    // 🔹 Récupération des filtres depuis la requête GET
    $mois_absence     = request('mois_absence') ?? now()->month;
    $annee_absence    = request('annee_absence') ?? now()->year;
    $mois_presence    = request('mois_presence') ?? now()->month;
    $annee_presence   = request('annee_presence') ?? now()->year;

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

    // 🟢 NOUVELLE MÉTHODE POUR LA PAGE DE SUIVI EN TEMPS RÉEL
  public function suivi()
    {
        $positions = BusPosition::with('trajet.chauffeur')
            ->where('updated_at', '>=', now()->subMinutes(2))
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('bus_positions')
                    ->groupBy('trajet_id');
            })
            ->get();

        return view('admin.suivi', compact('positions'));
    }

   // Méthode présence avec filtre mois/année
public function busPositions()
{
    return BusPosition::with('trajet.chauffeur')
        ->where('is_active', 1)
        ->where('updated_at', '>=', now()->subMinutes(2))
        ->whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')
                ->from('bus_positions')
                ->groupBy('trajet_id');
        })
        ->get();
}
 public function presences()
{
    // 🔹 Filtres
    $date_debut = request('date_debut') ?? now()->startOfMonth()->toDateString();
    $date_fin   = request('date_fin') ?? now()->endOfMonth()->toDateString();
    $search     = request('search'); // nom élève
    $trajet_id  = request('trajet_id'); // trajet

    // ✅ Générer les dates
    $dates = [];
    $current = \Carbon\Carbon::parse($date_debut);
    $end = \Carbon\Carbon::parse($date_fin);

    while ($current <= $end) {
        $dates[] = $current->toDateString();
        $current->addDay();
    }

    // 🔥 FILTRE TRAJETS
    $trajetsQuery = Trajet::with(['eleves' => function($q) use ($search) {
        if ($search) {
            $q->where(function($qq) use ($search) {
                $qq->where('nom', 'like', "%$search%")
                   ->orWhere('prenom', 'like', "%$search%");
            });
        }
    }]);

    if ($trajet_id) {
        $trajetsQuery->where('id', $trajet_id);
    }

    $trajets = $trajetsQuery->get();

    $data = [];

    foreach ($trajets as $trajet) {
        foreach ($trajet->eleves as $eleve) {

            // ✅ INITIALISATION
            foreach ($dates as $date) {
                $data[$trajet->id][$eleve->id][$date] = [
                    'matin' => ['presence' => null, 'absence' => null],
                    'soir'  => ['presence' => null, 'absence' => null],
                ];
            }

            // 🔵 PRESENCES
            $presences = $eleve->presences()
                ->where('trajet_id', $trajet->id)
                ->whereBetween('date_presence', [$date_debut, $date_fin])
                ->get();

            foreach ($presences as $p) {
                $data[$trajet->id][$eleve->id][$p->date_presence][$p->periode]['presence'] = $p;
            }

            // 🔴 ABSENCES
            $absences = $eleve->absences()
                ->where('trajet_id', $trajet->id)
                ->where('status', 'active')
                ->whereBetween('date_absence', [$date_debut, $date_fin])
                ->get();

            foreach ($absences as $a) {
                $data[$trajet->id][$eleve->id][$a->date_absence][$a->periode]['absence'] = $a;
            }
        }
    }

    // 🔹 pour select trajet
    $allTrajets = Trajet::all();

    return view('admin.presences.index', compact(
        'trajets',
        'data',
        'dates',
        'date_debut',
        'date_fin',
        'search',
        'trajet_id',
        'allTrajets'
    ));
}}