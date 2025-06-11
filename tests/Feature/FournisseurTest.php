<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Fournisseur;
use App\Models\ContactFournisseur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class FournisseurTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Créer un utilisateur pour l'authentification si nécessaire
        $this->user = User::factory()->create();
    }

    public function test_can_view_fournisseurs_index()
    {
        $response = $this->actingAs($this->user)->get(route('fournisseurs.index'));
        $response->assertStatus(200);
        $response->assertViewIs('fournisseurs.index');
    }

    public function test_can_view_create_fournisseur_form()
    {
        $response = $this->actingAs($this->user)->get(route('fournisseurs.create'));
        $response->assertStatus(200);
        $response->assertViewIs('fournisseurs.create');
    }

    public function test_can_create_fournisseur_with_valid_data()
    {
        $response = $this->actingAs($this->user)->post(route('fournisseurs.store'), [
            'nom' => 'Fournisseur Test',
            'code' => 'FOUR-TEST1',
            'contact_principal_nom' => 'Dupont',
            'contact_principal_prenom' => 'Jean',
            'email' => 'jean.dupont@test.com',
            'telephone' => '0123456789',
            'adresse' => '1 Rue de Test',
            'ville' => 'Testville',
            'code_postal' => '75000',
            'pays' => 'France',
            'notes' => 'Notes de test'
        ]);

        $response->assertRedirect(route('fournisseurs.show', Fournisseur::first()));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('fournisseurs', [
            'nom' => 'Fournisseur Test',
            'code' => 'FOUR-TEST1',
            'email' => 'jean.dupont@test.com'
        ]);
        $this->assertDatabaseHas('contact_fournisseurs', [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@test.com',
            'telephone' => '0123456789',
            'est_principal' => true
        ]);
    }
    
     public function test_can_create_fournisseur_with_generated_code()
    {
        $response = $this->actingAs($this->user)->post(route('fournisseurs.store'), [
            'nom' => 'Fournisseur Généré',
            // Pas de code fourni, il doit être généré
            'contact_principal_nom' => 'Durand',
            'contact_principal_prenom' => 'Pierre',
            'email' => 'pierre.durand@test.com',
            'telephone' => '0987654321',
            'adresse' => '2 Rue Générée',
            'ville' => 'Ville Test',
            'code_postal' => '78000',
            'pays' => 'France',
            'notes' => 'Notes de génération'
        ]);

        $response->assertRedirect(route('fournisseurs.show', Fournisseur::first()));
        $response->assertSessionHas('success');
        
        $fournisseur = Fournisseur::first();
        $this->assertNotNull($fournisseur->code); // Vérifie qu'un code a été généré
        $this->assertStringStartsWith('FOUR-', $fournisseur->code); // Vérifie le format du code généré

        $this->assertDatabaseHas('fournisseurs', [
            'nom' => 'Fournisseur Généré',
            'email' => 'pierre.durand@test.com'
        ]);
         $this->assertDatabaseHas('contact_fournisseurs', [
            'nom' => 'Durand',
            'prenom' => 'Pierre',
            'email' => 'pierre.durand@test.com',
            'telephone' => '0987654321',
            'est_principal' => true
        ]);
    }

    public function test_cannot_create_fournisseur_with_duplicate_code()
    {
        Fournisseur::factory()->create(['code' => 'FOUR-EXIST']);

        $response = $this->actingAs($this->user)->post(route('fournisseurs.store'), [
            'nom' => 'Fournisseur Duplicate',
            'code' => 'FOUR-EXIST', // Code déjà existant
            'contact_principal_nom' => 'Martin',
            'contact_principal_prenom' => 'Sophie',
            'email' => 'sophie.martin@test.com',
            'telephone' => '0102030405',
            'adresse' => '3 Rue Duplicate',
            'ville' => 'Ville Double',
            'code_postal' => '77000',
            'pays' => 'France',
            'notes' => 'Notes de duplicate'
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertDatabaseCount('fournisseurs', 1);
    }

    // Ajoute d'autres tests pour les champs requis manquants...

    public function test_can_view_fournisseur_show()
    {
        $fournisseur = Fournisseur::factory()->create();

        $response = $this->actingAs($this->user)->get(route('fournisseurs.show', $fournisseur));
        $response->assertStatus(200);
        $response->assertViewIs('fournisseurs.show');
        $response->assertViewHas('fournisseur', $fournisseur);
    }

    public function test_can_view_edit_fournisseur_form()
    {
        $fournisseur = Fournisseur::factory()->create();

        $response = $this->actingAs($this->user)->get(route('fournisseurs.edit', $fournisseur));
        $response->assertStatus(200);
        $response->assertViewIs('fournisseurs.edit');
        $response->assertViewHas('fournisseur', $fournisseur);
    }

    public function test_can_update_fournisseur_with_valid_data()
    {
        $fournisseur = Fournisseur::factory()->create([
             'nom' => 'Ancien Nom',
            'code' => 'OLD-CODE',
            'contact_principal' => 'Ancien Contact',
            'email' => 'ancien.contact@test.com',
            'telephone' => '0000000000',
            'adresse' => 'Ancienne Adresse',
            'ville' => 'Ancienne Ville',
            'code_postal' => '00000',
            'pays' => 'Ancien Pays',
            'notes' => 'Anciennes notes'
        ]);
        // Créer un contact principal associé (pour simuler l'état après création)
        $fournisseur->contacts()->create([
             'fournisseur_id' => $fournisseur->id,
             'nom' => 'Ancien',
             'prenom' => 'Contact',
             'fonction' => 'Contact Principal',
             'telephone' => '0000000000',
             'email' => 'ancien.contact@test.com',
             'est_principal' => true
        ]);

        $response = $this->actingAs($this->user)->put(route('fournisseurs.update', $fournisseur), [
            'nom' => 'Nouveau Nom',
            'code' => 'NEW-CODE',
            'contact_principal_nom' => 'Nouveau',
            'contact_principal_prenom' => 'Contact',
            'email' => 'nouveau.contact@test.com',
            'telephone' => '1111111111',
            'adresse' => 'Nouvelle Adresse',
            'ville' => 'Nouvelle Ville',
            'code_postal' => '11111',
            'pays' => 'Nouveau Pays',
            'notes' => 'Nouvelles notes',
            'actif' => true // Assumer qu'il y a un champ actif dans la vue
        ]);

        $response->assertRedirect(route('fournisseurs.show', $fournisseur));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('fournisseurs', [
            'id' => $fournisseur->id,
            'nom' => 'Nouveau Nom',
            'code' => 'NEW-CODE',
            'email' => 'nouveau.contact@test.com'
        ]);
         $this->assertDatabaseHas('contact_fournisseurs', [
            'fournisseur_id' => $fournisseur->id,
            'nom' => 'Nouveau',
            'prenom' => 'Contact',
            'email' => 'nouveau.contact@test.com',
            'telephone' => '1111111111',
            'est_principal' => true
        ]);
    }

    public function test_cannot_update_fournisseur_with_duplicate_code()
    {
        $fournisseur1 = Fournisseur::factory()->create(['code' => 'CODE-UNIQ1']);
        $fournisseur2 = Fournisseur::factory()->create(['code' => 'CODE-UNIQ2']);

        $response = $this->actingAs($this->user)->put(route('fournisseurs.update', $fournisseur2), [
            'nom' => 'Fournisseur 2 Modifié',
            'code' => 'CODE-UNIQ1', // Code déjà pris par fournisseur1
             'contact_principal_nom' => 'Contact2',
            'contact_principal_prenom' => 'Modifié',
            'email' => 'contact2.modifie@test.com',
            'telephone' => '2222222222',
            'adresse' => 'Adresse 2 Modifiée',
            'ville' => 'Ville 2 Modifiée',
            'code_postal' => '22222',
            'pays' => 'Pays 2 Modifié',
            'notes' => 'Notes 2 Modifiées',
            'actif' => true
        ]);

        $response->assertSessionHasErrors('code');
        // Assurer que fournisseur2 n'a pas été modifié
        $this->assertDatabaseHas('fournisseurs', [
            'id' => $fournisseur2->id,
            'code' => 'CODE-UNIQ2',
            'nom' => 'Fournisseur 2' // Assumer un nom par défaut du factory
        ]);
    }

     public function test_can_delete_fournisseur_without_dependencies()
    {
        $fournisseur = Fournisseur::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('fournisseurs.destroy', $fournisseur));

        $response->assertRedirect(route('fournisseurs.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('fournisseurs', ['id' => $fournisseur->id]);
    }

    // Note: Pour tester cannot_delete_fournisseur_with_approvisionnements et 
    // cannot_delete_fournisseur_with_solde, il faudrait d'abord créer 
    // des Approvisionnements et/ou modifier le solde du fournisseur, 
    // ce qui nécessite l'accès aux Factories ou la création directe via le modèle.
    // Ces tests sont plus complexes et nécessiteraient de simuler la création 
    // de ces dépendances.
    // Pour l'instant, je me concentre sur les tests de base et de validation.
} 