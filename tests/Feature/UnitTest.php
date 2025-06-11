<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Unite;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnitTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Créer un utilisateur pour l'authentification si nécessaire
        $this->user = User::factory()->create();
    }

    public function test_can_view_units_index()
    {
        $response = $this->actingAs($this->user)->get(route('units.index'));
        $response->assertStatus(200);
        $response->assertViewIs('units.index');
    }

    public function test_can_view_create_unit_form()
    {
        $response = $this->actingAs($this->user)->get(route('units.create'));
        $response->assertStatus(200);
        $response->assertViewIs('units.create');
    }

    public function test_can_create_unit_with_valid_data()
    {
        $response = $this->actingAs($this->user)->post(route('units.store'), [
            'nom' => 'Kilogramme',
            'symbole' => 'kg',
            'description' => 'Unité de masse'
        ]);

        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('units', ['nom' => 'Kilogramme', 'symbole' => 'kg']);
    }

    public function test_cannot_create_unit_with_duplicate_name()
    {
        Unite::factory()->create(['nom' => 'Kilogramme', 'symbole' => 'kg']);

        $response = $this->actingAs($this->user)->post(route('units.store'), [
            'nom' => 'Kilogramme',
            'symbole' => 'g',
            'description' => 'Autre unité de masse'
        ]);

        $response->assertSessionHasErrors('nom');
        $this->assertDatabaseCount('units', 1);
    }

    public function test_cannot_create_unit_with_duplicate_symbole()
    {
        Unite::factory()->create(['nom' => 'Kilogramme', 'symbole' => 'kg']);

        $response = $this->actingAs($this->user)->post(route('units.store'), [
            'nom' => 'Gramme',
            'symbole' => 'kg',
            'description' => 'Autre unité de masse'
        ]);

        $response->assertSessionHasErrors('symbole');
        $this->assertDatabaseCount('units', 1);
    }

    public function test_can_view_edit_unit_form()
    {
        $unite = Unite::factory()->create();

        $response = $this->actingAs($this->user)->get(route('units.edit', $unite));
        $response->assertStatus(200);
        $response->assertViewIs('units.edit');
        $response->assertViewHas('unit', $unite);
    }

    public function test_can_update_unit_with_valid_data()
    {
        $unite = Unite::factory()->create();

        $response = $this->actingAs($this->user)->put(route('units.update', $unite), [
            'nom' => 'Kilogramme Modifié',
            'symbole' => 'kgm',
            'description' => 'Description modifiée'
        ]);

        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('units', [
            'id' => $unite->id,
            'nom' => 'Kilogramme Modifié',
            'symbole' => 'kgm',
            'description' => 'Description modifiée'
        ]);
    }

    public function test_cannot_update_unit_with_duplicate_name()
    {
        $unite1 = Unite::factory()->create(['nom' => 'Kilogramme', 'symbole' => 'kg']);
        $unite2 = Unite::factory()->create(['nom' => 'Gramme', 'symbole' => 'g']);

        $response = $this->actingAs($this->user)->put(route('units.update', $unite2), [
            'nom' => 'Kilogramme', // Nom déjà pris par unite1
            'symbole' => 'gm',
            'description' => 'Description modifiée'
        ]);

        $response->assertSessionHasErrors('nom');
        // Assurer que unite2 n'a pas été modifié
        $this->assertDatabaseHas('units', [
            'id' => $unite2->id,
            'nom' => 'Gramme',
            'symbole' => 'g'
        ]);
    }

     public function test_cannot_update_unit_with_duplicate_symbole()
    {
        $unite1 = Unite::factory()->create(['nom' => 'Kilogramme', 'symbole' => 'kg']);
        $unite2 = Unite::factory()->create(['nom' => 'Gramme', 'symbole' => 'g']);

        $response = $this->actingAs($this->user)->put(route('units.update', $unite2), [
            'nom' => 'Gramme Modifié',
            'symbole' => 'kg', // Symbole déjà pris par unite1
            'description' => 'Description modifiée'
        ]);

        $response->assertSessionHasErrors('symbole');
        // Assurer que unite2 n'a pas été modifié
        $this->assertDatabaseHas('units', [
            'id' => $unite2->id,
            'nom' => 'Gramme',
            'symbole' => 'g'
        ]);
    }

    public function test_can_delete_unused_unit()
    {
        $unite = Unite::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('units.destroy', $unite));

        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('units', ['id' => $unite->id]);
    }

    public function test_cannot_delete_unit_used_by_products()
    {
        $unite = Unite::factory()->create();
        // Créer un produit qui utilise cette unité
        \App\Models\Produit::factory()->create(['unite_vente_id' => $unite->id]);

        $response = $this->actingAs($this->user)->delete(route('units.destroy', $unite));

        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('error');
        // Assurer que l'unité n'a pas été supprimée
        $this->assertDatabaseHas('units', ['id' => $unite->id]);
    }
} 