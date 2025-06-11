@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-blue-600 via-cyan-500 to-teal-400 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Modifier le Fournisseur</h1>
                <p class="mt-2 text-lg text-gray-500">Modifiez les informations de ce fournisseur.</p>
            </div>
            <a href="{{ route('fournisseurs.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-blue-600 to-teal-500 text-white font-bold shadow-xl hover:shadow-neon-blue hover:-translate-y-1 transition-all duration-200">
                <i class="fas fa-arrow-left"></i>
                Retour à la liste
            </a>
        </div>

        <!-- Formulaire avec design moderne -->
        <div class="bg-white/60 backdrop-blur-xl border border-blue-100 rounded-2xl shadow-2xl p-8">
            <form action="{{ route('fournisseurs.update', $fournisseur) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

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
                            <input type="text" name="nom" id="nom" value="{{ old('nom', $fournisseur->nom) }}" required
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('nom')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="code" class="block text-sm font-medium text-blue-700 mb-1">Code Fournisseur *</label>
                            <input type="text" name="code" id="code" value="{{ old('code', $fournisseur->code) }}" required
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        {{-- Utiliser les nouveaux champs pour le contact principal --}}
                         <div>
                            <label for="contact_principal_nom" class="block text-sm font-medium text-blue-700 mb-1">Nom du Contact Principal *</label>
                            <input type="text" name="contact_principal_nom" id="contact_principal_nom" value="{{ old('contact_principal_nom', $fournisseur->contacts()->where('est_principal', true)->first()->nom ?? '') }}" required
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('contact_principal_nom')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                         <div>
                            <label for="contact_principal_prenom" class="block text-sm font-medium text-blue-700 mb-1">Prénom du Contact Principal</label>
                            <input type="text" name="contact_principal_prenom" id="contact_principal_prenom" value="{{ old('contact_principal_prenom', $fournisseur->contacts()->where('est_principal', true)->first()->prenom ?? '') }}"
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('contact_principal_prenom')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @end error
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
                            <input type="email" name="email" id="email" value="{{ old('email', $fournisseur->email) }}" required
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="telephone" class="block text-sm font-medium text-blue-700 mb-1">Téléphone *</label>
                            <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $fournisseur->telephone) }}" required
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('telephone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                         <div>
                            <label for="ville" class="block text-sm font-medium text-blue-700 mb-1">Ville *</label>
                            <input type="text" name="ville" id="ville" value="{{ old('ville', $fournisseur->ville) }}" required
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('ville')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="code_postal" class="block text-sm font-medium text-blue-700 mb-1">Code Postal *</label>
                            <input type="text" name="code_postal" id="code_postal" value="{{ old('code_postal', $fournisseur->code_postal) }}" required
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('code_postal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                         <div>
                            <label for="pays" class="block text-sm font-medium text-blue-700 mb-1">Pays *</label>
                            <input type="text" name="pays" id="pays" value="{{ old('pays', $fournisseur->pays) }}" required
                                   class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all">
                            @error('pays')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                         <div class="md:col-span-2">
                            <label for="adresse" class="block text-sm font-medium text-blue-700 mb-1">Adresse Complète *</label>
                            <textarea name="adresse" id="adresse" rows="3" required
                                      class="mt-1 block w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all"
                                      placeholder="Rue, Ville, Code Postal, Pays...">{{ old('adresse', $fournisseur->adresse) }}</textarea>
                            @error('adresse')
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
                                  placeholder="Informations additionnelles...">{{ old('notes', $fournisseur->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                 <!-- Statut -->
                <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center gap-2">
                         <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center text-white">
                             <i class="fas fa-toggle-on"></i>
                         </div>
                        Statut
                    </h3>
                     <div>
                        <label class="inline-flex items-center text-blue-700">
                            <input type="checkbox" name="actif" value="1" {{ old('actif', $fournisseur->actif) ? 'checked' : '' }}
                                   class="rounded border-blue-300 text-blue-600 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                            <span class="ml-2 text-sm font-medium">Fournisseur Actif</span>
                        </label>
                         @error('actif')
                             <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                         @enderror
                    </div>
                </div>

                <!-- Message si utilisé par des produits (adapté) -->
                @if($fournisseur->products_count > 0)
                    <div class="rounded-md bg-blue-50 p-4 border border-blue-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700 font-medium">
                                    Cette unité est utilisée par {{ $fournisseur->products_count }} produit(s). La modification n'affectera pas les produits existants.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('fournisseurs.show', $fournisseur) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold shadow hover:bg-gray-200 hover:-translate-y-0.5 transition-all duration-200">
                        Annuler
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-blue-600 to-teal-500 text-white font-bold shadow-xl hover:shadow-neon-blue hover:-translate-y-1 transition-all duration-200">
                         <i class="fas fa-save"></i>
                        Mettre à jour le Fournisseur
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