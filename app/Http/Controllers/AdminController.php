<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Vérifie que l'utilisateur est admin
     */
    private function checkAdmin()
    {
        if (!auth()->check()) {
            abort(403, "Vous devez être connecté pour accéder à cette page.");
        }
        
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            abort(403, "Vous devez être administrateur pour accéder à cette page.");
        }
    }
    
    /**
     * Affiche le tableau de bord d'administration unifié
     */
    public function index()
    {
        $this->checkAdmin();
        $users = User::all();
        
        return view('admin.index', compact('users'));
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
     * Affiche le formulaire de création d'un utilisateur
     */
    public function createUser()
    {
        $this->checkAdmin();
        return view('admin.users.create');
    }
    
    /**
     * Enregistre un nouvel utilisateur
     */
    public function storeUser(Request $request)
    {
        $this->checkAdmin();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'telephone' => $validated['telephone'] ?? null,
            'adresse' => $validated['adresse'] ?? null,
            'actif' => true,
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }
    
    /**
     * Affiche le formulaire d'édition d'un utilisateur
     */
    public function editUser($id)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);
        
        return view('admin.users.edit', compact('user'));
    }
    
    /**
     * Met à jour un utilisateur
     */
    public function updateUser(Request $request, $id)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->telephone = $validated['telephone'] ?? null;
        $user->adresse = $validated['adresse'] ?? null;
        
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprime un utilisateur
     */
    public function destroyUser($id)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);
        
        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
