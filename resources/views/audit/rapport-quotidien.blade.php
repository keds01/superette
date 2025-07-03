@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Rapport quotidien d'audit</h1>
                <p class="mt-2 text-lg text-gray-500">Vue synthétique et détaillée de l'activité de la journée.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('audit.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-blue-500 to-teal-400 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                </a>
                <button id="print-report" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-gray-400 to-indigo-400 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-print"></i> Imprimer
                </button>
                <a href="{{ route('audit.rapport-quotidien', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-file-pdf"></i> Exporter PDF
                </a>
            </div>
        </div>

        <!-- Filtres / Sélecteur de date -->
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 mb-8">
            <form action="{{ route('audit.rapport-quotidien') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="w-64">
                    <label for="date" class="block text-sm font-medium text-indigo-700">Date du rapport</label>
                    <input type="date" name="date" id="date" value="{{ request('date', now()->format('Y-m-d')) }}"
                           class="mt-1 block w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors">Générer</button>
                </div>
            </form>
        </div>

        @php
            $aucuneDonnee = (
                ($rapportQuotidien['ventes']['total'] ?? 0) === 0 &&
                ($rapportQuotidien['paiements']['total'] ?? 0) === 0 &&
                ($rapportQuotidien['activites']['total'] ?? 0) === 0 &&
                ($rapportQuotidien['anomalies']['total'] ?? 0) === 0 &&
                (count($rapportQuotidien['produits']['top_vendus'] ?? []) === 0)
            );
        @endphp
        @if($aucuneDonnee)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-8 rounded-xl shadow">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-400 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-yellow-800">Aucune donnée pour cette journée</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Aucune vente, paiement, activité ou anomalie n'a été enregistrée pour la date sélectionnée.</p>
                            <p>Essayez de choisir une autre date ou d'effectuer des opérations dans le système pour générer des données.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div id="printable-report" @if($aucuneDonnee) style="display:none;" @endif>
            <!-- En-tête du rapport -->
            <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl mb-8 p-6 flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-indigo-800">Rapport d'activité du {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">Généré le {{ now()->format('d/m/Y à H:i') }}</p>
                </div>
                <div>
                    <span class="px-4 py-2 inline-flex text-base leading-5 font-semibold rounded-full {{ $rapportQuotidien['ventes']['montant_total'] > $rapportQuotidien['objectif_journalier'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $rapportQuotidien['ventes']['montant_total'] > $rapportQuotidien['objectif_journalier'] ? 'Objectif atteint' : 'Objectif non atteint' }}
                    </span>
                </div>
            </div>

            <!-- Résumé des ventes sous forme de cartes -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-tr from-indigo-50 to-blue-100 border border-indigo-200 rounded-2xl shadow-xl p-6 flex flex-col items-center">
                    <div class="text-sm text-indigo-700 font-medium">Nombre de ventes</div>
                    <div class="text-3xl font-extrabold text-indigo-900 mt-2">{{ $rapportQuotidien['ventes']['total'] }}</div>
                </div>
                <div class="bg-gradient-to-tr from-green-50 to-green-100 border border-green-200 rounded-2xl shadow-xl p-6 flex flex-col items-center">
                    <div class="text-sm text-green-700 font-medium">Chiffre d'affaires</div>
                    <div class="text-3xl font-extrabold text-green-900 mt-2">{{ number_format($rapportQuotidien['ventes']['montant_total'], 0, ',', ' ') }} FCFA</div>
                </div>
                <div class="bg-gradient-to-tr from-yellow-50 to-yellow-100 border border-yellow-200 rounded-2xl shadow-xl p-6 flex flex-col items-center">
                    <div class="text-sm text-yellow-700 font-medium">Panier moyen</div>
                    <div class="text-3xl font-extrabold text-yellow-900 mt-2">{{ number_format($rapportQuotidien['ventes']['panier_moyen'], 0, ',', ' ') }} FCFA</div>
                </div>
            </div>

            <!-- Graphique des ventes par heure -->
            <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
                <h4 class="text-lg font-bold text-indigo-700 mb-4">Répartition des ventes par heure</h4>
                <div class="h-64">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Top produits vendus -->
            <div class="grid grid-cols-1 gap-8 mb-8">
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-indigo-700 mb-4">Top produits vendus</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-indigo-100">
                            <thead class="bg-indigo-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Produit</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-indigo-700 uppercase tracking-wider">Quantité</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-indigo-700 uppercase tracking-wider">Montant</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-indigo-50">
                                @foreach($rapportQuotidien['produits']['top_vendus'] as $produit)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-900">{{ $produit['nom'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ $produit['quantite'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ number_format($produit['montant'], 0, ',', ' ') }} FCFA</td>
                                </tr>
                                @endforeach
                                @if(count($rapportQuotidien['produits']['top_vendus']) === 0)
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Aucune vente enregistrée</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Activités et anomalies -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Activités -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-indigo-700">Activités enregistrées</h3>
                        <a href="{{ route('audit.journal', ['date_debut' => $date, 'date_fin' => $date]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-indigo-100 text-indigo-700 text-xs font-semibold shadow hover:bg-indigo-200 transition">
                            <i class="fas fa-list"></i> Voir le détail
                        </a>
                    </div>
                    <div class="h-64">
                        <canvas id="activitiesChart"></canvas>
                    </div>
                </div>
                <!-- Anomalies -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-red-700">Anomalies détectées</h3>
                        <a href="{{ route('audit.anomalies', ['date_debut' => $date, 'date_fin' => $date]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-red-100 text-red-700 text-xs font-semibold shadow hover:bg-red-200 transition">
                            <i class="fas fa-bug"></i> Voir tout
                        </a>
                    </div>
                    <ul class="divide-y divide-red-100">
                        @forelse(is_array($rapportQuotidien['anomalies']['liste']) ? $rapportQuotidien['anomalies']['liste'] : [] as $anomalie)
                        <li>
                            <a href="{{ route('audit.detail-anomalie', ['id' => $anomalie['id']]) }}" class="block hover:bg-red-50 px-4 py-4 rounded transition">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-red-700 truncate">
                                        {{ ucfirst(str_replace('_', ' ', $anomalie['type'])) }}
                                    </p>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $anomalie['severite'] === 'haute' ? 'bg-red-100 text-red-800' : 
                                          ($anomalie['severite'] === 'moyenne' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($anomalie['severite']) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-2 text-xs text-gray-500">{{ $anomalie['description'] ?? '' }}</div>
                            </a>
                        </li>
                        @empty
                        <li>
                            <div class="px-4 py-4 text-center text-sm text-gray-500">Aucune anomalie détectée</div>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Impression du rapport
    document.getElementById('print-report').addEventListener('click', function() {
        window.print();
    });

    // Initialisation des graphiques avec Chart.js
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique des ventes par heure
        const salesChartCtx = document.getElementById('salesChart');
        if (salesChartCtx) {
            const heures = @json(array_keys($rapportQuotidien['ventes']['par_heure']));
            const ventesCount = @json(array_column($rapportQuotidien['ventes']['par_heure'], 'count'));
            const ventesMontant = @json(array_column($rapportQuotidien['ventes']['par_heure'], 'montant'));
            
            new Chart(salesChartCtx, {
                type: 'bar',
                data: {
                    labels: heures.map(h => `${h}h`),
                    datasets: [
                        {
                            label: 'Nombre de ventes',
                            data: ventesCount,
                            backgroundColor: 'rgba(99, 102, 241, 0.5)',
                            borderColor: 'rgb(79, 70, 229)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Montant (FCFA)',
                            data: ventesMontant,
                            type: 'line',
                            backgroundColor: 'rgba(16, 185, 129, 0.2)',
                            borderColor: 'rgb(5, 150, 105)',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.4,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nombre de ventes'
                            },
                            ticks: {
                                precision: 0
                            }
                        },
                        y1: {
                            position: 'right',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Montant (FCFA)'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        }

        // Graphique des activités
        const activitiesChartCtx = document.getElementById('activitiesChart');
        if (activitiesChartCtx) {
            const types = @json(array_keys($rapportQuotidien['activites']['par_type'] ?? []));
            const counts = @json(array_values($rapportQuotidien['activites']['par_type'] ?? []));
            
            new Chart(activitiesChartCtx, {
                type: 'bar',
                data: {
                    labels: types.map(t => t.charAt(0).toUpperCase() + t.slice(1).replace('_', ' ')),
                    datasets: [{
                        label: 'Nombre d\'activités',
                        data: counts,
                        backgroundColor: 'rgba(139, 92, 246, 0.7)',
                        borderColor: 'rgb(124, 58, 237)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    });
</script>
@endpush