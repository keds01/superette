@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <i class="fas fa-chart-bar text-indigo-500"></i>
                Statistiques des Ventes
            </h2>
            <div class="flex space-x-4">
                <button onclick="window.print()" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtres -->
            <div class="mb-8 rounded-xl bg-white p-6 shadow-sm">
                <form action="{{ route('statistiques.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-gray-700">Date début</label>
                        <input type="date" name="date_debut" id="date_debut" value="{{ request('date_debut') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-gray-700">Date fin</label>
                        <input type="date" name="date_fin" id="date_fin" value="{{ request('date_fin') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="periode" class="block text-sm font-medium text-gray-700">Période</label>
                        <select name="periode" id="periode"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="jour" {{ request('periode') === 'jour' ? 'selected' : '' }}>Journalier</option>
                            <option value="semaine" {{ request('periode') === 'semaine' ? 'selected' : '' }}>Hebdomadaire</option>
                            <option value="mois" {{ request('periode') === 'mois' ? 'selected' : '' }}>Mensuel</option>
                            <option value="annee" {{ request('periode') === 'annee' ? 'selected' : '' }}>Annuel</option>
                        </select>
                    </div>
                    <div class="md:col-span-1 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <i class="fas fa-search mr-2"></i>Filtrer
                        </button>
                    </div>
                </form>
            </div>

            @if($chiffreAffaires == 0 && $nombreVentes == 0 && $modesPaiement->isEmpty() && $topProduits->isEmpty())
                <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 p-6 mb-8 text-center rounded-xl shadow animate-fade-in-down">
                    <i class="fas fa-exclamation-triangle text-2xl mr-2"></i>
                    <span>Aucune donnée trouvée pour la période sélectionnée.<br>
                    Essayez d'élargir la période de recherche ou d'ajouter une vente pour voir apparaître les statistiques.</span>
                </div>
            @endif

            <!-- Cartes de statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
                <!-- Chiffre d'affaires -->
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-6 flex items-center gap-4">
                    <div class="bg-indigo-100 text-indigo-600 p-3 rounded-full">
                        <i class="fas fa-money-bill-wave text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Chiffre d'affaires</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($chiffreAffaires, 0, ',', ' ') }} FCFA</p>
                        @if($chiffreAffaires == 0)
                            <span class="text-sm text-gray-400">Aucune donnée</span>
                        @endif
                    </div>
                </div>
                <!-- Nombre de ventes -->
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-6 flex items-center gap-4">
                    <div class="bg-green-100 text-green-600 p-3 rounded-full">
                        <i class="fas fa-shopping-cart text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Nombre de ventes</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ $nombreVentes }}</p>
                        @if($nombreVentes == 0)
                            <span class="text-sm text-gray-400">Aucune donnée</span>
                        @endif
                    </div>
                </div>
                <!-- Panier moyen -->
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-6 flex items-center gap-4">
                    <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Panier moyen</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($panierMoyen, 0, ',', ' ') }} FCFA</p>
                        @if($panierMoyen == 0)
                            <span class="text-sm text-gray-400">Aucune donnée</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Graphiques -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Évolution des ventes -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2"><i class="fas fa-chart-line text-indigo-400"></i> Évolution des ventes</h3>
                    @if($evolutionVentes->isEmpty())
                        <div class="text-gray-400">Aucune donnée</div>
                    @else
                        <canvas id="evolutionVentes" height="300"></canvas>
                    @endif
                </div>
                <!-- Répartition des modes de paiement -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2"><i class="fas fa-coins text-green-400"></i> Répartition des modes de paiement</h3>
                    @if($modesPaiement->isEmpty())
                        <div class="text-gray-400">Aucune donnée</div>
                    @else
                        <canvas id="modesPaiement" height="300"></canvas>
                    @endif
                </div>
            </div>

            <!-- Top produits -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2"><i class="fas fa-crown text-yellow-400"></i> Top 10 des produits les plus vendus</h3>
                @if($topProduits->isEmpty())
                    <div class="text-gray-400">Aucune donnée</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité vendue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chiffre d'affaires</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($topProduits as $produit)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $produit->nom }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $produit->categorie_nom ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $produit->quantite_vendue }} {{ $produit->unite_vente }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ number_format($produit->chiffre_affaires, 0, ',', ' ') }} FCFA</div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Graphiques Chart.js
        const evolutionVentesCanvas = document.getElementById('evolutionVentes');
        const modesPaiementCanvas = document.getElementById('modesPaiement');
        if (evolutionVentesCanvas && {!! json_encode($evolutionVentes->isNotEmpty()) !!}) {
            const evolutionVentesCtx = evolutionVentesCanvas.getContext('2d');
            new Chart(evolutionVentesCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($evolutionVentes->pluck('date')) !!},
                    datasets: [{
                        label: "Chiffre d'affaires",
                        data: {!! json_encode($evolutionVentes->pluck('montant')) !!},
                        borderColor: 'rgb(99, 102, 241)',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.2,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' FCFA';
                                }
                            }
                        }
                    }
                }
            });
        }
        if (modesPaiementCanvas && {!! json_encode($modesPaiement->isNotEmpty()) !!}) {
            const modesPaiementCtx = modesPaiementCanvas.getContext('2d');
            new Chart(modesPaiementCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($modePaiementLabels) !!},
                    datasets: [{
                        data: {!! json_encode($modesPaiement->pluck('montant')) !!},
                        backgroundColor: [
                            'rgb(34, 197, 94)',
                            'rgb(59, 130, 246)',
                            'rgb(168, 85, 247)',
                            'rgb(253, 224, 71)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }
    </script>
    @endpush
@endsection
