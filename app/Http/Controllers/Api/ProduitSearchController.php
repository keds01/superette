<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produit; 
use App\Services\ProduitService; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProduitSearchController extends Controller
{
    private $produitService;

    public function __construct(ProduitService $produitService)
    {
        $this->produitService = $produitService;
    }

    /**
     * Recherche des produits en fonction du terme de recherche et du type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $query = $request->input('query');
        $searchType = $request->input('search_type', 'name'); // Valeur par défaut 'name'

        if (empty($query)) {
            return response()->json([]);
        }

        try {
            $produits = Produit::query(); // Utilisez le modèle Produit

            if ($searchType === 'barcode') {
                // Recherche par code-barres exact
                $produits->where('code_barres', $query);
            } else {
                // Recherche par nom ou référence
                $produits->where('nom', 'like', '%' . $query . '%')
                         ->orWhere('reference', 'like', '%' . $query . '%');
            }

            $produits = $produits->select('id', 'nom', 'reference', 'prix_vente_ttc', 'stock', 'tva', 'code_barres') // Sélectionner uniquement les champs nécessaires
                               ->orderBy('nom') // Trier par nom par défaut
                               ->limit(10) // Limiter le nombre de résultats pour la performance
                               ->get();

            return response()->json($produits);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche de produits: ' . $e->getMessage());
            return response()->json([ 'error' => 'Une erreur est survenue lors de la recherche.' ], 500);
        }
    }

    /**
     * Recherche un produit par son code-barres.
     *
     * @param  string  $barcode
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByBarcode($barcode)
    {
        try {
            $produit = Produit::where('code_barres', $barcode)
                            ->select('id', 'nom', 'reference', 'prix_vente_ttc', 'stock', 'tva', 'code_barres')
                            ->first();

            if (!$produit) {
                return response()->json([
                    'error' => 'Produit non trouvé',
                    'message' => 'Aucun produit trouvé avec ce code-barres'
                ], 404);
            }

            return response()->json($produit);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche par code-barres: ' . $e->getMessage());
            return response()->json([
                'error' => 'Une erreur est survenue lors de la recherche',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 