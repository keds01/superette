@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-indigo-900 flex items-center gap-3">
                <i class="fas fa-percent text-indigo-600"></i> Nouvelle Remise
            </h1>
            <p class="mt-1 text-lg text-gray-500">Étape 1: Sélectionnez la vente à laquelle appliquer une remise</p>
        </div>
        
        <!-- Filtres -->
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-xl p-6 mb-6">
            <form id="filterForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" id="search" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="N° vente ou client...">
                    </div>
                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                        <input type="date" id="date_debut" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                        <input type="date" id="date_fin" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="montant_min" class="block text-sm font-medium text-gray-700 mb-1">Montant min.</label>
                        <input type="number" id="montant_min" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="5000">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="reset" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg mr-2">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste des ventes -->
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Vente</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if($ventes->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Aucune vente disponible pour appliquer une remise.
                            </td>
                        </tr>
                    @else
                        @foreach($ventes as $vente)
                            <tr class="hover:bg-gray-50 transition-colors vente-row" data-vente-id="{{ $vente->id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                    #{{ $vente->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $vente->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $vente->client ? $vente->client->nom : 'Client occasionnel' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                    {{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('remises.create-for-vente', ['vente' => $vente->id]) }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-md transition-colors">
                                        <i class="fas fa-plus-circle mr-1.5"></i> Appliquer remise
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <div class="p-4 border-t">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        Affichage de {{ $ventes->count() }} vente(s) sur {{ $ventes->total() ?? $ventes->count() }}
                    </div>
                    @if(method_exists($ventes, 'links'))
                        {{ $ventes->links() }}
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Boutons de navigation -->
        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('remises.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sélection rapide des ventes
        document.querySelectorAll('.vente-row').forEach(row => {
            row.addEventListener('click', function() {
                const venteId = this.dataset.venteId;
                window.location.href = "{{ url('remises/ventes') }}/" + venteId + "/create";
            });
        });
        
        // Filtrage (simulation - serait à implémenter côté serveur)
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Appliquer visuellement le filtrage (simulation)
            alert('Filtrage à implémenter côté serveur');
        });
    });
</script>
@endsection 