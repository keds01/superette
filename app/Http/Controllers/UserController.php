<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Affiche le formulaire de création d'un utilisateur
     */
    public function create()
    {
        // Liste des rôles disponibles
        $roles = [
            User::ROLE_SUPER_ADMIN => 'Super Administrateur',
            User::ROLE_ADMIN => 'Administrateur',
            User::ROLE_RESPONSABLE => 'Responsable',
            User::ROLE_CAISSIER => 'Caissier'
        ];
        
        return view('users.create', compact('roles'));
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'role' => 'required|string|in:' . implode(',', [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_RESPONSABLE, User::ROLE_CAISSIER]),
            'actif' => 'nullable|boolean',
            'superette_id' => $request->role === User::ROLE_SUPER_ADMIN ? 'nullable|exists:superettes,id' : 'required|exists:superettes,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'role' => $request->role,
            'actif' => $request->has('actif') ? 1 : 0,
            'superette_id' => $request->superette_id
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Affiche les détails d'un utilisateur
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Affiche le formulaire d'édition d'un utilisateur
     */
    public function edit(User $user)
    {
        // Liste des rôles disponibles
        $roles = [
            User::ROLE_SUPER_ADMIN => 'Super Administrateur',
            User::ROLE_ADMIN => 'Administrateur',
            User::ROLE_RESPONSABLE => 'Responsable',
            User::ROLE_CAISSIER => 'Caissier'
        ];
        
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Met à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,'.$user->id,
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'role' => 'required|string|in:' . implode(',', [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_RESPONSABLE, User::ROLE_CAISSIER]),
            'actif' => 'nullable|boolean',
            'superette_id' => $request->role === User::ROLE_SUPER_ADMIN ? 'nullable|exists:superettes,id' : 'required|exists:superettes,id',
        ];

        // Vérifier si un mot de passe a été fourni
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Password::min(8)];
        }

        $request->validate($rules);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'role' => $request->role,
            'actif' => $request->has('actif') ? 1 : 0,
            'superette_id' => $request->superette_id
        ];

        // Mettre à jour le mot de passe si fourni
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprime un utilisateur
     */
    public function destroy(User $user)
    {
        // Empêcher la suppression de l'administrateur principal
        if ($user->isSuperAdmin()) {
            return redirect()->route('users.index')
                ->with('error', 'Impossible de supprimer un Super Administrateur.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
