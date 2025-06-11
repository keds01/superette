<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Vérifie que l'utilisateur est admin
     */
    private function checkAdmin()
    {
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403, "Accès interdit - Vous n'avez pas le rôle d'administrateur.");
        }
    }
    /**
     * Affiche le tableau de bord d'administration unifié
     */
    public function index()
    {
        $this->checkAdmin();
        $users = User::with('roles')->get();
        $roles = Role::with(['permissions', 'users'])->get();
        $permissions = Permission::with('roles')->get();
        
        // Regrouper les permissions par module
        $permissions_by_module = [];
        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->nom);
            $module = ucfirst($parts[0]);
            
            if (!isset($permissions_by_module[$module])) {
                $permissions_by_module[$module] = [];
            }
            
            $permissions_by_module[$module][] = $permission;
        }
        
        return view('admin.index', compact('users', 'roles', 'permissions', 'permissions_by_module'));
    }

    /**
     * Met à jour les rôles d'un utilisateur
     */
    public function updateRoles(Request $request, $id)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);
        $user = User::findOrFail($id);
        $user->syncRoles($validated['roles']);
        
        return response()->json(['message' => 'Rôles mis à jour avec succès']);
    }

    /**
     * Met à jour les permissions d'un rôle
     */
    public function updatePermissions(Request $request, $id)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        $role = Role::findOrFail($id);
        $role->permissions()->sync($validated['permissions']);
        
        return response()->json(['message' => 'Permissions mises à jour avec succès']);
    }

    /**
     * Active ou désactive un utilisateur
     */
    public function toggleUserStatus($id)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);
        $user->actif = !$user->actif;
        $user->save();
        
        return response()->json([
            'message' => 'Statut mis à jour avec succès',
            'active' => $user->actif
        ]);
    }

    /**
     * Récupère les rôles d'un utilisateur pour l'édition via AJAX
     */
    public function getUserRoles($id)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);
        $roles = Role::all();
        $user_roles = $user->roles->pluck('id')->toArray();
        
        return response()->json([
            'roles' => $roles,
            'user_roles' => $user_roles
        ]);
    }

    /**
     * Récupère les permissions d'un rôle pour l'édition via AJAX
     */
    public function getRolePermissions($id)
    {
        $this->checkAdmin();
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $role_permissions = $role->permissions->pluck('id')->toArray();
        
        return response()->json([
            'permissions' => $permissions,
            'role_permissions' => $role_permissions
        ]);
    }
    
    /**
     * Affiche le formulaire de création d'un utilisateur
     */
    public function createUser()
    {
        $this->checkAdmin();
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }
    
    /**
     * Enregistre un nouvel utilisateur
     */
    public function storeUser(Request $request)
    {
        $this->checkAdmin();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'roles' => 'array|nullable',
            'roles.*' => 'exists:roles,id',
        ]);
        
        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->actif = 1;
        $user->save();
        
        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }
    
    /**
     * Affiche le formulaire de création d'un rôle
     */
    public function createRole()
    {
        $this->checkAdmin();
        $permissions = Permission::all();
        
        // Regrouper les permissions par module
        $permissions_by_module = [];
        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $module = ucfirst($parts[0] ?? 'Autre');
            
            if (!isset($permissions_by_module[$module])) {
                $permissions_by_module[$module] = [];
            }
            
            $permissions_by_module[$module][] = $permission;
        }
        
        return view('admin.roles.create', compact('permissions', 'permissions_by_module'));
    }
    
    /**
     * Enregistre un nouveau rôle
     */
    public function storeRole(Request $request)
    {
        $this->checkAdmin();
        
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'description' => 'required|string|max:255',
            'permissions' => 'array|nullable',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        $role = new Role();
        $role->name = $validated['name'];
        $role->description = $validated['description'];
        $role->guard_name = 'web';
        $role->save();
        
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }
        
        return redirect()->route('admin.index')
            ->with('success', 'Rôle créé avec succès.');
    }
    
    /**
     * Affiche le formulaire de création d'une permission
     */
    public function createPermission()
    {
        $this->checkAdmin();
        return view('admin.permissions.create');
    }
    
    /**
     * Enregistre une nouvelle permission
     */
    public function storePermission(Request $request)
    {
        $this->checkAdmin();
        
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name|max:255',
            'description' => 'required|string|max:255'
        ]);
        
        $permission = new Permission();
        $permission->name = $validated['name'];
        $permission->description = $validated['description'];
        $permission->guard_name = 'web';
        $permission->save();
        
        return redirect()->route('admin.index')
            ->with('success', 'Permission créée avec succès.');
    }
    
    /**
     * Affiche la liste des rôles
     */
    public function roles()
    {
        $this->checkAdmin();
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        
        // Regrouper les permissions par module
        $permissions_by_module = [];
        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $module = ucfirst($parts[0] ?? 'Autre');
            
            if (!isset($permissions_by_module[$module])) {
                $permissions_by_module[$module] = [];
            }
            
            $permissions_by_module[$module][] = $permission;
        }
        
        return view('roles.index', compact('roles', 'permissions', 'permissions_by_module'));
    }

    /**
     * Affiche la liste des utilisateurs (admin)
     */
    public function users()
    {
        $this->checkAdmin();
        $users = \App\Models\User::with('roles')->paginate(10);
        $roles = \App\Models\Role::all();

        // Statistiques pour l'entête
        $stats = [
            'total' => \App\Models\User::count(),
            'active' => \App\Models\User::where('actif', true)->count(),
            'admins' => \App\Models\User::whereHas('roles', function($q){ $q->where('name', 'admin'); })->count(),
            'inactive' => \App\Models\User::where('actif', false)->count(),
        ];

        return view('admin.users.index', compact('users', 'roles', 'stats'));
    }

    /**
     * Affiche la fiche détaillée d'un utilisateur.
     */
    public function showUser($id)
    {
        $this->checkAdmin();
        $user = \App\Models\User::with('roles')->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Affiche le formulaire d'édition d'un utilisateur.
     */
    public function editUser($id)
    {
        $this->checkAdmin();
        $user = \App\Models\User::with('roles')->findOrFail($id);
        $roles = \App\Models\Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Met à jour un utilisateur.
     */
    public function updateUser(Request $request, $id)
    {
        $this->checkAdmin();
        $user = \App\Models\User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'actif' => 'nullable|boolean',
        ]);
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        $user->actif = $request->has('actif') ? 1 : 0;
        $user->save();
        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur modifié avec succès.');
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroyUser($id)
    {
        $this->checkAdmin();
        $user = \App\Models\User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé.');
    }
}
