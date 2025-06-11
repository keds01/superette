<?php

namespace App\Http\Controllers;

use App\Models\Unite;
use App\Models\Produit; // Pour stats produits liés
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        // Statistiques
        $stats = [
            'total' => Unite::count(),
            'active' => Unite::where('actif', true)->count(),
            'used' => Unite::has('produits')->count(),
            'total_products' => Produit::count(),
        ];

        $query = Unite::withCount('produits');

        // Recherche
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nom', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('symbole', 'like', "%{$searchTerm}%");
            });
        }

        // Filtre statut
        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('actif', true);
            } elseif ($request->input('status') === 'inactive') {
                $query->where('actif', false);
            }
        }

        // Tri
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $validSortFields = ['nom', 'created_at', 'products_count'];
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'created_at';
        }
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        $query->orderBy($sortField, $sortDirection);

        $unites = $query->paginate(10)->appends($request->query());

        return view('units.index', compact('unites', 'stats'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:unites',
            'symbole' => 'required|string|max:10|unique:unites',
            'description' => 'nullable|string'
        ]);

        Unite::create($validated);

        return redirect()
            ->route('units.index')
            ->with('success', 'Unité créée avec succès.');
    }

    public function edit(Unite $unite)
    {
        return view('units.edit', compact('unite'));
    }

    public function update(Request $request, Unite $unite)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:unites,nom,' . $unite->id,
            'symbole' => 'required|string|max:10|unique:unites,symbole,' . $unite->id,
            'description' => 'nullable|string'
        ]);

        $unite->update($validated);

        return redirect()
            ->route('unites.index')
            ->with('success', 'Unité mise à jour avec succès.');
    }

    public function destroy(Unite $unite)
    {
        if ($unite->produits()->exists()) {
            return redirect()
                ->route('unites.index')
                ->with('error', 'Impossible de supprimer cette unité car elle est utilisée par des produits.');
        }

        $unite->delete();

        return redirect()
            ->route('unites.index')
            ->with('success', 'Unité supprimée avec succès.');
    }
} 