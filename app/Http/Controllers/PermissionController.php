<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('name')->get();
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
            'description' => 'required|string',
        ]);
        Permission::create($request->only(['name', 'description']));
        return redirect()->route('admin.permissions.index')->with('success', 'Permission créée !');
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }
    
    /**
     * Affiche les détails d'une permission
     */
    public function show(Permission $permission)
    {
        // Récupérer les rôles associés à cette permission
        $roles = $permission->roles;
        
        // Extraire le module et l'action à partir du nom de la permission
        $parts = explode('.', $permission->name);
        $moduleInfo = [];
        
        if (count($parts) >= 2) {
            $moduleInfo = [
                'module' => ucfirst($parts[0]),
                'action' => $parts[1],
            ];
            
            // Pour les permissions plus complexes avec des sous-actions
            if (count($parts) >= 3) {
                $moduleInfo['sub_action'] = $parts[2];
            }
        }
        
        // Trouver d'autres permissions du même module
        $relatedPermissions = [];
        if (isset($moduleInfo['module'])) {
            $moduleName = strtolower($moduleInfo['module']);
            $relatedPermissions = Permission::where('name', 'LIKE', $moduleName . '.%')
                                    ->where('id', '!=', $permission->id)
                                    ->get();
        }
        
        return view('admin.permissions.show', compact('permission', 'roles', 'moduleInfo', 'relatedPermissions'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
            'description' => 'required|string',
        ]);
        $permission->update($request->only(['name', 'description']));
        return redirect()->route('admin.permissions.index')->with('success', 'Permission modifiée !');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.permissions.index')->with('success', 'Permission supprimée !');
    }
}
