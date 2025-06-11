<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $user = auth()->user();
        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);
        return view('profile.edit', compact('user', 'profile'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'telephone' => ['nullable', 'string', 'max:20'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:100'],
            'pays' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:1024'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'min:8', 'confirmed'],
        ]);

        // Mise à jour des informations de base
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Mise à jour du mot de passe si fourni
        if ($request->filled('new_password')) {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
        }

        // Mise à jour du profil
        $profileData = $request->only([
            'telephone',
            'adresse',
            'ville',
            'pays',
            'bio'
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profiles', 'public');
            $profileData['photo'] = $path;
        }

        $profile->fill($profileData);
        $profile->save();

        return redirect()->route('profile.edit')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password']
        ]);

        $user = auth()->user();

        // Suppression de la photo si elle existe
        if ($user->profile && $user->profile->photo) {
            Storage::delete($user->profile->photo);
        }

        // Suppression du profil
        if ($user->profile) {
            $user->profile->delete();
        }

        // Suppression de l'utilisateur
        $user->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string',
            'value' => 'required'
        ]);

        $profile = auth()->user()->profile;
        if ($profile) {
            $profile->updatePreferences($validated['key'], $validated['value']);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
