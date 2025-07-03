@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-indigo-100">
            <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-tr from-indigo-50 to-white">
                <h1 class="text-3xl font-extrabold text-indigo-800 flex items-center gap-2">
                    <i class="fas fa-user-circle text-indigo-400"></i> Mon Profil
                </h1>
                <p class="mt-1 text-sm text-gray-500">Gérez vos informations personnelles</p>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
                @csrf
                @method('PUT')

                <!-- Carte utilisateur moderne -->
                <div class="flex flex-col md:flex-row items-center gap-8 mb-8">
                    <div class="flex-shrink-0">
                        <img class="h-28 w-28 object-cover rounded-full border-4 border-indigo-200 shadow" 
                                src="{{ $profile->photo_url }}" 
                                alt="Photo de profil">
                    </div>
                    <div class="flex-1 w-full">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom complet</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <input type="tel" name="telephone" id="telephone" value="{{ old('telephone', $profile->telephone) }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('telephone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse</label>
                        <input type="text" name="adresse" id="adresse" value="{{ old('adresse', $profile->adresse) }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('adresse')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Photo de profil -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Changer la photo de profil</label>
                    <input type="file" name="photo" accept="image/*"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG ou GIF jusqu'à 2MB</p>
                    @error('photo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Section Rôle -->
                <div class="mb-6 p-5 rounded-xl bg-indigo-50 border border-indigo-100 shadow flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-id-badge text-2xl text-indigo-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-semibold">Rôle actuel</p>
                        <p class="text-base font-bold text-indigo-700 mb-1">
                            @php
                                $role = $user->role;
                                $roleLabel = [
                                    'super_admin' => 'Super Administrateur',
                                    'admin' => 'Administrateur',
                                    'responsable' => 'Responsable',
                                    'caissier' => 'Caissier',
                                ][$role] ?? ucfirst($role);
                                $roleDesc = [
                                    'super_admin' => "Accès total à toutes les fonctionnalités, gestion des utilisateurs, des superettes, des droits et de la configuration avancée.",
                                    'admin' => "Gestion complète de la supérette, accès à tous les modules sauf la gestion des super-admins.",
                                    'responsable' => "Gestion opérationnelle (stocks, ventes, clients, alertes), mais pas d'accès à l'audit, aux employés, aux statistiques, aux rapports ni aux promotions.",
                                    'caissier' => "Accès uniquement à la caisse, aux ventes et à la gestion des clients. Pas d'accès à la gestion des stocks ni aux modules avancés.",
                                ][$role] ?? '';
                            @endphp
                            {{ $roleLabel }}
                        </p>
                        <p class="text-gray-600 text-xs">{{ $roleDesc }}</p>
                    </div>
                </div>

                <!-- Statut -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Statut</h3>
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($user->actif) bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            @if($user->actif)
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Actif
                            @else
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                Inactif
                            @endif
                        </span>
                        <span class="text-sm text-gray-600">
                            @if($user->actif)
                                Votre compte est actif et vous pouvez accéder à toutes les fonctionnalités.
                            @else
                                Votre compte est temporairement désactivé. Contactez l'administrateur.
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Section Modifier le mot de passe -->
                <div class="mb-6 p-5 rounded-xl bg-gray-50 border border-gray-100 shadow">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-key text-indigo-400"></i>
                        <span class="text-sm font-semibold text-gray-700">Modifier le mot de passe</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="current_password" class="block text-xs font-medium text-gray-600">Mot de passe actuel</label>
                            <input type="password" name="current_password" id="current_password" autocomplete="current-password"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('current_password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="new_password" class="block text-xs font-medium text-gray-600">Nouveau mot de passe</label>
                            <input type="password" name="new_password" id="new_password" autocomplete="new-password"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('new_password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="new_password_confirmation" class="block text-xs font-medium text-gray-600">Confirmer le nouveau mot de passe</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" autocomplete="new-password"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('new_password_confirmation')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-between items-center pt-4 border-t border-gray-100 gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-tr from-indigo-600 to-purple-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow hover:bg-indigo-700">
                        <i class="fas fa-save mr-2"></i> Enregistrer
                    </button>
                    <button type="button" onclick="document.getElementById('delete-account-form').submit()"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-tr from-red-500 to-pink-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i> Supprimer
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

    if(photoInput) {
    photoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                photoPreview.src = e.target.result;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    }

    // Confirmation de suppression de compte
    const deleteBtn = document.querySelector('button[onclick="document.getElementById(\'delete-account-form\').submit()"]');
    if(deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')) {
                document.getElementById('delete-account-form').classList.remove('hidden');
            }
        });
    }
});
</script>
@endpush
@endsection 