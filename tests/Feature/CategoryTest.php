<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Categorie;
use App\Models\Produit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class CategoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_display_categories_index()
    {
        $categories = Categorie::factory()->count(3)->create();

        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)
                ->assertViewIs('categories.index')
                ->assertViewHas('categories')
                ->assertSee($categories[0]->nom)
                ->assertSee($categories[1]->nom)
                ->assertSee($categories[2]->nom);
    }

    /** @test */
    public function it_can_create_a_category()
    {
        $categoryData = [
            'nom' => 'Nouvelle Catégorie',
            'description' => 'Description de la nouvelle catégorie',
            'actif' => true
        ];

        $response = $this->post(route('categories.store'), $categoryData);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'nom' => $categoryData['nom'],
            'slug' => 'nouvelle-categorie',
            'description' => $categoryData['description'],
            'actif' => true
        ]);
    }

    /** @test */
    public function it_validates_category_creation()
    {
        $response = $this->post(route('categories.store'), []);

        $response->assertSessionHasErrors(['nom', 'description']);
    }

    /** @test */
    public function it_can_update_a_category()
    {
        $categorie = Categorie::factory()->create();
        $updateData = [
            'nom' => 'Catégorie Modifiée',
            'description' => 'Nouvelle description',
            'actif' => false
        ];

        $response = $this->put(route('categories.update', $categorie), $updateData);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $categorie->id,
            'nom' => $updateData['nom'],
            'slug' => 'categorie-modifiee',
            'description' => $updateData['description'],
            'actif' => false
        ]);
    }

    /** @test */
    public function it_can_delete_an_empty_category()
    {
        $categorie = Categorie::factory()->create();

        $response = $this->delete(route('categories.destroy', $categorie));

        $response->assertRedirect(route('categories.index'));
        $this->assertSoftDeleted('categories', ['id' => $categorie->id]);
    }

    /** @test */
    public function it_cannot_delete_category_with_products()
    {
        $categorie = Categorie::factory()->create();
        Product::factory()->create(['category_id' => $categorie->id]);

        $response = $this->delete(route('categories.destroy', $categorie));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $categorie->id]);
    }

    /** @test */
    public function it_can_display_category_details()
    {
        $categorie = Categorie::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $categorie->id]);

        $response = $this->get(route('categories.show', $categorie));

        $response->assertStatus(200)
                ->assertViewIs('categories.show')
                ->assertViewHas('category')
                ->assertSee($categorie->nom)
                ->assertSee($categorie->description)
                ->assertSee($products[0]->nom)
                ->assertSee($products[1]->nom)
                ->assertSee($products[2]->nom);
    }

    /** @test */
    public function it_can_search_categories()
    {
        Categorie::factory()->create(['nom' => 'Électronique']);
        Categorie::factory()->create(['nom' => 'Alimentation']);
        Categorie::factory()->create(['nom' => 'Vêtements']);

        $response = $this->get(route('categories.index', ['search' => 'Électronique']));

        $response->assertStatus(200)
                ->assertSee('Électronique')
                ->assertDontSee('Alimentation')
                ->assertDontSee('Vêtements');
    }

    /** @test */
    public function it_can_filter_categories_by_status()
    {
        Categorie::factory()->create(['actif' => true]);
        Categorie::factory()->create(['actif' => false]);

        $response = $this->get(route('categories.index', ['status' => 'active']));

        $response->assertStatus(200)
                ->assertSee('Active')
                ->assertDontSee('Inactive');
    }

    /** @test */
    public function it_can_sort_categories()
    {
        Categorie::factory()->create(['nom' => 'Zebra']);
        Categorie::factory()->create(['nom' => 'Alpha']);
        Categorie::factory()->create(['nom' => 'Beta']);

        $response = $this->get(route('categories.index', ['sort' => 'nom', 'direction' => 'asc']));

        $response->assertStatus(200);
        
        $content = $response->getContent();
        $alphaPos = strpos($content, 'Alpha');
        $betaPos = strpos($content, 'Beta');
        $zebraPos = strpos($content, 'Zebra');
        
        $this->assertTrue($alphaPos < $betaPos && $betaPos < $zebraPos);
    }
} 