@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Rapport quotidien d'audit</h1>
            <div>
                <button id="print-report" class="btn-secondary mr-2">
                    <i class="fas fa-print mr-2"></i>Imprimer
                </button>
                <a href="{{ route('audit.rapport-quotidien', ['format' => 'pdf']) }}" class="btn-primary">
                    <i class="fas fa-file-pdf mr-2"></i>Exporter PDF
                </a>
            </div>
        </div>
        
        <!-- Date picker -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <form action="{{ route('audit.rapport-quotidien') }}" method="GET" class="flex items-center space-x-4">
                    <div class="w-64">
                        <label for="date" class="block text-sm font-medium text-gray-700">Date du rapport</label>
                        <input type="date" name="date" id="date" value="{{ request('date', now()->format('Y-m-d')) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div class="pt-5">
                        <button type="submit" class="btn-primary">
                            Générer
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div id="printable-report">
            <!-- En-tête du rapport -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Rapport d'activité du {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Généré le {{ now()->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            {{ $rapportQuotidien['ventes']['montant_total'] > $rapportQuotidien['objectif_journalier'] ? 'Objectif atteint' : 'Objectif non atteint' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Résumé des ventes -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Résumé des ventes</h3>
                </div>
                <div class="border-t border-gray-200">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-6">
                        <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dt class="text-sm font-medium text-gray-500 truncate">Nombre de ventes</dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $rapportQuotidien['ventes']['total'] }}</dd>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dt class="text-sm font-medium text-gray-500 truncate">Chiffre d'affaires</dt>
                                <dd class="mt-1 text-3xl font-semibold text-green-600">
                                    {{ number_format($rapportQuotidien['ventes']['montant_total'], 0, ',', ' ') }} FCFA
                                </dd>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dt class="text-sm font-medium text-gray-500 truncate">Panier moyen</dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                    {{ number_format($rapportQuotidien['ventes']['panier_moyen'], 0, ',', ' ') }} FCFA
                                </dd>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dt class="text-sm font-medium text-gray-500 truncate">Ventes annulées</dt>
                                <dd class="mt-1 text-3xl font-semibold text-red-600">
                                    {{ $rapportQuotidien['ventes']['annulees']['total'] }}
                                </dd>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Graphique des ventes par heure -->
                    <div class="px-6 py-4">
                        <h4 class="text-base font-medium text-gray-900 mb-4">Répartition des ventes par heure</h4>
                        <div class="h-64">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Détail des paiements -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Méthodes de paiement -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Méthodes de paiement</h3>
                    </div>
                    <div class="p-6">
                        <div class="h-64">
                            <canvas id="paymentMethodsChart"></canvas>
                        </div>
                    </div>
                    <div class="px-6 pb-6">
                        <div class="mt-4">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Méthode</th>
                                        <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Transactions</th>
                                        <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($rapportQuotidien['paiements']['par_methode'] as $methode => $data)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ ucfirst($methode) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ $data['count'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ number_format($data['total'], 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $rapportQuotidien['paiements']['total_transactions'] }}</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ number_format($rapportQuotidien['paiements']['total_montant'], 0, ',', ' ') }} FCFA</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Top produits vendus -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Top produits vendus</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($rapportQuotidien['produits']['top_vendus'] as $produit)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $produit['nom'] }}</td>
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
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Activités -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Activités enregistrées</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-sm text-gray-700">Total: <span class="font-medium">{{ $rapportQuotidien['activites']['total'] }}</span> activités</p>
                            <a href="{{ route('audit.journal', ['date_debut' => $date, 'date_fin' => $date]) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                Voir le détail
                            </a>
                        </div>
                        
                        <div class="h-64">
                            <canvas id="activitiesChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Anomalies -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Anomalies détectées</h3>
                        <a href="{{ route('audit.anomalies', ['date_debut' => $date, 'date_fin' => $date]) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                            Voir tout
                        </a>
                    </div>
                    <div class="overflow-hidden">
                        <ul class="divide-y divide-gray-200">
                            @forelse($rapportQuotidien['anomalies'] as $anomalie)
                            <li>
                                <a href="{{ route('audit.detail-anomalie', ['id' => $anomalie['id']]) }}" class="block hover:bg-gray-50">
                                    <div class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-indigo-600 truncate">
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
                                        <div class="mt-2 sm:flex sm:justify-between">
                                            <div class="sm:flex">
                                                <p class="flex items-center text-sm text-gray-500">
                                                    {{ $anomalie['message'] }}
                                                </p>
                                            </div>
                                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                                <p>
                                                    {{ \Carbon\Carbon::parse($anomalie['created_at'])->format('H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @empty
                            <li class="px-4 py-6 text-center text-sm text-gray-500">
                                Aucune anomalie détectée pour cette journée
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Conclusion et recommandations -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Résumé et recommandations</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="prose max-w-none">
                        <p>Résumé de la journée du {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} :</p>
                        <ul>
                            <li>Chiffre d'affaires: <strong>{{ number_format($rapportQuotidien['ventes']['montant_total'], 0, ',', ' ') }} FCFA</strong> 
                                ({{ $rapportQuotidien['ventes']['montant_total'] > $rapportQuotidien['objectif_journalier'] ? 'Au-dessus' : 'En-dessous' }} de l'objectif 
                                de {{ number_format($rapportQuotidien['objectif_journalier'], 0, ',', ' ') }} FCFA)</li>
                            <li>{{ $rapportQuotidien['ventes']['total'] }} ventes enregistrées avec un panier moyen de 
                                {{ number_format($rapportQuotidien['ventes']['panier_moyen'], 0, ',', ' ') }} FCFA</li>
                            <li>{{ $rapportQuotidien['ventes']['annulees']['total'] }} ventes annulées pour un montant de 
                                {{ number_format($rapportQuotidien['ventes']['annulees']['montant_total'], 0, ',', ' ') }} FCFA</li>
                            <li>{{ count($rapportQuotidien['anomalies']) }} anomalies détectées</li>
                        </ul>
                        
                        @if(count($rapportQuotidien['recommandations']) > 0)
                        <p>Recommandations :</p>
                        <ul>
                            @foreach($rapportQuotidien['recommandations'] as $recommandation)
                            <li>{{ $recommandation }}</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuration des graphiques
        const salesChartCtx = document.getElementById('salesChart').getContext('2d');
        const paymentMethodsChartCtx = document.getElementById('paymentMethodsChart').getContext('2d');
        const activitiesChartCtx = document.getElementById('activitiesChart').getContext('2d');
        
        // Données pour le graphique des ventes par heure
        const salesChart = new Chart(salesChartCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($rapportQuotidien['ventes']['par_heure'])) !!},
                datasets: [{
                    label: 'Nombre de ventes',
                    data: {!! json_encode(array_column($rapportQuotidien['ventes']['par_heure'], 'count')) !!},
                    backgroundColor: 'rgba(79, 70, 229, 0.2)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1
                }, {
                    label: 'Montant (FCFA / 1000)',
                    data: {!! json_encode(array_map(function($item) { return $item['montant'] / 1000; }, $rapportQuotidien['ventes']['par_heure'])) !!},
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1,
                    yAxisID: 'y1'
                }]
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
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Montant (FCFA / 1000)'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
        
        // Données pour le graphique des méthodes de paiement
        const paymentMethods = {!! json_encode(array_keys($rapportQuotidien['paiements']['par_methode'])) !!};
        const paymentAmounts = {!! json_encode(array_column($rapportQuotidien['paiements']['par_methode'], 'total')) !!};
        const paymentCounts = {!! json_encode(array_column($rapportQuotidien['paiements']['par_methode'], 'count')) !!};
        
        const backgroundColors = [
            'rgba(79, 70, 229, 0.6)',
            'rgba(16, 185, 129, 0.6)',
            'rgba(245, 158, 11, 0.6)',
            'rgba(239, 68, 68, 0.6)',
            'rgba(107, 114, 128, 0.6)'
        ];
        
        const paymentMethodsChart = new Chart(paymentMethodsChartCtx, {
            type: 'pie',
            data: {
                labels: paymentMethods.map(method => method.charAt(0).toUpperCase() + method.slice(1)),
                datasets: [{
                    data: paymentAmounts,
                    backgroundColor: backgroundColors.slice(0, paymentMethods.length),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const count = paymentCounts[context.dataIndex];
                                const total = paymentAmounts.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${new Intl.NumberFormat('fr-FR').format(value)} FCFA (${percentage}%) - ${count} transaction(s)`;
                            }
                        }
                    }
                }
            }
        });
        
        // Données pour le graphique des activités
        const activitiesChart = new Chart(activitiesChartCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($rapportQuotidien['activites']['par_type'])) !!}.map(type => type.charAt(0).toUpperCase() + type.slice(1)),
                datasets: [{
                    data: {!! json_encode(array_values($rapportQuotidien['activites']['par_type'])) !!},
                    backgroundColor: backgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
        
        // Gestion de l'impression
        document.getElementById('print-report').addEventListener('click', function() {
            const printContents = document.getElementById('printable-report').innerHTML;
            const originalContents = document.body.innerHTML;
            
            document.body.innerHTML = `
                <div class="p-8">
                    <div class="text-center mb-8">
                        <h1 class="text-2xl font-bold">Rapport d'audit quotidien</h1>
                        <p>Superette - {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
                    </div>
                    ${printContents}
                </div>
            `;
            
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        });
    });
</script>
@endsection
