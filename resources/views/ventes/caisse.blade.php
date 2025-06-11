<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Caisse') }}
            </h2>
            <div class="flex space-x-4">
                <button onclick="imprimerTicket()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-print mr-2"></i>Imprimer
                </button>
                <button onclick="annulerVente()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-times mr-2"></i>Annuler
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Liste des produits -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-4">
                            <input type="text" id="searchProduct" placeholder="Rechercher un produit..." 
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4" id="productsList">
                            @foreach($products as $product)
                            <div class="product-card bg-white border rounded-lg p-4 cursor-pointer hover:shadow-lg transition-shadow"
                                data-id="{{ $product->id }}"
                                data-nom="{{ $product->nom }}"
                                data-prix="{{ $product->prix_vente }}"
                                data-stock="{{ $product->quantite }}">
                                <h3 class="font-semibold text-lg">{{ $product->nom }}</h3>
                                <p class="text-gray-600">{{ $product->categorie->nom }}</p>
                                <p class="text-green-600 font-bold">{{ number_format($product->prix_vente, 0, ',', ' ') }} FCFA</p>
                                <p class="text-sm text-gray-500">Stock: {{ $product->quantite }} {{ $product->unite->symbole }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Panier -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Panier</h3>
                        <div class="space-y-4">
                            <!-- Liste des articles -->
                            <div id="cartItems" class="space-y-2 max-h-96 overflow-y-auto">
                                <!-- Les articles seront ajoutés ici dynamiquement -->
                            </div>

                            <!-- Code de remise -->
                            <div class="border-t pt-4">
                                <div class="flex space-x-2">
                                    <input type="text" id="remiseCode" placeholder="Code remise" 
                                        class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <button onclick="verifierRemise()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                        Appliquer
                                    </button>
                                </div>
                                <div id="remiseInfo" class="mt-2 text-sm text-green-600 hidden"></div>
                            </div>

                            <!-- Totaux -->
                            <div class="border-t pt-4 space-y-2">
                                <div class="flex justify-between">
                                    <span>Sous-total</span>
                                    <span id="subtotal">0 FCFA</span>
                                </div>
                                <div class="flex justify-between text-green-600">
                                    <span>Remise</span>
                                    <span id="remise">0 FCFA</span>
                                </div>
                                <div class="flex justify-between font-bold text-lg">
                                    <span>Total</span>
                                    <span id="total">0 FCFA</span>
                                </div>
                            </div>

                            <!-- Mode de paiement -->
                            <div class="border-t pt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mode de paiement</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button onclick="selectPaymentMode('especes')" class="payment-mode p-2 border rounded-lg text-center hover:bg-gray-50">
                                        <i class="fas fa-money-bill-wave mb-1"></i>
                                        <div>Espèces</div>
                                    </button>
                                    <button onclick="selectPaymentMode('carte')" class="payment-mode p-2 border rounded-lg text-center hover:bg-gray-50">
                                        <i class="fas fa-credit-card mb-1"></i>
                                        <div>Carte</div>
                                    </button>
                                    <button onclick="selectPaymentMode('mobile_money')" class="payment-mode p-2 border rounded-lg text-center hover:bg-gray-50">
                                        <i class="fas fa-mobile-alt mb-1"></i>
                                        <div>Mobile Money</div>
                                    </button>
                                </div>
                            </div>

                            <!-- Bouton de paiement -->
                            <button onclick="finaliserVente()" id="paiementBtn" disabled
                                class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-check mr-2"></i>Finaliser la vente
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let cart = [];
        let selectedPaymentMode = null;
        let currentRemise = null;

        // Gestion des produits
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', () => {
                const id = card.dataset.id;
                const nom = card.dataset.nom;
                const prix = parseFloat(card.dataset.prix);
                const stock = parseInt(card.dataset.stock);

                const existingItem = cart.find(item => item.id === id);
                if (existingItem) {
                    if (existingItem.quantite < stock) {
                        existingItem.quantite++;
                        updateCart();
                    } else {
                        alert('Stock insuffisant');
                    }
                } else {
                    cart.push({ id, nom, prix, quantite: 1 });
                    updateCart();
                }
            });
        });

        // Mise à jour du panier
        function updateCart() {
            const cartItems = document.getElementById('cartItems');
            cartItems.innerHTML = '';

            cart.forEach((item, index) => {
                const itemTotal = item.prix * item.quantite;
                cartItems.innerHTML += `
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-semibold">${item.nom}</div>
                            <div class="text-sm text-gray-600">${item.prix.toLocaleString()} FCFA × ${item.quantite}</div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="font-bold">${itemTotal.toLocaleString()} FCFA</span>
                            <button onclick="removeItem(${index})" class="text-red-500 hover:text-red-600">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            updateTotals();
        }

        // Suppression d'un article
        function removeItem(index) {
            cart.splice(index, 1);
            updateCart();
        }

        // Mise à jour des totaux
        function updateTotals() {
            const subtotal = cart.reduce((sum, item) => sum + (item.prix * item.quantite), 0);
            const remise = currentRemise ? currentRemise.valeur : 0;
            const total = subtotal - remise;

            document.getElementById('subtotal').textContent = `${subtotal.toLocaleString()} FCFA`;
            document.getElementById('remise').textContent = `${remise.toLocaleString()} FCFA`;
            document.getElementById('total').textContent = `${total.toLocaleString()} FCFA`;

            // Activer/désactiver le bouton de paiement
            document.getElementById('paiementBtn').disabled = cart.length === 0 || !selectedPaymentMode;
        }

        // Sélection du mode de paiement
        function selectPaymentMode(mode) {
            selectedPaymentMode = mode;
            document.querySelectorAll('.payment-mode').forEach(btn => {
                btn.classList.remove('bg-blue-50', 'border-blue-500');
            });
            event.currentTarget.classList.add('bg-blue-50', 'border-blue-500');
            updateTotals();
        }

        // Vérification du code de remise
        function verifierRemise() {
            const code = document.getElementById('remiseCode').value;
            if (!code) return;

            fetch('/ventes/verifier-remise', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ code })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentRemise = data.remise;
                    document.getElementById('remiseInfo').textContent = data.remise.description;
                    document.getElementById('remiseInfo').classList.remove('hidden');
                    updateTotals();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la vérification du code de remise');
            });
        }

        // Finalisation de la vente
        function finaliserVente() {
            if (!selectedPaymentMode) {
                alert('Veuillez sélectionner un mode de paiement');
                return;
            }

            const venteData = {
                products: cart.map(item => ({
                    id: item.id,
                    quantite: item.quantite,
                    prix_unitaire: item.prix
                })),
                mode_paiement: selectedPaymentMode,
                code_remise: currentRemise ? document.getElementById('remiseCode').value : null
            };

            fetch('/ventes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(venteData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Vente enregistrée avec succès');
                    window.location.href = `/ventes/${data.vente.id}/imprimer`;
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'enregistrement de la vente');
            });
        }

        // Recherche de produits
        document.getElementById('searchProduct').addEventListener('input', function(e) {
            const search = e.target.value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                const nom = card.dataset.nom.toLowerCase();
                card.style.display = nom.includes(search) ? 'block' : 'none';
            });
        });

        // Annulation de la vente
        function annulerVente() {
            if (confirm('Voulez-vous vraiment annuler la vente en cours ?')) {
                cart = [];
                currentRemise = null;
                selectedPaymentMode = null;
                document.getElementById('remiseCode').value = '';
                document.getElementById('remiseInfo').classList.add('hidden');
                updateCart();
                document.querySelectorAll('.payment-mode').forEach(btn => {
                    btn.classList.remove('bg-blue-50', 'border-blue-500');
                });
            }
        }
    </script>
    @endpush
</x-app-layout> 