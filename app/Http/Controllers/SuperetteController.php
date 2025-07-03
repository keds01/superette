<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Superette;

class SuperetteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $superettes = Superette::orderBy('created_at', 'desc')->paginate(15);
        return view('superettes.index', compact('superettes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superettes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:superettes,code',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'actif' => 'nullable|boolean',
        ]);
        $validated['actif'] = $request->has('actif');
        $superette = Superette::create($validated);
        return redirect()->route('superettes.select')->with('success', 'Supérette créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $superette = Superette::findOrFail($id);
        
        // Récupérer les utilisateurs associés à cette superette
        $users = \App\Models\User::where('superette_id', $superette->id)->get();
        
        // Compter les produits de cette superette
        $nbProduits = \App\Models\Produit::where('superette_id', $superette->id)->count();
        
        // Compter les clients de cette superette
        $nbClients = \App\Models\Client::where('superette_id', $superette->id)->count();
        
        // Compter les ventes de cette superette
        $nbVentes = \App\Models\Vente::where('superette_id', $superette->id)->count();
        
        return view('superettes.show', compact('superette', 'users', 'nbProduits', 'nbClients', 'nbVentes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $superette = Superette::findOrFail($id);
        return view('superettes.edit', compact('superette'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $superette = Superette::findOrFail($id);
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:superettes,code,' . $superette->id,
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'actif' => 'nullable|boolean',
        ]);
        $validated['actif'] = $request->has('actif');
        $superette->update($validated);
        return redirect()->route('superettes.show', $superette)->with('success', 'Supérette mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $superette = Superette::findOrFail($id);
        $superette->delete();
        return redirect()->route('superettes.select')->with('success', 'Supérette supprimée avec succès.');
    }

    /**
     * Affiche la page de sélection de superette
     */
    public function select()
    {
        try {
            $superettes = Superette::where('actif', true)->get();
            return view('superettes.select', compact('superettes'));
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Erreur de connexion à la base de données lors de la sélection de superette: ' . $e->getMessage());
            
            // Return a friendly error view
            return view('superettes.select', [
                'superettes' => collect(),
                'error' => 'Une erreur de connexion à la base de données est survenue. Veuillez contacter l\'administrateur.'
            ]);
        }
    }

    /**
     * Active la superette sélectionnée (stocke en session et redirige)
     */
    public function activate(Request $request, $id)
    {
        $superette = Superette::findOrFail($id);
        session(['active_superette_id' => $superette->id]);
        return redirect()->route('dashboard')->with('success', 'Superette activée : ' . $superette->nom);
    }
}
