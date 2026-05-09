<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Eleve;
use App\Models\Trajet;
use App\Models\HistoriqueOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Arret;
use Illuminate\Support\Facades\Http;


class UserController extends Controller
{
    /* =========================================================
     * 🔐 ACCÈS ADMIN
     * ========================================================= */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role === 'admin') {
                return $next($request);
            }
            abort(403, 'Accès interdit');
        });
    }

    /* =========================================================
     * INDEX
     * ========================================================= */
    public function index()
    {
        $users = User::with(['eleve.trajet'])
            ->orderBy('nom')
            ->orderBy('prenom')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /* =========================================================
     * CREATE
     * ========================================================= */
    public function create()
    {
        return view('admin.users.create');
    }

    /* =========================================================
     * STORE
     * ========================================================= */
    public function store(Request $request)
    {
        // 1️⃣ Validation USER
        $validated = $request->validate([
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'telephone' => 'required|string|max:50',
            'role'      => 'required|in:admin,chauffeur,eleve',
            'password'  => 'required|min:6|confirmed',
        ],[
'nom.required' => 'Le nom est obligatoire',
'prenom.required' => 'Le prénom est obligatoire',
'email.required' => 'L email est obligatoire',
'email.email' => 'Format email invalide',
'email.unique' => 'Cet email existe déjà',
'telephone.required' => 'Le téléphone est obligatoire',
'role.required' => 'Veuillez choisir un rôle',
'password.required' => 'Le mot de passe est obligatoire',
'password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
'password.confirmed' => 'Les mots de passe ne correspondent pas',
]);

        $user = User::create([
            'nom'       => strtoupper($validated['nom']),
            'prenom'    =>strtoupper($validated['prenom']),
            'email'     => $validated['email'],
            'telephone' => $validated['telephone'],
            'role'      => $validated['role'],
            'password'  => Hash::make($validated['password']),
        ]);

        // 2️⃣ SI ÉLÈVE
       if ($validated['role'] === 'eleve') {

    // ✅ Validation
    $eleveData = $request->validate([
        'date_de_naissance' => 'nullable|date',
        'genre'             => 'nullable|string|max:20',
        'ecole'             => 'nullable|string|max:255',
        'niveau'            => 'nullable|string|max:255',
        'code_trajet'       => 'required|exists:trajets,code_trajet',
        'latitude'          => 'required|numeric',
        'longitude'         => 'required|numeric',
    ],[

'code_trajet.required' => 'Le code trajet est obligatoire',
'code_trajet.exists' => 'Ce code trajet n’existe pas',

'latitude.required' => 'La localisation est obligatoire',
'latitude.numeric' => 'Latitude invalide',

'longitude.required' => 'La localisation est obligatoire',
'longitude.numeric' => 'Longitude invalide',

]);

    // 🔍 Récupérer le trajet lié à un chauffeur
    $trajet = Trajet::where('code_trajet', $eleveData['code_trajet'])
        ->whereNotNull('chauffeur_id')
        ->firstOrFail();

    // 🗺️ Chercher un arrêt proche
    $arret = Arret::where('trajet_id', $trajet->id)
        ->selectRaw(
            "*, ST_Distance_Sphere(
                point(longitude, latitude),
                point(?, ?)
            ) AS distance",
            [$eleveData['longitude'], $eleveData['latitude']]
        )
        ->having('distance', '<', 30)
        ->first();

    // ➕ Créer l'arrêt s'il n'existe pas
    if (!$arret) {
        $arret = Arret::create([
            'trajet_id'    => $trajet->id,
            'nom'          => 'Arrêt élève ' . $user->nom,
            'latitude'     => $eleveData['latitude'],
            'longitude'    => $eleveData['longitude'],
            'order_number' => 0,
        ]);
    }
$adresse = $this->getAddressFromCoordinates(
    $eleveData['latitude'],
    $eleveData['longitude']
);

    // 👨‍🎓 Créer l’élève AVEC arrêt
    Eleve::create([
        'user_id'           => $user->id,
        'nom'               => $user->nom,
        'prenom'            => $user->prenom,
        'date_de_naissance' => $eleveData['date_de_naissance'] ?? null,
        'genre'             => $eleveData['genre'] ?? null,
        'ecole'             => $eleveData['ecole'] ?? null,
        'niveau'            => $eleveData['niveau'] ?? null,
        'trajet_id'         => $trajet->id,
        'arret_id'          => $arret->id,
        'adresse'           => $adresse
    ]);
}


        // 3️⃣ HISTORIQUE
        HistoriqueOperation::create([
            'user_id'     => auth()->id(),
            'action'      => 'création',
            'model_type'  => User::class,
            'model_id'    => $user->id,
            'description' => "Création utilisateur {$user->nom} {$user->prenom}",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès');
    }

    /* =========================================================
     * EDIT
     * ========================================================= */
    public function edit(User $user)
    {
        $user->load(['eleve.trajet']);
        return view('admin.users.edit', compact('user'));
    }

    /* =========================================================
     * UPDATE
     * ========================================================= */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'required|string|max:50',
            'role'      => 'required|in:admin,chauffeur,eleve',
            'password'  => 'nullable|min:6|confirmed',
        ]);

        $user->update([
            'nom'       => strtoupper($validated['nom']),
            'prenom'    => strtoupper($validated['prenom']),
            'email'     => $validated['email'],
            'telephone' => $validated['telephone'],
            'role'      => $validated['role'],
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // 🧠 ÉLÈVE
        if ($validated['role'] === 'eleve') {

    $eleveData = $request->validate([
        'date_de_naissance' => 'nullable|date',
        'genre'             => 'nullable|string|max:20',
        'ecole'             => 'nullable|string|max:255',
        'niveau'            => 'nullable|string|max:255',
        'code_trajet'       => 'required|exists:trajets,code_trajet',
        'latitude'          => 'required|numeric',
        'longitude'         => 'required|numeric',
    
    ]);

   $trajet = Trajet::where('code_trajet', $eleveData['code_trajet'])
->whereNotNull('chauffeur_id')
->first();

if(!$trajet){
return back()->withErrors([
'code_trajet' => 'Ce trajet n’a pas encore de chauffeur.'
])->withInput();
}
    $arret = Arret::where('trajet_id', $trajet->id)
        ->selectRaw(
            "*, ST_Distance_Sphere(
                point(longitude, latitude),
                point(?, ?)
            ) AS distance",
            [$eleveData['longitude'], $eleveData['latitude']]
        )
        ->having('distance', '<', 30)
        ->first();

    if (!$arret) {
        $arret = Arret::create([
            'trajet_id'    => $trajet->id,
            'nom'          => 'Arrêt élève ' . $user->nom,
            'latitude'     => $eleveData['latitude'],
            'longitude'    => $eleveData['longitude'],
            'order_number' => 0,
        ]);
    }
$adresse = $this->getAddressFromCoordinates(
    $eleveData['latitude'],
    $eleveData['longitude']
);

    $user->eleve()->updateOrCreate(
        ['user_id' => $user->id],
        [
            'nom'               => $user->nom,
            'prenom'            => $user->prenom,
            'date_de_naissance' => $eleveData['date_de_naissance'] ?? null,
            'genre'             => $eleveData['genre'] ?? null,
            'ecole'             => $eleveData['ecole'] ?? null,
            'niveau'            => $eleveData['niveau'] ?? null,
            'trajet_id'         => $trajet->id,
            'arret_id'          => $arret->id,
            'adresse'           => $adresse,
        ]
    );
}
 else {
            $user->eleve()->delete();
        }

        HistoriqueOperation::create([
            'user_id'     => auth()->id(),
            'action'      => 'modification',
            'model_type'  => User::class,
            'model_id'    => $user->id,
            'description' => "Modification utilisateur {$user->nom} {$user->prenom}",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur modifié');
    }

    /* =========================================================
     * 🔍 SEARCH AJAX
     * ========================================================= */
    public function search(Request $request)
    {
        $search = strtolower(trim($request->get('query', '')));
        $role   = $request->get('role_filter');

        $query = User::with(['eleve.trajet']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nom) LIKE ?', ["%$search%"])
                  ->orWhereRaw('LOWER(prenom) LIKE ?', ["%$search%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%$search%"])
                  ->orWhereHas('eleve.trajet', function ($q) use ($search) {
                      $q->whereRaw('LOWER(code_trajet) LIKE ?', ["%$search%"]);
                  });
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        return response()->json($query->get());
    }

    /* =========================================================
     * DESTROY
     * ========================================================= */
    public function destroy(User $user)
    {
        HistoriqueOperation::create([
            'user_id'     => auth()->id(),
            'action'      => 'suppression',
            'model_type'  => User::class,
            'model_id'    => $user->id,
            'description' => "Suppression utilisateur {$user->nom} {$user->prenom}",
            'ip_address'  => request()->ip(),
        ]);

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé');
    }
    private function getAddressFromCoordinates($latitude, $longitude)
{
    $response = Http::withHeaders([
        'User-Agent' => 'nvTransportScolaire'
    ])->get('https://nominatim.openstreetmap.org/reverse', [
        'lat' => $latitude,
        'lon' => $longitude,
        'format' => 'json'
    ]);

    if ($response->successful() && isset($response['display_name'])) {
        return $response['display_name'];
    }

    return null;
}

}
