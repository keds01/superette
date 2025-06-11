<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Produit; // Ajout de l'import pour Produit
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // Calcul des statistiques pour la vue
        $stats = [
            'total' => Categorie::count(),
            'active' => Categorie::where('actif', true)->count(),
            'with_products' => Categorie::has('products')->count(),
            'total_products' => Produit::count(),
        ];

        $query = Categorie::withCount('products');

        // Gestion de la recherche
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nom', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Gestion du filtre par statut
        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('actif', true);
            } elseif ($request->input('status') === 'inactive') {
                $query->where('actif', false);
            }
        }

        // Gestion du tri
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');

        // Valider les champs de tri pour éviter les erreurs
        $validSortFields = ['nom', 'created_at', 'products_count'];
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'created_at'; // Champ par défaut si invalide
        }
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc'; // Direction par défaut si invalide
        }

        $query->orderBy($sortField, $sortDirection);

        $categories = $query->paginate(10)->appends($request->query());

        return view('categories.index', compact('categories', 'stats'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|unique:categories',
            'description' => 'required'
        ]);

        Categorie::create([
            'nom' => $request->nom,
            'slug' => Str::slug($request->nom),
            'description' => $request->description
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    public function show(Categorie $categorie)
    {
        // Chargement relation produits et parent, et comptage alertes si présent
        $categorie->load(['products', 'parent']);
        $categorie->products_count = $categorie->products->count();
        // Si la relation alerts existe, compter aussi
        $categorie->alerts_count = method_exists($categorie, 'alerts') ? $categorie->alerts()->count() : 0;
        return view('categories.show', compact('categorie'));
    }

    public function edit(Categorie $categorie)
    {
        return view('categories.edit', compact('categorie'));
    }

    public function update(Request $request, Categorie $categorie)
    {
        $request->validate([
            'nom' => 'required|unique:categories,nom,' . $categorie->id,
            'description' => 'required'
        ]);

        $categorie->update([
            'nom' => $request->nom,
            'slug' => Str::slug($request->nom),
            'description' => $request->description
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(Categorie $categorie)
    {
        if ($categorie->products()->exists()) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient des produits.');
        }

        $categorie->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
} 