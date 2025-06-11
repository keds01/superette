<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EmployeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employes = User::with('roles')->paginate(10);
        return view('employes.index', compact('employes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('employes.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name']
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('employes.index')
            ->with('success', 'Employé créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $employe)
    {
        return view('employes.show', compact('employe'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $employe)
    {
        $roles = \App\Models\Role::all();
        return view('employes.edit', compact('employe', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $employe)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $employe->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name']
        ]);

        $employe->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($validated['password']) {
            $employe->update([
                'password' => Hash::make($validated['password'])
            ]);
        }

        $employe->syncRoles([$validated['role']]);

        return redirect()->route('employes.index')
            ->with('success', 'Employé mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $employe)
    {
        $employe->delete();

        return redirect()->route('employes.index')
            ->with('success', 'Employé supprimé avec succès.');
    }
}
