<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trajet;
use App\Models\User;
use App\Models\Transport;
use App\Models\HistoriqueOperation;
use Illuminate\Support\Str; 

class TrajetController extends Controller
{
    // Vérifie que l’utilisateur est admin
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role === 'admin') {
                return $next($request);
            }
            abort(403, 'Accès interdit');
        });
    }

    // Liste des trajets
    public function index()
    {
        // On charge explicitement la relation chauffeur pour l'affichage initial
        $trajets = Trajet::with(['chauffeur','transport','arrets'])
                         ->orderBy('nom', 'asc')
                         ->orderBy('debut', 'asc')
                         ->get();

        return view('admin.trajets.index', compact('trajets'));
    }

    // Formulaire création
    public function create()
    {
        $chauffeurs = User::where('role', 'chauffeur')->get();
        $transports = Transport::all();
        return view('admin.trajets.create', compact('chauffeurs','transports'));
    }// Affichage d'un trajet + ses arrêts
public function show(Trajet $trajet)
{
    $trajet->load([
        'chauffeur',
        'transport',
        'arrets' => function ($q) {
            $q->orderBy('order_number');
        },
        'eleves.user' // 🔥 ICI
    ]);

    return view('admin.trajets.show', compact('trajet'));
}


    // Enregistrement
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'chauffeur_id' => 'required|exists:users,id',
            'transport_id' => 'required|exists:transports,id',
            'debut' => 'required',
            'fin' => 'required',
            'fin' => 'required|after:debut',
            'arrets' => 'nullable|array', 
            'arrets.*.nom' => 'required|string|max:255',
            'arrets.*.latitude' => 'nullable|numeric',
            'arrets.*.longitude' => 'nullable|numeric',
            'fin.after' => 'L’heure de fin doit être après l’heure de début',
        ], [

    // 🔹 TRAJET
    'nom.required' => 'Le nom du trajet est obligatoire',
    'nom.max' => 'Le nom est trop long',

    'chauffeur_id.required' => 'Veuillez choisir un chauffeur',
    'chauffeur_id.exists' => 'Chauffeur invalide',

    'transport_id.required' => 'Veuillez choisir un transport',
    'transport_id.exists' => 'Transport invalide',

    'debut.required' => 'L’heure de début est obligatoire',
    'fin.required' => 'L’heure de fin est obligatoire',

    // 🔹 ARRETS
    'arrets.array' => 'Format des arrêts invalide',

    'arrets.*.nom.required' => 'Le nom de l’arrêt est obligatoire',
    'arrets.*.nom.max' => 'Nom d’arrêt trop long',

    'arrets.*.latitude.numeric' => 'Latitude invalide',
    'arrets.*.longitude.numeric' => 'Longitude invalide',
]);
        
        $must_have_char = [
            Str::upper(Str::random(1)), 
            Str::lower(Str::random(1)), 
            rand(0, 9), 
        ];

        $total_length = 10;
        $remaining_chars = Str::random($total_length - count($must_have_char));
        $codeTrajetBase = str_shuffle(implode('', $must_have_char) . $remaining_chars);

        $codeTrajet = $codeTrajetBase;
        $i = 1;
        while (Trajet::where('code_trajet', $codeTrajet)->exists()) {
            $codeTrajet = $codeTrajetBase . $i++;
        }

        $data = $request->only('nom','description','chauffeur_id','transport_id','debut','fin');
        $data['code_trajet'] = $codeTrajet;

        $trajet = Trajet::create($data); 

        if ($request->has('arrets') && is_array($request->arrets)) {
            foreach ($request->arrets as $index => $arret) {
                $trajet->arrets()->create([
                    'nom' => $arret['nom'],
                    'latitude' => $arret['latitude'] ?? null,
                    'longitude' => $arret['longitude'] ?? null,
                    'order_number' => $index + 1,
                ]);
            }
        }

        HistoriqueOperation::create([
            'user_id'    => auth()->id(),
            'action'     => 'création',
            'model_type' => 'Trajet',
            'model_id'   => $trajet->id,
            'description'=> "Trajet '{$trajet->nom}' créé (code: {$trajet->code_trajet}, id: {$trajet->id})",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.trajets.index')->with('success', "Trajet créé avec succès (Code : **{$trajet->code_trajet}**)");
    }

    // Formulaire modification (omission des méthodes edit/update/destroy pour la concision)
    public function edit(Trajet $trajet)
    {
        $chauffeurs = User::where('role', 'chauffeur')->get();
        $transports = Transport::all();
        $arrets = $trajet->arrets()->orderBy('order_number')->get();
        return view('admin.trajets.edit', compact('trajet', 'chauffeurs', 'transports','arrets'));
    }

    public function update(Request $request, Trajet $trajet)
{
    $request->validate([
        'nom' => 'required|string|max:255',
        'description' => 'nullable|string',
        'chauffeur_id' => 'required|exists:users,id',
        'transport_id' => 'required|exists:transports,id',
        'debut' => 'required',
        'fin' => 'required',
    ]);

    $trajet->update(
        $request->only('nom','description','chauffeur_id','transport_id','debut','fin')
    );

    HistoriqueOperation::create([
        'user_id'    => auth()->id(),
        'action'     => 'modification',
        'model_type' => 'Trajet',
        'model_id'   => $trajet->id,
        'description'=> "Trajet '{$trajet->nom}' mis à jour (code: {$trajet->code_trajet}, id: {$trajet->id})",
        'ip_address' => $request->ip(),
    ]);

    return redirect()->route('admin.trajets.index')
        ->with('success', 'Trajet mis à jour avec succès');
}


    public function destroy(Trajet $trajet)
    {
        $id = $trajet->id;
        $nom = $trajet->nom;
        $code = $trajet->code_trajet;
        $trajet->delete();

        HistoriqueOperation::create([
            'user_id'    => auth()->id(),
            'action'     => 'suppression',
            'model_type' => 'Trajet',
            'model_id'   => $id,
            'description'=> "Trajet '{$nom}' supprimé (code: {$code}, id: {$id})",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.trajets.index')->with('success', 'Trajet supprimé avec succès');
    }
    
    public function arrets(Trajet $trajet)
    {
        return response()->json($trajet->arrets()->orderBy('order_number')->get(), 200);
    }
    
    public function search(Request $request)
{
    $query = $request->get('query', '');
    $searchTerm = trim(strtolower($query));
    $roleFilter = $request->get('role'); // admin | chauffeur | eleve

    // Charger les relations nécessaires
    $queryBuilder = User::with(['eleve.trajet']);

    if (!empty($searchTerm)) {
        $queryBuilder->where(function ($q) use ($searchTerm) {

            // 🔍 Recherche sur User
            $q->whereRaw('LOWER(nom) LIKE ?', ["%{$searchTerm}%"])
              ->orWhereRaw('LOWER(prenom) LIKE ?', ["%{$searchTerm}%"])
              ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchTerm}%"])
              ->orWhereRaw('LOWER(telephone) LIKE ?', ["%{$searchTerm}%"])
              ->orWhereRaw('LOWER(role) LIKE ?', ["%{$searchTerm}%"]);

            // 🔍 Recherche sur le trajet de l’élève
            $q->orWhereHas('eleve.trajet', function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(nom) LIKE ?', ["%{$searchTerm}%"])
                  ->orWhereRaw('LOWER(code_trajet) LIKE ?', ["%{$searchTerm}%"]);
            });
        });

        // ⭐ Priorité d’affichage (comme TrajetController)
        $queryBuilder->orderByRaw("
            CASE 
                WHEN LOWER(nom) LIKE '{$searchTerm}%' THEN 0
                WHEN LOWER(prenom) LIKE '{$searchTerm}%' THEN 1
                WHEN LOWER(email) LIKE '{$searchTerm}%' THEN 2
                ELSE 3
            END
        ");
    }

    // 🎯 Filtrage par rôle (optionnel)
    if (!empty($roleFilter)) {
        $queryBuilder->where('role', $roleFilter);
    }

    $users = $queryBuilder
        ->orderBy('nom', 'asc')
        ->orderBy('prenom', 'asc')
        ->get();

    return response()->json($users);
}

}