@extends('layouts.app')

@section('title', 'Modifier l\'utilisateur')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-bold bg-gradient-to-tr from-indigo-400 to-purple-600 bg-clip-text text-transparent tracking-tight flex items-center gap-3">
                            <svg class="w-8 h-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7h.01M7 16h.01M12 13.25a1.25 1.25 0 110 2.5 1.25 1.25 0 010-2.5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2M10 5a2 2 0 114 0 2 2 0 01-4 0z" />
                            </svg>
                            Modifier l'Utilisateur
                        </h2>
                        <p class="mt-2 text-lg text-gray-500">{{ $user->name }}</p>
                    </div>
                    <div class="flex gap-3">
                         <a href="{{ route('users.index') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-indigo-200 text-indigo-600 font-semibold shadow-sm hover:bg-indigo-50 transition-all duration-200">
                            <i class="fas fa-arrow-left"></i>
                            Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Colonne principale -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Informations personnelles -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Informations personnelles
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nom complet</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('email') border-red-500 @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                    <input type="tel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('telephone') border-red-500 @enderror" id="telephone" name="telephone" value="{{ old('telephone', $user->telephone) }}">
                                    @error('telephone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('adresse') border-red-500 @enderror" id="adresse" name="adresse" value="{{ old('adresse', $user->adresse) }}">
                                    @error('adresse')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-span-2">
                                    <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe <small class="text-gray-500">(laissez vide pour conserver l'actuel)</small></label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <input type="password" class="block w-full rounded-none rounded-l-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('password') border-red-500 @enderror" id="password" name="password">
                                        <button type="button" class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm toggle-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                     @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-span-2">
                                     <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le nouveau mot de passe</label>
                                     <div class="mt-1 flex rounded-md shadow-sm">
                                        <input type="password" class="block w-full rounded-none rounded-l-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="password_confirmation" name="password_confirmation">
                                         <button type="button" class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm toggle-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                 <div class="col-span-full">
                                    <div class="flex items-center">
                                        <input class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" type="checkbox" id="actif" name="actif" value="1" {{ $user->actif ? 'checked' : '' }}>
                                        <label class="ml-2 block text-sm text-gray-900" for="actif">Compte actif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                 <!-- Colonne latérale pour les rôles -->
                <div class="space-y-6">
                    <!-- Rôles attribués -->
                     <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0h4m-6 9h6m-6 3h6" />
                                </svg>
                                Rôles attribués
                            </h3>
                            @foreach($roles as $role)
                            <div class="mb-4 border border-indigo-100 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <input class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" type="checkbox" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->id }}" {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                    <label class="ml-2 block text-sm font-semibold text-gray-900" for="role-{{ $role->id }}">
                                        {{ $role->nom }}
                                    </label>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ $role->description }}</p>
                                <div class="text-xs font-medium text-gray-700">
                                    Permissions:
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @foreach($role->permissions->take(3) as $permission)
                                            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-800">{{ $permission->description }}</span>
                                        @endforeach
                                        @if($role->permissions->count() > 3)
                                            <span class="px-2 py-0.5 rounded-full bg-gray-200 text-gray-700">+{{ $role->permissions->count() - 3 }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('users.show', $user) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-gray-300 text-gray-700 font-semibold shadow-sm hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold shadow-lg hover:bg-indigo-700 transition-colors duration-200">
                    <i class="fas fa-save"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Fonction pour basculer la visibilité du mot de passe
    $('.toggle-password').click(function() {
        const input = $(this).siblings('input');
        const icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});
</script>
@endpush
