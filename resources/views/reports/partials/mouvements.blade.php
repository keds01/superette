<div class="space-y-6">
    <!-- Graphique des mouvements -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="text-lg font-semibold mb-4">Évolution des mouvements de stock</h3>
        <canvas id="graphiqueMouvements" height="100"></canvas>
    </div>

    <!-- Tableau des mouvements -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="text-lg font-semibold mb-4">Détail des mouvements</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock avant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock après</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($mouvements as $mouvement)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $mouvement->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($mouvement->produit)
                                <div class="text-sm font-medium text-gray-900">{{ $mouvement->produit->nom }}</div>
                                <div class="text-sm text-gray-500">{{ $mouvement->produit->categorie->nom ?? '-' }}</div>
                            @else
                                <div class="text-sm font-medium text-gray-400 italic">Produit inconnu</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                @if($mouvement->type === 'entree')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Entrée
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Sortie
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($mouvement->type === 'entree')
                                    <span class="text-green-600">+{{ $mouvement->quantite }}</span>
                                @else
                                    <span class="text-red-600">-{{ $mouvement->quantite }}</span>
                                @endif
                                {{ $mouvement->produit->unite->symbole }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $mouvement->stock_avant }} {{ $mouvement->produit->unite->symbole }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $mouvement->stock_apres }} {{ $mouvement->produit->unite->symbole }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $mouvement->user->name }}</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $mouvements->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
function initialiserGraphiqueMouvements() {
    const ctx = document.getElementById('graphiqueMouvements').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($mouvementsGraphique->pluck('date')) !!},
            datasets: [{
                label: 'Entrées',
                data: {!! json_encode($mouvementsGraphique->pluck('entrees')) !!},
                backgroundColor: 'rgba(34, 197, 94, 0.2)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }, {
                label: 'Sorties',
                data: {!! json_encode($mouvementsGraphique->pluck('sorties')) !!},
                backgroundColor: 'rgba(239, 68, 68, 0.2)',
                borderColor: 'rgb(239, 68, 68)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Quantité'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Mouvements de stock'
                }
            }
        }
    });
}
</script>
@endpush 