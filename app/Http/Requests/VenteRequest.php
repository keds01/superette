<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Produit;

class VenteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $produits = $this->input('produits');

        // Check if 'produits' is a string (likely JSON from frontend)
        if (is_string($produits)) {
            // Attempt to decode the JSON string into an array
            $produits = json_decode($produits, true);

            // If decoding failed or resulted in a non-array, set to empty array
            if (!is_array($produits)) {
                $produits = [];
            }
        }

        // Merge the potentially decoded 'produits' back into the request data
        $this->merge([
            'produits' => $produits,
        ]);
    }

    public function rules()
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'type_vente' => 'required|in:sur_place,a_emporter,livraison',
            // 'date_vente' supprimé : la date est définie automatiquement dans le contrôleur
            'produits' => 'required|array|min:1',
            'produits.*.produit_id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|numeric|min:0.01',
            'produits.*.prix_unitaire' => 'required|numeric|min:0',
            'produits.*.remise' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:500'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('produits')) {
                foreach ($this->produits as $index => $produit) {
                    // Ensure $produit is an array and has 'produit_id' before accessing keys
                    if (!is_array($produit) || !isset($produit['produit_id'])) {
                         $validator->errors()->add(
                            "produits.{$index}",
                            "Format de produit invalide."
                        );
                        continue; // Skip to the next item
                    }

                    $stock = DB::table('produits')
                        ->where('id', $produit['produit_id'])
                        ->value('stock');

                    // Ensure stock and quantite are numeric before comparison
                    $quantiteDemandee = $produit['quantite'] ?? 0;

                    if (!is_numeric($stock) || !is_numeric($quantiteDemandee) || $stock < $quantiteDemandee) {
                        $validator->errors()->add(
                            "produits.{$index}.quantite",
                            "Stock insuffisant ou données invalides. Stock disponible : " . ($stock ?? 'N/A')
                        );
                    }
                }
            }
        });
    }
} 