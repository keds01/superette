<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SetUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:role {email} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attribue un rôle à un utilisateur (super_admin, admin, vendeur)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $role = $this->argument('role');
        
        // Vérifier que le rôle est valide
        $validRoles = [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_VENDEUR];
        if (!in_array($role, $validRoles)) {
            $this->error("Le rôle '$role' n'est pas valide. Les rôles valides sont : " . implode(', ', $validRoles));
            return 1;
        }
        
        // Trouver l'utilisateur
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Aucun utilisateur trouvé avec l'email '$email'");
            return 1;
        }
        
        // Attribuer le rôle
        $user->role = $role;
        $user->save();
        
        $this->info("Le rôle '$role' a été attribué à l'utilisateur $user->name ($email)");
        return 0;
    }
}
