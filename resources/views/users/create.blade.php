@extends('layouts.app')

@section('title', 'Créer un utilisateur')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-purple-500 to-pink-400 bg-clip-text text-transparent tracking-tight drop-shadow-lg">
                    Créer un utilisateur
                </h1>
                <p class="mt-2 text-lg text-gray-500">Créez un nouveau compte utilisateur et attribuez-lui des rôles.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-all duration-200">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la liste
                </a>
            </div>
        </div>

        <!-- Formulaire glassmorphique -->
        <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8">
            <form action="{{ route('users.store') }}" method="POST" class="space-y-8">
                @csrf

                <!-- Informations principales -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Nom et Email -->
                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-indigo-700 mb-1 flex items-center gap-1">
                                Nom complet <span class="text-red-500">*</span>
                                <i class="fas fa-info-circle text-indigo-400" data-bs-toggle="tooltip" title="Le nom complet de l'utilisateur"></i>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                   class="mt-1 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-indigo-700 mb-1 flex items-center gap-1">
                                Email <span class="text-red-500">*</span>
                                <i class="fas fa-info-circle text-indigo-400" data-bs-toggle="tooltip" title="L'adresse email de l'utilisateur"></i>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                   class="mt-1 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Téléphone et Adresse -->
                    <div class="space-y-6">
                        <div>
                            <label for="telephone" class="block text-sm font-medium text-indigo-700 mb-1 flex items-center gap-1">
                                Téléphone
                                <i class="fas fa-info-circle text-indigo-400" data-bs-toggle="tooltip" title="Le numéro de téléphone de l'utilisateur"></i>
                            </label>
                            <input type="tel" id="telephone" name="telephone" value="{{ old('telephone') }}"
                                   class="mt-1 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all">
                            @error('telephone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="adresse" class="block text-sm font-medium text-indigo-700 mb-1 flex items-center gap-1">
                                Adresse
                                <i class="fas fa-info-circle text-indigo-400" data-bs-toggle="tooltip" title="L'adresse de l'utilisateur"></i>
                            </label>
                            <input type="text" id="adresse" name="adresse" value="{{ old('adresse') }}"
                                   class="mt-1 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all">
                            @error('adresse')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Mot de passe -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="password" class="block text-sm font-medium text-indigo-700 mb-1 flex items-center gap-1">
                            Mot de passe <span class="text-red-500">*</span>
                            <i class="fas fa-info-circle text-indigo-400" data-bs-toggle="tooltip" title="Le mot de passe doit contenir au moins 8 caractères"></i>
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   class="mt-1 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all pr-10">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center toggle-password"
                                    onclick="togglePassword('password')">
                                <i class="fas fa-eye text-indigo-400 hover:text-indigo-600"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-indigo-700 mb-1 flex items-center gap-1">
                            Confirmer le mot de passe <span class="text-red-500">*</span>
                            <i class="fas fa-info-circle text-indigo-400" data-bs-toggle="tooltip" title="Confirmez le mot de passe"></i>
                        </label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   class="mt-1 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all pr-10">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center toggle-password"
                                    onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye text-indigo-400 hover:text-indigo-600"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statut du compte -->
                <div class="flex items-center space-x-3">
                    <div class="relative inline-block w-12 h-6 transition duration-200 ease-in-out rounded-full">
                        <input type="checkbox" id="actif" name="actif" value="1" checked
                               class="absolute w-6 h-6 transition duration-100 ease-in-out transform bg-white border-4 rounded-full appearance-none cursor-pointer border-indigo-200 checked:translate-x-full checked:border-indigo-600 focus:outline-none">
                        <label for="actif" class="block h-6 overflow-hidden bg-indigo-100 rounded-full cursor-pointer"></label>
                    </div>
                    <label for="actif" class="text-sm font-medium text-indigo-700">Compte actif</label>
                </div>

                <!-- Rôles -->
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-indigo-700 mb-2 flex items-center gap-1">
                        Rôles
                        <i class="fas fa-info-circle text-indigo-400" data-bs-toggle="tooltip" title="Sélectionnez les rôles de l'utilisateur"></i>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($roles as $role)
                        <div class="relative flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->id }}"
                                       class="h-4 w-4 text-indigo-600 border-indigo-300 rounded focus:ring-indigo-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="role-{{ $role->id }}" class="font-medium text-indigo-700">{{ $role->nom }}</label>
                                <p class="text-gray-500">{{ $role->description }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4 pt-6">
                    <a href="{{ route('users.index') }}" 
                       class="px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-all duration-200">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                        Créer l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialisation des tooltips
    document.addEventListener('DOMContentLoaded', function() {
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(tooltip => {
            new bootstrap.Tooltip(tooltip);
        });
    });

    // Fonction pour basculer la visibilité du mot de passe
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.nextElementSibling.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush
@endsection
