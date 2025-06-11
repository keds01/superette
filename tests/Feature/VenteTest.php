<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Produit;
use App\Models\Client;
use App\Models\Vente;
use App\Models\DetailVente;
use App\Models\Caisse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use App\Models\Employe;

class VenteTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $client;
    protected $products;
    protected $caisse;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Exécuter les migrations et les seeders nécessaires
        Artisan::call('migrate');
        
        // Créer un utilisateur, un employé, une caisse, un client et un produit pour les tests
        $this->actingAs($user = User::factory()->create());
        $employe = Employe::factory()->create(['user_id' => $user->id]);
        $this->client = Client::factory()->create();
        $this->caisse = Caisse::factory()->create(); // Assurez-vous d'avoir une Caisse factory
        $this->product = Product::factory()->create(['stock' => 50, 'prix_vente_ttc' => 1000, 'tva' => 18]);

        // Simuler la sélection d'une caisse en session si nécessaire pour les contrôleurs
        // Note: Les tests HTTP Feature n'utilisent pas la session web de manière standard par défaut.
        // On s'assurera que le contrôleur gère bien l'absence de caisse en session si besoin
        // ou on passera l'ID de caisse directement dans la requête POST si le contrôleur le demande.
        // D'après le contrôleur actuel, il attend un 'caisse_id' dans la request POST pour store, ce qui est bon.
    }

    /** @test */
    public function user_can_view_create_sale_page()
    {
        // S'assurer que la route existe et retourne une vue 200
        $response = $this->get(route('ventes.create'));
        $response->assertStatus(200);
        $response->assertViewIs('ventes.create');
         // Vérifier que les produits avec stock > 0 sont passés à la vue
         $response->assertViewHas('produits', function ($produits) {
             return $produits->isNotEmpty();
         });
         // Vérifier que les clients sont passés à la vue
         $response->assertViewHas('clients');
    }

    /** @test */
    public function can_create_sale_with_one_product()
    {
        $initialStock = $this->product->stock;
        $quantity = 5;
        $price = $this->product->prix_vente_ttc;
        $remise = 0;
        $tvaTaux = $this->product->tva / 100; // Assurez-vous que la TVA est stockée en pourcentage
        
        // Calculs attendus côté serveur (simulant prepareForValidation)
        $totalAvantRemiseLigne = $price * $quantity;
        $montantRemiseLigne = $totalAvantRemiseLigne * ($remise / 100);
        $sousTotalApresRemiseLigne = $totalAvantRemiseLigne - $montantRemiseLigne;
        $montantTvaLigne = $sousTotalApresRemiseLigne * $tvaTaux;
        $totalLigneTTC = $sousTotalApresRemiseLigne + $montantTvaLigne;

        $saleData = [
            'client_id' => $this->client->id,
            'type_vente' => 'sur_place',
            'date_vente' => now()->format('Y-m-d'),
            'notes' => $this->faker->sentence,
            'caisse_id' => $this->caisse->id, // L'ID de la caisse est requis dans la requête
            'produits' => [
                [
                    'produit_id' => $this->product->id,
                    'quantite' => $quantity,
                    'prix_unitaire' => $price,
                    'remise' => $remise,
                    // Les champs montants_total, montant_remise, montant_tva sont calculés par VenteRequest
                    // mais on peut les inclure si la validation le demande explicitement.
                    // D'après VenteRequest, ils sont calculés dans prepareForValidation, donc pas besoin de les mettre ici.
                    // On va s'assurer que la validation vérifie la cohérence.
                ]
            ],
            // Les totaux globaux sont calculés par VenteRequest
        ];

        $response = $this->postJson(route('ventes.store'), $saleData);

        $response->assertStatus(200); // S'attendre à un 200 pour une réponse JSON de succès
        $response->assertJson([
            'success' => true,
            'message' => 'Vente créée avec succès',
        ]);

        // Vérifier que la vente a été créée en base de données
        $this->assertDatabaseCount('ventes', 1);
        $vente = Vente::first();
        $this->assertEquals($this->client->id, $vente->client_id);
        $this->assertEquals(auth()->id(), $vente->employe_id);
        $this->assertEquals('sur_place', $vente->type_vente);
        $this->assertEquals(now()->format('Y-m-d'), $vente->date_vente->format('Y-m-d'));
        $this->assertEquals($saleData['notes'], $vente->notes);
         // Vérifier que les totaux globaux sont correctement calculés et enregistrés (vérifiés par la VenteRequest)
         // On ne peut pas les assert directement sur $saleData car ils sont calculés par la Request.
         // On va assert sur les champs de la vente créée.
         // Le montant_total de la vente devrait être le total TTC calculé.
         // On peut récupérer la vente fraîchement créée et vérifier ses montants calculés.
        $venteCreee = Vente::first();
        $this->assertEquals($totalLigneTTC, $venteCreee->montant_total); // Le montant total de la vente doit être le total TTC de la ligne
        $this->assertEquals($montantRemiseLigne, $venteCreee->montant_remise);
        $this->assertEquals($montantTvaLigne, $venteCreee->montant_tva);
        $this->assertEquals($totalLigneTTC, $venteCreee->montant_net);

        // Vérifier que le détail de vente a été créé
        $this->assertDatabaseCount('detail_ventes', 1);
        $detailVente = $vente->details()->first();
        $this->assertEquals($this->product->id, $detailVente->produit_id);
        $this->assertEquals($quantity, $detailVente->quantite);
        $this->assertEquals($price, $detailVente->prix_unitaire);
        $this->assertEquals($totalLigneTTC, $detailVente->montant_total); // Montant total du détail doit être le total TTC
        $this->assertEquals($remise, $detailVente->remise);
        $this->assertEquals($montantRemiseLigne, $detailVente->montant_remise);
        $this->assertEquals($montantTvaLigne, $detailVente->montant_tva);

        // Vérifier que le stock du produit a été mis à jour
        $this->assertEquals($initialStock - $quantity, $this->product->fresh()->stock);

        // Vérifier qu'un mouvement de stock a été créé
        $this->assertDatabaseCount('mouvements_stock', 1);
        $mouvementStock = \App\Models\MouvementStock::where('reference_id', $vente->id)->where('type', 'vente')->first();
        $this->assertNotNull($mouvementStock);
        $this->assertEquals($this->product->id, $mouvementStock->produit_id);
        $this->assertEquals(-$quantity, $mouvementStock->quantite); // Quantité négative pour une sortie
    }

    /** @test */
    public function cannot_create_sale_with_insufficient_stock()
    {
        $initialStock = $this->product->stock;
        $quantity = $initialStock + 1; // Quantité supérieure au stock
        $price = $this->product->prix_vente_ttc;
        $remise = 0;

        $saleData = [
            'client_id' => $this->client->id,
            'type_vente' => 'sur_place',
            'date_vente' => now()->format('Y-m-d'),
            'notes' => $this->faker->sentence,
            'caisse_id' => $this->caisse->id,
            'produits' => [
                [
                    'produit_id' => $this->product->id,
                    'quantite' => $quantity,
                    'prix_unitaire' => $price,
                    'remise' => $remise,
                ]
            ],
        ];

        $response = $this->postJson(route('ventes.store'), $saleData);

        // S'attendre à un statut d'erreur de validation (422 par défaut pour les Form Requests)
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['produits.0.quantite']); // Ou une erreur générique sur les produits
        // Vérifier que le message d'erreur indique un stock insuffisant
         $response->assertJsonFragment([
             'message' => "Stock insuffisant pour le produit {$this->product->nom}. Stock disponible: {$initialStock}",
         ]);

        // Vérifier qu'aucune vente, détail ou mouvement de stock n'a été créé
        $this->assertDatabaseCount('ventes', 0);
        $this->assertDatabaseCount('detail_ventes', 0);
        $this->assertDatabaseCount('mouvements_stock', 0);

        // Vérifier que le stock n'a pas changé
        $this->assertEquals($initialStock, $this->product->fresh()->stock);
    }

    /** @test */
    public function cannot_create_sale_with_negative_or_zero_quantity()
    {
         $initialStock = $this->product->stock;
         $price = $this->product->prix_vente_ttc;

         $saleData = [
             'client_id' => $this->client->id,
             'type_vente' => 'sur_place',
             'date_vente' => now()->format('Y-m-d'),
             'notes' => $this->faker->sentence,
             'caisse_id' => $this->caisse->id,
             'produits' => [
                 [
                     'produit_id' => $this->product->id,
                     'quantite' => 0, // Quantité nulle
                     'prix_unitaire' => $price,
                     'remise' => 0,
                 ]
             ],
         ];

         $response = $this->postJson(route('ventes.store'), $saleData);

         $response->assertStatus(422);
         $response->assertJsonValidationErrors(['produits.0.quantite']);
         // Le message d'erreur exact peut varier, mais la validation devrait échouer

         // Tester avec une quantité négative
         $saleData['produits'][0]['quantite'] = -5;

         $response = $this->postJson(route('ventes.store'), $saleData);

         $response->assertStatus(422);
         $response->assertJsonValidationErrors(['produits.0.quantite']);

         // Vérifier qu'aucune vente n'a été créée
         $this->assertDatabaseCount('ventes', 0);
         $this->assertDatabaseCount('detail_ventes', 0);
         $this->assertDatabaseCount('mouvements_stock', 0);
         $this->assertEquals($initialStock, $this->product->fresh()->stock);
    }

    /** @test */
    public function cannot_create_sale_with_missing_required_fields()
    {
        $initialStock = $this->product->stock;
        $price = $this->product->prix_vente_ttc;

        $saleData = [
            // 'client_id' est manquant
            'type_vente' => 'sur_place',
            'date_vente' => now()->format('Y-m-d'),
            'notes' => $this->faker->sentence,
            'caisse_id' => $this->caisse->id,
            'produits' => [
                [
                    'produit_id' => $this->product->id,
                    'quantite' => 5,
                    'prix_unitaire' => $price,
                    'remise' => 0,
                ]
            ],
        ];

        $response = $this->postJson(route('ventes.store'), $saleData);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['client_id']);

        // Tester avec produits array manquant ou vide
        unset($saleData['client_id']); // Remettre client_id pour ce test
        $saleData['client_id'] = $this->client->id;
        $saleData['produits'] = []; // Array vide

        $response = $this->postJson(route('ventes.store'), $saleData);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['produits']);

         // Tester avec un produit avec des champs manquants
         $saleData['produits'] = [
             [
                 'produit_id' => $this->product->id,
                 'quantite' => 5,
                 // 'prix_unitaire' manquant
                 'remise' => 0,
             ]
         ];

         $response = $this->postJson(route('ventes.store'), $saleData);
         $response->assertStatus(422);
         $response->assertJsonValidationErrors(['produits.0.prix_unitaire']);

        // Vérifier qu'aucune vente n'a été créée
        $this->assertDatabaseCount('ventes', 0);
        $this->assertDatabaseCount('detail_ventes', 0);
        $this->assertDatabaseCount('mouvements_stock', 0);
        $this->assertEquals($initialStock, $this->product->fresh()->stock);
    }

    /** @test */
    public function can_create_sale_with_multiple_products()
    {
        $product2 = Product::factory()->create(['stock' => 30, 'prix_vente_ttc' => 500, 'tva' => 10]);
        $initialStock1 = $this->product->stock;
        $initialStock2 = $product2->stock;

        $quantity1 = 5;
        $quantity2 = 10;
        $price1 = $this->product->prix_vente_ttc;
        $price2 = $product2->prix_vente_ttc;
        $remise1 = 0;
        $remise2 = 5; // Remise sur le deuxième produit

        $tvaTaux1 = $this->product->tva / 100;
        $tvaTaux2 = $product2->tva / 100;

        // Calculs attendus
        $totalAvantRemiseLigne1 = $price1 * $quantity1;
        $montantRemiseLigne1 = $totalAvantRemiseLigne1 * ($remise1 / 100);
        $sousTotalApresRemiseLigne1 = $totalAvantRemiseLigne1 - $montantRemiseLigne1;
        $montantTvaLigne1 = $sousTotalApresRemiseLigne1 * $tvaTaux1;
        $totalLigneTTC1 = $sousTotalApresRemiseLigne1 + $montantTvaLigne1;

        $totalAvantRemiseLigne2 = $price2 * $quantity2;
        $montantRemiseLigne2 = $totalAvantRemiseLigne2 * ($remise2 / 100);
        $sousTotalApresRemiseLigne2 = $totalAvantRemiseLigne2 - $montantRemiseLigne2;
        $montantTvaLigne2 = $sousTotalApresRemiseLigne2 * $tvaTaux2;
        $totalLigneTTC2 = $sousTotalApresRemiseLigne2 + $montantTvaLigne2;

        $totalVenteTTC = $totalLigneTTC1 + $totalLigneTTC2;
        $totalVenteRemise = $montantRemiseLigne1 + $montantRemiseLigne2;
        $totalVenteTVA = $montantTvaLigne1 + $montantTvaLigne2;
        $totalVenteNet = $totalVenteTTC; // Dans ce cas simple

        $saleData = [
            'client_id' => $this->client->id,
            'type_vente' => 'sur_place',
            'date_vente' => now()->format('Y-m-d'),
            'notes' => $this->faker->sentence,
            'caisse_id' => $this->caisse->id,
            'produits' => [
                [
                    'produit_id' => $this->product->id,
                    'quantite' => $quantity1,
                    'prix_unitaire' => $price1,
                    'remise' => $remise1,
                ],
                [
                    'produit_id' => $product2->id,
                    'quantite' => $quantity2,
                    'prix_unitaire' => $price2,
                    'remise' => $remise2,
                ]
            ],
        ];

        $response = $this->postJson(route('ventes.store'), $saleData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Vente créée avec succès',
        ]);

        // Vérifier la vente globale
        $this->assertDatabaseCount('ventes', 1);
        $vente = Vente::first();
        $this->assertEquals($totalVenteTTC, $vente->montant_total); // Vérifier les totaux globaux
        $this->assertEquals($totalVenteRemise, $vente->montant_remise);
        $this->assertEquals($totalVenteTVA, $vente->montant_tva);
        $this->assertEquals($totalVenteNet, $vente->montant_net);

        // Vérifier les détails de vente (2 lignes)
        $this->assertDatabaseCount('detail_ventes', 2);

        // Vérifier le stock des produits
        $this->assertEquals($initialStock1 - $quantity1, $this->product->fresh()->stock);
        $this->assertEquals($initialStock2 - $quantity2, $product2->fresh()->stock);

        // Vérifier les mouvements de stock (2 mouvements)
        $this->assertDatabaseCount('mouvements_stock', 2);
        $this->assertNotNull(\App\Models\MouvementStock::where('produit_id', $this->product->id)->where('reference_id', $vente->id)->first());
        $this->assertNotNull(\App\Models\MouvementStock::where('produit_id', $product2->id)->where('reference_id', $vente->id)->first());
    }

     /** @test */
     public function can_create_sale_with_anonymous_client()
     {
         $initialStock = $this->product->stock;
         $quantity = 5;
         $price = $this->product->prix_vente_ttc;
         $remise = 0;

         $tvaTaux = $this->product->tva / 100;
         $totalAvantRemiseLigne = $price * $quantity;
         $montantRemiseLigne = $totalAvantRemiseLigne * ($remise / 100);
         $sousTotalApresRemiseLigne = $totalAvantRemiseLigne - $montantRemiseLigne;
         $montantTvaLigne = $sousTotalApresRemiseLigne * $tvaTaux;
         $totalLigneTTC = $sousTotalApresRemiseLigne + $montantTvaLigne;

         $saleData = [
             'client_id' => null, // Client anonyme
             'type_vente' => 'sur_place',
             'date_vente' => now()->format('Y-m-d'),
             'notes' => $this->faker->sentence,
             'caisse_id' => $this->caisse->id,
             'produits' => [
                 [
                     'produit_id' => $this->product->id,
                     'quantite' => $quantity,
                     'prix_unitaire' => $price,
                     'remise' => $remise,
                 ]
             ],
         ];

         $response = $this->postJson(route('ventes.store'), $saleData);

         $response->assertStatus(200);
         $response->assertJson([
             'success' => true,
             'message' => 'Vente créée avec succès',
         ]);

         // Vérifier que la vente a été créée avec client_id null
         $this->assertDatabaseCount('ventes', 1);
         $vente = Vente::first();
         $this->assertNull($vente->client_id);
         // Vérifier les autres détails comme dans le test précédent
         $this->assertEquals($totalLigneTTC, $vente->montant_total);
         $this->assertDatabaseCount('detail_ventes', 1);
         $this->assertEquals($initialStock - $quantity, $this->product->fresh()->stock);
         $this->assertDatabaseCount('mouvements_stock', 1);
     }
     
    // TODO: Ajouter des tests pour la concurrence sur le stock
    // TODO: Ajouter des tests pour la concurrence sur la génération du numéro de vente
    // TODO: Tester la création de vente avec une remise globale (si cette fonctionnalité existe)
    // TODO: Tester la création de vente avec différents types de vente (a_emporter, livraison) si cela a un impact logique
    // TODO: Tester le cas où la caisse_id dans la requête est invalide

} 