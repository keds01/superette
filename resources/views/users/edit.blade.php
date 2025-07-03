@extends('layouts.app')

@section('title', 'Modifier l\'utilisateur')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8 animate-fade-in-down">
            <div class="flex items-center gap-4">
                @php
                    $profile = $user->profile;
                    $photoUrl = $profile && $profile->photo ? $profile->photo_url : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF';
                @endphp
                <img class="h-20 w-20 rounded-full object-cover border-4 border-indigo-200 shadow" src="{{ $photoUrl }}" alt="Photo de profil">
                    <div>
                    <h1 class="text-3xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight flex items-center gap-2">
                        <i class="fas fa-user-edit text-indigo-400"></i> Modifier l'utilisateur
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">{{ $user->email }}</p>
                    <div class="flex gap-2 mt-2">
                        @if($user->actif)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i> Actif</span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i> Inactif</span>
                        @endif
                        @if($user->role === 'super_admin')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-200 text-purple-800">Super Administrateur</span>
                        @elseif($user->role === 'admin')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-200 text-indigo-800">Administrateur</span>
                        @elseif($user->role === 'responsable')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-200 text-blue-800">Responsable</span>
                        @elseif($user->role === 'caissier')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-200 text-green-800">Caissier</span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-200 text-gray-800">{{ $user->role ?: 'Aucun rôle' }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
                <a href="{{ route('users.show', $user) }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-white border border-gray-300 text-gray-700 font-semibold shadow-sm hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-times"></i> Annuler
                </a>
            </div>
        </div>

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Colonne principale -->
                <div class="md:col-span-2 space-y-8">
                    <div class="bg-white rounded-xl shadow-xl border border-indigo-50 p-8">
                        <h3 class="text-lg font-bold text-indigo-700 mb-4 flex items-center gap-2"><i class="fas fa-user"></i> Informations personnelles</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nom complet</label>
                                <input type="text" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-500 @enderror" id="email" name="email" value="{{ old('email', $user->email) }}">
                                @error('email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                <input type="tel" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('telephone') border-red-500 @enderror" id="telephone" name="telephone" value="{{ old('telephone', $user->telephone) }}">
                                @error('telephone')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse</label>
                                <input type="text" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('adresse') border-red-500 @enderror" id="adresse" name="adresse" value="{{ old('adresse', $user->adresse) }}">
                                @error('adresse')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="col-span-2">
                                    <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe <small class="text-gray-500">(laissez vide pour conserver l'actuel)</small></label>
                                <input type="password" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('password') border-red-500 @enderror" id="password" name="password">
                                @error('password')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="col-span-2">
                                     <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le nouveau mot de passe</label>
                                <input type="password" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" id="password_confirmation" name="password_confirmation">
                                    </div>
                            <div class="col-span-full flex items-center mt-2">
                                        <input class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" type="checkbox" id="actif" name="actif" value="1" {{ $user->actif ? 'checked' : '' }}>
                                        <label class="ml-2 block text-sm text-gray-900" for="actif">Compte actif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Colonne latérale -->
                <div class="space-y-8">
                    <div class="bg-indigo-50 rounded-xl p-6 border border-indigo-100 shadow">
                        <h3 class="text-lg font-bold text-indigo-700 mb-2 flex items-center gap-2"><i class="fas fa-id-badge"></i> Rôle utilisateur</h3>
                        <div class="mb-4">
                            <label for="superette_id" class="block text-sm font-medium text-gray-700 mb-2">Superette</label>
                            <select name="superette_id" id="superette_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Aucune (Super Admin uniquement)</option>
                                @foreach(\App\Models\Superette::orderBy('nom')->get() as $superette)
                                    <option value="{{ $superette->id }}" {{ old('superette_id', $user->superette_id) == $superette->id ? 'selected' : '' }}>
                                        {{ $superette->nom }} ({{ $superette->code }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Laisser vide uniquement pour les Super Administrateurs. Les utilisateurs standards doivent être affectés à une superette.</p>
                            @error('superette_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                        <div class="mb-4">
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Sélectionnez un rôle</label>
                            <select id="role" name="role" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($roles as $value => $label)
                                    <option value="{{ $value }}" {{ $user->role === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                            </select>
                            @error('role')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                        <div class="text-sm text-gray-600 mt-4">
                            <p class="font-medium">Description des rôles :</p>
                            <ul class="mt-2 space-y-2">
                                <li class="flex gap-2">
                                    <span class="inline-block px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 font-medium">Super Administrateur</span>
                                    <span>Accès complet à toutes les fonctionnalités</span>
                                </li>
                                <li class="flex gap-2">
                                    <span class="inline-block px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 font-medium">Administrateur</span>
                                    <span>Gestion complète sauf administration utilisateurs</span>
                                </li>
                                <li class="flex gap-2">
                                    <span class="inline-block px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-medium">Responsable</span>
                                    <span>Gestion des stocks et des ventes</span>
                                </li>
                                <li class="flex gap-2">
                                    <span class="inline-block px-2 py-0.5 rounded-full bg-green-100 text-green-800 font-medium">Caissier</span>
                                    <span>Accès limité aux ventes</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('users.show', $user) }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-white border border-gray-300 text-gray-700 font-semibold shadow-sm hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-indigo-600 text-white font-semibold shadow-lg hover:bg-indigo-700 transition-colors duration-200">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
