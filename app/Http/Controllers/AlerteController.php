<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlerteController extends Controller
{
    public function create()
    {
        $produits = Produit::with('categorie')
            ->orderBy('nom')
            ->get();
            
        return view('alertes.create', compact('produits'));
    }
    
    public function index(Request $request)
    {
        $query = Alerte::with(['produit.categorie', 'produit.uniteVente'])
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('produit', function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut')) {
            if ($request->statut === 'active') {
                $query->where('estDeclenchee', false);
            } elseif ($request->statut === 'declenchee') {
                $query->where('estDeclenchee', true);
            }
        } else {
            // Par défaut, n'afficher que les alertes déclenchées
            $query->where('estDeclenchee', true);
        }

        if ($request->filled('tri')) {
            switch ($request->tri) {
                case 'ancien':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'urgence':
                    $query->orderBy('estDeclenchee', 'desc')
                          ->orderBy('created_at', 'desc');
                    break;
                case 'produit':
                    $query->join('produits', 'alertes.produit_id', '=', 'produits.id')
                          ->orderBy('produits.nom')
                          ->select('alertes.*');
                    break;
                default: // recent
                    $query->orderBy('created_at', 'desc');
            }
        }

        $alertesQuery = $query;
        // Si aucun filtre statut, filtrer dynamiquement sur l'état déclenché (méthode estDeclenchee)
        if (!$request->filled('statut')) {
            $alertesCollection = $alertesQuery->get();
            $filtered = $alertesCollection->filter(fn($a) => $a->estDeclenchee());
            $page = $request->input('page', 1);
            $perPage = 10;
            $alertes = new \Illuminate\Pagination\LengthAwarePaginator(
                $filtered->forPage($page, $perPage)->values(),
                $filtered->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $alertes = $alertesQuery->paginate(10)->withQueryString();
        }

        // Historique (uniquement déclenchées)
        $historiqueAlertes = Alerte::with(['produit.categorie', 'produit.uniteVente'])
            ->where(function($q){ $q->where('type','like','stock%')->orWhere('type','like','peremption%'); })
            ->orderBy('created_at','desc')
            ->paginate(10);

        // Récupération des produits pour le formulaire de création
        $produits = Produit::with('categorie')
            ->orderBy('nom')
            ->get();

        return view('alertes.index', compact('alertes', 'historiqueAlertes', 'produits'));
    }

    public function store(Request $request)
    {
        $rules = [
            'produit_id' => 'required|exists:produits,id',
            'type' => 'required|in:seuil_minimum,seuil_maximum,peremption',
            'message' => 'nullable|string|max:255'
        ];

        // Ajouter des règles spécifiques selon le type d'alerte
        if ($request->type === 'peremption') {
            $rules['periode'] = 'required|integer|min:1';
            $rules['date_peremption'] = 'nullable|date';
        } else {
            $rules['seuil'] = 'required|numeric|min:0';
        }

        $validated = $request->validate($rules);

        // Si c'est une alerte de péremption et que la date de péremption n'est pas fournie,
        // essayer de récupérer celle du produit
        if ($request->type === 'peremption' && !isset($validated['date_peremption'])) {
            $produit = Produit::find($request->produit_id);
            if ($produit && $produit->date_peremption) {
                $validated['date_peremption'] = $produit->date_peremption;
            }
        }

        // Ajouter automatiquement la superette active
        // Utiliser active_superette_id qui est défini par le middleware CheckSuperetteSelected
        $validated['superette_id'] = session('active_superette_id');
        
        // Si pour une raison quelconque la superette_id n'est pas dans la session, utiliser celle de l'utilisateur
        if (!$validated['superette_id'] && auth()->check() && auth()->user()->superette_id) {
            $validated['superette_id'] = auth()->user()->superette_id;
        }
        
        // Si toujours pas de superette_id, utiliser la superette par défaut (ID 1)
        if (!$validated['superette_id']) {
            $validated['superette_id'] = 1; // Superette par défaut
        }

        $alerte = Alerte::create($validated);

        return redirect()->route('alertes.index')
            ->with('success', 'Alerte créée avec succès.');
    }

    public function update(Request $request, Alerte $alerte)
    {
        $validated = $request->validate([
            'seuil' => 'required|numeric|min:0',
            'message' => 'nullable|string|max:255'
        ]);

        // S'assurer que la superette_id reste celle de l'alerte existante
        // ou utiliser celle de la session si l'alerte n'en a pas
        if (!$alerte->superette_id) {
            // Utiliser active_superette_id qui est défini par le middleware CheckSuperetteSelected
            $validated['superette_id'] = session('active_superette_id');
            
            // Si pour une raison quelconque la superette_id n'est pas dans la session, utiliser celle de l'utilisateur
            if (!$validated['superette_id'] && auth()->check() && auth()->user()->superette_id) {
                $validated['superette_id'] = auth()->user()->superette_id;
            }
            
            // Si toujours pas de superette_id, utiliser la superette par défaut (ID 1)
            if (!$validated['superette_id']) {
                $validated['superette_id'] = 1; // Superette par défaut
            }
        }

        $alerte->update($validated);

        return redirect()->route('alertes.index')
            ->with('success', 'Alerte mise à jour avec succès.');
    }

    public function destroy(Alerte $alerte)
    {
        $alerte->delete();

        return redirect()->route('alertes.index')
            ->with('success', 'Alerte supprimée avec succès.');
    }

    public function verifierAlertes()
    {
        $alertes = Alerte::with('produit')->get();
        $alertesDeclenchees = [];
        
        // Récupérer la superette active de manière robuste
        $superetteId = session('active_superette_id');
        if (!$superetteId && auth()->check() && auth()->user()->superette_id) {
            $superetteId = auth()->user()->superette_id;
        }
        if (!$superetteId) {
            $superetteId = 1; // Superette par défaut
        }

        // Vérification des alertes configurées
        foreach ($alertes as $alerte) {
            $produit = $alerte->produit;
            $estDeclenchee = false;

            if ($alerte->type === 'seuil_minimum' && $produit->stock <= $alerte->seuil) {
                $estDeclenchee = true;
            } elseif ($alerte->type === 'seuil_maximum' && $produit->stock >= $alerte->seuil) {
                $estDeclenchee = true;
            } elseif ($alerte->type === 'peremption' && $produit->date_peremption) {
                // Utiliser la période configurée dans l'alerte (pour les alertes manuelles)
                $joursRestants = now()->diffInDays($produit->date_peremption, false); // false pour obtenir des valeurs négatives si dépassé
                if ($joursRestants <= $alerte->periode && $joursRestants >= 0) {
                    $estDeclenchee = true;
                }
            }

            if ($estDeclenchee && !$alerte->estDeclenchee) {
                $alerte->update([
                    'estDeclenchee' => true,
                    'superette_id' => $alerte->superette_id ?? $superetteId
                ]);
                $alertesDeclenchees[] = [
                    'produit' => $produit->nom,
                    'type' => $alerte->type,
                    'message' => $alerte->message ?? "Le stock de {$produit->nom} a atteint le seuil défini"
                ];
            } elseif (!$estDeclenchee && $alerte->estDeclenchee) {
                $alerte->update(['estDeclenchee' => false]);
            }
        }
        
        // Vérification automatique des produits sans alertes configurées
        // Pour les produits en alerte stock ou proche de péremption
        $produitsSansAlerte = Produit::whereDoesntHave('alerts')
            ->where(function ($query) {
                $query->whereRaw('stock <= seuil_alerte')
                    ->orWhere(function ($q) {
                        $q->whereNotNull('date_peremption')
                          // Utiliser un intervalle de 30 jours par défaut
                          ->whereRaw('date_peremption <= DATE_ADD(NOW(), INTERVAL 30 DAY)');
                    });
            })
            ->get();
            
        foreach ($produitsSansAlerte as $produit) {
            // Créer une alerte automatique pour le stock
            if ($produit->estEnAlerteStock()) {
                $alerte = Alerte::firstOrCreate(
                    ['produit_id' => $produit->id, 'type' => 'seuil_minimum'],
                    [
                        'seuil' => $produit->seuil_alerte,
                        'message' => "Le stock de {$produit->nom} est inférieur au seuil minimum ({$produit->seuil_alerte})",
                        'estDeclenchee' => true,
                        'actif' => true,
                        'superette_id' => $superetteId
                    ]
                );
                
                $alertesDeclenchees[] = [
                    'produit' => $produit->nom,
                    'type' => 'seuil_minimum',
                    'message' => "Le stock de {$produit->nom} est inférieur au seuil minimum ({$produit->seuil_alerte})"
                ];
            }
            
            // Créer une alerte automatique pour la date de péremption
            if ($produit->estProchePeremption()) {
                $joursRestants = now()->diffInDays($produit->date_peremption);
                // Utiliser le délai personnalisé ou la valeur par défaut de 30 jours
                $delaiAlerte = $produit->delai_alerte_peremption ?? Produit::DELAI_ALERTE_PEREMPTION_DEFAUT;
                
                $alerte = Alerte::firstOrCreate(
                    ['produit_id' => $produit->id, 'type' => 'peremption'],
                    [
                        'periode' => $delaiAlerte,
                        'message' => "Le produit {$produit->nom} va expirer dans {$joursRestants} jour(s)",
                        'estDeclenchee' => true,
                        'actif' => true,
                        'date_peremption' => $produit->date_peremption,
                        'superette_id' => $superetteId
                    ]
                );
                
                $alertesDeclenchees[] = [
                    'produit' => $produit->nom,
                    'type' => 'peremption',
                    'message' => "Le produit {$produit->nom} va expirer dans {$joursRestants} jour(s)"
                ];
            }
        }

        return $alertesDeclenchees;
    }
}