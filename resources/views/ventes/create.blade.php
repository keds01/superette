@extends('layouts.app')

@section('title', 'Création d\'une vente')

@section('content')
<div class="w-full max-w-4xl mx-auto py-8" x-data="venteSystem">
    <!-- Message d'erreur -->
    <div x-show="erreur" x-transition class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-md">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span x-text="erreur"></span>
            <button @click="erreur = ''" class="ml-auto text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    
    <!-- Message de succès -->
    <div x-show="message" x-transition class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-md">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span x-text="message"></span>
            <button @click="message = ''" class="ml-auto text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <!-- Étape 1 : Infos vente -->
    <div class="bg-white/90 shadow-xl rounded-2xl p-6 mb-6 border border-blue-100">
        <h2 class="text-2xl font-bold mb-4 text-indigo-700 flex items-center gap-2">
            <i class="fas fa-cash-register"></i> Nouvelle Vente
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="type_vente" class="block text-sm font-medium text-gray-700 mb-1">Type de vente <span class="text-red-500">*</span></label>
                <select name="type_vente" id="type_vente" x-model="type_vente" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    <option value="">Sélectionner le type...</option>
    <option value="sur_place">Sur place</option>
    <option value="a_emporter">À emporter</option>
    <option value="livraison">Livraison</option>
</select>
            </div>
            <div>
                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client <span class="text-red-500">*</span></label>
                <select name="client_id" id="client_id" x-model="client_id" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Sélectionner un client...</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->nom }} {{ $client->prenom ? $client->prenom : '' }} - {{ $client->telephone }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Étape 2 : Sélection des produits -->
    <div class="bg-white/90 shadow-xl rounded-2xl p-6 mb-6 border border-blue-100">
        <h3 class="text-lg font-semibold text-blue-700 mb-4 flex items-center gap-2">
            <i class="fas fa-search"></i> Ajouter un produit au panier
        </h3>
        
        <!-- Mode de recherche: texte ou code-barre -->
        <div class="flex mb-3">
            <button @click="searchMode = 'text'" :class="{'bg-blue-500 text-white': searchMode === 'text', 'bg-gray-200 text-gray-700': searchMode !== 'text'}" class="px-4 py-2 rounded-l-lg transition">
                <i class="fas fa-keyboard mr-1"></i> Texte
            </button>
            <button @click="searchMode = 'barcode'; setupBarcodeScanner()" :class="{'bg-blue-500 text-white': searchMode === 'barcode', 'bg-gray-200 text-gray-700': searchMode !== 'barcode'}" class="px-4 py-2 rounded-r-lg transition">
                <i class="fas fa-barcode mr-1"></i> Code-barre
            </button>
        </div>
        
        <!-- Recherche par texte -->
        <div class="flex flex-col md:flex-row gap-4" x-show="searchMode === 'text'">
            <input type="text" x-model="search" placeholder="Rechercher un produit..." class="w-full md:w-2/3 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" @input="rechercherProduit">
            <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow transition" @click="ajouterProduitSelectionne">
                <i class="fas fa-plus mr-1"></i> Ajouter
            </button>
        </div>
        
        <!-- Scan code-barre par lecteur USB (pistolet) UX amélioré -->
        <div class="flex flex-col items-center justify-center min-h-[340px] py-4" x-show="searchMode === 'barcode'">
            <div class="bg-white border-4 border-indigo-400 rounded-2xl shadow-2xl p-8 w-full max-w-xl text-center relative">
                <div class="flex flex-col items-center gap-2 mb-4">
                    <span class="animate-bounce text-indigo-600 text-4xl">
                        <i class="fas fa-barcode"></i>
                    </span>
                    <span class="text-2xl font-bold text-indigo-700 tracking-wide">Scannez un produit</span>
                </div>
                <input type="text" x-ref="usbScan" x-model="barcode" autofocus autocomplete="off"
                    class="text-center text-xl tracking-wide font-mono font-semibold bg-gray-50 border border-indigo-400 rounded-lg px-4 py-3 outline-none shadow focus:border-green-500 transition-all mx-auto w-full max-w-md focus:ring-2 focus:ring-indigo-200"
                    placeholder="Scannez le code-barre"
                    @input="if(barcode.length > 30) barcode = ''" @focus="$event.target.select()" aria-label="Champ de scan code-barre">
                <div class="text-gray-400 text-xs mt-2 mb-4">Le champ reste sélectionné, scannez à la chaîne sans cliquer !</div>
                <template x-if="scanFeedback">
                    <div class="absolute left-1/2 -translate-x-1/2 top-2 animate-fadeInOut z-20">
                        <template x-if="scanFeedback === 'ok'">
                            <span class="flex items-center gap-2 px-4 py-2 rounded-xl bg-green-100 border border-green-400 text-green-800 text-xl font-bold shadow-lg">
                                <i class="fas fa-check-circle animate-scaleIn"></i> Produit ajouté !
                            </span>
                        </template>
                        <template x-if="scanFeedback === 'nok'">
                            <span class="flex items-center gap-2 px-4 py-2 rounded-xl bg-red-100 border border-red-400 text-red-800 text-xl font-bold shadow-lg">
                                <i class="fas fa-times-circle animate-scaleIn"></i> Produit inconnu
                            </span>
                        </template>
                    </div>
                </template>
            </div>
        </div>
        <div class="mt-2" x-show="suggestions.length > 0">
            <ul class="bg-white border rounded shadow max-h-48 overflow-y-auto">
                <template x-for="produit in suggestions" :key="produit.id">
                    <li @click="selectionnerProduit(produit)" class="px-4 py-2 hover:bg-blue-50 cursor-pointer flex justify-between items-center border-b last:border-0">
                        <div class="flex flex-col">
                            <span class="font-medium text-blue-800" x-text="produit.nom"></span>
                            <span class="text-xs text-gray-500" x-text="produit.code_barre ? 'Code: ' + produit.code_barre : ''"></span>
                        </div>
                        <span class="font-bold text-indigo-700" x-text="formatPrix(produit.prix_vente || produit.prix_vente_ttc || produit.prix || produit.pu || 0) + ' FCFA'"></span>
                    </li>
                </template>
            </ul>
        </div>
    </div>

    <!-- Étape 3 : Panier -->
    <div class="bg-white/90 shadow-xl rounded-2xl p-6 mb-6 border border-emerald-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-emerald-700 flex items-center gap-2">
            <i class="fas fa-shopping-cart"></i> Panier
        </h3>
            <button @click="togglePriceDetails()" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">
                <i class="fas" :class="showPriceDetails ? 'fa-eye-slash' : 'fa-eye'"></i>
                <span x-text="showPriceDetails ? 'Masquer les détails' : 'Afficher les détails'"></span>
            </button>
        </div>
        
        <!-- Message d'information sur les conditionnements automatiques -->
        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 rounded shadow-md">
            <div class="flex items-start">
                <i class="fas fa-info-circle mt-0.5 mr-2"></i>
                <div>
                    <p class="font-semibold">Application automatique des conditionnements et promotions</p>
                    <p class="text-sm">Le système applique automatiquement les tarifs par conditionnement quand vous atteignez la quantité requise. Les promotions sont également appliquées automatiquement et sont cumulables avec les prix par conditionnement.</p>
                    <p class="text-sm mt-1">Vous pouvez désactiver l'application automatique des conditionnements en décochant la case "Auto" à côté de la quantité.</p>
                </div>
            </div>
        </div>
        
        <template x-if="panier.length === 0">
            <div class="text-gray-400 italic">Aucun produit dans le panier.</div>
        </template>
        <template x-if="panier.length > 0">
            <table class="w-full text-sm border rounded-lg overflow-hidden">
                <thead class="bg-gradient-to-tr from-blue-50 to-teal-50">
                    <tr>
                        <th class="p-2">Produit</th>
                        <th class="p-2 text-center">Qté</th>
                        <th class="p-2 text-center">Prix unitaire</th>
                        <th class="p-2 text-right">Total ligne</th>
                        <th class="p-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, idx) in panier" :key="item.id">
                        <tr class="hover:bg-gray-50 border-t" :class="{'bg-yellow-50': item.prix_promo < item.prix_vente}">
                            <td class="p-2">
                                <div class="flex flex-col">
                                    <span x-text="item.nom.split(' - ')[0]" class="font-medium"></span>
                                    <template x-if="item.conditionnement_selectionne">
                                        <span class="text-xs text-blue-600 font-semibold">
                                            <i class="fas fa-box mr-1"></i>
                                            <span x-text="item.nom.includes(' - ') ? item.nom.split(' - ')[1] : ''"></span>
                                        </span>
                                    </template>
                                    <span x-show="item.prix_promo < item.prix_vente" class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs rounded-full inline-block w-fit mt-1">
                                        <i class="fas fa-tag mr-1"></i>Promo
                                    </span>
                                </div>
                            </td>
                            <td class="p-2 text-center">
                                <div class="flex items-center justify-center">
                                    <button type="button" @click="diminuerQuantite(idx)" 
                                        class="bg-gray-200 hover:bg-gray-300 rounded-l-lg px-2 py-1 text-gray-700">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" min="1" x-model.number="item.quantite" 
                                        class="w-14 text-center border-gray-300" 
                                        @change="majQuantite(idx)">
                                    <button type="button" @click="augmenterQuantite(idx)" 
                                        class="bg-gray-200 hover:bg-gray-300 rounded-r-lg px-2 py-1 text-gray-700">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <!-- Indicateur de conditionnement automatique -->
                                <div class="mt-1">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="item.conditionnement_auto" class="form-checkbox h-4 w-4 text-blue-600" @change="appliquerConditionnementAutomatique(idx)">
                                        <span class="ml-1 text-xs text-gray-600">Auto</span>
                                    </label>
                                </div>
                            </td>
                            <td class="p-2">
                                <!-- Mode détaillé -->
                                <template x-if="showPriceDetails">
                                    <div class="flex flex-col">
                                        <!-- Prix unitaire avec informations détaillées -->
                                        <div class="flex flex-col items-center bg-gray-50 p-2 rounded-lg border border-gray-100">
                                            <!-- Prix de base -->
                                            <div class="w-full flex justify-between items-center mb-1">
                                                <span class="text-xs text-gray-500">Prix base:</span>
                                                <span class="text-xs font-medium" x-text="formatPrix(item.prix_unitaire_base)"></span>
                                            </div>
                                            
                                            <!-- Prix conditionné si applicable -->
                                            <template x-if="item.conditionnement_selectionne">
                                                <div class="w-full flex justify-between items-center mb-1">
                                                    <span class="text-xs text-blue-600">Prix pack:</span>
                                                    <span class="text-xs font-medium text-blue-600" x-text="formatPrix(item.prix_vente)"></span>
                                                </div>
                                            </template>
                                            
                                            <!-- Prix promotionnel si applicable -->
                                            <template x-if="item.prix_promo < item.prix_vente">
                                                <div class="w-full flex justify-between items-center mb-1">
                                                    <span class="text-xs text-green-600">Prix promo:</span>
                                                    <span class="text-xs font-medium text-green-600" x-text="formatPrix(item.prix_promo)"></span>
                                                </div>
                                            </template>
                                            
                                            <!-- Prix final -->
                                            <div class="w-full flex justify-between items-center pt-1 border-t border-gray-200">
                                                <span class="font-medium">Prix final:</span>
                                                <span class="font-bold" x-text="formatPrix(item.prix_promo < item.prix_vente ? item.prix_promo : item.prix_vente)"></span>
                                            </div>
                                        </div>
                                        
                                        <!-- Remise -->
                                        <div class="mt-2 hidden">
                                            <label class="block text-xs text-center mb-1">Remise</label>
                                            <input type="number" min="0" x-model.number="item.remise" class="w-full rounded border-gray-300 text-center" @change="majRemise(idx)">
                                        </div>
                                    </div>
                                </template>
                                
                                <!-- Mode simplifié -->
                                <template x-if="!showPriceDetails">
                                    <div class="flex flex-col items-center">
                                        <div class="flex flex-col items-center mb-2">
                                            <span class="font-bold text-lg" x-text="formatPrix(item.prix_promo < item.prix_vente ? item.prix_promo : item.prix_vente)"></span>
                                            <template x-if="item.prix_promo < item.prix_vente">
                                                <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Promo</span>
                                            </template>
                                            <template x-if="item.conditionnement_selectionne">
                                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full mt-1">Pack</span>
                                            </template>
                                        </div>
                                        
                                        <!-- Remise simplifiée -->
                                        <div class="hidden">
                                            <input type="number" min="0" x-model.number="item.remise" class="w-full rounded border-gray-300 text-center text-sm" @change="majRemise(idx)" placeholder="Remise">
                                        </div>
                                    </div>
                                </template>
                            </td>
                            <td class="p-2 text-right">
                                <!-- Mode détaillé -->
                                <template x-if="showPriceDetails">
                                    <div class="flex flex-col items-end">
                                        <!-- Prix total avant remise -->
                                        <div class="mb-1">
                                            <span class="text-sm" x-text="formatPrix((item.prix_promo < item.prix_vente ? item.prix_promo : item.prix_vente) * item.quantite)"></span>
                                        </div>
                                        
                                        <!-- Remise si applicable -->
                                        <template x-if="item.remise > 0">
                                            <div class="text-red-600 text-sm">
                                                - <span x-text="formatPrix(item.remise)"></span>
                                            </div>
                                        </template>
                                        
                                        <!-- Total final -->
                                        <div class="font-bold text-lg mt-1 pt-1 border-t border-gray-200">
                                            <span x-text="formatPrix((item.prix_promo < item.prix_vente ? item.prix_promo : item.prix_vente) * item.quantite)"></span> FCFA
                                        </div>
                                    </div>
                                </template>
                                
                                <!-- Mode simplifié -->
                                <template x-if="!showPriceDetails">
                                    <div class="flex flex-col items-end">
                                        <div class="font-bold text-lg">
                                            <span x-text="formatPrix((item.prix_promo < item.prix_vente ? item.prix_promo : item.prix_vente) * item.quantite)"></span>
                                        </div>
                                        <template x-if="item.remise > 0">
                                            <div class="text-xs text-red-600">
                                                (remise: <span x-text="formatPrix(item.remise)"></span>)
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </td>
                            <td class="p-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" class="text-blue-600 hover:text-blue-800 p-2 rounded-full hover:bg-blue-50" @click="afficherDetailsCalcul(idx)" title="Voir les détails du calcul">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                    <button type="button" class="text-gray-600 hover:text-gray-800 p-2 rounded-full hover:bg-gray-50" @click="togglePriceDetails()" title="Afficher/masquer les détails de prix">
                                        <i class="fas" :class="showPriceDetails ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                    <button type="button" class="text-red-600 hover:text-red-800 p-2 rounded-full hover:bg-red-50" @click="supprimerDuPanier(idx)" title="Supprimer du panier">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot class="bg-gradient-to-r from-indigo-50 to-purple-50 font-bold border-t-2 border-indigo-200">
                    <tr>
                        <td colspan="2" class="p-3 text-right">TOTAL :</td>
                        <td colspan="2" class="p-3 text-right text-indigo-700 text-xl"><span x-text="formatPrix(totalPanier)"></span> FCFA</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </template>
    </div>
    
    <!-- Récapitulatif des achats -->
    <div class="bg-white/90 shadow-xl rounded-2xl p-6 mb-6 border border-purple-100" x-show="panier.length > 0">
        <h3 class="text-lg font-semibold text-purple-700 mb-4 flex items-center gap-2">
            <i class="fas fa-clipboard-list"></i> Récapitulatif
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-purple-50/70 p-4 rounded-lg">
                <h4 class="font-medium text-purple-800 mb-2">Détails de la vente</h4>
                <ul class="space-y-2 text-sm">
                    <li class="flex justify-between">
                        <span>Nombre d'articles :</span>
                        <span x-text="panier.reduce((acc, item) => acc + item.quantite, 0)" class="font-medium"></span>
                    </li>
                    <li class="flex justify-between">
                        <span>Nombre de produits différents :</span>
                        <span x-text="panier.length" class="font-medium"></span>
                    </li>
                    <template x-if="panier.some(item => item.prix_promo < item.prix_vente)">
                        <li class="flex justify-between text-green-700">
                            <span>Économies promotionnelles :</span>
                            <span x-text="formatPrix(panier.reduce((acc, item) => acc + ((item.prix_vente - (item.prix_promo || item.prix_vente)) * item.quantite), 0)) + ' FCFA'" class="font-medium"></span>
                        </li>
                    </template>
                </ul>
            </div>
            <div class="bg-blue-50/70 p-4 rounded-lg">
                <h4 class="font-medium text-blue-800 mb-2">Informations paiement</h4>
                <ul class="space-y-2 text-sm">
                    <li class="flex justify-between">
                        <span>Mode de paiement :</span>
                        <span x-text="modePaiement ? modePaiement.charAt(0).toUpperCase() + modePaiement.slice(1) : '-'" class="font-medium"></span>
                    </li>
                    <li class="flex justify-between">
                        <span>Montant payé :</span>
                        <span x-text="formatPrix(montantPaye) + ' FCFA'" class="font-medium"></span>
                    </li>
                    <li class="flex justify-between" :class="montantPaye - totalPanier >= 0 ? 'text-green-700' : 'text-red-700'">
                        <span>Monnaie à rendre :</span>
                        <span x-text="formatPrix(montantPaye - totalPanier) + ' FCFA'" class="font-medium"></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Étape 4 : Paiement -->
    <div class="bg-white/90 shadow-xl rounded-2xl p-6 border border-yellow-100 mb-6">
        <h3 class="text-lg font-semibold text-yellow-700 mb-4 flex items-center gap-2">
            <i class="fas fa-credit-card"></i> Paiement
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="modePaiement" class="block text-sm font-medium text-gray-700 mb-1">Mode de paiement <span class="text-red-500">*</span></label>
                <select name="modePaiement" id="modePaiement" x-model="modePaiement" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" x-ref="modePaiement">
    <option value="">Sélectionner...</option>
    <option value="especes">Espèces</option>
    <option value="mobile_money">Mobile Money</option>
    <option value="carte">Carte bancaire</option>
</select>
            </div>
            <div>
                <label for="montantPaye" class="block text-sm font-medium text-gray-700 mb-1">Montant payé <span class="text-red-500">*</span></label>
                <input type="number" min="0" step="0.01" x-model.number="montantPaye" name="montantPaye" id="montantPaye" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            </div>
        </div>
    </div>

    <!-- Zone feedback -->
    <template x-if="message">
    <div class="mb-4 p-4 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-center">
        <span x-text="message"></span>
    </div>
</template>
<template x-if="erreur">
    <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-800 border border-red-200 text-center">
        <span x-text="erreur"></span>
    </div>
</template>

    <!-- Bouton validation -->
    <div class="flex justify-end">
        <button type="button" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-8 py-3 rounded-xl shadow-lg text-lg transition disabled:opacity-60" @click="submitVente" :disabled="panier.length === 0 || !modePaiement || montantPaye < totalPanier">
            <i class="fas fa-check mr-2"></i> Valider la vente
        </button>
    </div>
</div>

<!-- Modale de détails de calcul -->
<div x-show="showCalculDetails" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showCalculDetails = false">
    <div class="bg-white rounded-xl shadow-2xl p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.outside="showCalculDetails = false">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">
                <i class="fas fa-calculator mr-2 text-blue-600"></i>
                Détails du calcul de prix
            </h2>
            <button @click="showCalculDetails = false" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <template x-if="currentDetailIndex !== null && panier[currentDetailIndex]">
            <div>
                <div class="bg-blue-50 p-4 rounded-lg mb-4">
                    <h3 class="font-bold text-blue-800" x-text="panier[currentDetailIndex].nom"></h3>
                    <p class="text-sm text-blue-600">
                        Quantité: <span class="font-medium" x-text="panier[currentDetailIndex].quantite"></span>
                    </p>
                </div>
                
                <!-- Prix de base -->
                <div class="mb-4">
                    <h4 class="font-medium text-gray-700 mb-2">Prix unitaire de base</h4>
                    <div class="bg-gray-50 p-3 rounded border border-gray-200">
                        <div class="flex justify-between">
                            <span>Prix catalogue:</span>
                            <span class="font-medium" x-text="getDetailsCalcul(panier[currentDetailIndex]).prixUnitaireBase + ' FCFA'"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Conditionnement si applicable -->
                <template x-if="getDetailsCalcul(panier[currentDetailIndex]).aConditionnement">
                    <div class="mb-4">
                        <h4 class="font-medium text-blue-700 mb-2">
                            <i class="fas fa-box mr-1"></i> Prix par conditionnement
                        </h4>
                        <div class="bg-blue-50 p-3 rounded border border-blue-200">
                            <div class="flex justify-between mb-1">
                                <span>Type de conditionnement:</span>
                                <span class="font-medium" x-text="getDetailsCalcul(panier[currentDetailIndex]).detailsConditionnement.type"></span>
                            </div>
                            <div class="flex justify-between mb-1">
                                <span>Quantité par conditionnement:</span>
                                <span class="font-medium" x-text="getDetailsCalcul(panier[currentDetailIndex]).detailsConditionnement.quantite"></span>
                            </div>
                            <div class="flex justify-between mb-1">
                                <span>Prix du conditionnement:</span>
                                <span class="font-medium" x-text="getDetailsCalcul(panier[currentDetailIndex]).detailsConditionnement.prix + ' FCFA'"></span>
                            </div>
                            <div class="flex justify-between pt-1 border-t border-blue-200">
                                <span>Économie par conditionnement:</span>
                                <span class="font-medium text-green-600" x-text="getDetailsCalcul(panier[currentDetailIndex]).detailsConditionnement.economie + ' FCFA'"></span>
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- Promotion si applicable -->
                <template x-if="getDetailsCalcul(panier[currentDetailIndex]).aPromotion">
                    <div class="mb-4">
                        <h4 class="font-medium text-green-700 mb-2">
                            <i class="fas fa-tag mr-1"></i> Promotion appliquée
                        </h4>
                        <div class="bg-green-50 p-3 rounded border border-green-200">
                            <div class="flex justify-between mb-1">
                                <span>Réduction:</span>
                                <span class="font-medium" x-text="getDetailsCalcul(panier[currentDetailIndex]).pourcentagePromotion"></span>
                            </div>
                            <div class="flex justify-between mb-1">
                                <span>Prix unitaire après promotion:</span>
                                <span class="font-medium" x-text="getDetailsCalcul(panier[currentDetailIndex]).prixUnitaireFinal + ' FCFA'"></span>
                            </div>
                            <div class="flex justify-between pt-1 border-t border-green-200">
                                <span>Économie totale (promotion):</span>
                                <span class="font-medium text-green-600" x-text="getDetailsCalcul(panier[currentDetailIndex]).montantPromotion + ' FCFA'"></span>
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- Calcul final -->
                <div class="mb-4">
                    <h4 class="font-medium text-purple-700 mb-2">Calcul final</h4>
                    <div class="bg-purple-50 p-3 rounded border border-purple-200">
                        <div class="flex justify-between mb-1">
                            <span>Prix unitaire final:</span>
                            <span class="font-medium" x-text="getDetailsCalcul(panier[currentDetailIndex]).prixUnitaireFinal + ' FCFA'"></span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span>Quantité:</span>
                            <span class="font-medium" x-text="panier[currentDetailIndex].quantite"></span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span>Total avant remise:</span>
                            <span class="font-medium" x-text="getDetailsCalcul(panier[currentDetailIndex]).totalAvantRemise + ' FCFA'"></span>
                        </div>
                        <template x-if="panier[currentDetailIndex].remise > 0">
                            <div class="flex justify-between mb-1">
                                <span>Remise manuelle:</span>
                                <span class="font-medium text-red-600">- <span x-text="getDetailsCalcul(panier[currentDetailIndex]).remise"></span> FCFA</span>
                            </div>
                        </template>
                        <div class="flex justify-between pt-1 border-t border-purple-200 font-bold">
                            <span>Total final:</span>
                            <span class="text-purple-700" x-text="getDetailsCalcul(panier[currentDetailIndex]).totalApresRemise + ' FCFA'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

@endSection
@push('scripts')
<!-- Ajout de la bibliothèque QuaggaJS pour le scanner de code-barre -->
<script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2@1.8.2/dist/quagga.min.js" defer></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('venteSystem', () => ({
        produits: @json($produits),
        panier: [],
        search: '',
        suggestions: [],
        showSuggestions: false,
        montantPaye: 0,
        modePaiement: 'especes',
        client_id: '',
        type_vente: 'sur_place',
        loading: false,
        message: '',
        erreur: '',
        searchMode: 'text',  // 'text' ou 'barcode'
        showCalculDetails: false, // Nouvel état pour afficher/masquer les détails
        currentDetailIndex: null, // Index de l'élément du panier dont on affiche les détails
        showPriceDetails: localStorage.getItem('showPriceDetails') !== 'false', // Charger la préférence depuis localStorage
        barcodeScanning: false,
        lastScannedBarcode: '',
        scanHistory: [],
        scanFeedback: null,
        barcode: '',
        
        // Nouvelle fonction pour basculer l'affichage des détails de prix
        togglePriceDetails() {
            this.showPriceDetails = !this.showPriceDetails;
            // Sauvegarder la préférence dans localStorage
            localStorage.setItem('showPriceDetails', this.showPriceDetails);
        },
        init() {
            let buffer = '';
            // Focus automatique sur le mode de paiement à l'ouverture
            this.$nextTick(() => {
                if (this.$refs.modePaiement) {
                    this.$refs.modePaiement.focus();
                }
            });
            let timer = null;
            const resetBuffer = () => { buffer = ''; };
            document.addEventListener('keydown', (e) => {
                // Si on est dans un champ de saisie texte normal, on ignore
                const tag = e.target.tagName.toLowerCase();
                if (['input', 'textarea', 'select'].includes(tag) && e.target !== this.$refs.usbScan) {
                    return;
                }
                if (e.key === 'Enter') {
                    if (buffer.length) {
                        this.scannerCodeBarre(buffer);
                        buffer = '';
                        if (timer) clearTimeout(timer);
                    }
                } else if (/^[0-9]$/.test(e.key)) {
                    buffer += e.key;
                    // Réinitialise après 300 ms d'inactivité (délimite un scan)
                    if (timer) clearTimeout(timer);
                    timer = setTimeout(resetBuffer, 300);
                }
            });
            // Focus initial sur le champ
            this.$nextTick(() => { this.$refs.usbScan && this.$refs.usbScan.focus(); });
        },
        rechercherProduit() {
            if (this.search.trim().length === 0) {
                this.suggestions = [];
                return;
            }
            const query = this.search.trim().toLowerCase();
            this.suggestions = this.produits.filter(p =>
                p.nom.toLowerCase().includes(query)
            ).slice(0, 10); // Limite à 10 suggestions
        },
        selectionnerProduit(produit) {
            // Vérifier si le produit est déjà dans le panier
            const index = this.panier.findIndex(item => item.id === produit.id);
            
            // Debug pour voir les informations de prix du produit
            console.log('Produit sélectionné:', {
                nom: produit.nom,
                prix_vente_ttc: produit.prix_vente_ttc,
                prix_promo: produit.prix_promo,
                promotion: produit.promotion,
                conditionnements: produit.conditionnements
            });
            
            if (index !== -1) {
                // Si oui, augmenter la quantité
                this.panier[index].quantite++;
                // Appliquer automatiquement les conditionnements si activé
                this.appliquerConditionnementAutomatique(index);
            } else {
                // Si non, ajouter le produit au panier avec ses conditionnements
                const prixVente = produit.prix_vente_ttc || produit.prix_vente || produit.prix || produit.pu || 0;
                const prixPromo = produit.prix_promo || prixVente;
                
                const produitPanier = {
                    id: produit.id,
                    nom: produit.nom,
                    prix_vente: prixVente,
                    prix_promo: prixPromo,
                    prix_unitaire_base: prixVente, // Garder le prix unitaire de base
                    quantite: 1,
                    remise: 0,
                    conditionnements: produit.conditionnements || [],
                    conditionnement_selectionne: null,
                    conditionnement_auto: true, // Activer l'application automatique des conditionnements
                    promotion: produit.promotion // Stocker les informations de promotion
                };
                
                // Debug pour voir les prix calculés
                console.log('Produit ajouté au panier:', {
                    nom: produitPanier.nom,
                    prix_vente: produitPanier.prix_vente,
                    prix_promo: produitPanier.prix_promo,
                    promotion: produitPanier.promotion
                });
                
                this.panier.push(produitPanier);
            }
            this.search = '';
            this.suggestions = [];
        },
        ajouterProduitSelectionne() {
            if (this.suggestions.length > 0) {
                this.selectionnerProduit(this.suggestions[0]);
            }
        },
        formatPrix(val) {
            if (val === null || val === undefined) return '0';
            if (typeof val !== 'number') val = parseFloat(val);
            if (isNaN(val)) return '0';
            return val.toLocaleString('fr-FR', { minimumFractionDigits: 0 })
        },
        
        // Calculer le total du panier
        get totalPanier() {
            return this.panier.reduce((total, item) => {
                const prixUnitaire = item.prix_promo < item.prix_vente ? item.prix_promo : item.prix_vente;
                return total + (prixUnitaire * item.quantite);
            }, 0);
        },
        
        // Gestion des quantités
        augmenterQuantite(idx) {
            this.panier[idx].quantite++;
            this.appliquerConditionnementAutomatique(idx);
        },
        
        diminuerQuantite(idx) {
            if (this.panier[idx].quantite > 1) {
                this.panier[idx].quantite--;
                this.appliquerConditionnementAutomatique(idx);
            }
        },
        
        majQuantite(idx) {
            if (this.panier[idx].quantite < 1) this.panier[idx].quantite = 1;
            this.appliquerConditionnementAutomatique(idx);
        },
        
        majRemise(idx) {
            if (this.panier[idx].remise < 0) this.panier[idx].remise = 0;
            
            // La remise ne doit pas dépasser le total de la ligne
            const prixUnitaire = this.panier[idx].prix_promo < this.panier[idx].prix_vente ? 
                this.panier[idx].prix_promo : this.panier[idx].prix_vente;
            const totalLigne = prixUnitaire * this.panier[idx].quantite;
            if (this.panier[idx].remise > totalLigne) {
                this.panier[idx].remise = totalLigne;
            }
        },
        
        supprimerDuPanier(idx) {
            this.panier.splice(idx, 1);
        },
        
        // Fonctions de scan de code-barre (simplifiées pour scanner physique)
        setupBarcodeScanner() {
            // Focus le champ de saisie du code-barres
            this.$nextTick(() => {
                if (this.$refs.usbScan) {
                    this.$refs.usbScan.focus();
                    this.$refs.usbScan.select();
                }
            });
        },

        async submitVente() {
            // Validation rapide côté JS
            if (this.panier.length === 0) {
                this.erreur = "Veuillez ajouter au moins un produit au panier.";
                return;
            }
            if (!this.modePaiement) {
                this.erreur = "Veuillez sélectionner un mode de paiement.";
                return;
            }
            if (!this.client_id) {
                this.erreur = "Veuillez sélectionner un client.";
                return;
            }
            if (!this.type_vente) {
                this.erreur = "Veuillez sélectionner un type de vente.";
                return;
            }
            if (this.montantPaye < this.totalPanier) {
                this.erreur = "Le montant payé est insuffisant.";
                return;
            }
            this.loading = true;
            this.erreur = '';
            this.message = '';
            // Construction des données à envoyer
            const produits = this.panier.map(item => ({
                produit_id: item.id,
                quantite: item.quantite,
                prix_unitaire: item.prix_vente,
                remise: item.remise
            }));
            const data = {
                produits,
                montant_paye: this.montantPaye,
                mode_paiement: this.modePaiement,
                client_id: this.client_id,
                type_vente: this.type_vente
            };
            try {
                const response = await fetch('/ventes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (response.ok) {
                    // Redirection automatique vers la fiche de la vente créée
                    if (result && result.redirect) {
                        window.location.href = result.redirect;
                        return;
                    }
                    if (result && result.vente && result.vente.id) {
                        window.location.href = `/ventes/${result.vente.id}`;
                        return;
                    }
                    if (result && result.id) {
                        window.location.href = `/ventes/${result.id}`;
                        return;
                    }
                    this.message = result.message || 'Vente enregistrée avec succès !';
                    this.panier = [];
                    this.montantPaye = 0;
                    this.modePaiement = '';
                } else {
                    this.erreur = result.message || 'Erreur lors de la validation de la vente.';
                }
            } catch (e) {
                this.erreur = "Erreur réseau ou serveur. Veuillez réessayer.";
            } finally {
                this.loading = false;
            }
        },

        scannerCodeBarre(code) {
            if (!code && this.barcode) {
                code = this.barcode.trim();
            }
            if (!code) return;
            
            console.log('Code scanné:', code); // Debug
            
            // Recherche du produit avec plus de robustesse
            const produit = this.produits.find(p => {
                // Convertir en string et comparer de façon stricte
                if (!p.code_barre) return false;
                
                // Essayer plusieurs formats possibles (avec/sans espaces ou tirets)
                const scanCode = code.toString().replace(/[\s-]/g, '');
                const dbCode = p.code_barre.toString().replace(/[\s-]/g, '');
                
                return dbCode === scanCode;
            });
            
            if (produit) {
                console.log('Produit trouvé:', produit.nom); // Debug
                
                // Vérifier si le produit est déjà dans le panier
                const index = this.panier.findIndex(item => item.id === produit.id);
                
                if (index !== -1) {
                    // Si oui, augmenter la quantité et vérifier si un conditionnement doit être appliqué
                    this.panier[index].quantite++;
                    this.appliquerConditionnementAutomatique(index);
                } else {
                    // Si non, ajouter le produit au panier avec des informations de prix claires
                    
                    // 1. Déterminer le prix de base (prix normal unitaire)
                    const prixBase = produit.prix_vente_ttc || produit.prix_vente || produit.prix || produit.pu || 0;
                    
                    // 2. Déterminer le prix promotionnel s'il existe
                    const prixPromo = produit.prix_promo || prixBase;
                    
                    // 3. Créer l'objet produit pour le panier avec des noms explicites
                    const produitPanier = {
                        id: produit.id,
                        nom: produit.nom,
                        prix_unitaire_base: prixBase, // Prix unitaire de base (toujours conservé)
                        prix_vente: prixBase,         // Prix de vente actuel (peut changer avec conditionnement)
                        prix_promo: prixPromo,        // Prix promotionnel (si applicable)
                        quantite: 1,
                        remise: 0,
                        conditionnements: produit.conditionnements || [],
                        conditionnement_selectionne: null,
                        conditionnement_auto: true,   // Activer l'application automatique des conditionnements
                        promotion: produit.promotion,  // Informations sur la promotion active
                        // Nouvelles propriétés pour plus de clarté
                        a_promotion: prixPromo < prixBase,
                        pourcentage_promo: prixPromo < prixBase ? Math.round((1 - prixPromo/prixBase) * 100) : 0
                    };
                    
                    console.log('Ajout au panier:', {
                        produit: produit.nom,
                        prix_base: prixBase,
                        prix_promo: prixPromo,
                        a_promotion: produitPanier.a_promotion,
                        reduction: produitPanier.pourcentage_promo + '%'
                    });
                    
                    this.panier.push(produitPanier);
                }
                
                this.barcode = '';
                this.scanFeedback = 'ok';
                this.scanHistory.push({ code, status: 'ok' });
                // Bip audio court
                const audio = new Audio('data:audio/mp3;base64,SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU2LjM2LjEwMAAAAAAAAAAAAAAA//OEAAAAAAAAAAAAAAAAAAAAAAAASW5mbwAAAA8AAAAEAAABIADAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDV1dXV1dXV1dXV1dXV1dXV1dXV1dXV1dXV1dXV1dXV1dXV1dXV6urq6urq6urq6urq6urq6urq6urq6v////////////////////////////////8AAAAATGF2YzU2LjQxAAAAAAAAAAAAAAAAJAAAAAAAAAAAASDs90hvAAAAAAAAAAAAAAAAAAAA//MUZAAAAAGkAAAAAAAAA0gAAAAATEFN//MUZAMAAAGkAAAAAAAAA0gAAAAARTMu//MUZAYAAAGkAAAAAAAAA0gAAAAAOTku//MUZAkAAAGkAAAAAAAAA0gAAAAANVVV');
                audio.play().catch(()=>{});
            } else {
                console.log('Produit non trouvé pour le code:', code); // Debug
                this.scanFeedback = 'nok';
                this.scanHistory.push({ code, status: 'nok' });
                this.erreur = `Aucun produit avec le code-barres ${code}`;
                setTimeout(() => { this.erreur = ''; }, 3000);
            }
            
            // Toujours refocaliser le champ après traitement
            setTimeout(() => {
                this.scanFeedback = null;
                this.barcode = '';
                if (this.$refs.usbScan) {
                    this.$refs.usbScan.focus();
                    this.$refs.usbScan.select();
                }
            }, 800);
            
            // Historique limité à 20
            if (this.scanHistory.length > 20) this.scanHistory = this.scanHistory.slice(-20);
        },
        
        // Fonction améliorée pour appliquer automatiquement les conditionnements
        appliquerConditionnementAutomatique(index) {
            const item = this.panier[index];
            
            // Si l'application automatique des conditionnements n'est pas activée, ne rien faire
            if (!item.conditionnement_auto) return;
            
            // Si le produit n'a pas de conditionnements, ne rien faire
            if (!item.conditionnements || item.conditionnements.length === 0) return;
            
            console.log('Calcul conditionnement pour', item.nom, 'quantité:', item.quantite);
            
            // Trier les conditionnements par quantité décroissante pour appliquer d'abord les plus grands
            const conditionnementsTries = [...item.conditionnements].sort((a, b) => b.quantite - a.quantite);
            
            // IMPORTANT: Sauvegarder le pourcentage de réduction promotionnelle
            // Si le produit a une promotion active, on doit conserver le même pourcentage de réduction
            const tauxPromotionOriginal = item.a_promotion ? item.pourcentage_promo / 100 : 0;
            
            // Réinitialiser le prix au prix unitaire de base
            let prixUnitaire = item.prix_unitaire_base;
            let nomProduit = item.nom.split(' - ')[0]; // Récupérer le nom de base du produit
            
            // Variables pour stocker les informations de conditionnement
            let conditionnementApplique = null;
            let detailConditionnement = '';
            
            // Calculer combien de conditionnements complets peuvent être appliqués
            for (const cond of conditionnementsTries) {
                if (item.quantite >= cond.quantite && item.quantite % cond.quantite === 0) {
                    // Si la quantité est un multiple exact du conditionnement
                    conditionnementApplique = cond;
                    prixUnitaire = cond.prix / cond.quantite; // Prix par unité avec le conditionnement
                    detailConditionnement = `${cond.type} (${cond.quantite})`;
                    break;
                }
            }
            
            // Si aucun conditionnement exact n'est trouvé, appliquer le plus grand possible et laisser le reste en unitaire
            if (!conditionnementApplique) {
                for (const cond of conditionnementsTries) {
                    if (item.quantite >= cond.quantite) {
                        const nbConditionnements = Math.floor(item.quantite / cond.quantite);
                        const nbUnites = item.quantite % cond.quantite;
                        
                        // Prix total = (prix du conditionnement × nb de conditionnements) + (prix unitaire × nb d'unités restantes)
                        const prixTotal = (cond.prix * nbConditionnements) + (item.prix_unitaire_base * nbUnites);
                        prixUnitaire = prixTotal / item.quantite; // Nouveau prix unitaire moyen
                        
                        conditionnementApplique = {
                            ...cond,
                            nbConditionnements,
                            nbUnites
                        };
                        
                        if (nbUnites > 0) {
                            detailConditionnement = `${nbConditionnements}×${cond.type} + ${nbUnites} unité(s)`;
                        } else {
                            detailConditionnement = `${nbConditionnements}×${cond.type}`;
                        }
                        break;
                    }
                }
            }
            
            // Mettre à jour l'élément du panier
            if (conditionnementApplique) {
                item.conditionnement_selectionne = conditionnementApplique;
                item.prix_vente = prixUnitaire;
                item.nom = `${nomProduit} - ${detailConditionnement}`;
                
                // IMPORTANT: Appliquer la même réduction promotionnelle au nouveau prix conditionné
                if (tauxPromotionOriginal > 0) {
                    const reductionPromo = prixUnitaire * tauxPromotionOriginal;
                    item.prix_promo = prixUnitaire - reductionPromo;
                    item.a_promotion = true;
                    item.pourcentage_promo = Math.round(tauxPromotionOriginal * 100);
                } else {
                    item.prix_promo = prixUnitaire;
                    item.a_promotion = false;
                    item.pourcentage_promo = 0;
                }
                
                // Debug pour vérifier les calculs
                console.log('Conditionnement appliqué:', {
                    produit: item.nom,
                    prixUnitaireBase: item.prix_unitaire_base,
                    prixUnitaireConditionnement: prixUnitaire,
                    tauxPromotionOriginal: tauxPromotionOriginal,
                    prixPromotionnel: item.prix_promo,
                    reductionEffective: tauxPromotionOriginal > 0 ? 
                        `${item.pourcentage_promo}% (${this.formatPrix(prixUnitaire - item.prix_promo)} FCFA)` : 'Aucune'
                });
            } else {
                // Si aucun conditionnement ne s'applique, revenir au prix unitaire de base
                item.conditionnement_selectionne = null;
                item.prix_vente = item.prix_unitaire_base;
                item.nom = nomProduit;
                
                // Restaurer le prix promotionnel original si applicable
                if (tauxPromotionOriginal > 0) {
                    item.prix_promo = item.prix_unitaire_base * (1 - tauxPromotionOriginal);
                } else {
                    item.prix_promo = item.prix_unitaire_base;
                }
                
                console.log('Aucun conditionnement applicable, retour au prix unitaire:', {
                    produit: item.nom,
                    prix: item.prix_vente,
                    prixPromo: item.prix_promo
                });
            }
        },

        // Nouvelle fonction pour afficher les détails de calcul d'un produit
        afficherDetailsCalcul(index) {
            this.currentDetailIndex = index;
            this.showCalculDetails = true;
        },
        
        // Fonction pour obtenir les détails de calcul formatés
        getDetailsCalcul(item) {
            if (!item) return {};
            
            const details = {
                nom: item.nom.split(' - ')[0],
                quantite: item.quantite,
                prixUnitaireBase: this.formatPrix(item.prix_unitaire_base),
                prixUnitaireActuel: this.formatPrix(item.prix_vente),
                prixUnitaireFinal: this.formatPrix(item.prix_promo < item.prix_vente ? item.prix_promo : item.prix_vente),
                totalAvantRemise: this.formatPrix((item.prix_promo < item.prix_vente ? item.prix_promo : item.prix_vente) * item.quantite),
                totalApresRemise: this.formatPrix(((item.prix_promo < item.prix_vente ? item.prix_promo : item.prix_vente) * item.quantite) - (item.remise || 0)),
                remise: this.formatPrix(item.remise || 0),
                aPromotion: item.prix_promo < item.prix_vente,
                montantPromotion: item.prix_promo < item.prix_vente ? this.formatPrix((item.prix_vente - item.prix_promo) * item.quantite) : '0',
                pourcentagePromotion: item.prix_promo < item.prix_vente ? Math.round((1 - item.prix_promo/item.prix_vente) * 100) + '%' : '0%',
                aConditionnement: !!item.conditionnement_selectionne,
                detailsConditionnement: item.conditionnement_selectionne ? {
                    type: item.conditionnement_selectionne.type,
                    quantite: item.conditionnement_selectionne.quantite,
                    prix: this.formatPrix(item.conditionnement_selectionne.prix),
                    economie: this.formatPrix(item.prix_unitaire_base * item.conditionnement_selectionne.quantite - item.conditionnement_selectionne.prix)
                } : null
            };
            
            return details;
        },
    }));
});
</script>
@endpush
