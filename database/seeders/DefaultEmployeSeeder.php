<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employe;
use App\Models\User;

class DefaultEmployeSeeder extends Seeder
{
    public function run()
    {
        // On tente d'associer l'employé au super admin existant si possible
        $user = User::where('email', 'superadmin@gnonel.com')->first();
        $employe = Employe::firstOrCreate(
            ['id' => 1],
            [
                'user_id' => $user ? $user->id : null,
                'nom' => 'Super',
                'prenom' => 'Admin',
                'email' => $user ? $user->email : 'superadmin@gnonel.com',
                'statut' => 'actif',
                'poste' => 'Administrateur',
            ]
        );
    }
}
