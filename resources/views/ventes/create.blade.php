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
                <input type="text" x-ref="usbScan" x-model="barcode" @keydown.enter.prevent="scannerCodeBarre(); $refs.usbScan.select()" autofocus autocomplete="off"
                    class="text-center text-3xl tracking-widest font-mono font-bold bg-gray-50 border-2 border-indigo-400 rounded-xl px-8 py-5 outline-none shadow focus:border-green-500 transition-all mx-auto w-full max-w-md focus:ring-2 focus:ring-indigo-200"
                    placeholder="Scannez ou tapez le code-barre puis validez"
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
                <div class="mt-6">
                    <div class="text-sm font-semibold text-indigo-700 mb-2">Derniers scans</div>
                    <ul class="flex flex-col gap-1 items-center">
                        <template x-for="(scan, idx) in scanHistory.slice(-5).reverse()" :key="idx">
                            <li class="flex gap-2 items-center text-base font-mono">
                                <span class="px-2 py-1 rounded bg-gray-100 border border-gray-300 text-gray-700" x-text="scan.code"></span>
                                <span :class="{'text-green-600': scan.status === 'ok', 'text-red-600': scan.status === 'nok'}">
                                    <i :class="scan.status === 'ok' ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                                </span>
                            </li>
                        </template>
                    </ul>
                </div>
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
        <h3 class="text-lg font-semibold text-emerald-700 mb-4 flex items-center gap-2">
            <i class="fas fa-shopping-cart"></i> Panier
        </h3>
        <template x-if="panier.length === 0">
            <div class="text-gray-400 italic">Aucun produit dans le panier.</div>
        </template>
        <template x-if="panier.length > 0">
            <table class="w-full text-sm border rounded-lg overflow-hidden">
                <thead class="bg-gradient-to-tr from-blue-50 to-teal-50">
                    <tr>
                        <th class="p-2">Produit</th>
                        <th class="p-2 text-center">Qté</th>
                        <th class="p-2 text-right">PU</th>
                        <th class="p-2 text-right">Remise</th>
                        <th class="p-2 text-right">Total</th>
                        <th class="p-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, idx) in panier" :key="item.id">
                        <tr class="hover:bg-gray-50 border-t">
                            <td class="p-2"><span x-text="item.nom" class="font-medium"></span></td>
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
                            </td>
                            <td class="p-2 text-right font-medium"><span x-text="formatPrix(item.prix_vente)"></span> FCFA</td>
                            <td class="p-2 text-right">
                                <input type="number" min="0" x-model.number="item.remise" class="w-16 rounded border-gray-300 text-center" @change="majRemise(idx)">
                            </td>
                            <td class="p-2 text-right font-bold"><span x-text="formatPrix(item.prix_vente * item.quantite - item.remise)"></span> FCFA</td>
                            <td class="p-2 text-center">
                                <button type="button" class="text-red-600 hover:text-red-800" @click="supprimerDuPanier(idx)"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot class="bg-gray-50 font-bold border-t-2 border-gray-300">
                    <tr>
                        <td colspan="4" class="p-2 text-right">TOTAL :</td>
                        <td class="p-2 text-right text-indigo-700"><span x-text="formatPrix(totalPanier)"></span> FCFA</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </template>
        <!-- Le total est maintenant dans le tfoot du tableau -->
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
                    <li class="flex justify-between">
                        <span>Total remises :</span>
                        <span x-text="formatPrix(panier.reduce((acc, item) => acc + item.remise, 0)) + ' FCFA'" class="font-medium"></span>
                    </li>
                    <li class="flex justify-between text-lg font-bold text-purple-900 border-t border-purple-200 pt-2 mt-2">
                        <span>Total à payer :</span>
                        <span x-text="formatPrix(totalPanier) + ' FCFA'"></span>
                    </li>
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
                <select name="modePaiement" id="modePaiement" x-model="modePaiement" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
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
        modePaiement: '',
        client_id: '',
        type_vente: '',
        loading: false,
        message: '',
        erreur: '',
        searchMode: 'text',  // 'text' ou 'barcode'
        barcodeScanning: false,
        lastScannedBarcode: '',
        barcodeScanner: null,
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
            const exist = this.panier.find(item => item.id === produit.id);
            if (exist) {
                // Si le produit existe déjà, augmenter simplement la quantité
                exist.quantite++;
            } else {
                // Récupérer le prix unitaire depuis l'API pour être sûr d'avoir la dernière valeur
                const prixUnitaire = Number(produit.prix_vente || produit.prix_vente_ttc || produit.prix || produit.pu || 0);
                
                console.log('Produit sélectionné:', produit); // Debug
                console.log('Prix unitaire brut:', produit.prix_vente); // Debug
                console.log('Prix unitaire calculé:', prixUnitaire); // Debug
                
                this.panier.push({
                    id: produit.id,
                    nom: produit.nom,
                    prix_vente: prixUnitaire,
                    quantite: 1,
                    remise: 0
                });
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
                return total + (item.prix_vente * item.quantite - item.remise);
            }, 0);
        },
        
        // Gestion des quantités
        augmenterQuantite(idx) {
            this.panier[idx].quantite++;
        },
        
        diminuerQuantite(idx) {
            if (this.panier[idx].quantite > 1) {
                this.panier[idx].quantite--;
            }
        },
        
        majQuantite(idx) {
            if (this.panier[idx].quantite < 1) this.panier[idx].quantite = 1;
        },
        
        majRemise(idx) {
            if (this.panier[idx].remise < 0) this.panier[idx].remise = 0;
            
            // La remise ne doit pas dépasser le total de la ligne
            const totalLigne = this.panier[idx].prix_vente * this.panier[idx].quantite;
            if (this.panier[idx].remise > totalLigne) {
                this.panier[idx].remise = totalLigne;
            }
        },
        
        supprimerDuPanier(idx) {
            this.panier.splice(idx, 1);
        },
        
        // Fonctions de scan de code-barre
        setupBarcodeScanner() {
            // Charger QuaggaJS si n'est pas encore chargé
            if (typeof Quagga === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/@ericblade/quagga2@1.8.2/dist/quagga.min.js';
                script.onload = () => this.initBarcodeScanner();
                document.head.appendChild(script);
            }
        },
        
        startBarcodeScanning() {
            this.barcodeScanning = true;
            this.$nextTick(() => {
                if (typeof Quagga !== 'undefined') {
                    this.initBarcodeScanner();
                } else {
                    this.setupBarcodeScanner();
                }
            });
        },
        
        initBarcodeScanner() {
            if (!this.barcodeScanning) return;
            
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#barcode-scanner'),
                    constraints: {
                        facingMode: "environment"
                    }
                },
                decoder: {
                    readers: [
                        "ean_reader",
                        "ean_8_reader",
                        "code_39_reader",
                        "code_128_reader"
                    ]
                }
            }, (err) => {
                if (err) {
                    console.error("Erreur d'initialisation du scanner:", err);
                    this.barcodeScanning = false;
                    return;
                }
                
                console.log("Scanner de code-barre actif");
                
                Quagga.start();
                
                Quagga.onDetected((result) => {
                    if (result && result.codeResult) {
                        const code = result.codeResult.code;
                        this.lastScannedBarcode = code;
                        console.log("Code-barre détecté:", code);
                        
                        // Rechercher le produit correspondant au code-barre
                        const produit = this.produits.find(p => p.code_barre == code);
                        if (produit) {
                            this.selectionnerProduit(produit);
                            
                            // Feedback visuel et sonore
                            const audioFeedback = new Audio('data:audio/mp3;base64,SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU2LjM2LjEwMAAAAAAAAAAAAAAA//OEAAAAAAAAAAAAAAAAAAAAAAAASW5mbwAAAA8AAAAEAAABIADAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDV1dXV1dXV1dXV1dXV1dXV1dXV1dXV1dXV1dXV6urq6urq6urq6urq6urq6urq6urq6urq6v////////////////////////////////8AAAAATGF2YzU2LjQxAAAAAAAAAAAAAAAAJAAAAAAAAAAAASDs90hvAAAAAAAAAAAAAAAAAAAA//MUZAAAAAGkAAAAAAAAA0gAAAAATEFN//MUZAMAAAGkAAAAAAAAA0gAAAAARTMu//MUZAYAAAGkAAAAAAAAA0gAAAAAOTku//MUZAkAAAGkAAAAAAAAA0gAAAAANVVV');
                            audioFeedback.play().catch(e => console.error("Erreur audio:", e));
                            
                            // Vibration si disponible
                            if (navigator.vibrate) {
                                navigator.vibrate(100);
                            }
                        } else {
                            console.log("Aucun produit avec ce code-barre:", code);
                            this.erreur = `Aucun produit avec le code-barre ${code}`;
                            setTimeout(() => { this.erreur = ''; }, 3000);
                        }
                    }
                });
            });
        },
        
        stopBarcodeScanning() {
            if (typeof Quagga !== 'undefined') {
                Quagga.stop();
                this.barcodeScanning = false;
            }
        },
        
        scanHistory: [],

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
                    this.message = result.message || 'Vente enregistrée avec succès !';
                    this.panier = [];
                    this.montantPaye = 0;
                    this.modePaiement = '';
                    // Redirection ou reset possible ici
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
            const produit = this.produits.find(p => p.code_barre && p.code_barre.toString() === code.toString());
            if (produit) {
                this.selectionnerProduit(produit);
                this.barcode = '';
                this.scanFeedback = 'ok';
                this.scanHistory.push({ code, status: 'ok' });
                // Bip audio court
                const audio = new Audio('data:audio/mp3;base64,SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU2LjM2LjEwMAAAAAAAAAAAAAAA//OEAAAAAAAAAAAAAAAAAAAAAAAASW5mbwAAAA8AAAAEAAABIADAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDV1dXV1dXV1dXV1dXV1dXV1dXV1dXV1dXV1dXV6urq6urq6urq6urq6urq6urq6urq6urq6v////////////////////////////////8AAAAATGF2YzU2LjQxAAAAAAAAAAAAAAAAJAAAAAAAAAAAASDs90hvAAAAAAAAAAAAAAAAAAAA//MUZAAAAAGkAAAAAAAAA0gAAAAATEFN//MUZAMAAAGkAAAAAAAAA0gAAAAARTMu//MUZAYAAAGkAAAAAAAAA0gAAAAAOTku//MUZAkAAAGkAAAAAAAAA0gAAAAANVVV');
                audio.play().catch(()=>{});
                setTimeout(() => { this.scanFeedback = null; }, 1000);
                this.$nextTick(() => {
                    this.$refs.usbScan && this.$refs.usbScan.focus();
                    // Scroll auto vers le panier
                    const panier = document.getElementById('panier-section');
                    if (panier) panier.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
            } else {
                this.scanFeedback = 'nok';
                this.scanHistory.push({ code, status: 'nok' });
                setTimeout(() => { this.scanFeedback = null; }, 1000);
                this.$nextTick(() => { this.$refs.usbScan && this.$refs.usbScan.focus(); });
            }
            // Historique limité à 20
            if (this.scanHistory.length > 20) this.scanHistory = this.scanHistory.slice(-20);
        },
    }));
});
</script>
@endpush
