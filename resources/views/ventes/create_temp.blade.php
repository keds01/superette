@extends('layouts.app')

@section('title', 'Nouvelle Vente')

@section('content')
<div class="py-12">
    <div class="max-w-[1200px] mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-tr from-indigo-600 via-purple-500 to-pink-400 bg-clip-text text-transparent">
                    Nouvelle Vente
                </h1>
                <p class="mt-2 text-gray-500">Enregistrez une nouvelle transaction avec tous les détails nécessaires</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('ventes.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 transition-all duration-200">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la liste
                </a>
            </div>
        </div>

        <!-- Message d'erreur -->
        @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <!-- Formulaire de vente -->
        <form action="{{ route('ventes.store') }}" method="POST" id="formVente" class="space-y-6">
            @csrf

            <!-- Informations générales -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-info-circle text-indigo-500"></i>
                    Informations Générales
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Client -->
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                            Client <span class="text-red-500">*</span>
                        </label>
                        <select name="client_id" id="client_id" required
                                class="block w-full rounded-lg border border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all">
                            <option value="">Sélectionner un client...</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->nom }} {{ $client->prenom }} - {{ $client->telephone ?? 'Pas de téléphone' }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type de vente -->
                    <div>
                        <label for="type_vente" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                            Type de vente <span class="text-red-500">*</span>
                        </label>
                        <select name="type_vente" id="type_vente" required
                                class="block w-full rounded-lg border border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all">
                            <option value="">Sélectionner...</option>
                            <option value="sur_place" {{ old('type_vente') == 'sur_place' ? 'selected' : '' }}>Sur place</option>
                            <option value="a_emporter" {{ old('type_vente') == 'a_emporter' ? 'selected' : '' }}>À emporter</option>
                            <option value="livraison" {{ old('type_vente') == 'livraison' ? 'selected' : '' }}>Livraison</option>
                        </select>
                        @error('type_vente')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Notes additionnelles
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="block w-full rounded-lg border border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all"
                                  placeholder="Notes ou commentaires sur la vente...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Produits -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-shopping-cart text-indigo-500"></i>
                        Produits de la Vente
                    </h3>
                    <button type="button" id="ajouterLigne" 
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-500 text-white font-medium hover:bg-indigo-600 transition-all duration-200">
                        <i class="fas fa-plus"></i>
                        Ajouter un produit
                    </button>
                </div>

                <div id="produitsContainer" class="space-y-4">
                    <!-- Les lignes de produits seront ajoutées ici dynamiquement -->
                </div>

                <!-- Template pour une ligne de produit (invisible) -->
                <div id="ligneTemplate" class="hidden">
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                            <!-- Produit -->
                            <div class="md:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Produit</label>
                                <select name="produits[INDEX][produit_id]" class="produit-select block w-full rounded-lg border border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all" required>
                                    <option value="">Sélectionner un produit...</option>
                                    @foreach($produits as $produit)
                                        <option value="{{ $produit->id }}" 
                                            data-prix="{{ $produit->prix_vente_ttc }}"
                                            data-stock="{{ $produit->stock }}">
                                            {{ $produit->nom }} (Stock: {{ $produit->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Prix unitaire -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Prix unitaire</label>
                                <div class="relative">
                                    <input type="number" name="produits[INDEX][prix_unitaire]" 
                                           class="prix-unitaire block w-full rounded-lg border border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all pr-12" 
                                           step="0.01" readonly>
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500">FCFA</span>
                                </div>
                            </div>

                            <!-- Quantité -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                                <div class="flex items-center gap-2">
                                    <button type="button" class="decrementer p-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" name="produits[INDEX][quantite]" 
                                           class="quantite block w-full rounded-lg border border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all text-center" 
                                           min="1" value="1" required>
                                    <button type="button" class="incrementer p-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Remise -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Remise</label>
                                <div class="relative">
                                    <input type="number" name="produits[INDEX][remise]" 
                                           class="remise block w-full rounded-lg border border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all pr-12" 
                                           step="0.01" min="0" value="0">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500">FCFA</span>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                                <div class="relative">
                                    <input type="text" class="total-ligne block w-full rounded-lg border border-gray-300 bg-white shadow-sm sm:text-sm transition-all pr-12" readonly>
                                    <input type="hidden" name="produits[INDEX][montant]" class="montant-ligne">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500">FCFA</span>
                                </div>
                            </div>

                            <!-- Bouton supprimer -->
                            <div class="md:col-span-1">
                                <button type="button" class="supprimer-ligne p-2 rounded-lg border border-red-300 text-red-600 hover:bg-red-50 transition-colors w-full" title="Supprimer ce produit">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Récapitulatif financier -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-calculator text-indigo-500"></i>
                    Récapitulatif Financier
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Sous-total et TVA -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Sous-total HT</span>
                            <span class="font-semibold text-gray-800" id="sousTotalHT">0 FCFA</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">TVA (18%)</span>
                            <span class="font-semibold text-gray-800" id="montantTVA">0 FCFA</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Total des remises</span>
                            <span class="font-semibold text-red-600" id="totalRemises">0 FCFA</span>
                        </div>
                    </div>

                    <!-- Total et statistiques -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 bg-indigo-500 rounded-lg text-white">
                            <span class="font-medium">Total TTC</span>
                            <span class="text-2xl font-bold" id="montantTotal">0 FCFA</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-green-50 rounded-lg">
                                <div class="text-sm text-green-600">Nombre d'articles</div>
                                <div class="text-xl font-semibold text-green-700" id="nombreArticles">0</div>
                            </div>
                            <div class="p-4 bg-blue-50 rounded-lg">
                                <div class="text-sm text-blue-600">Moyenne par article</div>
                                <div class="text-xl font-semibold text-blue-700" id="moyenneArticle">0 FCFA</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('ventes.index') }}" 
                   class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 transition-all duration-200">
                    Annuler
                </a>
                <button type="submit" id="submitVente"
                        class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition-all duration-200">
                    Enregistrer la vente
                </button>
            </div>

            <!-- Champ caché pour le montant total -->
            <input type="hidden" name="montant_total" id="montant_total" value="0">
        </form>
    </div>
</div>

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #c7d2fe;
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a5b4fc;
    }
</style>
@endpush

@push('scripts')
<script>
    // Fonction pour formater les nombres
    function formatNumber(number) {
        return new Intl.NumberFormat('fr-FR').format(number);
    }

    // Fonction pour calculer le total d'une ligne
    function calculerTotalLigne(ligne) {
        const prix = parseFloat(ligne.find('.prix-unitaire').val()) || 0;
        const quantite = parseInt(ligne.find('.quantite').val()) || 0;
        const remise = parseFloat(ligne.find('.remise').val()) || 0;
        
        const total = (prix * quantite) - remise;
        ligne.find('.total-ligne').val(formatNumber(total));
        ligne.find('.montant-ligne').val(total);
        
        calculerTotalGeneral();
    }

    // Fonction pour calculer le total général
    function calculerTotalGeneral() {
        let sousTotalHT = 0;
        let totalRemises = 0;
        let nombreArticles = 0;
        
        // Calculer les totaux
        $('.produit-select').each(function() {
            const ligne = $(this).closest('.bg-gray-50');
            if ($(this).val()) {
                const prix = parseFloat(ligne.find('.prix-unitaire').val()) || 0;
                const quantite = parseInt(ligne.find('.quantite').val()) || 0;
                const remise = parseFloat(ligne.find('.remise').val()) || 0;
                
                sousTotalHT += prix * quantite;
                totalRemises += remise;
                nombreArticles += quantite;
            }
        });
        
        // Calculer la TVA (18%)
        const tva = sousTotalHT * 0.18;
        const totalTTC = sousTotalHT + tva - totalRemises;
        const moyenneArticle = nombreArticles > 0 ? totalTTC / nombreArticles : 0;
        
        // Mettre à jour l'affichage
        $('#sousTotalHT').text(formatNumber(sousTotalHT) + ' FCFA');
        $('#montantTVA').text(formatNumber(tva) + ' FCFA');
        $('#totalRemises').text(formatNumber(totalRemises) + ' FCFA');
        $('#montantTotal').text(formatNumber(totalTTC) + ' FCFA');
        $('#nombreArticles').text(nombreArticles);
        $('#moyenneArticle').text(formatNumber(moyenneArticle) + ' FCFA');
        
        // Mettre à jour le champ caché
        $('#montant_total').val(totalTTC);
    }

    // Gestion des événements sur les lignes de produits
    $(document).on('change', '.produit-select', function() {
        const ligne = $(this).closest('.bg-gray-50');
        const option = $(this).find('option:selected');
        const prix = option.data('prix');
        const stock = option.data('stock');
        
        ligne.find('.prix-unitaire').val(prix);
        ligne.find('.quantite').attr('max', stock);
        calculerTotalLigne(ligne);
    });

    $(document).on('change', '.quantite, .remise', function() {
        const ligne = $(this).closest('.bg-gray-50');
        calculerTotalLigne(ligne);
    });

    // Gestion des boutons + et -
    $(document).on('click', '.incrementer', function() {
        const ligne = $(this).closest('.bg-gray-50');
        const quantiteInput = ligne.find('.quantite');
        const quantite = parseInt(quantiteInput.val()) || 0;
        const max = parseInt(quantiteInput.attr('max')) || 999;
        
        if (quantite < max) {
            quantiteInput.val(quantite + 1).trigger('change');
        } else {
            alert('Stock maximum atteint: ' + max + ' unités');
        }
    });

    $(document).on('click', '.decrementer', function() {
        const ligne = $(this).closest('.bg-gray-50');
        const quantiteInput = ligne.find('.quantite');
        const quantite = parseInt(quantiteInput.val()) || 0;
        
        if (quantite > 1) {
            quantiteInput.val(quantite - 1).trigger('change');
        }
    });

    // Gestion de l'ajout/suppression de lignes
    let ligneIndex = 0;
    
    $('#ajouterLigne').click(function() {
        const template = $('#ligneTemplate').html().replace(/INDEX/g, ligneIndex++);
        $('#produitsContainer').append(template);
    });

    $(document).on('click', '.supprimer-ligne', function() {
        $(this).closest('.bg-gray-50').remove();
        calculerTotalGeneral();
    });

    // Validation du formulaire
    $('#formVente').on('submit', function(e) {
        if (!$('#client_id').val()) {
            e.preventDefault();
            alert('Veuillez sélectionner un client.');
            $('#client_id').focus();
            return false;
        }
        
        if (!$('#type_vente').val()) {
            e.preventDefault();
            alert('Veuillez sélectionner un type de vente.');
            $('#type_vente').focus();
            return false;
        }
        
        let produitsValides = 0;
        $('.produit-select').each(function() {
            if ($(this).val()) produitsValides++;
        });
        
        if (produitsValides === 0) {
            e.preventDefault();
            alert('Veuillez ajouter au moins un produit à la vente.');
            return false;
        }
        
        $('#submitVente').html('<i class="fas fa-spinner fa-spin mr-2"></i> Traitement en cours...');
        $('#submitVente').prop('disabled', true);
        
        return true;
    });

    // Ajouter une première ligne au chargement
    $(document).ready(function() {
        $('#ajouterLigne').click();
    });
</script>
@endpush
@endsection
