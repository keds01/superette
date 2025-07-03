@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg flex items-center gap-3">
                    <i class="fas fa-chart-bar text-indigo-400"></i> Statistiques des ventes
                </h1>
                <p class="mt-2 text-lg text-gray-500">Analyse détaillée des ventes, paiements et produits</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-blue-500 to-teal-400 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-arrow-left"></i> Retour au dashboard
                </a>
                <button onclick="window.print()" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 mb-8">
            <form action="{{ route('statistiques.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                <div>
                    <label for="date_debut" class="block text-sm font-medium text-indigo-700">Date début</label>
                    <input type="date" name="date_debut" id="date_debut" value="{{ request('date_debut') }}"
                        class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="date_fin" class="block text-sm font-medium text-indigo-700">Date fin</label>
                    <input type="date" name="date_fin" id="date_fin" value="{{ request('date_fin') }}"
                        class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="periode" class="block text-sm font-medium text-indigo-700">Période</label>
                    <select name="periode" id="periode"
                        class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="jour" {{ request('periode') === 'jour' ? 'selected' : '' }}>Journalier</option>
                        <option value="semaine" {{ request('periode') === 'semaine' ? 'selected' : '' }}>Hebdomadaire</option>
                        <option value="mois" {{ request('periode') === 'mois' ? 'selected' : '' }}>Mensuel</option>
                        <option value="annee" {{ request('periode') === 'annee' ? 'selected' : '' }}>Annuel</option>
                    </select>
                </div>
                <div class="md:col-span-1 flex justify-end gap-3">
                    <a href="{{ route('statistiques.index') }}" class="px-6 py-3 rounded-xl bg-gray-200 text-gray-700 font-bold hover:bg-gray-300 transition-colors">
                        <i class="fas fa-undo mr-2"></i>Réinitialiser
                    </a>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filtrer
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
            <div class="bg-gradient-to-tr from-indigo-50 to-blue-100 border border-indigo-200 rounded-2xl shadow-xl p-6 flex items-center gap-4">
                <div class="bg-indigo-100 text-indigo-600 p-3 rounded-full">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-indigo-700">Chiffre d'affaires</h3>
                    <p class="text-2xl font-bold text-indigo-900">{{ number_format($chiffreAffaires, 0, ',', ' ') }} FCFA</p>
                    @if($chiffreAffaires == 0)
                        <span class="text-sm text-gray-400">Aucune donnée</span>
                    @endif
                </div>
            </div>
            <!-- Nombre de ventes -->
            <div class="bg-gradient-to-tr from-green-50 to-green-100 border border-green-200 rounded-2xl shadow-xl p-6 flex items-center gap-4">
                <div class="bg-green-100 text-green-600 p-3 rounded-full">
                    <i class="fas fa-shopping-cart text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-green-700">Nombre de ventes</h3>
                    <p class="text-2xl font-bold text-green-900">{{ $nombreVentes }}</p>
                    @if($nombreVentes == 0)
                        <span class="text-sm text-gray-400">Aucune donnée</span>
                    @endif
                </div>
            </div>
            <!-- Panier moyen -->
            <div class="bg-gradient-to-tr from-purple-50 to-purple-100 border border-purple-200 rounded-2xl shadow-xl p-6 flex items-center gap-4">
                <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-purple-700">Panier moyen</h3>
                    <p class="text-2xl font-bold text-purple-900">{{ number_format($panierMoyen, 0, ',', ' ') }} FCFA</p>
                    @if($panierMoyen == 0)
                        <span class="text-sm text-gray-400">Aucune donnée</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Évolution des ventes -->
            <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-xl p-6">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-indigo-700"><i class="fas fa-chart-line text-indigo-400"></i> Évolution des ventes</h3>
                @if($evolutionVentes->isEmpty())
                    <div class="text-gray-400">Aucune donnée</div>
                @else
                    <canvas id="evolutionVentes" height="300"></canvas>
                @endif
            </div>
            <!-- Répartition des modes de paiement -->
            <div class="bg-white/60 backdrop-blur-xl border border-green-100 rounded-2xl shadow-xl p-6">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-green-700"><i class="fas fa-coins text-green-400"></i> Répartition des modes de paiement</h3>
                @if($modesPaiement->isEmpty())
                    <div class="text-gray-400">Aucune donnée</div>
                @else
                    <canvas id="modesPaiement" height="300"></canvas>
                @endif
            </div>
        </div>

        <!-- Top produits -->
        <div class="bg-white/60 backdrop-blur-xl border border-yellow-100 rounded-2xl shadow-xl p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-yellow-700"><i class="fas fa-crown text-yellow-400"></i> Top 10 des produits les plus vendus</h3>
            @if($topProduits->isEmpty())
                <div class="text-gray-400">Aucune donnée</div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-yellow-100">
                        <thead class="bg-yellow-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-yellow-700 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-yellow-700 uppercase tracking-wider">Catégorie</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-yellow-700 uppercase tracking-wider">Quantité vendue</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-yellow-700 uppercase tracking-wider">Chiffre d'affaires</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-yellow-50">
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
