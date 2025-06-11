<?php

namespace Database\Seeders;

use App\Models\Reception;
use App\Models\DetailReception;
use Illuminate\Database\Seeder;

class ReceptionSeeder extends Seeder
{
    public function run()
    {
        // Créer 20 réceptions
        Reception::factory(20)->create()->each(function ($reception) {
            // Pour chaque réception, créer entre 1 et 10 détails de réception
            DetailReception::factory(rand(1, 10))->create([
                'reception_id' => $reception->id,
            ]);
        });
    }
}
