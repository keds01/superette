<?php

namespace Tests\Feature;

use App\Models\Alerte;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\MouvementStock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AlertTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Crée un utilisateur pour les tests et agit en tant que lui (si l'authentification est active)
        // Si l'authentification est désactivée globalement, cela n'aura pas d'impact sur les tests non authentifiés
        $this->user = User::factory()->create();
        // $this->actingAs($this->user); // Commenté car l'authentification est potentiellement désactivée

        // Exécute les seeders nécessaires pour les tests
        Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
        Artisan::call('db:seed', ['--class' => 'UnitSeeder']);
        Artisan::call('db:seed', ['--class' => 'ProductSeeder']);
        // Artisan::call('db:seed', ['--class' => 'RoleSeeder']); // Commenté pour éviter les erreurs SQLite avec Spatie permissions
        // Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']); // Commenté pour éviter les erreurs SQLite avec Spatie permissions
    }

    /** @test */
    public function it_can_display_alerts_index()
    {
        Alerte::factory()->count(5)->create();

        // Utilise $this->get sans authentification si elle est désactivée
        $response = $this->get(route('alertes.index'));

        $response->assertStatus(200)
                ->assertViewIs('alertes.index')
                ->assertViewHas('alertes')
                ->assertViewHas('categories')
                ->assertViewHas('alertTypes');
    }

    /** @test */
    public function it_can_create_an_alert()
    {
        $categorie = Categorie::factory()->create();
        $alertData = [
            'type' => 'stock_bas',
            'categorie_id' => $categorie->id,
            'seuil' => 10,
            'actif' => true,
            'notification_email' => 'test@example.com'
        ];

        $response = $this->post(route('alertes.store'), $alertData);

        $response->assertRedirect(route('alertes.index'));
        $this->assertDatabaseHas('alertes', [
            'type' => 'stock_bas',
            'categorie_id' => $categorie->id,
            'seuil' => 10.00, // Seuil est casté en decimal(2)
            'actif' => true,
            'notification_email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function it_validates_alert_creation()
    {
        $response = $this->post(route('alertes.store'), []);

        $response->assertSessionHasErrors(['type', 'seuil']);
    }

    /** @test */
    public function it_requires_periode_for_peremption_and_mouvement_important_alert_types()
    {
        $alertDataPeremption = [
            'type' => 'peremption',
            'seuil' => 5,
            'actif' => true,
            'notification_email' => 'test@example.com'
        ];

        $responsePeremption = $this->post(route('alertes.store'), $alertDataPeremption);
        $responsePeremption->assertSessionHasErrors(['periode']);

        $alertDataMouvement = [
            'type' => 'mouvement_important',
            'seuil' => 100,
            'actif' => true,
            'notification_email' => 'test@example.com'
        ];

        $responseMouvement = $this->post(route('alertes.store'), $alertDataMouvement);
        $responseMouvement->assertSessionHasErrors(['periode']);
    }

    /** @test */
    public function it_can_update_an_alert()
    {
        $alert = Alert::factory()->create([
            'type' => 'stock_bas',
            'seuil' => 10,
            'actif' => true
        ]);

        $updateData = [
            'seuil' => 5,
            'periode' => 30,
            'actif' => false,
            'notification_email' => 'updated@example.com'
        ];

        $response = $this->put(route('alertes.update', $alerte), $updateData);

        $response->assertRedirect(route('alertes.index'));
        $this->assertDatabaseHas('alertes', [
            'id' => $alerte->id,
            'seuil' => 5.00,
            // 'periode' should be null in DB if type is stock_bas
            'actif' => false,
            'notification_email' => 'updated@example.com'
        ]);
         // Vérifier spécifiquement que periode est null si le type ne le requiert pas
         $updatedAlert = $alert->fresh();
         if (!in_array($updatedAlert->type, ['peremption', 'mouvement_important'])) {
              $this->assertNull($updatedAlert->periode);
          } else {
               $this->assertEquals(30, $updatedAlert->periode);
         }
    }

     /** @test */
    public function it_validates_alert_update()
    {
         $alerte = Alerte::factory()->create([
             'type' => 'peremption', // Utiliser un type qui requiert periode
             'periode' => 10
             ]);

         $response = $this->put(route('alertes.update', $alerte), [
             'seuil' => -5, // Invalid seuil
             'periode' => 0, // Invalid periode (min: 1)
             'notification_email' => 'invalid-email' // Invalid email
         ]);

         $response->assertSessionHasErrors(['seuil', 'periode', 'notification_email']); // La validation email devrait être là
     }

    /** @test */
    public function it_can_delete_an_alert()
    {
        $alerte = Alerte::factory()->create();

        $response = $this->delete(route('alertes.destroy', $alerte));

        $response->assertRedirect(route('alertes.index'));
        $this->assertDatabaseMissing('alertes', ['id' => $alerte->id]);
    }

    // Tests pour la logique de checkAlerts

    /** @test */
    public function checkalerts_identifies_low_stock_products()
    {
        // Crée un produit avec stock bas et un avec stock suffisant
        $productLowStock = Product::factory()->create(['stock' => 5]);
        $productOKStock = Product::factory()->create(['stock' => 20]);

        // Crée une alerte de stock bas
        $alert = Alert::factory()->create([
            'type' => 'stock_bas',
            'seuil' => 10,
            'actif' => true,
        ]);

        // Appelle la méthode checkAlerts (simule l'appel depuis une commande/job)
        $controller = new \App\Http\Controllers\AlertController();
        $notifications = $controller->checkAlerts();

        // Vérifie que l'alerte de stock bas pour le produit bas est présente
        $this->assertCount(1, $notifications);
        $this->assertEquals('stock_bas', $notifications[0]['type']);
        $this->assertStringContainsString('stock de '. $productLowStock->nom .' est bas', $notifications[0]['message']);
        $this->assertEquals($productLowStock->id, $notifications[0]['product_id']);
        $this->assertEquals($alert->id, $notifications[0]['alert_id']);
    }

     /** @test */
    public function checkalerts_identifies_expiring_products()
    {
         // Crée des mouvements de stock avec date de péremption dans la période et en dehors
         $product1 = Product::factory()->create(); // Pour mouvement expirant
         $product2 = Product::factory()->create(); // Pour mouvement non expirant

         StockMovement::factory()->create([
             'produit_id' => $product1->id,
             'type' => 'entree',
             'quantite_apres_conditionnement' => 10,
             'date_peremption' => now()->addDays(15), // Expire dans 15 jours
         ]);

         StockMovement::factory()->create([
             'produit_id' => $product2->id,
             'type' => 'entree',
             'quantite_apres_conditionnement' => 10,
             'date_peremption' => now()->addDays(40), // Expire dans 40 jours
         ]);

         // Crée une alerte de péremption avec période de 30 jours
         $alert = Alert::factory()->create([
             'type' => 'peremption',
             'seuil' => 0, // Seuil non pertinent ici
             'periode' => 30,
             'actif' => true,
         ]);

         // Appelle la méthode checkAlerts
         $controller = new \App\Http\Controllers\AlertController();
         $notifications = $controller->checkAlerts();

         // Vérifie que l'alerte de péremption pour le produit 1 est présente
         $this->assertCount(1, $notifications);
         $this->assertEquals('peremption', $notifications[0]['type']);
         // Note: Le message dans le controller utilise $movement->product->nom qui est maintenant correct
         $this->assertStringContainsString('produit '. $product1->nom, $notifications[0]['message']);
         $this->assertEquals($product1->id, $notifications[0]['product_id']);
         $this->assertEquals($alert->id, $notifications[0]['alert_id']);
     }

     /** @test */
    public function checkalerts_identifies_high_stock_value()
    {
        // Crée des produits avec différentes valeurs de stock
        $produitHauteValeur = Produit::factory()->create(['stock' => 10, 'prix_achat_ht' => 50]); // Valeur: 500
        $produitFaibleValeur = Produit::factory()->create(['stock' => 5, 'prix_achat_ht' => 10]); // Valeur: 50

        // Crée une alerte de valeur stock
        $alert = Alert::factory()->create([
            'type' => 'valeur_stock',
            'seuil' => 200,
            'actif' => true,
        ]);

        // Appelle la méthode checkAlerts
        $controller = new \App\Http\Controllers\AlertController();
        $notifications = $controller->checkAlerts();

        // Vérifie que l'alerte de valeur stock pour le produit de haute valeur est présente
        $this->assertCount(1, $notifications);
        $this->assertEquals('valeur_stock', $notifications[0]['type']);
        $this->assertStringContainsString('valeur du stock de '. $produitHauteValeur->nom .' (', $notifications[0]['message']); // Vérifie une partie du message corrigé
        $this->assertEquals($produitHauteValeur->id, $notifications[0]['product_id']);
        $this->assertEquals($alerte->id, $notifications[0]['alert_id']);
    }

    /** @test */
    public function checkalerts_identifies_important_movements()
    {
        // Crée des mouvements importants et non importants (basé sur la date et la quantité)
        $produit1 = Produit::factory()->create(); // Pour mouvement important
        $produit2 = Produit::factory()->create(); // Pour mouvement non important

        // Mouvement important (quantité >= seuil, dans la période)
        StockMovement::factory()->create([
            'produit_id' => $product1->id,
            'type' => 'sortie',
            'quantite_apres_conditionnement' => 60,
            'created_at' => now()->subDays(5), // Dans la période de 7 jours
        ]);

        // Mouvement non important (quantité < seuil)
        StockMovement::factory()->create([
            'produit_id' => $product2->id,
            'type' => 'entree',
            'quantite_apres_conditionnement' => 30,
            'created_at' => now()->subDays(5),
        ]);

         // Mouvement non important (en dehors de la période)
         StockMovement::factory()->create([
             'produit_id' => $product1->id,
             'type' => 'sortie',
             'quantite_apres_conditionnement' => 60,
             'created_at' => now()->subDays(10), // En dehors de la période de 7 jours
         ]);

        // Crée une alerte de mouvement important
        $alert = Alert::factory()->create([
            'type' => 'mouvement_important',
            'seuil' => 50,
            'periode' => 7, // 7 derniers jours
            'actif' => true,
        ]);

        // Appelle la méthode checkAlerts
        $controller = new \App\Http\Controllers\AlertController();
        $notifications = $controller->checkAlerts();

        // Vérifie que l'alerte pour le mouvement important est présente
        $this->assertCount(1, $notifications);
        $this->assertEquals('mouvement_important', $notifications[0]['type']);
         // Note: Le message dans le controller utilise les champs corrigés
         $this->assertStringContainsString('Mouvement important ('. 60 .' unités) pour '. $produit1->nom, $notifications[0]['message']);
         $this->assertEquals($produit1->id, $notifications[0]['product_id']);
         $this->assertEquals($alerte->id, $notifications[0]['alert_id']);
    }
}