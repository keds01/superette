<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ProduitService;
use App\Models\Produit;
use App\Models\Categorie;
use App\Models\Unite;
use App\Models\MouvementStock;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProduitCreationTest extends TestCase
{
    use DatabaseTransactions;
    
    protected ProduitService $produitService;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->produitService = app(ProduitService::class);
        
        // Préparer le stockage des images
        Storage::fake('public');
    }
    
    /**
     * Test de création d'un produit avec le service optimisé.
     */
    public function test_creation_produit_avec_service(): void
    {
        // Assurons-nous d'avoir une catégorie et une unité pour le test
        $categorie = Categorie::first() ?: Categorie::create(['nom' => 'Catégorie Test']);
        $unite = Unite::first() ?: Unite::create(['nom' => 'Unité Test', 'symbole' => 'UT']);
        
        // Données de test pour un nouveau produit
        $produitData = [
            'nom' => 'Produit Test',
            'reference' => 'REF-TEST-' . time(),
            'code_barres' => '123456789' . rand(1000, 9999),
            'categorie_id' => $categorie->id,
            'description' => 'Description du produit test',
            'unite_vente_id' => $unite->id,
            'conditionnement_fournisseur' => 'Carton',
            'quantite_par_conditionnement' => 10,
            'stock_initial' => 50,
            'seuil_alerte' => 10,
            'emplacement_rayon' => 'A',
            'emplacement_etagere' => '1',
            'prix_achat_ht' => 10.00,
            'marge' => 20,  // 20%
            'tva' => 20,    // 20%
            'image' => UploadedFile::fake()->image('produit.jpg')
        ];
        
        // Création du produit avec le service
        $produit = $this->produitService->createProduct($produitData);
        
        // Vérifications
        $this->assertInstanceOf(Produit::class, $produit);
        $this->assertEquals('Produit Test', $produit->nom);
        $this->assertEquals($produitData['reference'], $produit->reference);
        $this->assertEquals($produitData['code_barres'], $produit->code_barres);
        $this->assertEquals($categorie->id, $produit->categorie_id);
        
        // Vérifier que le prix a été calculé correctement
        $this->assertEquals(10.00, $produit->prix_achat_ht);
        $this->assertEquals(12.00, $produit->prix_vente_ht); // prix_achat_ht * (1 + marge/100) = 10 * 1.2
        $this->assertEquals(14.40, $produit->prix_vente_ttc); // prix_vente_ht * (1 + tva/100) = 12 * 1.2
        
        // Vérifier que l'image a été traitée
        $this->assertNotNull($produit->image);
        Storage::disk('public')->assertExists($produit->image);
        
        // Vérifier que le mouvement de stock initial a été créé
        $mouvement = MouvementStock::where('produit_id', $produit->id)->first();
        $this->assertNotNull($mouvement);
        $this->assertEquals('entree', $mouvement->type);
        $this->assertEquals(50, $mouvement->quantite);
    }
    
    /**
     * Test de mise à jour d'un produit avec le service optimisé.
     */
    public function test_mise_a_jour_produit_avec_service(): void
    {
        // Créer d'abord un produit pour le test
        $categorie = Categorie::first() ?: Categorie::create(['nom' => 'Catégorie Test']);
        $unite = Unite::first() ?: Unite::create(['nom' => 'Unité Test', 'symbole' => 'UT']);
        
        $produit = Produit::create([
            'nom' => 'Produit à Modifier',
            'reference' => 'REF-MOD-' . time(),
            'categorie_id' => $categorie->id,
            'unite_vente_id' => $unite->id,
            'conditionnement_fournisseur' => 'Boîte',
            'quantite_par_conditionnement' => 5,
            'stock' => 20,
            'seuil_alerte' => 5,
            'emplacement_rayon' => 'B',
            'emplacement_etagere' => '2',
            'prix_achat_ht' => 5.00,
            'prix_vente_ht' => 6.00,
            'prix_vente_ttc' => 7.20,
            'marge' => 20,
            'tva' => 20,
            'actif' => true
        ]);
        
        // Données de mise à jour
        $updateData = [
            'nom' => 'Produit Modifié',
            'description' => 'Nouvelle description',
            'prix_achat_ht' => 6.00,
            'marge' => 25,  // 25%
            'tva' => 20,    // 20%
            'seuil_alerte' => 10
        ];
        
        // Mise à jour du produit avec le service
        $produitMaj = $this->produitService->updateProduct($produit, $updateData);
        
        // Vérifications
        $this->assertEquals('Produit Modifié', $produitMaj->nom);
        $this->assertEquals('Nouvelle description', $produitMaj->description);
        $this->assertEquals(6.00, $produitMaj->prix_achat_ht);
        $this->assertEquals(25, $produitMaj->marge);
        
        // Vérifier que les prix ont été recalculés
        $this->assertEquals(7.50, $produitMaj->prix_vente_ht); // prix_achat_ht * (1 + marge/100) = 6 * 1.25
        $this->assertEquals(9.00, $produitMaj->prix_vente_ttc); // prix_vente_ht * (1 + tva/100) = 7.5 * 1.2
    }
}
