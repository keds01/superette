<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\ContactFournisseur;
use App\Models\EvaluationFournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FournisseurController extends Controller
{
    public function index(Request $request)
    {
        $query = Fournisseur::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('contact_principal_nom', 'like', "%{$search}%")
                  ->orWhere('contact_principal_prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $fournisseurs = $query->latest()->paginate(10);

        // Statistiques
        $totalFournisseurs = Fournisseur::count();
        $fournisseursActifs = Fournisseur::where('actif', true)->count();
        $fournisseursInactifs = $totalFournisseurs - $fournisseursActifs;

        return view('fournisseurs.index', compact('fournisseurs', 'totalFournisseurs', 'fournisseursActifs', 'fournisseursInactifs'));
    }

    public function create()
    {
        return view('fournisseurs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:fournisseurs',
            'contact_principal_nom' => 'required|string|max:255',
            'contact_principal_prenom' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string',
            'ville' => 'required|string|max:100',
            'code_postal' => 'required|string|max:20',
            'pays' => 'required|string|max:100',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Générer un code unique pour le fournisseur si non fourni
            if (empty($validated['code'])) {
                do {
                    $code = 'FOUR-' . strtoupper(Str::random(6));
                } while (Fournisseur::where('code', $code)->exists());
                $validated['code'] = $code;
            }
            
            $validated['statut'] = 'actif';
            $validated['contact_principal'] = $validated['contact_principal_nom'];

            $fournisseur = Fournisseur::create($validated);

            // Créer le contact principal
            $fournisseur->contacts()->create([
                'nom' => $validated['contact_principal_nom'],
                'prenom' => $validated['contact_principal_prenom'] ?? '',
                'fonction' => 'Contact Principal',
                'telephone' => $validated['telephone'],
                'email' => $validated['email'],
                'est_principal' => true
            ]);

            DB::commit();

            return redirect()
                ->route('fournisseurs.show', $fournisseur)
                ->with('success', 'Fournisseur créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log l'erreur pour le débogage
            \Log::error('Erreur lors de la création du fournisseur', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Erreur technique : ' . $e->getMessage()); // Affiche le message technique réel
        }
    }

    public function show(Fournisseur $fournisseur)
    {
        $fournisseur->load(['contacts', 'evaluations' => function ($query) {
            $query->latest()->take(5);
        }, 'approvisionnements' => function ($query) {
            $query->latest()->take(5);
        }]);

        $statistiques = [
            'total_commandes' => $fournisseur->nombre_commandes,
            'montant_total_commandes' => $fournisseur->montant_total_commandes,
            'delai_moyen_livraison' => $fournisseur->delai_moyen_livraison,
            'note_moyenne' => $fournisseur->note_moyenne,
            'solde_total' => $fournisseur->solde_total
        ];

        return view('fournisseurs.show', compact('fournisseur', 'statistiques'));
    }

    public function edit(Fournisseur $fournisseur)
    {
        return view('fournisseurs.edit', compact('fournisseur'));
    }

    public function update(Request $request, Fournisseur $fournisseur)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:fournisseurs,code,' . $fournisseur->id,
            'contact_principal_nom' => 'required|string|max:255',
            'contact_principal_prenom' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string',
            'ville' => 'required|string|max:100',
            'code_postal' => 'required|string|max:20',
            'pays' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'actif' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $fournisseur->update($validated);

            // Mettre à jour ou créer le contact principal
            $contactPrincipal = $fournisseur->contacts()->where('est_principal', true)->first();
            
            if ($contactPrincipal) {
                $contactPrincipal->update([
                    'nom' => $validated['contact_principal_nom'],
                    'prenom' => $validated['contact_principal_prenom'] ?? '',
                    'telephone' => $validated['telephone'],
                    'email' => $validated['email']
                ]);
            } else {
                 // Créer le contact principal s'il n'existe pas (cas inattendu ou migration de données)
                 $fournisseur->contacts()->create([
                    'nom' => $validated['contact_principal_nom'],
                    'prenom' => $validated['contact_principal_prenom'] ?? '',
                    'fonction' => 'Contact Principal',
                    'telephone' => $validated['telephone'],
                    'email' => $validated['email'],
                    'est_principal' => true
                ]);
            }

            DB::commit();

            return redirect()
                ->route('fournisseurs.show', $fournisseur)
                ->with('success', 'Fournisseur mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
             // Log l'erreur pour le débogage
            \Log::error('Erreur lors de la mise à jour du fournisseur', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour du fournisseur. Veuillez vérifier les informations et réessayer.'); // Message d'erreur générique
        }
    }

    public function destroy(Fournisseur $fournisseur)
    {
        try {
            // Vérifier si le fournisseur a des approvisionnements en cours
            if ($fournisseur->approvisionnements()->where('statut', '!=', 'annulee')->exists()) {
                throw new \Exception('Impossible de supprimer un fournisseur avec des approvisionnements en cours.');
            }

            // Vérifier si le fournisseur a un solde
            if ($fournisseur->solde_actuel > 0) {
                throw new \Exception('Impossible de supprimer un fournisseur avec un solde positif.');
            }

            $fournisseur->delete();

            return redirect()
                ->route('fournisseurs.index')
                ->with('success', 'Fournisseur supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression du fournisseur : ' . $e->getMessage());
        }
    }

    public function evaluer(Request $request, Fournisseur $fournisseur)
    {
        $validated = $request->validate([
            'qualite_produits' => 'required|integer|min:0|max:10',
            'delai_livraison' => 'required|integer|min:0|max:10',
            'prix_competitifs' => 'required|integer|min:0|max:10',
            'service_client' => 'required|integer|min:0|max:10',
            'commentaire' => 'nullable|string',
            'recommandation' => 'nullable|string'
        ]);

        try {
            $evaluation = $fournisseur->evaluations()->create([
                'user_id' => auth()->check() ? auth()->id() : 1, // User ID 1 par défaut si non connecté
                'date_evaluation' => now(),
                'qualite_produits' => $validated['qualite_produits'],
                'delai_livraison' => $validated['delai_livraison'],
                'prix_competitifs' => $validated['prix_competitifs'],
                'service_client' => $validated['service_client'],
                'commentaire' => $validated['commentaire'],
                'recommandation' => $validated['recommandation']
            ]);

            return redirect()
                ->route('fournisseurs.show', $fournisseur)
                ->with('success', 'Évaluation enregistrée avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement de l\'évaluation : ' . $e->getMessage());
        }
    }

    public function contacts(Fournisseur $fournisseur)
    {
        $contacts = $fournisseur->contacts()->paginate(10);
        return view('fournisseurs.contacts', compact('fournisseur', 'contacts'));
    }

    public function evaluations(Fournisseur $fournisseur)
    {
        $evaluations = $fournisseur->evaluations()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('fournisseurs.evaluations', compact('fournisseur', 'evaluations'));
    }

    public function approvisionnements(Fournisseur $fournisseur)
    {
        $approvisionnements = $fournisseur->approvisionnements()
            ->with(['user', 'details.produit'])
            ->latest()
            ->paginate(10);

        return view('fournisseurs.approvisionnements', compact('fournisseur', 'approvisionnements'));
    }

    public function factures(Fournisseur $fournisseur)
    {
        $factures = $fournisseur->factures()
            ->with('approvisionnement')
            ->latest()
            ->paginate(10);

        return view('fournisseurs.factures', compact('fournisseur', 'factures'));
    }
} 