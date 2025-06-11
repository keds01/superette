<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vider les tables pour éviter les doublons
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_permission')->truncate();
        DB::table('user_role')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Créer les rôles
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrateur avec tous les droits',
                'guard_name' => 'web'
            ],
            [
                'name' => 'gérant',
                'description' => 'Gérant de la superette',
                'guard_name' => 'web'
            ],
            [
                'name' => 'caissier',
                'description' => 'Employé qui gère la caisse',
                'guard_name' => 'web'
            ],
            [
                'name' => 'stockiste',
                'description' => 'Employé qui gère le stock',
                'guard_name' => 'web'
            ],
            [
                'name' => 'vendeur',
                'description' => 'Employé qui gère les ventes',
                'guard_name' => 'web'
            ],
            [
                'name' => 'responsable',
                'description' => 'Responsable de boutique avec droits étendus mais pas système',
                'guard_name' => 'web'
            ]
        ];

        // Création des rôles en base
        $roleModels = [];
        foreach ($roles as $roleData) {
            $role = Role::create([
                'name' => $roleData['name'],
                'description' => $roleData['description'],
                'guard_name' => $roleData['guard_name']
            ]);
            $roleModels[$roleData['name']] = $role;
        }

        // Créer les permissions par module
        $permissionGroups = [
            // Gestion des produits
            'produits' => [
                'voir_produits' => 'Voir la liste des produits',
                'ajouter_produit' => 'Ajouter un nouveau produit',
                'modifier_produit' => 'Modifier un produit existant',
                'supprimer_produit' => 'Supprimer un produit',
                'importer_produits' => 'Importer des produits en masse'
            ],
            
            // Gestion du stock
            'stocks' => [
                'voir_stocks' => 'Voir l\'état du stock',
                'ajuster_stock' => 'Ajuster le stock manuellement',
                'voir_mouvements' => 'Voir les mouvements de stock',
                'ajouter_mouvement' => 'Ajouter un mouvement de stock',
                'voir_alertes_stock' => 'Voir les alertes de stock',
                'gérer_inventaire' => 'Gérer l\'inventaire'
            ],
            
            // Gestion des ventes
            'ventes' => [
                'voir_ventes' => 'Voir l\'historique des ventes',
                'créer_vente' => 'Créer une nouvelle vente',
                'annuler_vente' => 'Annuler une vente',
                'gérer_remises' => 'Gérer les remises et promotions',
                'voir_statistiques_ventes' => 'Voir les statistiques de ventes'
            ],
            
            // Gestion de la caisse
            'caisse' => [
                'ouvrir_caisse' => 'Ouvrir la caisse',
                'fermer_caisse' => 'Fermer la caisse',
                'encaisser' => 'Encaisser un paiement',
                'rembourser' => 'Effectuer un remboursement',
                'voir_transactions_caisse' => 'Voir les transactions de caisse',
                'ajuster_caisse' => 'Ajuster le montant en caisse'
            ],
            
            // Administration du système
            'administration' => [
                'gérer_roles' => 'Gérer les rôles et permissions',
                'gérer_utilisateurs' => 'Gérer les comptes utilisateurs',
                'configurer_système' => 'Configurer les paramètres du système'
            ]
        ];
        
        // Création des permissions en base
        $permissionModels = [];
        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permName => $permDescription) {
                $permission = Permission::create([
                    'name' => $permName,
                    'description' => $permDescription,
                    'guard_name' => 'web'
                ]);
                $permissionModels[$permName] = $permission;
            }
        }
        
        // Attribution des permissions aux rôles via la table pivot 'role_permission'
        
        // 1. Administrateur - tous les droits
        $adminPermissions = array_values($permissionModels);
        $roleModels['admin']->permissions()->attach(array_column($adminPermissions, 'id'));
        
        // 2. Gérant - presque tous les droits sauf admin système
        $gerantPermissions = [];
        foreach ($permissionModels as $name => $permission) {
            if (!in_array($name, ['gérer_roles', 'gérer_utilisateurs', 'configurer_système'])) {
                $gerantPermissions[] = $permission->id;
            }
        }
        $roleModels['gérant']->permissions()->attach($gerantPermissions);
        
        // 3. Caissier - droits caisse et vente limitée
        $caissierPermissions = [];
        $caissierPerms = [
            'voir_produits', 'voir_stocks', 'voir_ventes', 'créer_vente',
            'ouvrir_caisse', 'fermer_caisse', 'encaisser', 'rembourser',
            'voir_transactions_caisse'
        ];
        foreach ($caissierPerms as $perm) {
            if (isset($permissionModels[$perm])) {
                $caissierPermissions[] = $permissionModels[$perm]->id;
            }
        }
        $roleModels['caissier']->permissions()->attach($caissierPermissions);
        
        // 4. Stockiste - gestion du stock et produits
        $stockistePermissions = [];
        $stockistePerms = [
            'voir_produits', 'ajouter_produit', 'modifier_produit',
            'voir_stocks', 'ajuster_stock', 'voir_mouvements', 
            'ajouter_mouvement', 'voir_alertes_stock', 'gérer_inventaire'
        ];
        foreach ($stockistePerms as $perm) {
            if (isset($permissionModels[$perm])) {
                $stockistePermissions[] = $permissionModels[$perm]->id;
            }
        }
        $roleModels['stockiste']->permissions()->attach($stockistePermissions);

        // 5. Responsable - droits similaires au gérant mais sans administration système
        $responsablePermissions = $gerantPermissions; // utilise les mêmes règles que gérant (déjà filtré)
        $roleModels['responsable']->permissions()->attach($responsablePermissions);

        // 6. Vendeur - ventes et clients
        $vendeurPermissions = [];
        $vendeurPerms = [
            'voir_produits', 'voir_stocks', 'voir_ventes', 'créer_vente', 'encaisser'
        ];
        foreach ($vendeurPerms as $perm) {
            if (isset($permissionModels[$perm])) {
                $vendeurPermissions[] = $permissionModels[$perm]->id;
            }
        }
        $roleModels['vendeur']->permissions()->attach($vendeurPermissions);
        
        // Assigner le rôle admin au premier utilisateur via la table pivot 'user_role'
        $admin = User::first();
        if ($admin) {
            $admin->roles()->attach($roleModels['admin']->id);
            echo "Utilisateur admin " . $admin->name . " a reçu le rôle d'administrateur\n";
        }
    }
}
