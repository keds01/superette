<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use Illuminate\Http\Request;

class ApiProduitController extends Controller
{
    /**
     * Récupère les détails d'un produit par son ID
     *
     * @param int $id Identifiant du produit
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $produit = Produit::with('categorie')->find($id);
        
        if ($produit) {
            return response()->json([
                'success' => true,
                'produit' => [
                    'id' => $produit->id,
                    'nom' => $produit->nom,
                    'prix_vente' => $produit->prix_vente,
                    'stock' => $produit->stock,
                    'is_active' => $produit->is_active,
                    'categorie' => $produit->categorie ? [
                        'id' => $produit->categorie->id,
                        'nom' => $produit->categorie->nom
                    ] : null,
                    'date_peremption' => $produit->date_peremption,
                    'delai_alerte_peremption' => $produit->delai_alerte_peremption
                ]
            ]);
        } else {
            return response()->json([
                'success' => false, 
                'message' => 'Produit non trouvé'
            ], 404);
        }
    }
    
    public function checkStock(Produit $produit, Request $request)
    {
        $quantity = $request->input('quantity', 1);

        if ($produit->stock >= $quantity) {
            return response()->json(['available' => true, 'stock' => $produit->stock]);
        } else {
            return response()->json(['available' => false, 'stock' => $produit->stock, 'message' => 'Stock insuffisant.']);
        }
    }
    
    public function searchByBarcode($barcode)
    {
        $produit = Produit::where('code_barres', $barcode)->first();
        
        if ($produit) {
            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $produit->id,
                    'nom' => $produit->nom,
                    'prix' => $produit->prix_vente_ttc,
                    'stock' => $produit->stock,
                    'tva' => $produit->tva,
                    'date_peremption' => $produit->date_peremption,
                    'delai_alerte_peremption' => $produit->delai_alerte_peremption
                ]
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Produit non trouvé']);
        }
    }
}