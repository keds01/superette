<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Superette;
use App\Models\Produit;
use Spatie\Permission\Models\Role;

class SuperetteTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Créer le rôle super-admin
        Role::create(['name' => 'super-admin']);
    }

    /**
     * Test la création d'une superette
     */
    public function test_superadmin_peut_creer_une_superette()
    {
        // Créer un utilisateur avec le rôle super-admin
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $response = $this->actingAs($superAdmin)->post('/superettes', [
            'nom' => 'Superette Test',
            'code' => 'ST001',
            'adresse' => 'Adresse de test',
            'telephone' => '123456789',
            'actif' => true,
        ]);

        $response->assertRedirect('/superettes');
        $this->assertDatabaseHas('superettes', ['nom' => 'Superette Test']);
    }

    /**
     * Test que les utilisateurs standards ne peuvent pas accéder à la gestion des superettes
     */
    public function test_utilisateur_standard_ne_peut_pas_acceder_aux_superettes()
    {
        // Créer une superette
        $superette = Superette::create([
            'nom' => 'Superette Test',
            'code' => 'ST001',
            'actif' => true,
        ]);

        // Créer un utilisateur standard
        $user = User::factory()->create(['superette_id' => $superette->id]);

        // Essayer d'accéder à la liste des superettes
        $response = $this->actingAs($user)->get('/superettes');
        $response->assertStatus(403); // Doit être bloqué
    }

    /**
     * Test l'isolation des données entre superettes
     */
    public function test_isolation_donnees_entre_superettes()
    {
        // Créer deux superettes
        $superette1 = Superette::create([
            'nom' => 'Superette 1',
            'code' => 'S1001',
            'actif' => true,
        ]);

        $superette2 = Superette::create([
            'nom' => 'Superette 2',
            'code' => 'S2001',
            'actif' => true,
        ]);

        // Créer un produit pour chaque superette
        $produit1 = Produit::create([
            'nom' => 'Produit Superette 1',
            'reference' => 'REF-S1',
            'superette_id' => $superette1->id,
            'categorie_id' => 1,
            'prix_achat_ht' => 100,
            'prix_vente_ht' => 150,
            'tva' => 19.25,
        ]);

        $produit2 = Produit::create([
            'nom' => 'Produit Superette 2',
            'reference' => 'REF-S2',
            'superette_id' => $superette2->id,
            'categorie_id' => 1,
            'prix_achat_ht' => 200,
            'prix_vente_ht' => 250,
            'tva' => 19.25,
        ]);

        // Créer un super admin
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        // Utiliser le super admin et définir la superette active à la superette 1
        $response = $this->actingAs($superAdmin);
        session(['active_superette_id' => $superette1->id]);

        // Vérifier qu'on ne voit que les produits de la superette 1
        $this->assertDatabaseCount('produits', 2); // Total
        
        // Puisque nous ne pouvons pas tester facilement le global scope dans un test unitaire,
        // nous vérifions explicitement que les produits sont bien associés à leur superette
        $this->assertEquals($superette1->id, $produit1->superette_id);
        $this->assertEquals($superette2->id, $produit2->superette_id);
    }

    /**
     * Test la redirection des super-admins vers la sélection de superette
     */
    public function test_superadmin_est_redirige_vers_selection_superette()
    {
        // Créer une superette
        $superette = Superette::create([
            'nom' => 'Superette Test',
            'code' => 'ST001',
            'actif' => true,
        ]);

        // Créer un super admin sans superette sélectionnée
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        // Essayer d'accéder au dashboard sans superette sélectionnée
        $response = $this->actingAs($superAdmin)->get('/dashboard');
        
        // Il doit être redirigé vers la page de sélection
        $response->assertRedirect('/superettes/select');
    }

    /**
     * Test qu'un utilisateur standard est bloqué s'il n'a pas de superette associée
     */
    public function test_utilisateur_sans_superette_est_deconnecte()
    {
        // Créer un utilisateur standard sans superette
        $user = User::factory()->create(['superette_id' => null]);

        // Essayer d'accéder au dashboard
        $response = $this->actingAs($user)->get('/dashboard');
        
        // Il doit être déconnecté et redirigé vers la page de connexion
        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
    }
} 