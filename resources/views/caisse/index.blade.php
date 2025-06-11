@extends('layouts.app')

@section('content')
<div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 overflow-x-auto">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Caisse & Opérations</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Gérez les entrées et sorties de votre caisse.
                </p>
            </div>
            <div class="flex items-center gap-4">
                <button type="button" onclick="document.getElementById('nouvelle-operation').showModal()"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-indigo-600 text-white font-semibold shadow hover:bg-indigo-500 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nouvelle opération
                </button>
                <a href="{{ route('caisse.rapport') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Rapport
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-indigo-900">Solde global Caisse</h3>
                <p class="text-3xl font-bold text-indigo-600 mt-2">
                    {{ number_format($caisseSoldeGlobal ?? 0, 0, ',', ' ') }} FCFA
                </p>
            </div>
            <div class="bg-green-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-green-900">Entrées du jour</h3>
                <p class="text-3xl font-bold text-green-600 mt-2">
                    {{ number_format($entreesJour ?? 0, 0, ',', ' ') }} FCFA
                </p>
            </div>
            <div class="bg-red-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-red-900">Sorties du jour</h3>
                <p class="text-3xl font-bold text-red-600 mt-2">
                    {{ number_format($sortiesJour ?? 0, 0, ',', ' ') }} FCFA
                </p>
            </div>
        </div>

        <!-- Graphique -->
        <div class="bg-white rounded-lg p-6 mb-8 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Évolution du solde (page actuelle)</h3>
            <canvas id="soldeChart" height="80"></canvas>
        </div>

        <!-- Tableau des opérations -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Opération</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode de paiement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opérateur</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($operations as $operation)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $operation->numero }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $operation->type_operation === 'entree' ? 'bg-green-100 text-green-800' : ($operation->type_operation === 'sortie' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($operation->type_operation) ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm 
                            {{ $operation->type_operation === 'entree' ? 'text-green-600' : ($operation->type_operation === 'sortie' ? 'text-red-600' : 'text-gray-600') }} font-semibold">
                            {{ $operation->type_operation === 'entree' ? '+' : ($operation->type_operation === 'sortie' ? '-' : '') }}
                            {{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ucfirst($operation->mode_paiement) ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $operation->created_at->format('d/m/Y H:i') ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $operation->user->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($operation->vente_id)
                                <a href="{{ route('ventes.show', $operation->vente_id) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 mr-3">Voir Vente</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-lg font-semibold">Aucune opération trouvée</span>
                                <span class="text-sm mt-1">Essayez d'ajouter une nouvelle opération.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $operations->links() }}
        </div>
    </div>
</div>

<!-- Modal Nouvelle Opération -->
<dialog id="nouvelle-operation" class="p-0 bg-transparent">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Nouvelle Opération</h3>
                <button onclick="this.closest('dialog').close()" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            @if($caisse)
            <form action="{{ route('caisse.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="caisse_id" value="{{ $caisse->id }}">
                
                <div>
                    <label for="type_operation" class="block text-sm font-medium text-gray-700">Type d'opération</label>
                    <select name="type_operation" id="type_operation" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="entree">Entrée</option>
                        <option value="sortie">Sortie</option>
                    </select>
                </div>

                <div>
                    <label for="montant" class="block text-sm font-medium text-gray-700">Montant</label>
                    <input type="number" name="montant" id="montant" required min="1" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="mode_paiement" class="block text-sm font-medium text-gray-700">Mode de paiement</label>
                    <select name="mode_paiement" id="mode_paiement" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="especes">Espèces</option>
                        <option value="carte">Carte</option>
                        <option value="mobile">Mobile Money</option>
                    </select>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="showModal = false" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                        Annuler
                    </button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Enregistrer
                    </button>
                </div>
            </form>
            @else
            <div class="text-center py-8">
                <p class="text-gray-600 text-lg">Veuillez d'abord sélectionner ou activer une caisse pour enregistrer une opération.</p>
                <svg class="mx-auto h-12 w-12 text-gray-400 mt-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <button onclick="this.closest('dialog').close()" class="mt-6 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Fermer
                </button>
            </div>
            @endif
        </div>
    </div>
</dialog>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique du solde
    const ctx = document.getElementById('soldeChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labelsGraphique ?? []) !!},
            datasets: [{
                label: 'Solde',
                data: {!! json_encode($dataGraphique ?? []) !!},
                borderColor: 'rgb(79, 70, 229)',
                tension: 0.1,
                fill: false
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('fr-TG', { style: 'currency', currency: 'XOF' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    type: 'category',
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Solde (FCFA)'
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' FCFA';
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection 