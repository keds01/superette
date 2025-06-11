<?php
namespace Database\Seeders;

use App\Models\Employe;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeSeeder extends Seeder
{
    /**
     * Crée les employés pour chaque utilisateur du système.
     */
    public function run(): void
    {
        // Récupérer tous les utilisateurs
        $users = User::all();

        foreach ($users as $user) {
            // Vérifier si l'employé existe déjà pour cet utilisateur
            $existingEmploye = Employe::where('user_id', $user->id)->first();
            
            if (!$existingEmploye) {
                // Extraire le prénom/nom du champ name de l'utilisateur si possible
                $nameParts = explode(' ', $user->name, 2);
                $prenom = $nameParts[0];
                $nom = isset($nameParts[1]) ? $nameParts[1] : $prenom;

                // Créer un employé pour cet utilisateur
                Employe::create([
                    'user_id' => $user->id,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $user->email,
                    'telephone' => '0123456789', // Valeur par défaut
                    'poste' => $this->getPosteFromRoles($user),
                    'statut' => 'actif',
                    'date_embauche' => now(),
                ]);

                $this->command->info("Employé créé pour l'utilisateur {$user->name}");
            } else {
                $this->command->info("Employé existe déjà pour l'utilisateur {$user->name}");
            }
        }
    }

    /**
     * Détermine le poste de l'employé en fonction de ses rôles.
     */
    private function getPosteFromRoles(User $user): string
    {
        if ($user->hasRole('admin')) {
            return 'Directeur';
        } elseif ($user->hasRole('gestionnaire')) {
            return 'Chef de rayon';
        } else {
            return 'Caissier';
        }
    }
}
