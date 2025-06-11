@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête de page -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <div class="bg-gradient-to-br from-purple-600 to-indigo-700 p-3 rounded-2xl shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Nouvelle Promotion</h2>
                    <p class="mt-1 text-gray-600">Créez une offre spéciale pour stimuler les ventes d'un produit</p>
                </div>
            </div>
            <a href="{{ route('promotions.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg border border-purple-200 bg-white text-purple-700 font-medium shadow-sm hover:bg-purple-50 hover:text-purple-900 transition-all duration-200">
                <i class="fas fa-arrow-left"></i>
                Retour à la liste
            </a>
        </div>

        <!-- Carte principale -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
            
            <!-- En-tête du formulaire -->
            <div class="border-b border-gray-200">
                <div class="px-8 py-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-purple-100 rounded-lg">
                            <i class="fas fa-tag text-purple-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Détails de la promotion</h3>
                            <p class="text-gray-500 text-sm">Tous les champs marqués * sont obligatoires</p>
                        </div>
                    </div>
                    <div class="bg-indigo-50 px-3 py-1.5 rounded-full text-xs font-medium text-indigo-700">
                        <span>Formulaire de création</span>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('promotions.store') }}" method="POST" class="p-8 space-y-8">
                @csrf
                
                <!-- Bloc résumé produit (initialement caché) -->
                <div id="resume-produit" class="mb-8 hidden">
                    <div class="relative overflow-hidden bg-white border border-gray-200 rounded-xl shadow-sm">
                        <!-- Fond décoratif avec dégradé -->
                        <div class="absolute left-0 top-0 w-full h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>
                        
                        <!-- En-tête du bloc -->
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 rounded-lg bg-indigo-100">
                                    <i class="fas fa-info-circle text-indigo-600"></i>
                                </div>
                                <h4 class="text-sm font-medium text-gray-700">Informations du produit sélectionné</h4>
                            </div>
                            <span class="text-xs bg-green-100 text-green-800 px-2.5 py-1 rounded-full font-medium flex items-center gap-1">
                                <i class="fas fa-check-circle"></i>
                                Produit actif
                            </span>
                        </div>
                        
                        <!-- Contenu -->
                        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Catégorie -->
                            <div class="flex items-start gap-4">
                                <div class="shrink-0 p-2 bg-indigo-100 rounded-lg">
                                    <i class="fas fa-tag text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs uppercase font-medium text-gray-500 tracking-wider">Catégorie</p>
                                    <p class="text-base font-medium text-gray-900 mt-1" id="categorie-produit">--</p>
                                </div>
                            </div>
                            
                            <!-- Stock actuel -->
                            <div class="flex items-start gap-4">
                                <div class="shrink-0 p-2 bg-green-100 rounded-lg">
                                    <i class="fas fa-cubes text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs uppercase font-medium text-gray-500 tracking-wider">Stock actuel</p>
                                    <p class="text-base font-medium text-gray-900 mt-1" id="stock-produit">--</p>
                                </div>
                            </div>
                            
                            <!-- Prix de vente -->
                            <div class="flex items-start gap-4">
                                <div class="shrink-0 p-2 bg-blue-100 rounded-lg">
                                    <i class="fas fa-money-bill-wave text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs uppercase font-medium text-gray-500 tracking-wider">Prix de vente</p>
                                    <p class="text-base font-medium text-gray-900 mt-1">
                                        <span id="prix-ht-produit">--</span> <span class="text-xs text-gray-500">FCFA HT</span>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <span id="prix-ttc-produit">--</span> <span class="text-xs">FCFA TTC</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Alerte promotion existante (cachée par défaut) -->
                <div id="alerte-promotion" class="mb-8 hidden">
                    <div class="border border-amber-200 bg-amber-50 rounded-lg overflow-hidden">
                        <div class="px-4 py-3 bg-amber-100 border-b border-amber-200 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 rounded-md bg-amber-200">
                                    <i class="fas fa-exclamation-triangle text-amber-700"></i>
                                </div>
                                <h4 class="font-medium text-amber-900">Promotion active en cours</h4>
                            </div>
                            <span class="text-xs bg-amber-200 text-amber-800 px-2.5 py-1 rounded-full font-medium flex items-center gap-1.5">
                                <i class="fas fa-clock"></i>
                                Fin: <span id="promo-date-fin">--</span>
                            </span>
                        </div>
                        
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <!-- Type de promotion -->
                                <div class="flex items-start gap-3 bg-white p-3 rounded-md border border-amber-100 shadow-sm">
                                    <div class="shrink-0 p-1.5 bg-amber-100 rounded-md">
                                        <i class="fas fa-percent text-amber-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase font-medium text-amber-700 tracking-wider">Type</p>
                                        <p class="text-sm font-medium text-gray-900 mt-1" id="promo-type">--</p>
                                    </div>
                                </div>
                                
                                <!-- Valeur de la promotion -->
                                <div class="flex items-start gap-3 bg-white p-3 rounded-md border border-amber-100 shadow-sm">
                                    <div class="shrink-0 p-1.5 bg-amber-100 rounded-md">
                                        <i class="fas fa-tag text-amber-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase font-medium text-amber-700 tracking-wider">Valeur</p>
                                        <p class="text-sm font-medium text-gray-900 mt-1" id="promo-valeur">--</p>
                                    </div>
                                </div>
                                
                                <!-- Prix réduit -->
                                <div class="flex items-start gap-3 bg-white p-3 rounded-md border border-amber-100 shadow-sm">
                                    <div class="shrink-0 p-1.5 bg-amber-100 rounded-md">
                                        <i class="fas fa-money-bill-wave text-amber-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase font-medium text-amber-700 tracking-wider">Prix réduit</p>
                                        <p class="text-sm font-medium text-amber-600 mt-1" id="promo-prix-reduit">--</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="bg-white p-3 rounded-md border border-amber-100 mt-3">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-info-circle text-amber-600 text-sm"></i>
                                    <p class="text-xs uppercase font-medium text-amber-700 tracking-wider">Description</p>
                                </div>
                                <p class="text-sm text-gray-700 pl-6" id="promo-description">--</p>
                            </div>
                            
                            <!-- Avertissement -->
                            <div class="mt-3 p-3 bg-amber-100 border-l-4 border-amber-400 rounded-r-md">
                                <div class="flex gap-2">
                                    <i class="fas fa-exclamation-circle text-amber-700 mt-0.5"></i>
                                    <div class="text-sm text-amber-800">
                                        Si vous créez une nouvelle promotion, elle prendra effet uniquement après la fin de la promotion actuelle, ou remplacera celle-ci si les dates se chevauchent.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Produit, Type et Valeur -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <!-- Produit -->
                    <div>
                        <label for="produit_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Produit <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="produit_id" name="produit_id" class="pl-3 pr-10 py-2.5 bg-white border border-purple-200 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full appearance-none @error('produit_id') border-red-300 @enderror transition duration-200" required>
                                <option value="">-- Sélectionnez un produit --</option>
                                @foreach($produits as $produit)
                                    <option value="{{ $produit->id }}" {{ old('produit_id') == $produit->id ? 'selected' : '' }}>{{ $produit->nom }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-purple-500">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                        @error('produit_id')
                        <p class="text-red-500 text-xs mt-1 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Type de promotion -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="type" class="block text-sm font-semibold text-gray-700">
                                Type de réduction <span class="text-red-500">*</span>
                            </label>
                            <div class="group relative flex items-center">
                                <i class="fas fa-question-circle text-gray-400 hover:text-gray-600 cursor-help"></i>
                                <div class="absolute bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-48 z-10">
                                    Pourcentage : réduction en % sur le prix initial<br>
                                    Montant fixe : réduction en FCFA
                                </div>
                            </div>
                        </div>
                        <div class="relative">
                            <select id="type" name="type" class="block w-full py-3 pr-10 pl-4 bg-white border border-purple-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200">
                                <option value="pourcentage" {{ old('type') == 'pourcentage' ? 'selected' : '' }}>Pourcentage (%)</option>
                                <option value="montant" {{ old('type') == 'montant' ? 'selected' : '' }}>Montant fixe (FCFA)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-chevron-down text-purple-500"></i>
                            </div>
                        </div>
                        
                        @error('type')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-500">Le type de réduction détermine comment le prix sera diminué</p>
                    </div>
                </div>

                <!-- Section Valeur et Période -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-2">
                    <div class="relative">
                        <div class="flex justify-between items-center mb-2">
                            <label for="valeur" class="block text-base font-semibold text-gray-900">
                                <i class="fas fa-tags text-purple-500 mr-1.5"></i>
                                Valeur de la réduction *
                            </label>
                            <span class="text-xs text-purple-600 font-medium bg-purple-100 px-2 py-0.5 rounded-full">
                                Obligatoire
                            </span>
                        </div>
                        
                        <div class="relative rounded-lg shadow-sm">
                            <input type="number" name="valeur" id="valeur" step="0.01" min="0" required
                                   value="{{ old('valeur') }}"
                                   class="block w-full pl-4 py-3 pr-12 bg-white border border-purple-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                <span class="text-purple-600 font-medium" id="valeur-suffix">%</span>
                            </div>
                        </div>
                        
                        @error('valeur')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-500">Pour un pourcentage, utilisez des valeurs entre 1 et 100</p>
                    </div>

                    <div class="relative">
                        <div class="flex justify-between items-center mb-2">
                            <label for="date_debut" class="block text-base font-semibold text-gray-900">
                                <i class="fas fa-calendar-alt text-purple-500 mr-1.5"></i>
                                Date de début *
                            </label>
                            <span class="text-xs text-purple-600 font-medium bg-purple-100 px-2 py-0.5 rounded-full">
                                Obligatoire
                            </span>
                        </div>
                        
                        <div class="relative rounded-lg shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar text-gray-400"></i>
                            </div>
                            <input type="datetime-local" name="date_debut" id="date_debut" required
                                   value="{{ old('date_debut') }}"
                                   class="block w-full pl-10 py-3 bg-white border border-purple-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200">
                        </div>
                        
                        @error('date_debut')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="relative">
                        <div class="flex justify-between items-center mb-2">
                            <label for="date_fin" class="block text-base font-semibold text-gray-900">
                                <i class="fas fa-calendar-check text-purple-500 mr-1.5"></i>
                                Date de fin *
                            </label>
                            <span class="text-xs text-purple-600 font-medium bg-purple-100 px-2 py-0.5 rounded-full">
                                Obligatoire
                            </span>
                        </div>
                        
                        <div class="relative rounded-lg shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-times text-gray-400"></i>
                            </div>
                            <input type="datetime-local" name="date_fin" id="date_fin" required
                                   value="{{ old('date_fin') }}"
                                   class="block w-full pl-10 py-3 bg-white border border-purple-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200">
                        </div>
                        
                        @error('date_fin')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-500">Doit être après la date de début</p>
                    </div>
                </div>

                <!-- Section Description -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-2">
                        <label for="description" class="block text-sm font-semibold text-gray-700">
                            Description
                        </label>
                        <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md text-xs font-medium">
                            Optionnel
                        </span>
                    </div>
                    <div class="relative">
                        <textarea name="description" id="description" rows="4"
                                class="pl-3 pr-3 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full @error('description') border-red-300 @enderror"
                                placeholder="Décrivez les détails de cette promotion pour les vendeurs et autres utilisateurs...">{{ old('description') }}</textarea>
                    </div>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-gray-500">500 caractères maximum</p>
                </div>
                
                <!-- Activation checkbox -->
                <div class="mb-8">
                    <div class="flex items-center p-4 bg-indigo-50 border border-indigo-100 rounded-lg">
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="actif" id="actif" value="1" {{ old('actif', true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-indigo-200 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            <label for="actif" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer">
                                Activer cette promotion immédiatement
                            </label>
                        </div>
                        <div class="ml-auto text-indigo-600">
                            <i class="fas fa-info-circle" title="Décochez si vous souhaitez préparer cette promotion sans l'activer tout de suite"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    <div class="flex items-start gap-2">
                        <div class="shrink-0 mt-0.5">
                            <i class="fas fa-info-circle text-gray-400"></i>
                        </div>
                        <p>Cette promotion sera applicable aux produits pendant la période spécifiée.</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('promotions.index') }}" 
                       class="px-5 py-2.5 border border-gray-300 text-gray-700 bg-white rounded-lg hover:bg-gray-50 shadow-sm transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-times"></i>
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 shadow-md hover:shadow-indigo-500/30 transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-check"></i>
                        Créer la promotion
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sécurisation : ne rien faire si les éléments n'existent pas
    const typeSelect = document.getElementById('type');
    const valeurSuffix = document.getElementById('valeur-suffix');
    const produitSelect = document.getElementById('produit_id');
    const resumeProduit = document.getElementById('resume-produit');
    const alertePromotion = document.getElementById('alerte-promotion');
    if (!typeSelect || !valeurSuffix || !produitSelect) return;

    // Appliquer la classe de transition aux éléments qu'on souhaite animer
    if (resumeProduit) resumeProduit.classList.add('transition-all', 'duration-300', 'ease-in-out');
    if (alertePromotion) alertePromotion.classList.add('transition-all', 'duration-300', 'ease-in-out');

    // Mise à jour du suffixe de la valeur selon le type
    typeSelect.addEventListener('change', function() {
        const type = this.value;
        const suffix = type === 'pourcentage' ? '%' : (type === 'montant' ? 'FCFA' : '');
        document.getElementById('valeur-suffix').textContent = suffix;

        // Ajuster le placeholder et les indications selon le type
        const valeurInput = document.getElementById('valeur');
        if (valeurInput) {
            valeurInput.placeholder = type === 'pourcentage' ? 'ex: 15' : 'ex: 500';
        }
    });

    // Validation des dates
    document.getElementById('date_debut').addEventListener('change', function() {
        const dateFin = document.getElementById('date_fin');
        if (dateFin.value && this.value > dateFin.value) {
            dateFin.value = this.value;
        }
        dateFin.min = this.value;
    });

    document.getElementById('date_fin').addEventListener('change', function() {
        const dateDebut = document.getElementById('date_debut');
        if (this.value < dateDebut.value) {
            this.value = dateDebut.value;
        }
    });

    // Initial trigger for the suffix
    document.getElementById('type').dispatchEvent(new Event('change'));
    
    // Fonction pour afficher avec effet fade-in
    function showWithFadeIn(element) {
        if (!element) return;
        // D'abord appliquer opacity-0 et display block
        element.classList.remove('hidden');
        element.style.opacity = '0';
        
        // Forcer un reflow pour que la transition fonctionne
        void element.offsetWidth;
        
        // Puis appliquer opacity-100
        element.style.opacity = '1';
    }
    
    // Fonction pour cacher avec effet fade-out
    function hideWithFadeOut(element, callback) {
        if (!element) return;
        element.style.opacity = '0';
        
        // Attendre la fin de la transition avant de cacher
        setTimeout(() => {
            element.classList.add('hidden');
            if (callback) callback();
        }, 300); // Durée correspond à duration-300
    }
    
    // Chargement dynamique des détails du produit via AJAX
    document.getElementById('produit_id').addEventListener('change', function() {
        const productId = this.value;
        
        // Masquer les blocs si aucun produit n'est sélectionné
        if (!productId) {
            hideWithFadeOut(resumeProduit);
            hideWithFadeOut(alertePromotion);
            return;
        }
        
        // Afficher un indicateur de chargement
        showWithFadeIn(resumeProduit);
        resumeProduit.classList.add('animate-pulse');
        
        document.getElementById('categorie-produit').textContent = 'Chargement...';
        document.getElementById('stock-produit').textContent = 'Chargement...';
        document.getElementById('prix-ht-produit').textContent = 'Chargement...';
        document.getElementById('prix-ttc-produit').textContent = 'Chargement...';
        
        // Effectuer la requête AJAX
        fetch(`/ajax/produit/${productId}/details-promotion`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .then(data => {
                // Retirer l'animation de chargement
                resumeProduit.classList.remove('animate-pulse');
                
                if (data.success) {
                    const produit = data.produit;
                    
                    // Mettre à jour le résumé du produit
                    document.getElementById('categorie-produit').textContent = produit.categorie || 'Non catégorisé';
                    document.getElementById('stock-produit').textContent = 
                        `${produit.stock.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${produit.unite || ''}`;
                    document.getElementById('prix-ht-produit').textContent = 
                        `${produit.prix_vente.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    document.getElementById('prix-ttc-produit').textContent = 
                        `${produit.prix_vente_ttc.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    
                    // Mise à jour du nom du produit
                    const nomElement = document.getElementById('nom-produit');
                    if (nomElement) {
                        nomElement.textContent = produit.nom || 'Produit sélectionné';
                    }
                    
                    // Afficher les informations sur la promotion existante si applicable
                    if (produit.en_promotion && produit.promotion_active) {
                        const promo = produit.promotion_active;
                        
                        // Préparer toutes les données avant d'afficher le bloc
                        // Formater la date de fin pour un affichage plus convivial
                        const dateFin = new Date(promo.date_fin);
                        const options = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' };
                        const dateFinFormatee = dateFin.toLocaleDateString('fr-FR', options);
                        
                        document.getElementById('promo-type').textContent = 
                            promo.type === 'pourcentage' ? 'Pourcentage' : 'Montant fixe';
                        
                        document.getElementById('promo-valeur').textContent = 
                            promo.type === 'pourcentage' 
                                ? `${promo.valeur.toLocaleString('fr-FR')}%` 
                                : `${promo.valeur.toLocaleString('fr-FR')} FCFA`;
                        
                        document.getElementById('promo-prix-reduit').textContent = 
                            `${promo.prix_promo.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})} FCFA`;
                        
                        document.getElementById('promo-date-fin').textContent = dateFinFormatee;
                        document.getElementById('promo-description').textContent = 
                            promo.description || 'Pas de description disponible';
                            
                        // Afficher le bloc avec animation
                        showWithFadeIn(alertePromotion);
                    } else {
                        // Masquer le bloc avec animation si nécessaire
                        if (!alertePromotion.classList.contains('hidden')) {
                            hideWithFadeOut(alertePromotion);
                        }
                    }
                    
                } else {
                    console.error('Erreur:', data.message);
                    hideWithFadeOut(resumeProduit);
                    hideWithFadeOut(alertePromotion);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                
                // Retirer l'animation de chargement
                resumeProduit.classList.remove('animate-pulse');
                
                // Créer une notification d'erreur plus élégante
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-md z-50 transform transition-all duration-500 translate-x-full';
                notification.innerHTML = `
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">Une erreur est survenue lors du chargement des détails du produit.</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button type="button" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(notification);
                
                // Animation d'entrée
                setTimeout(() => {
                    notification.classList.remove('translate-x-full');
                    notification.classList.add('translate-x-0');
                }, 10);
                
                // Fermeture automatique après 5 secondes
                setTimeout(() => {
                    notification.classList.remove('translate-x-0');
                    notification.classList.add('translate-x-full');
                    setTimeout(() => notification.remove(), 500);
                }, 5000);
                
                // Clic pour fermer
                notification.querySelector('button').addEventListener('click', () => {
                    notification.classList.remove('translate-x-0');
                    notification.classList.add('translate-x-full');
                    setTimeout(() => notification.remove(), 500);
                });
                
                // Cacher les blocs avec animation
                hideWithFadeOut(resumeProduit);
                hideWithFadeOut(alertePromotion);
            });
    });
    
    // Déclencher le chargement si un produit est pré-sélectionné
    if (document.getElementById('produit_id').value) {
        document.getElementById('produit_id').dispatchEvent(new Event('change'));
    }
    
    // Animation pour les transitions
    document.getElementById('produit_id').addEventListener('change', function() {
        const resumeProduit = document.getElementById('resume-produit');
        if (this.value) {
            resumeProduit.style.opacity = '0';
            setTimeout(() => {
                resumeProduit.style.opacity = '1';
            }, 300);
        }
    });
});
</script>
@endpush
@endsection