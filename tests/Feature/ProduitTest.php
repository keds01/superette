<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Categorie;
use App\Models\Unite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProduitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_can_create_product_with_valid_data()
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create();
        $unite = Unite::factory()->create();

        $response = $this->actingAs($user)->post('/produits', [
            'nom' => 'Test Product',
            'reference' => 'TEST001',
            'code_barres' => '123456789',
            'categorie_id' => $categorie->id,
            'description' => 'Test description',
            'unite_vente_id' => $unite->id,
            'conditionnement_fournisseur' => 'Box',
            'quantite_par_conditionnement' => 10,
            'stock_initial' => 100,
            'seuil_alerte' => 20,
            'emplacement_rayon' => 'A1',
            'emplacement_etagere' => 'B2',
            'date_peremption' => now()->addMonths(6)->format('Y-m-d'),
            'prix_achat_ht' => 10.00,
            'marge' => 20,
            'tva' => 20,
            'image' => UploadedFile::fake()->image('product.jpg', 100, 100)
        ]);

        $response->assertRedirect(route('stocks.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('produits', [
            'nom' => 'Test Product',
            'reference' => 'TEST001',
            'code_barres' => '123456789'
        ]);
    }

    public function test_cannot_create_product_with_duplicate_reference()
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create();
        $unite = Unite::factory()->create();

        // Créer un premier produit
        $this->actingAs($user)->post('/produits', [
            'nom' => 'First Product',
            'reference' => 'TEST001',
            'categorie_id' => $categorie->id,
            'unite_vente_id' => $unite->id,
            'conditionnement_fournisseur' => 'Box',
            'quantite_par_conditionnement' => 10,
            'stock_initial' => 100,
            'seuil_alerte' => 20,
            'emplacement_rayon' => 'A1',
            'emplacement_etagere' => 'B2',
            'prix_achat_ht' => 10.00,
            'marge' => 20,
            'tva' => 20
        ]);

        // Tenter de créer un second produit avec la même référence
        $response = $this->actingAs($user)->post('/produits', [
            'nom' => 'Second Product',
            'reference' => 'TEST001',
            'categorie_id' => $categorie->id,
            'unite_vente_id' => $unite->id,
            'conditionnement_fournisseur' => 'Box',
            'quantite_par_conditionnement' => 10,
            'stock_initial' => 100,
            'seuil_alerte' => 20,
            'emplacement_rayon' => 'A1',
            'emplacement_etagere' => 'B2',
            'prix_achat_ht' => 10.00,
            'marge' => 20,
            'tva' => 20
        ]);

        $response->assertSessionHasErrors('reference');
    }

    public function test_cannot_create_product_with_past_expiration_date()
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create();
        $unite = Unite::factory()->create();

        $response = $this->actingAs($user)->post('/produits', [
            'nom' => 'Test Product',
            'reference' => 'TEST001',
            'categorie_id' => $categorie->id,
            'unite_vente_id' => $unite->id,
            'conditionnement_fournisseur' => 'Box',
            'quantite_par_conditionnement' => 10,
            'stock_initial' => 100,
            'seuil_alerte' => 20,
            'emplacement_rayon' => 'A1',
            'emplacement_etagere' => 'B2',
            'date_peremption' => now()->subDays(1)->format('Y-m-d'),
            'prix_achat_ht' => 10.00,
            'marge' => 20,
            'tva' => 20
        ]);

        $response->assertSessionHasErrors('date_peremption');
    }

    public function test_cannot_create_product_with_invalid_image()
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create();
        $unite = Unite::factory()->create();

        $response = $this->actingAs($user)->post('/produits', [
            'nom' => 'Test Product',
            'reference' => 'TEST001',
            'categorie_id' => $categorie->id,
            'unite_vente_id' => $unite->id,
            'conditionnement_fournisseur' => 'Box',
            'quantite_par_conditionnement' => 10,
            'stock_initial' => 100,
            'seuil_alerte' => 20,
            'emplacement_rayon' => 'A1',
            'emplacement_etagere' => 'B2',
            'prix_achat_ht' => 10.00,
            'marge' => 20,
            'tva' => 20,
            'image' => UploadedFile::fake()->create('document.pdf', 100)
        ]);

        $response->assertSessionHasErrors('image');
    }

    public function test_stock_movement_is_created_with_initial_stock()
    {
        $user = User::factory()->create();
        $categorie = Categorie::factory()->create();
        $unite = Unite::factory()->create();

        $this->actingAs($user)->post('/produits', [
            'nom' => 'Test Product',
            'reference' => 'TEST001',
            'categorie_id' => $categorie->id,
            'unite_vente_id' => $unite->id,
            'conditionnement_fournisseur' => 'Box',
            'quantite_par_conditionnement' => 10,
            'stock_initial' => 100,
            'seuil_alerte' => 20,
            'emplacement_rayon' => 'A1',
            'emplacement_etagere' => 'B2',
            'prix_achat_ht' => 10.00,
            'marge' => 20,
            'tva' => 20
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'type' => 'entree',
            'quantite' => 100,
            'motif' => 'Stock initial'
        ]);
    }
} 