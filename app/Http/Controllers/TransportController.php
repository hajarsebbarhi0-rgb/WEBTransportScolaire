<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transport;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    /**
     * Display a listing of the resource.
     * Affiche la liste des transports.
     */
    public function index()
    {
        $transports = Transport::all();
        return view('admin.transports.index', compact('transports'));
    }

    /**
     * Show the form for creating a new resource.
     * Affiche le formulaire de création d'un nouveau transport.
     */
    public function create()
    {
        return view('admin.transports.create');
    }

    /**
     * Store a newly created resource in storage.
     * Enregistre un nouveau transport dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'plaque_immatriculation' => 'required|string|unique:transports,plaque_immatriculation|max:255',
            'capacite_passagers' => 'required|integer|min:1',
            'status' => 'required|string',
        ]);

        Transport::create([
            'marque' => $request->marque,
            'modele' => $request->modele,
            'plaque_immatriculation' => $request->plaque_immatriculation,
            'capacite_passagers' => $request->capacite_passagers,
            'status' => 'required|string',
        ]);

        return redirect()->route('admin.transports.index')
            ->with('success', 'Transport créé avec succès.');
    }

    /**
     * Display the specified resource.
     * Affiche les détails d'un transport spécifique.
     */
    public function show(Transport $transport)
    {
        return view('admin.transports.show', compact('transport'));
    }

    /**
     * Show the form for editing the specified resource.
     * Affiche le formulaire de modification d'un transport.
     */
    public function edit(Transport $transport)
    {
        return view('admin.transports.edit', compact('transport'));
    }

    /**
     * Update the specified resource in storage.
     * Met à jour les informations d'un transport dans la base de données.
     */
    public function update(Request $request, Transport $transport)
    {
        $request->validate([
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'plaque_immatriculation' => 'required|string|max:255|unique:transports,plaque_immatriculation,' . $transport->id,
            'capacite_passagers' => 'required|integer|min:1',
            'status' => 'required|string',
        ]);

        $transport->update([
            'marque' => $request->marque,
            'modele' => $request->modele,
            'plaque_immatriculation' => $request->plaque_immatriculation,
            'capacite_passagers' => $request->capacite_passagers,
            'status' => 'required|string',
        ]);

        return redirect()->route('admin.transports.index')
            ->with('success', 'Transport mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     * Supprime un transport de la base de données.
     */
    public function destroy(Transport $transport)
    {
        $transport->delete();

        return redirect()->route('admin.transports.index')
            ->with('success', 'Transport supprimé avec succès.');
    }
}
