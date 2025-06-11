<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
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
        $users = User::with('roles')->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Affiche le formulaire de création d'un utilisateur
     */
    public function create()
    {
        $roles = Role::with('permissions')->get();
        return view('users.create', compact('roles'));
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'actif' => 'nullable|boolean'
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
                'actif' => $request->has('actif') ? 1 : 0
            ]);

            if ($request->has('roles')) {
                $user->roles()->attach($request->roles);
            }
        });

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Affiche les détails d'un utilisateur
     */
    public function show(User $user)
    {
        $user->load('roles.permissions');
        // Si vous avez un package de suivi d'activités comme spatie/laravel-activitylog, vous pouvez l'utiliser ici
        // $activities = $user->activities()->orderBy('created_at', 'desc')->take(10)->get();
        
        return view('users.show', compact('user'));
    }

    /**
     * Affiche le formulaire d'édition d'un utilisateur
     */
    public function edit(User $user)
    {
        $roles = Role::with('permissions')->get();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Met à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'actif' => 'nullable|boolean'
        ];

        // Vérifier si un mot de passe a été fourni
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Password::min(8)->mixedCase()->numbers()];
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $user) {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
                'actif' => $request->has('actif') ? 1 : 0
            ];

            // Mettre à jour le mot de passe si fourni
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Synchroniser les rôles
            if ($request->has('roles')) {
                $user->roles()->sync($request->roles);
            } else {
                $user->roles()->detach();
            }
        });

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprime un utilisateur
     */
    public function destroy(User $user)
    {
        // Empêcher la suppression de l'administrateur principal
        if ($user->hasRole('Administrateur')) {
            return redirect()->route('users.index')
                ->with('error', 'Impossible de supprimer un utilisateur avec le rôle Administrateur.');
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
