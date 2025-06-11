<?php

namespace App\Http\Controllers;

use App\Models\DocumentEmploye;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentEmployeController extends Controller
{
    /**
     * Constructeur pour définir les middlewares
     */
    public function __construct()
    {
    
        // Note: Les middlewares ont été déplacés dans routes/web.php pour compatibilité avec Laravel 12
}

    /**
     * Affiche la liste des documents avec filtres
     */
    public function index(Request $request)
    {
        $query = DocumentEmploye::with('employe');

        // Si l'utilisateur n'a pas la permission de voir les documents confidentiels,
        // on filtre pour ne montrer que les documents non confidentiels
        if (!auth()->user()->can('voir_documents_confidentiels')) {
            $query->where('est_confidentiel', false);
        }

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('employe', function($q) use ($search) {
                      $q->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('confidentiel')) {
            // Ne permettre le filtre confidentiel que si l'utilisateur a la permission
            if (auth()->user()->can('voir_documents_confidentiels')) {
                $query->where('est_confidentiel', $request->confidentiel);
            }
        }

        if ($request->filled('expire_bientot')) {
            $jours = (int) $request->expire_bientot;
            $query->whereNotNull('date_expiration')
                  ->where('date_expiration', '<=', now()->addDays($jours))
                  ->where('date_expiration', '>', now());
        }

        // Tri
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $documents = $query->paginate(10)->withQueryString();

        return view('documents-employes.index', compact('documents'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create(Employe $employe)
    {
        return view('documents-employes.create', compact('employe'));
    }

    /**
     * Enregistre un nouveau document
     */
    public function store(Request $request, Employe $employe)
    {
        // Vérifier la permission pour les documents confidentiels
        if ($request->boolean('est_confidentiel') && !auth()->user()->can('modifier_documents_confidentiels')) {
            abort(403, 'Vous n\'avez pas la permission de créer des documents confidentiels.');
        }

        $validated = $request->validate([
            'type' => 'required|in:cv,contrat,diplome,certificat,autre',
            'titre' => 'required|string|max:255',
            'fichier' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            'date_document' => 'required|date',
            'date_expiration' => 'nullable|date|after:date_document',
            'est_confidentiel' => 'boolean',
            'description' => 'nullable|string'
        ]);

        try {
            // Gestion du fichier
            $file = $request->file('fichier');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::slug($employe->nom . '-' . $employe->prenom . '-' . $validated['titre']) . '-' . time() . '.' . $extension;
            $path = $file->storeAs('documents/employes', $filename, 'public');

            // Création du document
            $document = $employe->documents()->create([
                'type' => $validated['type'],
                'titre' => $validated['titre'],
                'fichier' => $filename,
                'date_document' => $validated['date_document'],
                'date_expiration' => $validated['date_expiration'],
                'est_confidentiel' => $request->boolean('est_confidentiel'),
                'description' => $validated['description']
            ]);

            return redirect()
                ->route('documents-employes.show', $document)
                ->with('success', 'Le document a été ajouté avec succès.');
        } catch (\Exception $e) {
            // En cas d'erreur, supprimer le fichier uploadé
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'ajout du document.');
        }
    }

    /**
     * Affiche les détails d'un document
     */
    public function show(DocumentEmploye $document)
    {
        // Vérifier les permissions d'accès
        if ($document->est_confidentiel && !auth()->user()->can('voir_documents_confidentiels')) {
            abort(403, 'Vous n\'avez pas la permission de voir ce document confidentiel.');
        }

        return view('documents-employes.show', compact('document'));
    }

    /**
     * Affiche le formulaire de modification
     */
    public function edit(DocumentEmploye $document)
    {
        // Vérifier les permissions d'accès
        if ($document->est_confidentiel && !auth()->user()->can('modifier_documents_confidentiels')) {
            abort(403, 'Vous n\'avez pas la permission de modifier ce document confidentiel.');
        }

        return view('documents-employes.edit', compact('document'));
    }

    /**
     * Met à jour un document
     */
    public function update(Request $request, DocumentEmploye $document)
    {
        // Vérifier les permissions d'accès
        if ($document->est_confidentiel && !auth()->user()->can('modifier_documents_confidentiels')) {
            abort(403, 'Vous n\'avez pas la permission de modifier ce document confidentiel.');
        }

        // Vérifier la permission pour changer le statut confidentiel
        if ($request->boolean('est_confidentiel') !== $document->est_confidentiel && 
            !auth()->user()->can('modifier_documents_confidentiels')) {
            abort(403, 'Vous n\'avez pas la permission de modifier le statut confidentiel du document.');
        }

        $validated = $request->validate([
            'type' => 'required|in:cv,contrat,diplome,certificat,autre',
            'titre' => 'required|string|max:255',
            'fichier' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            'date_document' => 'required|date',
            'date_expiration' => 'nullable|date|after:date_document',
            'est_confidentiel' => 'boolean',
            'description' => 'nullable|string'
        ]);

        try {
            // Gestion du fichier si un nouveau est uploadé
            if ($request->hasFile('fichier')) {
                $file = $request->file('fichier');
                $extension = $file->getClientOriginalExtension();
                $filename = Str::slug($document->employe->nom . '-' . $document->employe->prenom . '-' . $validated['titre']) . '-' . time() . '.' . $extension;
                $path = $file->storeAs('documents/employes', $filename, 'public');

                // Supprimer l'ancien fichier
                Storage::disk('public')->delete('documents/employes/' . $document->fichier);

                $validated['fichier'] = $filename;
            }

            // Mise à jour du document
            $document->update([
                'type' => $validated['type'],
                'titre' => $validated['titre'],
                'fichier' => $validated['fichier'] ?? $document->fichier,
                'date_document' => $validated['date_document'],
                'date_expiration' => $validated['date_expiration'],
                'est_confidentiel' => $request->boolean('est_confidentiel'),
                'description' => $validated['description']
            ]);

            return redirect()
                ->route('documents-employes.show', $document)
                ->with('success', 'Le document a été mis à jour avec succès.');
        } catch (\Exception $e) {
            // En cas d'erreur, supprimer le nouveau fichier uploadé
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du document.');
        }
    }

    /**
     * Supprime un document
     */
    public function destroy(DocumentEmploye $document)
    {
        // Vérifier les permissions d'accès
        if ($document->est_confidentiel && !auth()->user()->can('supprimer_documents_confidentiels')) {
            abort(403, 'Vous n\'avez pas la permission de supprimer ce document confidentiel.');
        }

        try {
            // Supprimer le fichier
            Storage::disk('public')->delete('documents/employes/' . $document->fichier);
            
            // Supprimer l'enregistrement
            $document->delete();

            return redirect()
                ->route('documents-employes.index')
                ->with('success', 'Le document a été supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la suppression du document.');
        }
    }

    /**
     * Télécharge un document
     */
    public function download(DocumentEmploye $document)
    {
        // Vérifier les permissions d'accès
        if ($document->est_confidentiel && !auth()->user()->can('telecharger_documents_confidentiels')) {
            abort(403, 'Vous n\'avez pas la permission de télécharger ce document confidentiel.');
        }

        $path = 'documents/employes/' . $document->fichier;
        
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Le fichier n\'existe plus.');
        }

        return Storage::disk('public')->download($path, $document->titre . '.' . pathinfo($document->fichier, PATHINFO_EXTENSION));
    }
} 