<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::orderBy('created_at', 'desc')->paginate(10);
        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClientRequest $request)
    {
        Client::create($request->validated());

        return redirect()->route('clients.index')
            ->with('success', 'Client créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $client = Client::with(['ventes' => function($query) {
            // Charger toutes les ventes, triées par date de vente (la plus récente en premier)
            $query->orderBy('date_vente', 'desc');
        }])->findOrFail($id);

        // Calculer les statistiques des ventes
        $ventesCollection = $client->ventes;
        $ventes_count = $ventesCollection->count();
        $ventes_sum_montant_total = $ventesCollection->sum('montant_total');
        
        // Obtenir la date de la dernière vente (sera une instance de Carbon ou null)
        $derniere_vente_date = $ventesCollection->first()?->date_vente;
        
        return view('clients.show', compact('client', 'ventes_count', 'ventes_sum_montant_total', 'derniere_vente_date'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $client = Client::findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClientRequest $request, string $id)
    {
        $client = Client::findOrFail($id);
        $client->update($request->validated());

        return redirect()->route('clients.index')
            ->with('success', 'Client mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);
        
        // Vérifier si le client a des ventes associées
        if ($client->ventes()->count() > 0) {
            return redirect()->route('clients.index')
                ->with('error', 'Ce client ne peut pas être supprimé car il a des ventes associées.');
        }
        
        $client->delete();
        
        return redirect()->route('clients.index')
            ->with('success', 'Client supprimé avec succès.');
    }
}
