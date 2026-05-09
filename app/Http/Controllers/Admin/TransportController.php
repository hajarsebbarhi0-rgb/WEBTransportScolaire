<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transport;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    // Ajout du middleware admin (si non fait via les routes)
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role === 'admin') {
                return $next($request);
            }
            abort(403, 'Accès interdit');
        });
    }

    /**
     * Affiche la liste des transports (utilisée pour le premier chargement).
     */
    public function index()
    {
        // Pas de filtre ici, on charge tous les transports pour le chargement initial de la vue
        // On applique le tri ici aussi pour garantir un ordre initial cohérent
       $transports = Transport::orderBy('marque', 'asc')->orderBy('modele', 'asc')->paginate(10);
        return view('admin.transports.index', compact('transports'));
    }

    /**
     * Gère la recherche dynamique (AJAX) des transports.
     */
    // ... code inchangé avant ...

    /**
     * Gère la recherche dynamique (AJAX) des transports.
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');
        $statusFilter = $request->get('status_filter', '');
        $searchTerm = strtolower(trim($query));

        $queryBuilder = Transport::query();

        // Application du filtre de recherche (Marque, Modèle, Plaque)
        if (!empty($searchTerm)) {
            $queryBuilder->where(function ($q) use ($searchTerm) {
                // Utilise la recherche "contient" (%recherche%) pour la sélection
                $q->whereRaw('LOWER(marque) LIKE ?', ["%{$searchTerm}%"])
                  ->orWhereRaw('LOWER(modele) LIKE ?', ["%{$searchTerm}%"])
                  ->orWhereRaw('LOWER(plaque_immatriculation) LIKE ?', ["%{$searchTerm}%"]);
            });
        }
        
        // Application du filtre par état (Status)
        if (!empty($statusFilter)) {
            $queryBuilder->where('status', $statusFilter);
        }

        // MODIFICATION FINALE : Tri pour prioriser les résultats qui COMMENCENT par le terme
        if (!empty($searchTerm)) {
            $queryBuilder->orderByRaw("
                CASE 
                    WHEN LOWER(marque) LIKE '{$searchTerm}%' THEN 0 
                    WHEN LOWER(modele) LIKE '{$searchTerm}%' THEN 1 
                    WHEN LOWER(plaque_immatriculation) LIKE '{$searchTerm}%' THEN 2 
                    ELSE 3 
                END
            ");
        }

        // Tri secondaire alphabétique pour les résultats ayant la même priorité
        $queryBuilder->orderBy('marque', 'asc')
                     ->orderBy('modele', 'asc');

        $transports = $queryBuilder->get();

        // On retourne les résultats au format JSON
        return response()->json($transports);
    }
    


    /**
     * Affiche le formulaire de création d'un nouveau transport.
     */
    public function create()
    {
        return view('admin.transports.create');
    }

    /**
     * Enregistre un nouveau transport dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'plaque_immatriculation' => 'required|string|unique:transports,plaque_immatriculation|max:255',
            'capacite_passagers' => 'required|integer|min:1',
            'status' => 'required|string|in:disponible,en_service,en_maintenance',
        ],[
        'marque.required' => 'La marque du transport est obligatoire',
        'marque.max' => 'La marque ne doit pas dépasser 255 caractères',

        'modele.required' => 'Le modèle du transport est obligatoire',
        'modele.max' => 'Le modèle ne doit pas dépasser 255 caractères',

        'plaque_immatriculation.required' => 'La plaque d’immatriculation est obligatoire',
        'plaque_immatriculation.unique' => 'Cette plaque d’immatriculation existe déjà',
        'plaque_immatriculation.max' => 'La plaque ne doit pas dépasser 255 caractères',

        'capacite_passagers.required' => 'La capacité des passagers est obligatoire',
        'capacite_passagers.integer' => 'La capacité doit être un nombre',
        'capacite_passagers.min' => 'La capacité doit être au moins 1 passager',

        'status.required' => 'Veuillez choisir le statut du transport',
        'status.in' => 'Statut invalide',
    ]);

        Transport::create($request->all());

        return redirect()->route('admin.transports.index')
            ->with('success', 'Transport créé avec succès.');
    }

    /**
     * Affiche les détails d'un transport spécifique.
     */
    public function show(Transport $transport)
    {
        return view('admin.transports.show', compact('transport'));
    }

    /**
     * Affiche le formulaire de modification d'un transport.
     */
    public function edit(Transport $transport)
    {
        return view('admin.transports.edit', compact('transport'));
    }

    /**
     * Met à jour les informations d'un transport dans la base de données.
     */
    public function update(Request $request, Transport $transport)
    {
        $request->validate([
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'plaque_immatriculation' => 'required|string|max:255|unique:transports,plaque_immatriculation,' . $transport->id,
            'capacite_passagers' => 'required|integer|min:1',
            'status' => 'required|string|in:disponible,en_service,en_maintenance',
        ],[
        'marque.required' => 'La marque du transport est obligatoire',
        'marque.max' => 'La marque ne doit pas dépasser 255 caractères',

        'modele.required' => 'Le modèle du transport est obligatoire',
        'modele.max' => 'Le modèle ne doit pas dépasser 255 caractères',

        'plaque_immatriculation.required' => 'La plaque d’immatriculation est obligatoire',
        'plaque_immatriculation.unique' => 'Cette plaque d’immatriculation existe déjà',

        'capacite_passagers.required' => 'La capacité des passagers est obligatoire',
        'capacite_passagers.integer' => 'La capacité doit être un nombre',
        'capacite_passagers.min' => 'La capacité doit être au moins 1 passager',

        'status.required' => 'Veuillez choisir le statut du transport',
        'status.in' => 'Statut invalide',
    ]);

        $transport->update($request->all());

        return redirect()->route('admin.transports.index')
            ->with('success', 'Transport mis à jour avec succès.');
    }

    /**
     * Supprime un transport de la base de données.
     */
    public function destroy(Transport $transport)
    {
        $transport->delete();

        return redirect()->route('admin.transports.index')
            ->with('success', 'Transport supprimé avec succès.');
    }
}