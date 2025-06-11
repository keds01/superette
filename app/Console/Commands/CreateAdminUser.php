<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'app:create-admin-user {name?} {email?} {password?}';
    protected $description = 'Créer un utilisateur administrateur';

    public function handle()
    {
        $name = $this->argument('name') ?? $this->ask('Nom d\'utilisateur?', 'Administrateur');
        $email = $this->argument('email') ?? $this->ask('Email?', 'admin@gnonel.com');
        $password = $this->argument('password') ?? $this->secret('Mot de passe?') ?? 'admin123';

        // Vérifier si l'utilisateur existe déjà
        $user = User::where('email', $email)->first();
        
        if ($user) {
            // Mise à jour du mot de passe si l'utilisateur existe
            $user->password = Hash::make($password);
            $user->save();
            $this->info("Utilisateur {$email} mis à jour avec le nouveau mot de passe.");
        } else {
            // Création d'un nouvel utilisateur
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'actif' => true
            ]);
            $this->info("Utilisateur {$email} créé avec succès.");
        }

        // S'assurer que le rôle admin existe
        $adminRole = Role::firstOrCreate(
            ['nom' => 'admin'],
            ['description' => 'Administrateur système avec tous les droits']
        );

        // Attacher le rôle admin à l'utilisateur
        if (!$user->hasRole('admin')) {
            $user->roles()->attach($adminRole->id);
            $this->info("Rôle 'admin' attribué à l'utilisateur {$email}.");
        }

        $this->info("------------------------------------");
        $this->info("IDENTIFIANTS DE CONNEXION:");
        $this->info("Email: {$email}");
        $this->info("Mot de passe: {$password}");
        $this->info("------------------------------------");
        
        return Command::SUCCESS;
    }
}
