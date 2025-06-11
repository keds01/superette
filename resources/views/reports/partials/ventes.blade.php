@php($ventes = $ventes ?? [])
<div class="space-y-6">
    <!-- Graphique des ventes -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="text-lg font-semibold mb-4">Évolution des ventes</h3>
        <canvas id="graphiqueVentes" height="300"></canvas>
    </div>

    <!-- Tableau des ventes -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="text-lg font-semibold mb-4">Détail des ventes</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remise</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($ventes as $detail)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $detail->vente->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $detail->produit->nom }}</div>
                            <div class="text-sm text-gray-500">{{ $detail->produit->categorie->nom }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $detail->quantite }} {{ $detail->produit->unite->symbole }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($detail->prix_unitaire, 0, ',', ' ') }} FCFA</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($detail->remise)
                                    {{ number_format($detail->remise, 0, ',', ' ') }} FCFA
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($detail->montant_total, 0, ',', ' ') }} FCFA</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-8">
                            <i class="fas fa-info-circle mr-2"></i> Aucune vente sur la période sélectionnée.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right font-medium">Total</td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium">
    {{ number_format(
        ($ventes instanceof \Illuminate\Support\Collection || $ventes instanceof \Illuminate\Pagination\AbstractPaginator)
            ? $ventes->sum('montant_total')
            : (is_array($ventes) ? array_sum(array_column($ventes, 'montant_total')) : 0)
    , 0, ',', ' ') }} FCFA
</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @if($ventes instanceof \Illuminate\Pagination\AbstractPaginator)
        <div class="mt-4">
            {{ $ventes->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function initialiserGraphiqueVentes() {
    const ctx = document.getElementById('graphiqueVentes').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($evolutionVentes->pluck('date')) !!},
            datasets: [{
                label: 'Chiffre d\'affaires',
                data: {!! json_encode($evolutionVentes->pluck('montant')) !!},
                borderColor: 'rgb(59, 130, 246)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Évolution des ventes sur la période'
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
</script>
@endpush 