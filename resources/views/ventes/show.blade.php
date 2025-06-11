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
                        <a href="{{ route('ventes.edit', $vente) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-tr from-yellow-400 to-yellow-500 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                            <i class="fas fa-edit"></i>
                            Modifier
                        </a>
                        <a href="{{ route('ventes.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-indigo-200 text-indigo-600 font-semibold shadow-sm hover:bg-indigo-50 transition-all duration-200">
                            <i class="fas fa-arrow-left"></i>
                            Retour
                        </a>
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
                                <div class="text-lg font-semibold text-gray-900">{{ $vente->employe->nom }} {{ $vente->employe->prenom }}</div>
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
                                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Quantité</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Remise</th>
                                        <th class="px-4 py-2 text-right font-semibold text-gray-600">Sous-total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($vente->details as $detail)
                                    <tr>
                                        <td class="px-4 py-2">{{ $detail->produit->nom }}</td>
                                        <td class="px-4 py-2">{{ number_format($detail->prix_unitaire, 2, ',', ' ') }} F</td>
                                        <td class="px-4 py-2">{{ $detail->quantite }}</td>
                                        <td class="px-4 py-2">
                                            @if($detail->remise > 0)
                                                <span class="inline-block px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 text-xs">{{ $detail->remise }}%</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-right">{{ number_format($detail->sous_total, 2, ',', ' ') }} F</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50">
                                        <td colspan="4" class="px-4 py-2 text-right font-semibold">Montant Total :</td>
                                        <td class="px-4 py-2 text-right font-bold text-indigo-700">{{ number_format($vente->montant_total, 2, ',', ' ') }} F</td>
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
                            <div class="flex justify-between text-sm">
                                <span>Montant total :</span>
                                <span class="font-medium">{{ number_format($vente->montant_total, 2, ',', ' ') }} F</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Montant payé :</span>
                                <span class="font-medium text-green-700">{{ number_format($vente->montant_paye, 2, ',', ' ') }} F</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Restant à payer :</span>
                                <span class="font-medium @if($vente->montant_restant > 0) text-red-700 @else text-green-700 @endif">{{ number_format($vente->montant_restant, 2, ',', ' ') }} F</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Monnaie rendue :</span>
                                <span class="font-medium text-blue-700">
                                    {{ number_format(max(0, $vente->montant_paye - $vente->montant_total), 2, ',', ' ') }} F
                                </span>
                            </div>
                        </div>
                        @if($vente->montant_restant > 0)
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
        @if($vente->montant_restant > 0)
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
                        <input type="number" class="mt-1 block w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" id="montant" name="montant" step="0.01" max="{{ $vente->montant_restant }}" required>
                        <div class="text-xs text-gray-400">Montant restant à payer: {{ number_format($vente->montant_restant, 2, ',', ' ') }} F</div>
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
