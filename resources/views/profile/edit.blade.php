@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Mon Profil</h1>
                <p class="mt-1 text-sm text-gray-600">Gérez vos informations personnelles et vos préférences</p>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PATCH')

                <!-- Photo de profil -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Photo de profil</label>
                    <div class="flex items-center space-x-6">
                        <div class="shrink-0">
                            <img class="h-24 w-24 object-cover rounded-full" 
                                src="{{ $profile->photo_url }}" 
                                alt="Photo de profil">
                        </div>
                        <div class="flex-1">
                            <input type="file" name="photo" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="mt-1 text-sm text-gray-500">PNG, JPG ou GIF jusqu'à 2MB</p>
                            @error('photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informations personnelles -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom complet</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <input type="tel" name="telephone" id="telephone" value="{{ old('telephone', $profile->telephone) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('telephone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse</label>
                        <input type="text" name="adresse" id="adresse" value="{{ old('adresse', $profile->adresse) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('adresse')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Rôle (uniquement pour les administrateurs) -->
                @if(auth()->user()->hasRole('admin'))
                <div class="mb-6">
                    <label for="role" class="block text-sm font-medium text-gray-700">Rôle</label>
                    <select name="role" id="role"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="admin" {{ auth()->user()->hasRole('admin') ? 'selected' : '' }}>Administrateur</option>
                        <option value="gestionnaire" {{ auth()->user()->hasRole('gestionnaire') ? 'selected' : '' }}>Gestionnaire</option>
                        <option value="caissier" {{ auth()->user()->hasRole('caissier') ? 'selected' : '' }}>Caissier</option>
                        <option value="stockiste" {{ auth()->user()->hasRole('stockiste') ? 'selected' : '' }}>Stockiste</option>
                        <option value="vendeur" {{ auth()->user()->hasRole('vendeur') ? 'selected' : '' }}>Vendeur</option>
                    </select>
                    @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                <!-- Statut -->
                <div class="mb-6">
                    <label for="actif" class="block text-sm font-medium text-gray-700">Statut</label>
                    <select name="actif" id="actif"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="1" {{ old('actif', $profile->actif) ? 'selected' : '' }}>Actif</option>
                        <option value="0" {{ old('actif', $profile->actif) ? '' : 'selected' }}>Inactif</option>
                    </select>
                    @error('actif')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Préférences -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Préférences</label>
                    <div class="space-y-4">
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="preferences[notifications_email]" value="1"
                                    {{ old('preferences.notifications_email', $profile->getPreference('notifications_email', true)) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">Recevoir les notifications par email</span>
                            </label>
                        </div>
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="preferences[notifications_sms]" value="1"
                                    {{ old('preferences.notifications_sms', $profile->getPreference('notifications_sms', false)) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">Recevoir les notifications par SMS</span>
                            </label>
                        </div>
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="preferences[theme_sombre]" value="1"
                                    {{ old('preferences.theme_sombre', $profile->getPreference('theme_sombre', false)) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">Utiliser le thème sombre</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        Enregistrer les modifications
                    </button>

                    <button type="button" onclick="document.getElementById('delete-account-form').submit()"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Supprimer mon compte
                    </button>
                </div>
            </form>
        </div>

        <!-- Formulaire de suppression de compte -->
        <form id="delete-account-form" action="{{ route('profile.destroy') }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
            <div class="mt-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe actuel</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prévisualisation de la photo
    const photoInput = document.querySelector('input[name="photo"]');
    const photoPreview = document.querySelector('img[alt="Photo de profil"]');

    photoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                photoPreview.src = e.target.result;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Confirmation de suppression de compte
    document.querySelector('button[onclick="document.getElementById(\'delete-account-form\').submit()"]')
        .addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')) {
                document.getElementById('delete-account-form').classList.remove('hidden');
            }
        });
});
</script>
@endpush
@endsection 