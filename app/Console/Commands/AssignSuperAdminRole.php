<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignSuperAdminRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-super-admin
                            {email : Email de l\'utilisateur}
                            {--sync : Supprimer tous les autres rôles}
                            {--permissions : Attribuer toutes les permissions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attribuer le rôle de super-admin à un utilisateur';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $sync = $this->option('sync');
        $givePermissions = $this->option('permissions');

        // Vérifier si l'utilisateur existe
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Utilisateur avec l'email '{$email}' non trouvé.");
            return 1;
        }

        $this->info("Utilisateur trouvé: {$user->name} (ID: {$user->id})");

        // Vérifier si le rôle super-admin existe
        $superAdminRole = Role::where('name', 'super-admin')->first();

        if (!$superAdminRole) {
            $this->info("Le rôle 'super-admin' n'existe pas. Création du rôle...");
            
            try {
                $superAdminRole = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
                $this->info("Rôle 'super-admin' créé avec succès.");
            } catch (\Exception $e) {
                $this->error("Erreur lors de la création du rôle: {$e->getMessage()}");
                return 1;
            }
        }

        // Vérifier si l'utilisateur a déjà le rôle
        if ($user->hasRole('super-admin')) {
            $this->info("L'utilisateur a déjà le rôle 'super-admin'.");
        } else {
            try {
                if ($sync) {
                    $user->syncRoles(['super-admin']);
                    $this->info("Tous les autres rôles ont été supprimés.");
                } else {
                    $user->assignRole('super-admin');
                }
                
                $this->info("Rôle 'super-admin' attribué à {$user->name} avec succès!");
            } catch (\Exception $e) {
                $this->error("Erreur lors de l'attribution du rôle: {$e->getMessage()}");
                return 1;
            }
        }

        // Attribuer toutes les permissions si demandé
        if ($givePermissions) {
            try {
                $permissions = Permission::all();
                
                if ($permissions->count() > 0) {
                    $user->syncPermissions($permissions->pluck('name')->toArray());
                    $this->info("Toutes les permissions ont été attribuées à {$user->name}.");
                } else {
                    $this->info("Aucune permission n'est définie dans le système.");
                }
            } catch (\Exception $e) {
                $this->error("Erreur lors de l'attribution des permissions: {$e->getMessage()}");
                return 1;
            }
        }

        $this->newLine();
        $this->info("L'utilisateur {$user->name} ({$email}) est maintenant super-admin.");
        $this->info("Il peut accéder à toutes les superettes et fonctionnalités d'administration.");
        
        return 0;
    }
} 