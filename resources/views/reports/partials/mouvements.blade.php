<div class="space-y-6">
    <!-- Graphique des mouvements -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Évolution des mouvements de stock</h2>
        
        @if(isset($mouvementsGraphique) && count($mouvementsGraphique) > 0)
            <div class="h-80">
                <canvas id="mouvementsChart"></canvas>
            </div>
            <script>
                function initMouvementsChart() {
                    const ctx = document.getElementById('mouvementsChart').getContext('2d');
                    
                    // Préparation des données
                    const dates = @json($mouvementsGraphique->pluck('date'));
                    const entrees = @json($mouvementsGraphique->pluck('entrees'));
                    const sorties = @json($mouvementsGraphique->pluck('sorties'));
                    
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: dates,
                            datasets: [
                                {
                                    label: 'Entrées',
                                    data: entrees,
                                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                                    borderColor: 'rgb(34, 197, 94)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Sorties',
                                    data: sorties,
                                    backgroundColor: 'rgba(239, 68, 68, 0.7)',
                                    borderColor: 'rgb(239, 68, 68)',
                                    borderWidth: 1
                                }
                            ]
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
                                    intersect: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    stacked: false,
                                    title: {
                                        display: true,
                                        text: 'Quantité'
                                    }
                                },
                                x: {
                                    stacked: false,
                                    grid: {
                                        display: false
                                    },
                                    title: {
                                        display: true,
                                        text: 'Date'
                                    }
                                }
                            }
                        }
                    });
                }
            </script>
        @else
            <div class="bg-gray-50 rounded-md p-6 text-center">
                <i class="fas fa-chart-bar text-gray-400 text-4xl mb-3"></i>
                <p class="text-gray-500">Aucune donnée disponible pour la période sélectionnée</p>
            </div>
        @endif
    </div>
    
    <!-- Synthèse des mouvements -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 mr-4">
                    <i class="fas fa-arrow-circle-up text-green-600"></i>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total entrées</h3>
                    <p class="text-xl font-semibold text-gray-900">
                        @if(isset($mouvementsGraphique))
                            {{ number_format($mouvementsGraphique->sum('entrees'), 2) }}
                        @else
                            0
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 mr-4">
                    <i class="fas fa-arrow-circle-down text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total sorties</h3>
                    <p class="text-xl font-semibold text-gray-900">
                        @if(isset($mouvementsGraphique))
                            {{ number_format($mouvementsGraphique->sum('sorties'), 2) }}
                        @else
                            0
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 mr-4">
                    <i class="fas fa-balance-scale text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Balance</h3>
                    <p class="text-xl font-semibold text-gray-900">
                        @if(isset($mouvementsGraphique))
                            @php
                                $balance = $mouvementsGraphique->sum('entrees') - $mouvementsGraphique->sum('sorties');
                                $textColor = $balance >= 0 ? 'text-green-600' : 'text-red-600';
                            @endphp
                            <span class="{{ $textColor }}">{{ number_format($balance, 2) }}</span>
                        @else
                            0
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Liste des mouvements -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Historique des mouvements</h2>
                <a href="{{ route('mouvements-stock.create') }}" class="px-3 py-1 text-sm text-indigo-600 hover:text-indigo-900 font-medium flex items-center">
                    Nouveau mouvement <i class="fas fa-plus-circle ml-1"></i>
                </a>
            </div>
        </div>
        
        @if(isset($mouvements) && count($mouvements) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Réf</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avant</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Après</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($mouvements as $mouvement)
                            @php
                                $quantite = $mouvement->quantite_apres_conditionnement - $mouvement->quantite_avant_conditionnement;
                                $typeClasse = $mouvement->type == 'entree' || $mouvement->type == 'ajustement_positif' 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-red-100 text-red-800';
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        #{{ $mouvement->id }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $mouvement->date_mouvement ? $mouvement->date_mouvement->format('d/m/Y') : ($mouvement->created_at ? $mouvement->created_at->format('d/m/Y') : 'N/A') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $mouvement->date_mouvement ? $mouvement->date_mouvement->format('H:i') : ($mouvement->created_at ? $mouvement->created_at->format('H:i') : '') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        @if($mouvement->produit)
                                            {{ $mouvement->produit->nom }}
                                            @if($mouvement->produit->deleted_at)
                                                <span class="text-xs text-gray-500">(supprimé)</span>
                                            @endif
                                        @else
                                            <span class="text-gray-500">Produit inconnu</span>
                                        @endif
                                    </div>
                                    @if($mouvement->produit && $mouvement->produit->categorie)
                                        <div class="text-xs text-gray-500">
                                            {{ $mouvement->produit->categorie->nom }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $typeClasse }}">
                                        {{ ucfirst(str_replace('_', ' ', $mouvement->type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium {{ $quantite >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $quantite >= 0 ? '+' : '' }}{{ number_format(abs($quantite), 2) }}
                                        @if($mouvement->produit && $mouvement->produit->uniteVente)
                                            {{ $mouvement->produit->uniteVente->symbole }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ number_format($mouvement->quantite_avant_conditionnement, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ number_format($mouvement->quantite_apres_conditionnement, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap max-w-xs truncate">
                                    <div class="text-sm text-gray-900" title="{{ $mouvement->motif }}">
                                        {{ $mouvement->motif ?: 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('mouvements-stock.show', $mouvement->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $mouvements->withQueryString()->links() }}
            </div>
        @else
            <div class="p-6 text-center">
                <p class="text-gray-500">Aucun mouvement de stock pour la période sélectionnée</p>
            </div>
        @endif
    </div>
</div> 