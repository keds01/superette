@extends('layouts.app')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold bg-gradient-to-tr from-blue-400 to-teal-600 bg-clip-text text-transparent tracking-tight flex items-center gap-3 animate-fade-in">
                        <svg class="w-8 h-8 text-blue-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 13V7m0 6l-3 3m3-3l3 3m9-3V7m0 6l-3 3m3-3l3 3m-9 8a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Nouveau Mouvement de Stock
                    </h2>
                    <p class="mt-2 text-lg text-gray-500">Enregistrez une entrée ou une sortie de stock pour un produit</p>
                </div>
                <a href="{{ route('mouvements-stock.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la liste
                </a>
            </div>

            @if($selectedProduct)
                <div class="mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Résumé produit -->
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-xl shadow">
                            <div class="font-semibold text-blue-700 text-lg mb-2 flex items-center gap-2">
                                <i class="fas fa-box"></i> {{ $selectedProduct->nom }}
                            </div>
                            <div class="text-gray-700 mb-1">
                                <span class="font-medium">Catégorie :</span> {{ $selectedProduct->categorie->nom ?? '-' }}
                            </div>
                            <div class="text-gray-700 mb-1">
                                <span class="font-medium">Stock actuel :</span> <span id="stock-actuel">{{ number_format($selectedProduct->stock, 2) }}</span> {{ $selectedProduct->uniteVente->symbole ?? '' }}
                                @if($selectedProduct->stock <= $selectedProduct->seuil_alerte)
                                    <span class="ml-2 inline-block px-2 py-1 bg-red-100 text-red-700 rounded text-xs">Stock bas</span>
                                @endif
                            </div>
                            <div class="text-gray-700 mb-1">
                                <span class="font-medium">Seuil d'alerte :</span> {{ number_format($selectedProduct->seuil_alerte, 2) }}
                            </div>
                            <div class="text-gray-700 mb-1">
                                <span class="font-medium">Prix d'achat HT :</span> {{ number_format($selectedProduct->prix_achat_ht, 0, ',', ' ') }} FCFA
                            </div>
                            <div class="text-gray-700 mb-1">
                                <span class="font-medium">Prix de vente HT :</span> {{ number_format($selectedProduct->prix_vente_ht, 0, ',', ' ') }} FCFA
                            </div>
                            <div class="text-gray-700 mb-1">
                                <span class="font-medium">Prix de vente TTC :</span> {{ number_format($selectedProduct->prix_vente_ttc, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <!-- Historique -->
                        <div class="md:col-span-2 bg-gray-50 p-4 rounded-xl shadow">
                            <div class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-history"></i> 3 derniers mouvements
                            </div>
                            @if($lastMovements->count())
                                <ul class="divide-y divide-gray-200">
                                    @foreach($lastMovements as $mvt)
                                        <li class="py-2 flex items-center justify-between">
                                            <div>
                                                <span class="font-medium {{ $mvt->type === 'entree' ? 'text-green-700' : 'text-red-700' }}">
                                                    {{ ucfirst($mvt->type) }}
                                                </span>
                                                <span class="ml-2 text-gray-600">{{ number_format($mvt->quantite_apres_unite - $mvt->quantite_avant_unite, 2) }} {{ $selectedProduct->uniteVente->symbole ?? '' }}</span>
                                                <span class="ml-2 text-gray-500 text-xs">({{ $mvt->created_at->format('d/m/Y H:i') }})</span>
                                            </div>
                                            <div class="text-gray-500 text-xs">{{ $mvt->motif }}</div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-gray-400 italic">Aucun mouvement récent.</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Bloc résumé produit -->
            <div id="product-summary-container" class="mt-8 p-4 bg-blue-50 rounded-xl border border-blue-200" style="{{ !$selectedProduct ? 'display: none;' : '' }}">
                <h4 class="font-semibold text-blue-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> Résumé du produit
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p><span class="text-gray-600">Catégorie:</span> <span class="font-medium" id="product-category">{{ $selectedProduct ? ($selectedProduct->categorie->nom ?? 'Non catégorisé') : '-' }}</span></p>
                        <p><span class="text-gray-600">Stock actuel:</span> <span class="font-medium" id="product-stock">{{ $selectedProduct ? number_format($selectedProduct->stock, 2) . ' ' . ($selectedProduct->uniteVente->symbole ?? '') : '-' }}</span></p>
                        <p><span class="text-gray-600">Seuil d'alerte:</span> <span class="font-medium" id="product-alert-threshold">{{ $selectedProduct ? number_format($selectedProduct->seuil_alerte, 2) : '-' }}</span></p>
                    </div>
                    <div>
                        <p><span class="text-gray-600">Prix d'achat HT:</span> <span class="font-medium" id="product-purchase-price">{{ $selectedProduct ? number_format($selectedProduct->prix_achat_ht, 0) . ' FCFA' : '-' }}</span></p>
                        <p><span class="text-gray-600">Prix de vente HT:</span> <span class="font-medium" id="product-sale-price">{{ $selectedProduct ? number_format($selectedProduct->prix_vente_ht, 0) . ' FCFA' : '-' }}</span></p>
                        <p><span class="text-gray-600">Prix de vente TTC:</span> <span class="font-medium" id="product-sale-price-ttc">{{ $selectedProduct ? number_format($selectedProduct->prix_vente_ttc, 0) . ' FCFA' : '-' }}</span></p>
                    </div>
                </div>
            </div>
            
            <!-- Historique récent -->
            <div id="product-history-container" class="mt-8 p-4 bg-gray-50 rounded-xl border border-gray-200" style="{{ !$selectedProduct ? 'display: none;' : '' }}">
                <h4 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-history"></i> 3 derniers mouvements
                </h4>
                <div id="product-history-content">
                    @if($selectedProduct && $lastMovements->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white rounded-lg">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                        <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Quantité</th>
                                        <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                        <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Motif</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lastMovements as $movement)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-2 px-4 border-b">
                                                @if($movement->type == 'entree')
                                                    <span class="text-green-600">Entrée</span>
                                                @elseif($movement->type == 'sortie')
                                                    <span class="text-red-600">Sortie</span>
                                                @else
                                                    <span class="text-blue-600">{{ ucfirst($movement->type) }}</span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                @php
                                                    $quantite = $movement->quantite_apres_unite - $movement->quantite_avant_unite;
                                                @endphp
                                                {{ number_format(abs($quantite), 2) }} {{ $selectedProduct->uniteVente->symbole ?? '' }}
                                            </td>
                                            <td class="py-2 px-4 border-b">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="py-2 px-4 border-b">{{ $movement->motif }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 italic">Aucun mouvement récent.</p>
                    @endif
                </div>
            </div>

            <form action="{{ route('mouvements-stock.store') }}" method="POST" class="space-y-8">
                @csrf

@if(session('error'))
    <div class="alert alert-danger bg-red-100 text-red-800 border border-red-300 p-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif
@if(session('success'))
    <div class="alert alert-success bg-green-100 text-green-800 border border-green-300 p-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif
@if(
    \Illuminate\Support\Facades\Session::has('errors') && count(\Illuminate\Support\Facades\Session::get('errors')) > 0)
    <div class="alert alert-danger bg-red-100 text-red-800 border border-red-300 p-3 rounded mb-4">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

                <!-- Informations du mouvement -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Détails du Mouvement
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Produit -->
                        <div>
                            <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Produit concerné</label>
                            @if($selectedProduct)
                                <input type="hidden" name="produit_id" value="{{ $selectedProduct->id }}">
                                <div class="mt-1 p-2 w-full bg-gray-100 rounded-lg border border-gray-200">
                                    <div class="font-semibold text-gray-800">{{ $selectedProduct->nom }}</div>
                                    <div class="text-gray-500 text-sm">{{ $selectedProduct->categorie->nom }} - {{ number_format($selectedProduct->stock, 2) }} {{ $selectedProduct->uniteVente->symbole ?? '' }}</div>
                                </div>
                                <div class="mt-1 text-xs text-blue-500"><i class="fas fa-lock mr-1"></i> Produit verrouillé</div>
                            @else
                                <select name="product_id" id="product_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Sélectionnez un produit</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" @selected(old('product_id', $selectedProductId ?? null) == $product->id)
                                            data-stock="{{ $product->stock }}" 
                                            data-unite="{{ $product->uniteVente->symbole ?? '' }}"
                                            data-prix-achat="{{ $product->prix_achat_ht }}">
                                            {{ $product->nom }} ({{ $product->categorie->nom }})
                                            - Stock actuel : {{ number_format($product->stock, 2) }} {{ $product->uniteVente->symbole ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            @error('product_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type de mouvement -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type de mouvement</label>
                            <select name="type" id="type" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="entree" @selected(old('type') === 'entree')>Entrée de stock</option>
                                <option value="sortie" @selected(old('type') === 'sortie')>Sortie de stock</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Quantité -->
                        <div>
                            <label for="quantite" class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                            <input type="number" name="quantite" id="quantite" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('quantite') }}" step="0.01" min="0.01" required>
                            @error('quantite')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Prix unitaire -->
                        <div>
                            <label for="prix_unitaire" class="block text-sm font-medium text-gray-700 mb-1">Prix unitaire (FCFA)</label>
                            <input type="number" name="prix_unitaire" id="prix_unitaire" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('prix_unitaire') }}" min="0" required>
                            @error('prix_unitaire')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date du mouvement -->
                        <div>
                            <label for="date_mouvement" class="block text-sm font-medium text-gray-700 mb-1">Date et heure du mouvement</label>
                            <input type="datetime-local" name="date_mouvement" id="date_mouvement" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('date_mouvement', now()->format('Y-m-d\TH:i')) }}" required>
                            @error('date_mouvement')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date de péremption (visible si type = entree) -->
                        <div id="date_peremption_container" style="{{ old('type') === 'sortie' ? 'display: none;' : '' }}">
                            <label for="date_peremption" class="block text-sm font-medium text-gray-700 mb-1">Date de péremption (pour entrée)</label>
                            <input type="date" name="date_peremption" id="date_peremption" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('date_peremption') }}" {{ old('type') === 'sortie' ? 'disabled' : '' }}>
                            @error('date_peremption')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Motif -->
                        <div class="md:col-span-2">
                            <label for="motif" class="block text-sm font-medium text-gray-700 mb-1">Motif du mouvement</label>
                            <textarea name="motif" id="motif" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" rows="3" required placeholder="Ex: Achat fournisseur, Vente client, Correction d'inventaire...">{{ old('motif') }}</textarea>
                            @error('motif')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Bloc Stock Prévisionnel -->
                    <div class="mt-8 p-4 bg-blue-50 rounded-xl border border-blue-200">
                        <h4 class="font-semibold text-blue-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-calculator"></i> Stock prévisionnel après ce mouvement
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <span class="text-gray-600">Stock actuel :</span>
                                <span class="font-medium" id="current-stock-display">
                                    @if($selectedProduct)
                                        {{ number_format($selectedProduct->stock, 2) }} {{ $selectedProduct->uniteVente->symbole ?? '' }}
                                    @else
                                        <span class="text-gray-400 italic">Sélectionnez un produit</span>
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-600">Mouvement :</span>
                                <span class="font-medium" id="movement-display">-</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Stock après mouvement :</span>
                                <span class="font-medium" id="final-stock-display">-</span>
                            </div>
                        </div>
                        <div id="stock-warning" class="hidden mt-2 text-sm font-medium text-red-600 bg-red-50 p-2 rounded">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Attention: Ce mouvement va créer un stock négatif!
                        </div>
                        <div id="stock-alert" class="hidden mt-2 text-sm font-medium text-yellow-600 bg-yellow-50 p-2 rounded">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Attention: Ce mouvement va réduire le stock sous le seuil d'alerte!
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('mouvements-stock.index') }}" 
                       class="px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-all duration-200">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 rounded-xl bg-gradient-to-tr from-blue-600 to-teal-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                        Enregistrer le mouvement
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Variables globales
        let currentStock = {{ $selectedProduct ? $selectedProduct->stock : '0' }};
        let currentStockUnit = "{{ $selectedProduct ? ($selectedProduct->uniteVente->symbole ?? '') : '' }}";
        let currentPrixAchat = {{ $selectedProduct ? $selectedProduct->prix_achat_ht : '0' }};
        let seuilAlerte = {{ $selectedProduct ? $selectedProduct->seuil_alerte : '0' }};
        let submitButton;
        let produitSelectionne = {{ $selectedProduct ? 'true' : 'false' }};

        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            submitButton = document.querySelector('button[type="submit"]');
            const productSelect = document.getElementById('product_id');
            const quantiteInput = document.getElementById('quantite');
            const typeSelect = document.getElementById('type');
            const prixInput = document.getElementById('prix_unitaire');
            
            // Initialise l'affichage si un produit est déjà sélectionné
            if (produitSelectionne) {
                // Préremplir le prix selon le produit et le type
                prixInput.value = currentPrixAchat.toFixed(0);
            } else {
                // Si aucun produit n'est sélectionné, désactiver le bouton
                if (submitButton) {
                    submitButton.classList.add('bg-gray-400');
                    submitButton.classList.remove('from-blue-600', 'to-teal-500');
                    submitButton.disabled = true;
                    submitButton.title = 'Veuillez sélectionner un produit';
                }
            }
            
            // Afficher/masquer le champ date_peremption selon le type
            typeSelect.addEventListener('change', function() {
                const datePeremptionContainer = document.getElementById('date_peremption_container');
                const datePeremptionField = document.getElementById('date_peremption');
                
                // Gestion de l'affichage du champ date de péremption
                if (this.value === 'entree') {
                    datePeremptionContainer.style.display = 'block';
                    datePeremptionField.removeAttribute('disabled');
                } else {
                    datePeremptionContainer.style.display = 'none';
                    datePeremptionField.setAttribute('disabled', 'disabled');
                    datePeremptionField.value = '';
                }
                
                // Mise à jour du calcul de stock prévisionnel
                updateStockPreview();
            });
            
            // Si le select produit existe (pas verrouillé), on l'initialise
            if (productSelect) {
                productSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.value) {
                        produitSelectionne = true;
                        currentStock = parseFloat(selectedOption.dataset.stock || 0);
                        currentStockUnit = selectedOption.dataset.unite || '';
                        currentPrixAchat = parseFloat(selectedOption.dataset.prixAchat || 0);
                        
                        // Mise à jour de l'affichage du stock actuel
                        document.getElementById('current-stock-display').textContent = 
                            `${currentStock.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${currentStockUnit}`;
                        
                        // Préremplit le prix selon le produit et le type
                        if (typeSelect.value === 'entree') {
                            prixInput.value = currentPrixAchat.toFixed(0);
                        }
                        
                        // Active le bouton si un produit est sélectionné et que les autres champs sont OK
                        if (submitButton && quantiteInput.value > 0) {
                            submitButton.classList.remove('bg-gray-400');
                            submitButton.classList.add('from-blue-600', 'to-teal-500');
                            submitButton.disabled = false;
                            submitButton.title = '';
                        }
                        
                        // Mise à jour du calcul de stock prévisionnel
                        updateStockPreview();
                    } else {
                        produitSelectionne = false;
                        // Désactive le bouton si aucun produit n'est sélectionné
                        if (submitButton) {
                            submitButton.classList.add('bg-gray-400');
                            submitButton.classList.remove('from-blue-600', 'to-teal-500');
                            submitButton.disabled = true;
                            submitButton.title = 'Veuillez sélectionner un produit';
                        }
                        
                        // Réinitialise l'affichage
                        document.getElementById('current-stock-display').innerHTML = 
                            `<span class="text-gray-400 italic">Sélectionnez un produit</span>`;
                        document.getElementById('movement-display').textContent = '-';
                        document.getElementById('final-stock-display').textContent = '-';
                    }
                });
            }
            
            // Événements pour recalculer le stock prévisionnel
            quantiteInput.addEventListener('input', updateStockPreview);
            typeSelect.addEventListener('change', updateStockPreview);
            
            // Déclencher les événements pour l'état initial
            if (productSelect) productSelect.dispatchEvent(new Event('change'));
            typeSelect.dispatchEvent(new Event('change'));
            updateStockPreview();
        });
        
        // Fonction de calcul et mise à jour de l'affichage du stock prévisionnel
        function updateStockPreview() {
            const quantiteInput = document.getElementById('quantite');
            const typeSelect = document.getElementById('type');
            const stockWarning = document.getElementById('stock-warning');
            const stockAlert = document.getElementById('stock-alert');
            const submitButton = document.querySelector('button[type="submit"]');
            
            // N'effectue pas de calcul si aucun produit n'est sélectionné
            if (!produitSelectionne) {
                return;
            }
            
            // Récupère les valeurs actuelles
            const quantite = parseFloat(quantiteInput.value) || 0;
            const type = typeSelect.value;
            
            // Calcule le stock prévisionnel
            let stockFinal = currentStock;
            let mouvement = 0;
            
            if (type === 'entree') {
                mouvement = quantite;
                stockFinal += quantite;
                document.getElementById('movement-display').innerHTML = 
                    `<span class="text-green-600">+${quantite.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${currentStockUnit}</span>`;
            } else if (type === 'sortie') {
                mouvement = -quantite;
                stockFinal -= quantite;
                document.getElementById('movement-display').innerHTML = 
                    `<span class="text-red-600">-${quantite.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${currentStockUnit}</span>`;
            }
            
            // Mise à jour de l'affichage du stock final
            document.getElementById('final-stock-display').textContent = 
                `${stockFinal.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${currentStockUnit}`;
            
            // Gestion des alertes
            if (stockFinal < 0) {
                stockWarning.classList.remove('hidden');
                submitButton.classList.add('bg-gray-400');
                submitButton.classList.remove('from-blue-600', 'to-teal-500');
                submitButton.disabled = true;
                submitButton.title = 'Le stock ne peut pas être négatif';
            } else {
                stockWarning.classList.add('hidden');
                
                // Si un produit est sélectionné et quantité valide
                if (produitSelectionne && quantite > 0) {
                    submitButton.classList.remove('bg-gray-400');
                    submitButton.classList.add('from-blue-600', 'to-teal-500');
                    submitButton.disabled = false;
                    submitButton.title = '';
                }
                
                // Alerte si stock sous le seuil
                if (stockFinal <= seuilAlerte && stockFinal > 0) {
                    stockAlert.classList.remove('hidden');
                } else {
                    stockAlert.classList.add('hidden');
                }
            }
            
            // Si la quantité n'est pas valide, on désactive le bouton
            if (quantite <= 0 || isNaN(quantite)) {
                submitButton.classList.add('bg-gray-400');
                submitButton.classList.remove('from-blue-600', 'to-teal-500');
                submitButton.disabled = true;
                submitButton.title = 'La quantité doit être supérieure à 0';
            }
        }
    </script>
@endpush
@endsection 