<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AuditJournalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function journal_affiche_les_activites_filtrees_et_paginees()
    {
        $user = User::factory()->create(['name' => 'Alice']);
        $autre = User::factory()->create(['name' => 'Bob']);
        ActivityLog::factory()->create([
            'type' => 'connexion',
            'description' => 'Connexion réussie',
            'user_id' => $user->id,
            'created_at' => Carbon::now()->subDays(2),
        ]);
        ActivityLog::factory()->create([
            'type' => 'modification',
            'description' => 'Modification profil',
            'user_id' => $autre->id,
            'created_at' => Carbon::now()->subDay(),
        ]);

        $this->actingAs($user)
            ->get(route('audit.journal', ['type' => 'connexion']))
            ->assertStatus(200)
            ->assertSee('Connexion réussie')
            ->assertDontSee('Modification profil');
    }

    /** @test */
    public function journal_modal_metadata_est_robuste()
    {
        $user = User::factory()->create();
        $meta = ['ip' => '127.0.0.1', 'navigateur' => 'Firefox'];
        ActivityLog::factory()->create([
            'type' => 'consultation',
            'description' => 'Consultation fiche',
            'user_id' => $user->id,
            'metadata' => $meta,
        ]);

        $this->actingAs($user)
            ->get(route('audit.journal'))
            ->assertSee('Voir détails');
    }

    /** @test */
    public function journal_export_pdf_fonctionne_et_affiche_les_filtres()
    {
        $user = User::factory()->create(['name' => 'Alice']);
        ActivityLog::factory()->create([
            'type' => 'connexion',
            'description' => 'Connexion réussie',
            'user_id' => $user->id,
        ]);
        $this->actingAs($user)
            ->get(route('audit.exporter-journal', ['type' => 'connexion']))
            ->assertStatus(200)
            ->assertSee('Journal des activités')
            ->assertSee('Connexion réussie')
            ->assertSee('Alice');
    }
}
