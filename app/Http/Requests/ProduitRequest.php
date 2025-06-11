<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProduitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        // Récupération correcte de l'ID du produit à partir de la route
        $produitId = $this->route('produit') ? $this->route('produit')->id : null;
        
        return [
            'nom' => 'required|string|max:255',
            'reference' => 'required|string|max:50|unique:produits,reference,' . $produitId,
            'code_barres' => 'nullable|string|max:50|unique:produits,code_barres,' . $produitId,
            'categorie_id' => 'required|integer|exists:categories,id',
            'description' => 'nullable|string',
            'unite_vente_id' => 'required|exists:unites,id',
            'conditionnement_fournisseur' => 'required|string|max:255',
            'quantite_par_conditionnement' => 'required|numeric|min:1',
            'stock_initial' => 'required|numeric|min:0',
            'seuil_alerte' => 'required|numeric|min:0',
            'emplacement_rayon' => 'required|string|max:50',
            'emplacement_etagere' => 'required|string|max:50',
            'date_peremption' => 'nullable|date|after_or_equal:today',
            'prix_achat_ht' => 'required|numeric|min:0|max:999999999.99',
            'marge' => 'required|numeric|min:0|max:1000',
            'tva' => 'required|numeric|min:0|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048|dimensions:min_width=100,min_height=100',
            // Champs pré-calculés (optionnels car générés par JS)
            'prix_vente_ht' => 'nullable|numeric',
            'prix_vente_ttc' => 'nullable|numeric',
            'valeur_marge' => 'nullable|numeric',
            'valeur_tva' => 'nullable|numeric',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'nom.required' => 'Le nom du produit est obligatoire.',
            'reference.required' => 'La référence est obligatoire.',
            'reference.unique' => 'Cette référence existe déjà.',
            'code_barres.unique' => 'Ce code-barres existe déjà.',
            'categorie_id.required' => 'La catégorie est obligatoire.',
            'categorie_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'unite_vente_id.required' => 'L\'unité de vente est obligatoire.',
            'unite_vente_id.exists' => 'L\'unité de vente sélectionnée n\'existe pas.',
            'conditionnement_fournisseur.required' => 'Le conditionnement fournisseur est obligatoire.',
            'quantite_par_conditionnement.required' => 'La quantité par conditionnement est obligatoire.',
            'quantite_par_conditionnement.min' => 'La quantité par conditionnement doit être au moins 1.',
            'stock_initial.required' => 'Le stock initial est obligatoire.',
            'stock_initial.min' => 'Le stock initial ne peut pas être négatif.',
            'seuil_alerte.required' => 'Le seuil d\'alerte est obligatoire.',
            'seuil_alerte.min' => 'Le seuil d\'alerte ne peut pas être négatif.',
            'emplacement_rayon.required' => 'L\'emplacement en rayon est obligatoire.',
            'emplacement_etagere.required' => 'L\'emplacement en étagère est obligatoire.',
            'date_peremption.date' => 'La date de péremption n\'est pas une date valide.',
            'date_peremption.after_or_equal' => 'La date de péremption doit être aujourd\'hui ou une date future.',
            'prix_achat_ht.required' => 'Le prix d\'achat HT est obligatoire.',
            'prix_achat_ht.min' => 'Le prix d\'achat HT ne peut pas être négatif.',
            'marge.required' => 'La marge est obligatoire.',
            'marge.min' => 'La marge ne peut pas être négative.',
            'tva.required' => 'Le taux de TVA est obligatoire.',
            'tva.min' => 'Le taux de TVA ne peut pas être négatif.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être de type : jpeg, png, jpg, gif.',
            'image.max' => 'L\'image ne doit pas dépasser 2MB.',
            'image.dimensions' => 'Les dimensions minimales de l\'image sont 100x100 pixels.'
        ];
    }

    /**
     * Préparer les données validées pour la création/modification
     *
     * @return array
     */
    public function produitData()
    {
        $validated = $this->validated();
        
        // Récupérer les valeurs des champs numériques
        $prix_achat_ht = (float) $validated['prix_achat_ht'];
        $marge = (float) $validated['marge'];
        $tva = (float) $validated['tva'];
        
        // Utiliser les valeurs précalculées si présentes, sinon les calculer
        $prix_vente_ht = isset($validated['prix_vente_ht']) 
            ? (float) $validated['prix_vente_ht']
            : round($prix_achat_ht * (1 + $marge / 100), 2);
            
        $prix_vente_ttc = isset($validated['prix_vente_ttc'])
            ? (float) $validated['prix_vente_ttc']
            : round($prix_vente_ht * (1 + $tva / 100), 2);
        
        return [
            'nom' => $validated['nom'],
            'reference' => $validated['reference'],
            'code_barres' => $validated['code_barres'] ?? null,
            'categorie_id' => (int) $validated['categorie_id'],
            'description' => $validated['description'] ?? null,
            'unite_vente_id' => (int) $validated['unite_vente_id'],
            'conditionnement_fournisseur' => $validated['conditionnement_fournisseur'],
            'quantite_par_conditionnement' => $validated['quantite_par_conditionnement'],
            'stock' => $validated['stock_initial'] ?? 0,
            'seuil_alerte' => $validated['seuil_alerte'],
            'emplacement_rayon' => $validated['emplacement_rayon'],
            'emplacement_etagere' => $validated['emplacement_etagere'],
            'date_peremption' => $validated['date_peremption'] ?? null,
            'prix_achat_ht' => $prix_achat_ht,
            'prix_vente_ht' => $prix_vente_ht,
            'prix_vente_ttc' => $prix_vente_ttc,
            'marge' => $marge,
            'tva' => $tva,
            'actif' => true
        ];
    }
}
