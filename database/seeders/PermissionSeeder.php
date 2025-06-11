<?php
namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Création et attribution des permissions aux rôles.
     */
    public function run(): void
    {
        // Création des permissions par module
        $permissions = [
            // Permissions Produits
            'voir_produits' => 'Permet de voir la liste des produits',
            'ajouter_produit' => 'Permet d\'ajouter un nouveau produit',
            'modifier_produit' => 'Permet de modifier les informations d\'un produit',
            'supprimer_produit' => 'Permet de supprimer un produit',
            
            // Permissions Catégories
            'voir_categories' => 'Permet de voir la liste des catégories',
            'ajouter_categorie' => 'Permet d\'ajouter une nouvelle catégorie',
            'modifier_categorie' => 'Permet de modifier une catégorie',
            'supprimer_categorie' => 'Permet de supprimer une catégorie',
            
            // Permissions Stock
            'voir_stock' => 'Permet de voir l\'état du stock',
            'ajouter_mouvement' => 'Permet d\'enregistrer un mouvement de stock',
            'ajuster_stock' => 'Permet d\'ajuster le stock manuellement',
            'voir_mouvements' => 'Permet de consulter l\'historique des mouvements',
            
            // Permissions Ventes
            'voir_ventes' => 'Permet de voir la liste des ventes',
            'ajouter_vente' => 'Permet d\'enregistrer une nouvelle vente',
            'annuler_vente' => 'Permet d\'annuler une vente',
            'voir_caisse' => 'Permet d\'accéder au module caisse',
            
            // Permissions Alertes
            'voir_alertes' => 'Permet de voir les alertes',
            'configurer_alertes' => 'Permet de configurer les seuils d\'alerte',
            
            // Permissions Rapports
            'voir_rapports' => 'Permet de voir les rapports et statistiques',
            'exporter_rapports' => 'Permet d\'exporter les rapports',
            
            // Permissions Utilisateurs et Rôles
            'voir_utilisateurs' => 'Permet de voir la liste des utilisateurs',
            'ajouter_utilisateur' => 'Permet d\'ajouter un utilisateur',
            'modifier_utilisateur' => 'Permet de modifier un utilisateur',
            'supprimer_utilisateur' => 'Permet de supprimer un utilisateur',
            'voir_roles' => 'Permet de voir les rôles',
            'gerer_roles' => 'Permet de gérer les rôles et permissions',
        ];

        // Création des permissions en base de données
        foreach ($permissions as $nom => $description) {
            Permission::firstOrCreate(
                ['nom' => $nom],
                ['description' => $description]
            );
        }

        // Attribution des permissions aux rôles
        $this->attribuerPermissionsAdmin();
        $this->attribuerPermissionsGestionnaire();
        $this->attribuerPermissionsCaissier();

        $this->command->info('Permissions créées et attribuées avec succès!');
    }

    /**
     * Attribution des permissions au rôle Administrateur
     */
    private function attribuerPermissionsAdmin()
    {
        $role = Role::where('nom', 'admin')->first();
        if (!$role) {
            $this->command->warn('Rôle admin non trouvé!');
            return;
        }

        // L'administrateur a accès à toutes les permissions
        $permissions = Permission::all();
        $role->permissions()->sync($permissions->pluck('id')->toArray());
    }

    /**
     * Attribution des permissions au rôle Gestionnaire
     */
    private function attribuerPermissionsGestionnaire()
    {
        $role = Role::where('nom', 'gestionnaire')->first();
        if (!$role) {
            $this->command->warn('Rôle gestionnaire non trouvé!');
            return;
        }

        // Liste des permissions NON accessibles au gestionnaire
        $restrictedPermissions = [
            'supprimer_utilisateur',
            'gerer_roles',
        ];

        $permissions = Permission::whereNotIn('nom', $restrictedPermissions)->get();
        $role->permissions()->sync($permissions->pluck('id')->toArray());
    }

    /**
     * Attribution des permissions au rôle Caissier
     */
    private function attribuerPermissionsCaissier()
    {
        $role = Role::where('nom', 'caissier')->first();
        if (!$role) {
            $this->command->warn('Rôle caissier non trouvé!');
            return;
        }

        // Liste des permissions accessibles au caissier
        $caissierPermissions = [
            'voir_produits',
            'voir_categories',
            'voir_stock',
            'voir_ventes',
            'ajouter_vente',
            'voir_caisse',
            'voir_alertes',
        ];

        $permissions = Permission::whereIn('nom', $caissierPermissions)->get();
        $role->permissions()->sync($permissions->pluck('id')->toArray());
    }
}
