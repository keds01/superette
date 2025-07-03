@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-bold bg-gradient-to-tr from-indigo-400 to-purple-600 bg-clip-text text-transparent tracking-tight flex items-center gap-3">
                            <i class="fas fa-receipt text-indigo-500"></i>
                            Détail de la Vente
                        </h2>
                        <p class="mt-2 text-lg text-gray-500">N° {{ $vente->numero_vente }} — {{ date('d/m/Y H:i', strtotime($vente->date_vente)) }}</p>
                    </div>
                    <div class="flex gap-3">
                        @php
                            $user = auth()->user();
                            $isAuthorized = $user && ($user->isAdmin() || $user->isSuperAdmin() || $user->isResponsable());
                        @endphp
                        
                        @if($isAuthorized)
                        <a href="{{ route('ventes.edit', $vente) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-tr from-yellow-400 to-yellow-500 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                            <i class="fas fa-edit"></i>
                            Modifier
                        </a>
                        @endif
                        
                        <a href="{{ route('ventes.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-indigo-200 text-indigo-600 font-semibold shadow-sm hover:bg-indigo-50 transition-all duration-200">
                            <i class="fas fa-arrow-left"></i>
                            Retour
                        </a>
                        
                        <!-- Boutons d'impression accessibles à tous les utilisateurs, y compris les caissiers -->
                        <a href="{{ route('ventes.facture', $vente) }}" target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-tr from-blue-600 to-blue-400 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                            <i class="fas fa-file-invoice"></i>
                            Facture
                        </a>
                        <a href="{{ route('ventes.recu', $vente) }}" target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-tr from-gray-800 to-gray-600 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                            <i class="fas fa-receipt"></i>
                            Ticket
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Infos principales vente -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-user text-indigo-500"></i>
                            Informations Générales
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-2">
                                <div class="text-sm font-medium text-gray-500">Client</div>
                                <div class="text-lg font-semibold text-gray-900">{{ $vente->client->nom }} {{ $vente->client->prenom }}</div>
                                <div class="text-sm text-gray-500">Type :
                                    @if($vente->type_vente == 'sur_place')
                                        <span class="inline-block px-2 py-0.5 rounded bg-green-100 text-green-800 text-xs">Sur place</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs">Livraison</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="text-sm font-medium text-gray-500">Vendeur</div>
                                <div class="text-lg font-semibold text-gray-900">
                                    @if($vente->employe)
                                        {{ $vente->employe->nom }} {{ $vente->employe->prenom ?? '' }}
                                    @else
                                        <span class="text-gray-500 italic">Non assigné</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">Statut :
                                    @if($vente->statut == 'terminee')
                                        <span class="inline-block px-2 py-0.5 rounded bg-green-100 text-green-800 text-xs">Terminée</span>
                                    @elseif($vente->statut == 'en_attente')
                                        <span class="inline-block px-2 py-0.5 rounded bg-yellow-100 text-yellow-800 text-xs">En attente</span>
                                    @elseif($vente->statut == 'annulee')
                                        <span class="inline-block px-2 py-0.5 rounded bg-red-100 text-red-800 text-xs">Annulée</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">Notes : <span class="text-gray-700">{{ $vente->notes ?: 'Aucune note' }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau des produits -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-boxes text-indigo-500"></i>
                            Produits achetés
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Produit</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Prix unitaire</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Promo</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Quantité</th>
                                        <th class="px-4 py-2 text-right font-semibold text-gray-600">Sous-total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @php
                                        $totalPromos = 0;
                                    @endphp
                                    @foreach($vente->details as $detail)
                                    @php
                                        $prixPromo = $detail->produit->prix_promo ?? $detail->prix_unitaire;
                                        $estEnPromo = $prixPromo < $detail->prix_unitaire;
                                        if ($estEnPromo) {
                                            $totalPromos += ($detail->prix_unitaire - $prixPromo) * $detail->quantite;
                                        }
                                    @endphp
                                    <tr class="@if($estEnPromo) bg-yellow-50 @endif">
                                        <td class="px-4 py-2">
                                            <div class="flex items-center gap-2">
                                                {{ $detail->produit->nom }}
                                                @if($estEnPromo)
                                                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs rounded-full">
                                                        <i class="fas fa-tag mr-1"></i>Promo
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-2">
                                            <div class="flex flex-col items-start">
                                                <span @if($estEnPromo) class="line-through text-gray-400" @endif>
                                                    {{ number_format($detail->prix_unitaire, 0, ',', ' ') }} F
                                                </span>
                                                @if($estEnPromo)
                                                    <span class="text-green-600 font-bold">
                                                        {{ number_format($prixPromo, 0, ',', ' ') }} F
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($estEnPromo)
                                                <span class="text-green-600 font-bold">
                                                    -{{ number_format($detail->prix_unitaire - $prixPromo, 0, ',', ' ') }} F
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">{{ $detail->quantite }}</td>
                                        <td class="px-4 py-2 text-right">
                                            @php
                                                $sousTotal = ($estEnPromo ? $prixPromo : $detail->prix_unitaire) * $detail->quantite;
                                            @endphp
                                            {{ number_format($sousTotal, 0, ',', ' ') }} F
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    @if($totalPromos > 0)
                                    <tr class="bg-gray-50">
                                        <td colspan="4" class="px-4 py-2 text-right font-semibold text-green-700">Économies promotionnelles :</td>
                                        <td class="px-4 py-2 text-right font-bold text-green-700">-{{ number_format($totalPromos, 0, ',', ' ') }} F</td>
                                    </tr>
                                    @endif
                                    
                                    @php
                                        $totalRemises = 0;
                                        foreach($vente->remises as $remise) {
                                            $totalRemises += $remise->montant_remise;
                                        }
                                    @endphp
                                    
                                    @if($vente->remises->count() > 0)
                                    <tr class="bg-indigo-50">
                                        <td colspan="4" class="px-4 py-2 text-right font-semibold text-indigo-700">Remises appliquées :</td>
                                        <td class="px-4 py-2 text-right font-bold text-indigo-700">-{{ number_format($totalRemises, 0, ',', ' ') }} F</td>
                                    </tr>
                                    @foreach($vente->remises as $remise)
                                    <tr class="bg-indigo-50/60">
                                        <td colspan="2" class="px-4 py-1 text-right text-sm text-indigo-600">
                                            {{ $remise->type_remise === 'pourcentage' ? 'Remise ' . number_format($remise->valeur_remise, 2, ',', ' ') . '%' : 'Remise fixe' }}
                                            @if($remise->code_remise)
                                                (code: {{ $remise->code_remise }})
                                            @endif
                                        </td>
                                        <td colspan="2" class="px-4 py-1 text-right text-sm text-indigo-600">
                                            {{ $remise->description ?: 'Remise' }}
                                        </td>
                                        <td class="px-4 py-1 text-right text-sm font-medium text-indigo-700">
                                            -{{ number_format($remise->montant_remise, 0, ',', ' ') }} F
                                        </td>
                                    </tr>
                                    @endforeach
                                    @elseif(auth()->user() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                                    <tr class="bg-indigo-50">
                                        <td colspan="5" class="px-4 py-2 text-center">
                                            <a href="{{ route('remises.create-for-vente', $vente) }}" class="text-indigo-600 hover:text-indigo-900 inline-flex items-center gap-1">
                                                <i class="fas fa-plus-circle"></i> Ajouter une remise
                                            </a>
                                        </td>
                                    </tr>
                                    @endif

                                    <tr class="bg-gray-50">
                                        <td colspan="4" class="px-4 py-2 text-right font-semibold">Montant Total :</td>
                                        <td class="px-4 py-2 text-right font-bold text-indigo-700">
                                            @php
                                                $totalFinal = 0;
                                                foreach($vente->details as $detail) {
                                                    $prixPromo = $detail->produit->prix_promo ?? $detail->prix_unitaire;
                                                    $estEnPromo = $prixPromo < $detail->prix_unitaire;
                                                    $totalFinal += ($estEnPromo ? $prixPromo : $detail->prix_unitaire) * $detail->quantite;
                                                }
                                                // Soustraire le montant total des remises
                                                $totalFinal -= $totalRemises;
                                            @endphp
                                            {{ number_format($totalFinal, 0, ',', ' ') }} F
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne latérale paiement -->
            <div class="space-y-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-money-bill text-indigo-500"></i>
                            Paiement
                        </h3>
                        <div class="flex flex-col gap-2 mb-4">
                            @php
                                $totalInitial = 0;
                                $totalPromos = 0;
                                $totalRemises = 0;
                                $totalFinal = 0;
                                
                                // Calcul du total initial et des promotions
                                foreach($vente->details as $detail) {
                                    $prixUnitaire = $detail->prix_unitaire;
                                    $prixPromo = $detail->produit->prix_promo ?? $prixUnitaire;
                                    $estEnPromo = $prixPromo < $prixUnitaire;
                                    $totalInitial += $prixUnitaire * $detail->quantite;
                                    $totalFinal += ($estEnPromo ? $prixPromo : $prixUnitaire) * $detail->quantite;
                                    if ($estEnPromo) {
                                        $totalPromos += ($prixUnitaire - $prixPromo) * $detail->quantite;
                                    }
                                }
                                
                                // Calcul des remises
                                foreach($vente->remises as $remise) {
                                    $totalRemises += $remise->montant_remise;
                                }
                                
                                // Application des remises au total final
                                $totalFinal -= $totalRemises;
                                
                                $montantPaye = $vente->montant_paye;
                                $montantRestant = max(0, $totalFinal - $montantPaye);
                                $monnaieRendue = max(0, $montantPaye - $totalFinal);
                            @endphp
                            <div class="flex justify-between text-sm">
                                <span>Montant total initial :</span>
                                <span class="font-medium">{{ number_format($totalInitial, 0, ',', ' ') }} F</span>
                            </div>
                            @if($totalPromos > 0)
                            <div class="flex justify-between text-sm">
                                <span>Économies promotionnelles :</span>
                                <span class="font-medium text-green-700">-{{ number_format($totalPromos, 0, ',', ' ') }} F</span>
                            </div>
                            @endif
                            
                            @if($totalRemises > 0)
                            <div class="flex justify-between text-sm">
                                <span>Remises appliquées :</span>
                                <span class="font-medium text-indigo-700">-{{ number_format($totalRemises, 0, ',', ' ') }} F</span>
                            </div>
                            @endif
                            
                            <div class="flex justify-between text-sm font-bold border-t border-gray-200 pt-2 mt-1">
                                <span>Montant total final :</span>
                                <span class="font-medium text-indigo-700">{{ number_format($totalFinal, 0, ',', ' ') }} F</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Montant payé :</span>
                                <span class="font-medium text-green-700">{{ number_format($montantPaye, 0, ',', ' ') }} F</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Restant à payer :</span>
                                <span class="font-medium @if($montantRestant > 0) text-red-700 @else text-green-700 @endif">{{ number_format($montantRestant, 0, ',', ' ') }} F</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Monnaie rendue :</span>
                                <span class="font-medium text-blue-700">{{ number_format($monnaieRendue, 0, ',', ' ') }} F</span>
                            </div>
                        </div>
                        @if($montantRestant > 0)
                        <div class="mt-4 text-center">
                            <button type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200" data-modal-target="#paiementModal">
                                <i class="fas fa-money-bill"></i> Enregistrer un paiement
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal paiement (simulé Tailwind, pas Bootstrap!) -->
        @if($montantRestant > 0)
        <div id="paiementModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <button type="button" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600" onclick="document.getElementById('paiementModal').classList.add('hidden')">
                    <i class="fas fa-times"></i>
                </button>
                <form action="{{ route('paiements.paiements.store', $vente) }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="vente_id" value="{{ $vente->id }}">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Nouveau paiement</h3>
                    <div>
                        <label for="montant" class="block text-sm font-medium text-gray-700">Montant</label>
                        <input type="number" class="mt-1 block w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" id="montant" name="montant" step="0.01" max="{{ $montantRestant }}" required>
                        <div class="text-xs text-gray-400">Montant restant à payer: {{ number_format($montantRestant, 0, ',', ' ') }} F</div>
                    </div>
                    <div>
                        <label for="methode_paiement" class="block text-sm font-medium text-gray-700">Méthode de paiement</label>
                        <select class="mt-1 block w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" id="methode_paiement" name="methode_paiement" required>
                            <option value="especes">Espèces</option>
                            <option value="carte">Carte bancaire</option>
                            <option value="mobile">Paiement mobile</option>
                            <option value="cheque">Chèque</option>
                        </select>
                    </div>
                    <div>
                        <label for="reference" class="block text-sm font-medium text-gray-700">Référence (facultatif)</label>
                        <input type="text" class="mt-1 block w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" id="reference" name="reference">
                        <div class="text-xs text-gray-400">Numéro de transaction, chèque, etc.</div>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300" onclick="document.getElementById('paiementModal').classList.add('hidden')">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
        <script>
            document.querySelectorAll('[data-modal-target="#paiementModal"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('paiementModal').classList.remove('hidden');
                });
            });
        </script>
        @endif
    </div>
</div>
@endsection
