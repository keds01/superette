<div class="space-y-6">
    <!-- Graphiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Répartition des ventes par catégorie -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold mb-4">Répartition des ventes par catégorie</h3>
            <canvas id="graphiqueVentesCategories" height="300"></canvas>
        </div>

        <!-- Répartition du stock par catégorie -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold mb-4">Répartition du stock par catégorie</h3>
            <canvas id="graphiqueStockCategories" height="300"></canvas>
        </div>
    </div>

    <!-- Tableau des statistiques par catégorie -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="text-lg font-semibold mb-4">Statistiques détaillées par catégorie</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de produits</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur du stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ventes (30 jours)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marge brute</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taux de rotation</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($categories as $categorie)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $categorie->nom }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $categorie->nombre_produits }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($categorie->valeur_stock, 0, ',', ' ') }} FCFA</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($categorie->ventes_30_jours, 0, ',', ' ') }} FCFA</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($categorie->marge_brute, 0, ',', ' ') }} FCFA</div>
                            <div class="text-xs text-gray-500">{{ number_format($categorie->taux_marge, 1, ',', ' ') }}%</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                @php
                                    $tauxRotation = $categorie->taux_rotation;
                                    $couleur = $tauxRotation >= 4 ? 'green' : ($tauxRotation >= 2 ? 'yellow' : 'red');
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $couleur }}-100 text-{{ $couleur }}-800">
                                    {{ number_format($tauxRotation, 1, ',', ' ') }}
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top produits par catégorie -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="text-lg font-semibold mb-4">Top produits par catégorie</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($topProduitsParCategorie as $categorie => $produits)
            <div class="border rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-3">{{ $categorie }}</h4>
                <div class="space-y-3">
                    @foreach($produits as $produit)
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $produit->nom }}</p>
                            <p class="text-xs text-gray-500">
                                {{ number_format($produit->ventes_30_jours, 0, ',', ' ') }} FCFA
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $produit->quantite_vendue }} unités
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
function initialiserGraphiquesCategories() {
    // Graphique des ventes par catégorie
    const ctxVentes = document.getElementById('graphiqueVentesCategories').getContext('2d');
    new Chart(ctxVentes, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categories->pluck('nom')) !!},
            datasets: [{
                data: {!! json_encode($categories->pluck('ventes_30_jours')) !!},
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(236, 72, 153, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                },
                title: {
                    display: true,
                    text: 'Répartition des ventes'
                }
            }
        }
    });

    // Graphique du stock par catégorie
    const ctxStock = document.getElementById('graphiqueStockCategories').getContext('2d');
    new Chart(ctxStock, {
        type: 'pie',
        data: {
            labels: {!! json_encode($categories->pluck('nom')) !!},
            datasets: [{
                data: {!! json_encode($categories->pluck('valeur_stock')) !!},
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(236, 72, 153, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                },
                title: {
                    display: true,
                    text: 'Répartition du stock'
                }
            }
        }
    });
}
</script>
@endpush 