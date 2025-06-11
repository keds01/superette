<?php

namespace Database\Seeders;

use App\Models\Commande;
use App\Models\DetailCommande;
use Illuminate\Database\Seeder;

class CommandeSeeder extends Seeder
{
    public function run()
    {
        // Créer 30 commandes
        Commande::factory(30)->create()->each(function ($commande) {
            // Pour chaque commande, créer entre 2 et 8 détails de commande
            $details = DetailCommande::factory(rand(2, 8))->create([
                'commande_id' => $commande->id,
            ]);

            // Mettre à jour le montant total de la commande
            $commande->update(['montant_total' => $details->sum('montant_total')]);
        });
    }
}
