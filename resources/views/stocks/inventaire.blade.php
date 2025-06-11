@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <h1 class="text-2xl font-semibold text-gray-900 mb-6">Inventaire physique <i class="fas fa-info-circle text-indigo-500" data-bs-toggle="tooltip" title="L'inventaire physique permet de vérifier et corriger les écarts entre le stock théorique et le stock réel dans votre superette."></i></h1>
                
                <!-- Filtres -->
                <div class="mb-8 bg-white p-4 rounded-lg shadow-sm">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label for="categorie_filter" class="block text-sm font-medium text-gray-700">Catégorie <i class="fas fa-info-circle text-indigo-500" data-bs-toggle="tooltip" title="Filtrez l'inventaire par catégorie de produits pour faciliter l'organisation de votre inventaire."></i></label>
                            <select id="categorie_filter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Toutes les catégories</option>
                                @foreach($categories as $categorie)
                                    <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="search_filter" class="block text-sm font-medium text-gray-700">Recherche <i class="fas fa-info-circle text-indigo-500" data-bs-toggle="tooltip" title="Recherchez rapidement un produit par son nom, sa référence ou son code-barres pour l'inventorier."></i></label>
                            <input type="text" id="search_filter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Nom, référence, code-barres...">
                        </div>
                        
                        <div class="flex items-end">
                            <button type="button" id="btnFilter" class="btn-primary w-full">
                                <i class="fas fa-filter mr-2"></i>Filtrer
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Mode d'inventaire -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <span class="mr-4 text-sm font-medium text-gray-700">Mode d'inventaire : <i class="fas fa-info-circle text-indigo-500" data-bs-toggle="tooltip" title="Choisissez le mode d'inventaire en fonction de vos besoins : mode rapide pour scanner plusieurs produits rapidement, mode précis pour saisir les quantités produit par produit."></i></span>
                        <label class="inline-flex items-center">
                            <input type="radio" name="mode_inventaire" value="rapide" class="form-radio text-indigo-600" checked>
                            <span class="ml-2">Mode rapide (scan continu)</span>
                        </label>
                        <label class="inline-flex items-center ml-4">
                            <input type="radio" name="mode_inventaire" value="precis" class="form-radio text-indigo-600">
                            <span class="ml-2">Mode précis (fiche par produit)</span>
                        </label>
                    </div>
                    
                    <div>
                        <button type="button" id="btnScanBarcode" class="btn-secondary">
                            <i class="fas fa-barcode mr-2"></i>Scanner un code-barres
                        </button>
                    </div>
                </div>
                
                <!-- Tableau d'inventaire moderne -->
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-indigo-100">
        <thead class="bg-gradient-to-tr from-indigo-100 to-purple-100">
            <tr>
                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-bold text-indigo-900 sm:pl-6">Produit</th>
                <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-indigo-900">Stock théorique</th>
                <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-indigo-900">Stock réel</th>
                <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-indigo-900">Écart</th>
                <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-indigo-900">Catégorie</th>
                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
            </tr>
        </thead>
        <tbody id="products-container" class="bg-white divide-y divide-indigo-50">
            <!-- Les produits seront chargés ici via JavaScript -->
            <tr>
                <td colspan="6" class="px-3 py-4 text-center text-sm text-gray-500">
                    Chargement des produits...
                </td>
            </tr>
        </tbody>
    </table>
</div>
                
                <!-- Récapitulatif et validation -->
                <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900">Produits comptés</h3>
                        <p class="mt-2 text-3xl font-semibold text-indigo-600" id="produits-comptes">0</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900">Écarts détectés</h3>
                        <p class="mt-2 text-3xl font-semibold text-red-600" id="ecarts-detectes">0</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900">Valeur des écarts</h3>
                        <p class="mt-2 text-3xl font-semibold text-orange-600" id="valeur-ecarts">0 FCFA</p>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="button" id="btnValiderInventaire" class="btn-primary">
                        <i class="fas fa-check mr-2"></i>Valider l'inventaire
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour saisie des quantités -->
<div id="modal-saisie" class="fixed inset-0 z-10 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-produit-nom"></h3>
                        <div class="mt-4">
                            <input type="hidden" id="modal-produit-id">
                            
                            <div class="mb-4">
                                <label for="modal-cartons" class="block text-sm font-medium text-gray-700">Cartons</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="number" name="cartons" id="modal-cartons" class="form-input flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md" min="0" value="0">
                                    <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm" id="modal-conditionnement"></span>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="modal-unites" class="block text-sm font-medium text-gray-700">Unités</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="number" name="unites" id="modal-unites" class="form-input flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md" min="0" step="0.01" value="0">
                                    <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm" id="modal-unite"></span>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="modal-motif" class="block text-sm font-medium text-gray-700">Motif de l'écart (si détecté)</label>
                                <select id="modal-motif" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">Sélectionnez un motif...</option>
                                    <option value="Erreur de comptage précédent">Erreur de comptage précédent</option>
                                    <option value="Perte/casse">Perte/casse</option>
                                    <option value="Vol">Vol</option>
                                    <option value="Erreur administrative">Erreur administrative</option>
                                    <option value="Erreur de livraison">Erreur de livraison</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>
                            
                            <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-700">Stock théorique:</span>
                                    <span class="text-sm text-gray-900" id="modal-stock-theorique"></span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-sm font-medium text-gray-700">Stock réel:</span>
                                    <span class="text-sm text-gray-900" id="modal-stock-reel"></span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-sm font-medium text-gray-700">Écart:</span>
                                    <span class="text-sm font-bold" id="modal-ecart"></span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-sm font-medium text-gray-700">Valeur de l'écart:</span>
                                    <span class="text-sm font-bold" id="modal-valeur-ecart"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="btn-modal-valider" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Valider
                </button>
                <button type="button" id="btn-modal-annuler" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let products = [];
        let productMap = {};
        
        // Chargement initial des produits
        loadProducts();
        
        // Événement de filtrage
        document.getElementById('btnFilter').addEventListener('click', loadProducts);
        
        // Événement de validation d'inventaire
        document.getElementById('btnValiderInventaire').addEventListener('click', validerInventaire);
        
        // Événements pour le modal
        document.getElementById('btn-modal-valider').addEventListener('click', validerSaisie);
        document.getElementById('btn-modal-annuler').addEventListener('click', fermerModal);
        
        // Événements pour le calcul en temps réel
        document.getElementById('modal-cartons').addEventListener('input', calculerStockReel);
        document.getElementById('modal-unites').addEventListener('input', calculerStockReel);
        
        // Fonction pour charger les produits
        function loadProducts() {
            const categorieId = document.getElementById('categorie_filter').value;
            const search = document.getElementById('search_filter').value;
            
            fetch(`/api/stocks/products-for-inventaire?categorie_id=${categorieId}&search=${search}`)
                .then(response => response.json())
                .then(data => {
                    products = data;
                    productMap = {};
                    
                    // Création d'un map pour accès rapide par ID
                    products.forEach(product => {
                        productMap[product.id] = product;
                    });
                    
                    renderProducts();
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des produits:', error);
                    alert('Erreur lors du chargement des produits');
                });
        }
        
        // Fonction pour afficher les produits dans le tableau
        function renderProducts() {
            const container = document.getElementById('products-container');
            
            if (products.length === 0) {
                container.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            Aucun produit trouvé
                        </td>
                    </tr>
                `;
                return;
            }
            
            let html = '';
            products.forEach(product => {
                const ecartClass = product.ecart !== 0 ? 'text-red-600 font-bold' : 'text-gray-500';
                const stockReel = product.stock_reel > 0 ? 
                    `${product.cartons_reels} cartons + ${product.unites_reelles} ${product.symbole_unite}` : 
                    '—';
                
                html += `
                    <tr data-product-id="${product.id}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">${product.nom}</div>
                                    <div class="text-sm text-gray-500">${product.reference} ${product.code_barres ? '• ' + product.code_barres : ''}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${product.categorie}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${product.cartons_theoriques} cartons + ${product.unites_theoriques} ${product.symbole_unite}
                            <div class="text-xs text-gray-400">(${product.stock_theorique} ${product.symbole_unite} total)</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${stockReel}
                            ${product.stock_reel > 0 ? `<div class="text-xs text-gray-400">(${product.stock_reel} ${product.symbole_unite} total)</div>` : ''}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm ${ecartClass}">
                            ${product.ecart !== 0 ? product.ecart + ' ' + product.symbole_unite : '—'}
                            ${product.ecart !== 0 ? `<div class="text-xs">(${product.valeur_ecart} FCFA)</div>` : ''}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button" onclick="ouvrirSaisie(${product.id})" class="text-indigo-600 hover:text-indigo-900">
                                Saisir
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            container.innerHTML = html;
            
            // Mise à jour des compteurs
            updateCounters();
        }
        
        // Fonction pour ouvrir le modal de saisie
        window.ouvrirSaisie = function(productId) {
            const product = productMap[productId];
            if (!product) return;
            
            // Remplissage des données du modal
            document.getElementById('modal-produit-id').value = product.id;
            document.getElementById('modal-produit-nom').textContent = product.nom;
            document.getElementById('modal-conditionnement').textContent = product.conditionnement_fournisseur;
            document.getElementById('modal-unite').textContent = product.symbole_unite;
            document.getElementById('modal-stock-theorique').textContent = `${product.cartons_theoriques} cartons + ${product.unites_theoriques} ${product.symbole_unite} (${product.stock_theorique} ${product.symbole_unite} total)`;
            
            // Pré-remplissage des valeurs déjà saisies
            document.getElementById('modal-cartons').value = product.cartons_reels || 0;
            document.getElementById('modal-unites').value = product.unites_reelles || 0;
            
            // Calcul des valeurs en temps réel
            calculerStockReel();
            
            // Affichage du modal
            document.getElementById('modal-saisie').classList.remove('hidden');
        };
        
        // Fonction pour calculer le stock réel et l'écart en temps réel
        function calculerStockReel() {
            const productId = document.getElementById('modal-produit-id').value;
            const product = productMap[productId];
            if (!product) return;
            
            const cartons = parseFloat(document.getElementById('modal-cartons').value) || 0;
            const unites = parseFloat(document.getElementById('modal-unites').value) || 0;
            
            // Calcul du stock réel total
            const stockReel = (cartons * product.quantite_par_conditionnement) + unites;
            
            // Calcul de l'écart
            const ecart = stockReel - product.stock_theorique;
            
            // Calcul de la valeur de l'écart
            const valeurEcart = ecart * product.prix_achat_ht;
            
            // Mise à jour des affichages
            document.getElementById('modal-stock-reel').textContent = `${cartons} cartons + ${unites} ${product.symbole_unite} (${stockReel} ${product.symbole_unite} total)`;
            
            const ecartElement = document.getElementById('modal-ecart');
            ecartElement.textContent = `${ecart} ${product.symbole_unite}`;
            ecartElement.className = 'text-sm font-bold ' + (ecart < 0 ? 'text-red-600' : ecart > 0 ? 'text-green-600' : 'text-gray-600');
            
            const valeurEcartElement = document.getElementById('modal-valeur-ecart');
            valeurEcartElement.textContent = `${Math.abs(valeurEcart).toLocaleString('fr-FR')} FCFA`;
            valeurEcartElement.className = 'text-sm font-bold ' + (ecart < 0 ? 'text-red-600' : ecart > 0 ? 'text-green-600' : 'text-gray-600');
            
            // Affichage du champ motif uniquement si écart
            const motifField = document.getElementById('modal-motif').parentElement;
            motifField.style.display = ecart !== 0 ? 'block' : 'none';
        }
        
        // Fonction pour valider la saisie
        function validerSaisie() {
            const productId = document.getElementById('modal-produit-id').value;
            const product = productMap[productId];
            if (!product) return;
            
            const cartons = parseFloat(document.getElementById('modal-cartons').value) || 0;
            const unites = parseFloat(document.getElementById('modal-unites').value) || 0;
            
            // Calcul du stock réel
            const stockReel = (cartons * product.quantite_par_conditionnement) + unites;
            const ecart = stockReel - product.stock_theorique;
            const valeurEcart = ecart * product.prix_achat_ht;
            const motif = document.getElementById('modal-motif').value;
            
            // Vérification du motif si écart
            if (ecart !== 0 && !motif) {
                alert('Veuillez sélectionner un motif pour l\'écart détecté.');
                return;
            }
            
            // Mise à jour des données du produit
            product.cartons_reels = cartons;
            product.unites_reelles = unites;
            product.stock_reel = stockReel;
            product.ecart = ecart;
            product.valeur_ecart = valeurEcart;
            product.motif_ecart = motif;
            
            // Mise à jour de l'affichage
            renderProducts();
            
            // Fermeture du modal
            fermerModal();
        }
        
        // Fonction pour fermer le modal
        function fermerModal() {
            document.getElementById('modal-saisie').classList.add('hidden');
        }
        
        // Fonction pour mettre à jour les compteurs
        function updateCounters() {
            let produitsComptes = 0;
            let ecartsDetectes = 0;
            let valeurEcarts = 0;
            
            products.forEach(product => {
                if (product.stock_reel > 0) {
                    produitsComptes++;
                }
                
                if (product.ecart !== 0) {
                    ecartsDetectes++;
                    valeurEcarts += Math.abs(product.valeur_ecart);
                }
            });
            
            document.getElementById('produits-comptes').textContent = produitsComptes;
            document.getElementById('ecarts-detectes').textContent = ecartsDetectes;
            document.getElementById('valeur-ecarts').textContent = valeurEcarts.toLocaleString('fr-FR') + ' FCFA';
        }
        
        // Fonction pour valider l'inventaire complet
        function validerInventaire() {
            if (products.length === 0) {
                alert('Aucun produit à inventorier.');
                return;
            }
            
            const produitsComptes = products.filter(p => p.stock_reel > 0);
            if (produitsComptes.length === 0) {
                alert('Veuillez saisir au moins un produit avant de valider l\'inventaire.');
                return;
            }
            
            // Préparation des données
            const produitsAjustes = produitsComptes.map(p => ({
                produit_id: p.id,
                cartons_reels: p.cartons_reels,
                unites_reelles: p.unites_reelles,
                motif: p.ecart !== 0 ? p.motif_ecart || 'Inventaire physique' : 'Inventaire physique'
            }));
            
            // Confirmation
            if (!confirm(`Vous allez valider l'inventaire pour ${produitsComptes.length} produits. Cette action est irréversible. Continuer ?`)) {
                return;
            }
            
            // Envoi des données en série
            const promises = produitsAjustes.map(data => 
                fetch('/api/stocks/ajuster', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                }).then(response => response.json())
            );
            
            Promise.all(promises)
                .then(results => {
                    const succes = results.filter(r => r.success).length;
                    const echecs = results.length - succes;
                    
                    if (echecs > 0) {
                        alert(`Inventaire terminé avec ${succes} ajustements réussis et ${echecs} échecs.`);
                    } else {
                        alert('Inventaire validé avec succès !');
                    }
                    
                    // Rechargement des produits
                    loadProducts();
                })
                .catch(error => {
                    console.error('Erreur lors de la validation de l\'inventaire:', error);
                    alert('Erreur lors de la validation de l\'inventaire');
                });
        }
    });
</script>
@endsection
