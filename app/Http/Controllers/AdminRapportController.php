<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Eleve;
use App\Models\Transport;
use App\Models\Trajet;
use App\Models\Presence;
use App\Models\Absence;
use App\Models\BusPosition;

class AdminRapportController extends Controller
{
   public function index(Request $request)
{
    // ==========================
    // FILTRES DATES
    // ==========================
    $date_debut = $request->get('date_debut', now()->startOfMonth()->toDateString());
    $date_fin   = $request->get('date_fin',   now()->toDateString());

    // ==========================
    // STATISTIQUES GÉNÉRALES
    // ==========================
    $chauffeursCount = User::where('role', 'chauffeur')->count();
    $elevesCount     = Eleve::count();
    $transportsCount = Transport::count();
    $trajetsCount    = Trajet::count();

    // ==========================
    // BUS ACTIFS
    // ==========================
   $busActifs = BusPosition::where('updated_at', '>=', now()->subMinutes(2))
    ->distinct('transport_id')  // ou 'bus_id' selon ton schéma
    ->count('transport_id');

    // ==========================
    // ABSENCES PAR TRAJET
    // ==========================
    $absencesByTrajet = Trajet::withCount(['absences' => function ($q) use ($date_debut, $date_fin) {
        $q->whereBetween('date_absence', [$date_debut, $date_fin]);
    }])->get();

    // ==========================
    // PRÉSENCES PAR TRAJET
    // ==========================
    $presencesByTrajet = Trajet::withCount(['presences' => function ($q) use ($date_debut, $date_fin) {
        $q->whereBetween('date_presence', [$date_debut, $date_fin]);
    }])->get();

    // ==========================
    // ÉLÈVES PAR TRAJET
    // ==========================
    $elevesParTrajet = Trajet::withCount('eleves')->get();

    // ==========================
    // TOP ÉLÈVES ABSENTS
    // ==========================
    $elevesAbsents = Eleve::withCount(['absences' => function ($q) use ($date_debut, $date_fin) {
        $q->whereBetween('date_absence', [$date_debut, $date_fin]);
    }])
    ->orderByDesc('absences_count')
    ->take(10)
    ->get();

    // ==========================
    // UTILISATION TRANSPORTS
    // ==========================
    $utilisationTransports = Transport::withCount('trajets')->get();

    // ==========================
    // PRÉSENCES PAR MOIS
    // ==========================
    $presencesParMois = [];
    for ($i = 1; $i <= 12; $i++) {
        $presencesParMois[] = Presence::whereYear('date_presence', now()->year)
            ->whereMonth('date_presence', $i)
            ->count();
    }

    // ==========================
    // ABSENCES PAR MOIS
    // ==========================
    $absencesParMois = [];
    for ($i = 1; $i <= 12; $i++) {
        $absencesParMois[] = Absence::whereYear('date_absence', now()->year)
            ->whereMonth('date_absence', $i)
            ->count();
    }

    return view('admin.rapports', compact(
        'chauffeursCount', 'elevesCount', 'transportsCount',
        'trajetsCount', 'busActifs',
        'absencesByTrajet', 'presencesByTrajet', 'elevesParTrajet',
        'elevesAbsents', 'utilisationTransports',
        'presencesParMois', 'absencesParMois',
        'date_debut', 'date_fin'
    ));
}
}