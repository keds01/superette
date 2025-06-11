<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\ContactFournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactFournisseurController extends Controller
{
    public function store(Request $request, Fournisseur $fournisseur)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'fonction' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'est_principal' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Si le contact est marqué comme principal, désactiver les autres contacts principaux
            if ($validated['est_principal']) {
                $fournisseur->contacts()->update(['est_principal' => false]);
            }

            $contact = $fournisseur->contacts()->create($validated);

            DB::commit();

            return redirect()
                ->route('fournisseurs.contacts', $fournisseur)
                ->with('success', 'Contact ajouté avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'ajout du contact : ' . $e->getMessage());
        }
    }

    public function update(Request $request, Fournisseur $fournisseur, ContactFournisseur $contact)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'fonction' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'est_principal' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Si le contact est marqué comme principal, désactiver les autres contacts principaux
            if ($validated['est_principal']) {
                $fournisseur->contacts()
                    ->where('id', '!=', $contact->id)
                    ->update(['est_principal' => false]);
            }

            $contact->update($validated);

            DB::commit();

            return redirect()
                ->route('fournisseurs.contacts', $fournisseur)
                ->with('success', 'Contact mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour du contact : ' . $e->getMessage());
        }
    }

    public function destroy(Fournisseur $fournisseur, ContactFournisseur $contact)
    {
        try {
            // Vérifier si c'est le dernier contact
            if ($fournisseur->contacts()->count() <= 1) {
                throw new \Exception('Impossible de supprimer le dernier contact du fournisseur.');
            }

            // Vérifier si c'est le contact principal
            if ($contact->est_principal) {
                throw new \Exception('Impossible de supprimer le contact principal. Veuillez d\'abord définir un autre contact comme principal.');
            }

            $contact->delete();

            return redirect()
                ->route('fournisseurs.contacts', $fournisseur)
                ->with('success', 'Contact supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression du contact : ' . $e->getMessage());
        }
    }

    public function definirPrincipal(Fournisseur $fournisseur, ContactFournisseur $contact)
    {
        try {
            DB::beginTransaction();

            // Désactiver le statut principal pour tous les autres contacts
            $fournisseur->contacts()
                ->where('id', '!=', $contact->id)
                ->update(['est_principal' => false]);

            // Définir ce contact comme principal
            $contact->update(['est_principal' => true]);

            DB::commit();

            return redirect()
                ->route('fournisseurs.contacts', $fournisseur)
                ->with('success', 'Contact principal mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la mise à jour du contact principal : ' . $e->getMessage());
        }
    }
} 