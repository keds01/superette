<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        DB::transaction(function () use ($request) {
            $role = Role::create([
                'nom' => $request->nom,
                'description' => $request->description
            ]);

            $role->permissions()->attach($request->permissions);
        });

        return redirect()->route('roles.index')
            ->with('success', 'Rôle créé avec succès.');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:roles,nom,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        DB::transaction(function () use ($request, $role) {
            $role->update([
                'nom' => $request->nom,
                'description' => $request->description
            ]);

            $role->permissions()->sync($request->permissions);
        });

        return redirect()->route('roles.index')
            ->with('success', 'Rôle mis à jour avec succès.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Impossible de supprimer ce rôle car il est attribué à des utilisateurs.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rôle supprimé avec succès.');
    }
    
    /**
     * Affiche les détails d'un rôle spécifique
     */
    public function show(Role $role)
    {
        // Récupérer les permissions associées à ce rôle
        $permissions = $role->permissions;
        
        // Regrouper les permissions par module
        $permissionsByModule = [];
        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $module = ucfirst($parts[0] ?? 'Autre');
            
            if (!isset($permissionsByModule[$module])) {
                $permissionsByModule[$module] = [];
            }
            
            $permissionsByModule[$module][] = $permission;
        }
        
        // Récupérer les utilisateurs ayant ce rôle
        $users = $role->users;
        
        return view('admin.roles.show', compact('role', 'permissionsByModule', 'users'));
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $user->roles()->sync($request->roles);

        return redirect()->back()
            ->with('success', 'Rôles attribués avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'un rôle
     */
    public function edit(Role $role)
    {
        // Toutes les permissions groupées par module
        $permissions = \App\Models\Permission::all();
        $permissionsByModule = [];
        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $module = ucfirst($parts[0] ?? 'Autre');
            if (!isset($permissionsByModule[$module])) {
                $permissionsByModule[$module] = [];
            }
            $permissionsByModule[$module][] = $permission;
        }
        return view('admin.roles.edit', compact('role', 'permissionsByModule'));
    }
} 