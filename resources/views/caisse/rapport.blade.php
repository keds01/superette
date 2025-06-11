@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rapport de Caisse</h1>
            <p class="text-gray-600">Période du {{ $debut->format('d/m/Y') }} au {{ $fin->format('d/m/Y') }}</p>
        </div>
        <div class="flex space-x-4">
            <form action="{{ route('caisse.rapport') }}" method="GET" class="flex space-x-2">
                <input type="date" name="debut" value="{{ $debut->format('Y-m-d') }}" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <input type="date" name="fin" value="{{ $fin->format('Y-m-d') }}"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Filtrer
                </button>
            </form>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Imprimer
            </button>
        </div>
    </div>

    <!-- Résumé -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">Solde Initial</div>
            <div class="mt-2 text-3xl font-semibold text-gray-900">
                {{ number_format($soldeInitial, 0, ',', ' ') }} FCFA
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">Total Entrées</div>
            <div class="mt-2 text-3xl font-semibold text-green-600">
                +{{ number_format($totalEntrees, 0, ',', ' ') }} FCFA
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">Total Sorties</div>
            <div class="mt-2 text-3xl font-semibold text-red-600">
                -{{ number_format($totalSorties, 0, ',', ' ') }} FCFA
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">Solde Final</div>
            <div class="mt-2 text-3xl font-semibold text-indigo-600">
                {{ number_format($soldeFinal, 0, ',', ' ') }} FCFA
            </div>
        </div>
    </div>

    <!-- Graphique -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Évolution du solde</h2>
        <canvas id="soldeChart" height="100"></canvas>
    </div>

    <!-- Détails des opérations -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Détails des opérations</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Opération</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opérateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($operations as $operation)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $operation->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $operation->numero }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $operation->type_operation === 'entree' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $operation->type_operation === 'entree' ? 'Entrée' : 'Sortie' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm 
                            {{ $operation->type_operation === 'entree' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $operation->type_operation === 'entree' ? '+' : '-' }}
                            {{ number_format($operation->montant, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ucfirst($operation->mode_paiement) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $operation->user->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $operation->description ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            Aucune opération trouvée pour cette période
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($operations->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $operations->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('soldeChart').getContext('2d');
    
    // Configuration du graphique
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labelsGraphique) !!},
            datasets: [{
                label: 'Solde',
                data: {!! json_encode($dataGraphique) !!},
                borderColor: '#4F46E5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4
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
                            return new Intl.NumberFormat('fr-FR', {
                                style: 'currency',
                                currency: 'XOF',
                                minimumFractionDigits: 0
                            }).format(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR', {
                                style: 'currency',
                                currency: 'XOF',
                                minimumFractionDigits: 0
                            }).format(value);
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
@media print {
    .container {
        width: 100%;
        max-width: none;
        padding: 0;
        margin: 0;
    }
    .no-print {
        display: none;
    }
    .bg-white {
        background: none !important;
    }
    .shadow {
        box-shadow: none !important;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    th {
        background-color: #f8f9fa !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
@endpush
@endsection 