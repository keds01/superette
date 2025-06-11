@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/60 backdrop-blur-xl border border-violet-100 rounded-2xl shadow-2xl p-8 md:p-10">
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-violet-600 via-indigo-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">
                    Modifier la Commande
                </h1>
                <p class="mt-3 text-gray-600">Ajustez les informations de la commande fournisseur.</p>
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('commandes.update', $commande) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Informations de base -->
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <div>
                        <label for="numero_commande" class="block text-sm font-medium text-gray-700">
                            Numéro de commande
                        </label>
                        <input type="text" name="numero_commande" id="numero_commande" value="{{ $commande->numero_commande }}" class="mt-1 block w-full rounded-xl border-gray-300 bg-gray-50" readonly>
                    </div>
                    <div>
                        <label for="fournisseur_id" class="block text-sm font-medium text-gray-700">
                            Fournisseur <span class="text-red-500">*</span>
                        </label>
                        <select name="fournisseur_id" id="fournisseur_id" class="mt-1 block w-full rounded-xl border-gray-300" required>
                            <option value="">Sélectionnez un fournisseur</option>
                            @foreach($fournisseurs as $fournisseur)
                                <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id', $commande->fournisseur_id) == $fournisseur->id ? 'selected' : '' }}>{{ $fournisseur->nom }}</option>
                            @endforeach
                        </select>
                        @error('fournisseur_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <div>
                        <label for="date_commande" class="block text-sm font-medium text-gray-700">
                            Date de commande <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_commande" id="date_commande" value="{{ old('date_commande', $commande->date_commande->format('Y-m-d')) }}" class="mt-1 block w-full rounded-xl border-gray-300" required>
                        @error('date_commande')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="date_livraison_prevue" class="block text-sm font-medium text-gray-700">
                            Date de livraison prévue <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_livraison_prevue" id="date_livraison_prevue" value="{{ old('date_livraison_prevue', $commande->date_livraison_prevue->format('Y-m-d')) }}" class="mt-1 block w-full rounded-xl border-gray-300" required>
                        @error('date_livraison_prevue')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Liste des produits -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Produits de la commande</h3>
                    <div id="produits-container" class="space-y-4">
                        @foreach($commande->details as $i => $detail)
                        <div class="produit-item bg-white/80 backdrop-blur-sm p-4 rounded-xl border border-violet-100">
                            <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Produit <span class="text-red-500">*</span></label>
                                    <select name="produits[]" class="produit-select mt-1 block w-full rounded-xl border-gray-300" required>
                                        <option value="">Sélectionnez un produit</option>
                                        @foreach($produits as $produit)
                                            <option value="{{ $produit->id }}" data-prix="{{ $produit->prix_achat_ht ?? 0 }}" {{ $detail->produit_id == $produit->id ? 'selected' : '' }}>{{ $produit->nom }} ({{ $produit->categorie->nom }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Quantité <span class="text-red-500">*</span></label>
                                    <input type="number" name="quantites[]" min="0.01" step="0.01" value="{{ $detail->quantite }}" class="quantite-input mt-1 block w-full rounded-xl border-gray-300" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Prix unitaire <span class="text-red-500">*</span></label>
                                    <input type="number" name="prix_unitaire[]" min="0.01" step="0.01" value="{{ $detail->prix_unitaire }}" class="prix-input mt-1 block w-full rounded-xl border-gray-300" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Total</label>
                                    <input type="text" class="total-ligne mt-1 block w-full rounded-xl border-gray-300 bg-gray-50" value="{{ number_format($detail->quantite * $detail->prix_unitaire, 2, ',', ' ') }} {{ $commande->devise }}" readonly>
                                </div>
                                <div class="flex items-end pb-2">
                                    <button type="button" class="supprimer-produit px-3 py-2 rounded-xl bg-red-100 hover:bg-red-200 text-red-600 {{ count($commande->details) <= 1 ? 'hidden' : '' }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="ajouter-produit" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl text-violet-700 bg-violet-100 hover:bg-violet-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500">
                        <i class="fas fa-plus mr-2"></i> Ajouter un produit
                    </button>
                </div>

                <!-- Résumé de la commande -->
                <div class="mt-8 bg-white/80 backdrop-blur-sm p-6 rounded-xl border border-violet-100">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Résumé de la commande</h3>
                    <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Montant total</label>
                            <input type="text" id="montant_total_affichage" class="mt-1 block w-full rounded-xl border-gray-300" value="{{ number_format($commande->montant_total, 2, ',', ' ') }} {{ $commande->devise }}" readonly>
                            <input type="hidden" name="montant_total" id="montant_total" value="{{ $commande->montant_total }}">
                        </div>
                        <div>
                            <label for="devise" class="block text-sm font-medium text-gray-700">Devise</label>
                            <select name="devise" id="devise" class="mt-1 block w-full rounded-xl border-gray-300">
                                <option value="FCFA" {{ $commande->devise == 'FCFA' ? 'selected' : '' }}>FCFA</option>
                                <option value="EUR" {{ $commande->devise == 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="USD" {{ $commande->devise == 'USD' ? 'selected' : '' }}>USD</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-end space-x-4 pt-6">
                    <a href="{{ route('commandes.show', $commande) }}" class="px-6 py-3 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-colors">Annuler</a>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-tr from-violet-600 via-indigo-500 to-purple-600 text-white font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transform transition-all duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Même logique JS que pour la création (voir create.blade.php)
document.addEventListener('DOMContentLoaded', function() {
    function formaterMontant(montant, devise = 'FCFA') {
        return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(montant) + ' ' + devise;
    }
    function calculerTotalLigne(ligne) {
        const quantite = parseFloat(ligne.querySelector('.quantite-input').value) || 0;
        const prixUnitaire = parseFloat(ligne.querySelector('.prix-input').value) || 0;
        const total = quantite * prixUnitaire;
        const champTotal = ligne.querySelector('.total-ligne');
        if (champTotal) {
            const devise = document.getElementById('devise').value;
            champTotal.value = formaterMontant(total, devise);
        }
        return total;
    }
    function mettreAJourMontantTotal() {
        let total = 0;
        document.querySelectorAll('.produit-item').forEach(ligne => {
            total += calculerTotalLigne(ligne);
        });
        const devise = document.getElementById('devise').value;
        document.getElementById('montant_total_affichage').value = formaterMontant(total, devise);
        document.getElementById('montant_total').value = total.toFixed(2);
        const lignesProduits = document.querySelectorAll('.produit-item');
        if (lignesProduits.length <= 1) {
            document.querySelector('.supprimer-produit').classList.add('hidden');
        } else {
            document.querySelectorAll('.supprimer-produit').forEach(btn => {
                btn.classList.remove('hidden');
            });
        }
    }
    function initialiserLigneProduit(ligne) {
        const select = ligne.querySelector('.produit-select');
        if (select && select.selectedIndex > 0) {
            const option = select.options[select.selectedIndex];
            if (option && option.value) {
                const prix = parseFloat(option.dataset.prix) || 0;
                ligne.querySelector('.prix-input').value = prix.toFixed(2);
                calculerTotalLigne(ligne);
            }
        }
        const btnSupprimer = ligne.querySelector('.supprimer-produit');
        if (btnSupprimer) {
            btnSupprimer.addEventListener('click', function() {
                if (document.querySelectorAll('.produit-item').length > 1) {
                    ligne.remove();
                    mettreAJourMontantTotal();
                }
            });
        }
    }
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('produit-select')) {
            const ligne = e.target.closest('.produit-item');
            const option = e.target.options[e.target.selectedIndex];
            if (option && option.value) {
                const prix = parseFloat(option.dataset.prix) || 0;
                ligne.querySelector('.prix-input').value = prix.toFixed(2);
                calculerTotalLigne(ligne);
                mettreAJourMontantTotal();
            }
        }
    });
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantite-input') || e.target.classList.contains('prix-input')) {
            const ligne = e.target.closest('.produit-item');
            calculerTotalLigne(ligne);
            mettreAJourMontantTotal();
        }
    });
    document.getElementById('devise').addEventListener('change', function() {
        mettreAJourMontantTotal();
    });
    document.getElementById('ajouter-produit').addEventListener('click', function() {
        const template = document.querySelector('.produit-item').cloneNode(true);
        template.querySelectorAll('input').forEach(input => {
            if (input.classList.contains('quantite-input')) {
                input.value = '1';
            } else if (!input.readOnly) {
                input.value = '';
            }
        });
        const selectProduit = template.querySelector('.produit-select');
        if (selectProduit) {
            selectProduit.selectedIndex = 0;
        }
        document.getElementById('produits-container').appendChild(template);
        initialiserLigneProduit(template);
        mettreAJourMontantTotal();
    });
    document.querySelectorAll('.produit-item').forEach(ligne => {
        initialiserLigneProduit(ligne);
    });
    mettreAJourMontantTotal();
});
</script>
@endpush
