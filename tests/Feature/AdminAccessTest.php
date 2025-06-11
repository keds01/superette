<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function guest_cannot_access_admin_dashboard()
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function non_admin_user_cannot_access_admin_dashboard()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/admin');
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_user_can_access_admin_dashboard()
    {
        $admin = User::factory()->create();
        // Simuler le rôle admin (adapter selon votre système de rôles)
        $admin->roles()->attach(1); // Supposons que le rôle ID 1 = admin
        $this->actingAs($admin);
        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Administration du Système');
    }
}
