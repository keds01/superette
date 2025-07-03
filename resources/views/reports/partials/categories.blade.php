<div class="space-y-6">
    <!-- Graphique de distribution des ventes par catégorie -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Distribution des ventes par catégorie</h2>
        
        @if(isset($categories) && count($categories) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="h-80">
                    <canvas id="categoriesChart"></canvas>
                </div>
                <div class="h-80">
                    <canvas id="categoriesBarChart"></canvas>
                </div>
            </div>
            
            <script>
                function initCategoriesChart() {
                    // Préparation des données
                    const labels = @json($categories->pluck('nom'));
                    const data = @json($categories->pluck('total'));
                    const backgroundColors = [
                        'rgba(79, 70, 229, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(139, 92, 246, 0.7)',
                        'rgba(14, 165, 233, 0.7)',
                        'rgba(236, 72, 153, 0.7)',
                        'rgba(55, 65, 81, 0.7)',
                        'rgba(249, 115, 22, 0.7)',
                        'rgba(168, 85, 247, 0.7)'
                    ];
                    
                    // Graphique circulaire
                    const pieCtx = document.getElementById('categoriesChart').getContext('2d');
                    new Chart(pieCtx, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: backgroundColors,
                                borderColor: 'white',
                                borderWidth: 2,
                                hoverOffset: 15
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 15,
                                        font: {
                                            size: 11
                                        },
                                        generateLabels: function(chart) {
                                            const data = chart.data;
                                            if (data.labels.length && data.datasets.length) {
                                                return data.labels.map((label, i) => {
                                                    const value = data.datasets[0].data[i];
                                                    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                                    const percentage = Math.round((value / total) * 100);
                                                    return {
                                                        text: `${label} (${percentage}%)`,
                                                        fillStyle: data.datasets[0].backgroundColor[i],
                                                        strokeStyle: data.datasets[0].borderColor,
                                                        lineWidth: data.datasets[0].borderWidth,
                                                        hidden: false,
                                                        index: i
                                                    };
                                                });
                                            }
                                            return [];
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = Math.round((value / total) * 100);
                                            return `${label}: ${new Intl.NumberFormat('fr-FR', {
                                                style: 'currency',
                                                currency: 'XOF'
                                            }).format(value)} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                    
                    // Graphique à barres
                    const barCtx = document.getElementById('categoriesBarChart').getContext('2d');
                    new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Chiffre d\'affaires (FCFA)',
                                data: data,
                                backgroundColor: backgroundColors,
                                borderColor: backgroundColors.map(color => color.replace('0.7', '1')),
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
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
                                                notation: 'compact'
                                            }).format(value);
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            </script>
        @else
            <div class="bg-gray-50 rounded-md p-6 text-center">
                <i class="fas fa-chart-pie text-gray-400 text-4xl mb-3"></i>
                <p class="text-gray-500">Aucune donnée disponible pour la période sélectionnée</p>
            </div>
        @endif
    </div>
    
    <!-- KPIs sur les catégories -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 mr-4">
                    <i class="fas fa-tags text-indigo-600"></i>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Catégories actives</h3>
                    <p class="text-xl font-semibold text-gray-900">
                        @if(isset($categories))
                            {{ $categories->count() }}
                        @else
                            0
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 mr-4">
                    <i class="fas fa-trophy text-green-600"></i>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Meilleure catégorie</h3>
                    <p class="text-xl font-semibold text-gray-900">
                        @if(isset($categories) && $categories->count() > 0)
                            {{ $categories->first()->nom }}
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 mr-4">
                    <i class="fas fa-percentage text-purple-600"></i>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Top 3 (% du CA)</h3>
                    <p class="text-xl font-semibold text-gray-900">
                        @if(isset($categories) && $categories->count() > 0)
                            @php
                                $totalCA = $categories->sum('total');
                                $top3CA = $categories->take(3)->sum('total');
                                $percentage = $totalCA > 0 ? ($top3CA / $totalCA) * 100 : 0;
                            @endphp
                            {{ number_format($percentage, 1) }}%
                        @else
                            0%
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Classement des catégories -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-semibold text-gray-800">Classement des catégories</h2>
        </div>
        
        @if(isset($categories) && count($categories) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rang</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produits vendus</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chiffre d'affaires</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% du total</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $totalGeneral = $categories->sum('total'); @endphp
                        @foreach($categories as $index => $categorie)
                            <tr class="{{ $index < 3 ? 'bg-indigo-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($index === 0)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-yellow-400 text-white font-bold">1</span>
                                    @elseif($index === 1)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-300 text-white font-bold">2</span>
                                    @elseif($index === 2)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-yellow-700 text-white font-bold">3</span>
                                    @else
                                        <span class="text-gray-700">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $categorie->nom }}</div>
                                    <div class="text-xs text-gray-500">{{ $categorie->nb_produits ?? 'N/A' }} produits</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $categorie->quantite_vendue ?? '0' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ number_format($categorie->total, 2) }} €
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php $pourcentage = $totalGeneral > 0 ? ($categorie->total / $totalGeneral) * 100 : 0; @endphp
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900 mr-2">{{ number_format($pourcentage, 1) }}%</div>
                                        <div class="w-16 h-2 bg-gray-200 rounded-full">
                                            <div class="h-2 bg-indigo-600 rounded-full" style="width: {{ $pourcentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('categories.show', $categorie->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6 text-center">
                <p class="text-gray-500">Aucune donnée de catégorie disponible pour cette période</p>
            </div>
        @endif
    </div>

    <!-- Meilleurs produits par catégorie -->
    @if(isset($topProduitsParCategorie) && count($topProduitsParCategorie) > 0)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-xl font-semibold text-gray-800">Top produits par catégorie</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @foreach($topProduitsParCategorie as $categorie => $produits)
                    <div class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200">
                        <div class="px-4 py-3 bg-gray-100 border-b border-gray-200">
                            <h3 class="font-medium text-gray-800">{{ $categorie }}</h3>
                        </div>
                        <ul class="divide-y divide-gray-200">
                            @foreach($produits as $index => $produit)
                                <li class="px-4 py-3 flex items-center justify-between {{ $index < 3 ? 'font-medium' : '' }}">
                                    <span class="text-sm truncate" title="{{ $produit->nom }}">
                                        {{ $index + 1 }}. {{ $produit->nom }}
                                    </span>
                                    <span class="text-sm text-gray-700">
                                        {{ number_format($produit->total, 2) }} €
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div> 