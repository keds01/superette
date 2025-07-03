<div class="space-y-6">
    <!-- Graphique d'évolution des ventes -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Évolution des ventes</h2>
        
        @if(isset($evolutionVentes) && count($evolutionVentes) > 0)
            <div class="h-80">
                <canvas id="ventesChart"></canvas>
            </div>
            <script>
                function initVentesChart() {
                    const ctx = document.getElementById('ventesChart').getContext('2d');
                    
                    // Préparation des données
                    const dates = @json($evolutionVentes->pluck('date'));
                    const montants = @json($evolutionVentes->pluck('total'));
                    
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: dates,
                            datasets: [{
                                label: 'Chiffre d\'affaires (FCFA)',
                                data: montants,
                                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                borderColor: 'rgba(79, 70, 229, 1)',
                                borderWidth: 2,
                                pointBackgroundColor: 'rgba(79, 70, 229, 1)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 1,
                                pointRadius: 4,
                                tension: 0.3,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + new Intl.NumberFormat('fr-FR', {
                                                style: 'currency',
                                                currency: 'XOF'
                                            }).format(context.raw);
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return new Intl.NumberFormat('fr-FR', {
                                                style: 'currency',
                                                currency: 'XOF',
                                                maximumSignificantDigits: 3
                                            }).format(value);
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                }
            </script>
        @else
            <div class="bg-gray-50 rounded-md p-6 text-center">
                <i class="fas fa-chart-line text-gray-400 text-4xl mb-3"></i>
                <p class="text-gray-500">Aucune donnée disponible pour la période sélectionnée</p>
            </div>
        @endif
    </div>
    
    <!-- KPIs spécifiques aux ventes -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="relative bg-white/60 backdrop-blur-xl border border-blue-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
            <div class="p-3 rounded-full bg-blue-100 mb-2">
                <i class="fas fa-receipt text-blue-600"></i>
            </div>
            <h3 class="text-sm font-medium text-blue-700">Panier moyen</h3>
            <p class="text-2xl font-bold text-gray-900 mt-2">
                @if(isset($nombre_ventes) && $nombre_ventes > 0 && isset($chiffre_affaires))
                    {{ number_format($chiffre_affaires / $nombre_ventes, 2) }} FCFA
                @else
                    0.00 FCFA
                @endif
            </p>
        </div>
        <div class="relative bg-white/60 backdrop-blur-xl border border-green-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
            <div class="p-3 rounded-full bg-green-100 mb-2">
                <i class="fas fa-percentage text-green-600"></i>
            </div>
            <h3 class="text-sm font-medium text-green-700">Taux de marge</h3>
            <p class="text-2xl font-bold text-gray-900 mt-2">
                @if(isset($chiffre_affaires) && $chiffre_affaires > 0 && isset($marge_brute))
                    {{ number_format(($marge_brute / $chiffre_affaires) * 100, 1) }} %
                @else
                    0.0 %
                @endif
            </p>
        </div>
        <div class="relative bg-white/60 backdrop-blur-xl border border-purple-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
            <div class="p-3 rounded-full bg-purple-100 mb-2">
                <i class="fas fa-calendar-day text-purple-600"></i>
            </div>
            <h3 class="text-sm font-medium text-purple-700">Ventes journalières</h3>
            <p class="text-2xl font-bold text-gray-900 mt-2">
                @if(isset($nombre_ventes) && isset($date_debut) && isset($date_fin))
                    @php
                        $days = max(1, $date_debut->diffInDays($date_fin) + 1);
                        $ventesParJour = $nombre_ventes > 0 ? $nombre_ventes / $days : 0;
                    @endphp
                    {{ number_format($ventesParJour, 1) }} / jour
                @else
                    0.0 / jour
                @endif
            </p>
        </div>
    </div>
    
    <!-- Liste des dernières ventes -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-semibold text-gray-800">Dernières ventes</h2>
        </div>
        
        @if(isset($ventes) && count($ventes) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Articles</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($ventes as $vente)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        #{{ $vente->id }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $vente->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $vente->client ? $vente->client->nom : 'Client occasionnel' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if(isset($vente->details_count))
                                            {{ $vente->details_count }} article(s)
                                        @else
                                            {{ $vente->details->count() }} article(s)
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ number_format($vente->montant_total, 2) }} FCFA
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $vente->statut == 'terminee' ? 'bg-green-100 text-green-800' : 
                                           ($vente->statut == 'en_cours' ? 'bg-yellow-100 text-yellow-800' : 
                                           'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst(str_replace('_', ' ', $vente->statut)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="{{ route('ventes.show', $vente->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($vente->statut == 'terminee')
                                    <a href="{{ route('ventes.facture', $vente->id) }}" class="text-gray-600 hover:text-gray-900" target="_blank">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $ventes->withQueryString()->links() }}
            </div>
        @else
            <div class="p-6 text-center">
                <p class="text-gray-500">Aucune vente pour la période sélectionnée</p>
            </div>
        @endif
    </div>
</div> 