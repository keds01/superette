@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-blue-600 via-cyan-500 to-teal-400 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Nouveau Fournisseur</h1>
                <p class="mt-2 text-lg text-gray-500">Ajoutez un nouveau fournisseur à votre liste.</p>
            </div>
            <a href="{{ route('fournisseurs.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-blue-600 to-teal-500 text-white font-bold shadow-xl hover:shadow-neon-blue hover:-translate-y-1 transition-all duration-200">
                <i class="fas fa-arrow-left"></i>
                Retour à la liste
            </a>
        </div>

        <!-- Formulaire avec design moderne -->
        <div class="bg-white/60 backdrop-blur-xl border border-blue-100 rounded-2xl shadow-2xl p-8">
            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-200 border border-red-400 text-red-900 shadow animate-pulse">
                    <strong>Erreur :</strong> {{ session('error') }}
                </div>
            @endif
            <form action="{{ route('fournisseurs.store') }}" method="POST" class="space-y-8">
                @csrf

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-100 border border-red-300 text-red-800 shadow animate-pulse">
                        <strong>Veuillez corriger les erreurs suivantes :</strong>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Informations de base -->
                <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center gap-2">
                         <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center text-white">
                             <i class="fas fa-info"></i>
                         </div>
                        Informations de base
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nom" class="block text-sm font-medium text-blue-700 mb-1">Nom du fournisseur *</label>
                            <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('nom')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="code" class="block text-sm font-medium text-blue-700 mb-1">Code Fournisseur</label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}"
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all"
                                   placeholder="Laisser vide pour générer automatiquement">
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                         <div>
                            <label for="contact_principal_nom" class="block text-sm font-medium text-blue-700 mb-1">Nom du Contact Principal *</label>
                            <input type="text" name="contact_principal_nom" id="contact_principal_nom" value="{{ old('contact_principal_nom') }}" required
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('contact_principal_nom')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                         <div>
                            <label for="contact_principal_prenom" class="block text-sm font-medium text-blue-700 mb-1">Prénom du Contact Principal</label>
                            <input type="text" name="contact_principal_prenom" id="contact_principal_prenom" value="{{ old('contact_principal_prenom') }}"
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('contact_principal_prenom')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Coordonnées -->
                <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center text-white">
                             <i class="fas fa-envelope"></i>
                         </div>
                        Coordonnées
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-blue-700 mb-1">Email *</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="telephone" class="block text-sm font-medium text-blue-700 mb-1">Téléphone *</label>
                            <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}" required
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('telephone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="adresse" class="block text-sm font-medium text-blue-700 mb-1">Adresse Complète *</label>
                            <textarea name="adresse" id="adresse" rows="3" required
                                      class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all"
                                      placeholder="Rue, Ville, Code Postal, Pays...">{{ old('adresse') }}</textarea>
                            @error('adresse')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                         <div>
                            <label for="ville" class="block text-sm font-medium text-blue-700 mb-1">Ville *</label>
                            <input type="text" name="ville" id="ville" value="{{ old('ville') }}" required
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('ville')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="code_postal" class="block text-sm font-medium text-blue-700 mb-1">Code Postal *</label>
                            <input type="text" name="code_postal" id="code_postal" value="{{ old('code_postal') }}" required
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('code_postal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                         <div>
                            <label for="pays" class="block text-sm font-medium text-blue-700 mb-1">Pays *</label>
                            <input type="text" name="pays" id="pays" value="{{ old('pays') }}" required
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('pays')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center gap-2">
                         <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center text-white">
                             <i class="fas fa-sticky-note"></i>
                         </div>
                        Notes Supplémentaires
                    </h3>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-blue-700 mb-1">Notes internes concernant le fournisseur</label>
                        <textarea name="notes" id="notes" rows="4"
                                  class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all"
                                  placeholder="Informations additionnelles...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('fournisseurs.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold shadow hover:bg-gray-200 hover:-translate-y-0.5 transition-all duration-200">
                        Annuler
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-blue-600 to-teal-500 text-white font-bold shadow-xl hover:shadow-neon-blue hover:-translate-y-1 transition-all duration-200">
                        <i class="fas fa-save"></i>
                        Créer le Fournisseur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Styles pour l'animation du header */
.animate-fade-in-down {
    animation: fadeInDown 0.5s ease-out;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Style pour l'ombre néon (à adapter si nécessaire) */
.hover\:shadow-neon-blue:hover {
    box-shadow: 0 0 15px rgba(59, 130, 246, 0.5); /* blue-500 */
}
</style>
@endpush
@endsection 