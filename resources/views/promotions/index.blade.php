@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-purple-500 to-pink-500 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Gestion des Promotions</h1>
                <p class="mt-2 text-lg text-gray-500">Visualisez, filtrez et gérez toutes les promotions en cours ou à venir.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('promotions.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-pink-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Nouvelle promotion
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-8">
            <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-indigo-700">Promotions actives</h3>
                <p class="text-3xl font-bold text-indigo-900 mt-2">{{ $totalPromotionsActives ?? 0 }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-pink-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-pink-700">Produits en promotion</h3>
                <p class="text-3xl font-bold text-pink-900 mt-2">{{ $totalProduitsEnPromotion ?? 0 }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-purple-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-purple-700">Promotions à venir</h3>
                <p class="text-3xl font-bold text-purple-900 mt-2">{{ $totalPromotionsAVenir ?? 0 }}</p>
            </div>
        </div>

        <!-- Filtres glassy -->
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 mb-8">
            <form action="{{ route('promotions.index') }}" method="GET" class="flex flex-wrap gap-4 items-center">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher une promotion..." class="flex-1 rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <select name="statut" class="w-48 rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('statut') === 'active' ? 'selected' : '' }}>Actives</option>
                    <option value="a_venir" {{ request('statut') === 'a_venir' ? 'selected' : '' }}>À venir</option>
                    <option value="expiree" {{ request('statut') === 'expiree' ? 'selected' : '' }}>Expirées</option>
                </select>
                <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors">Filtrer</button>
            </form>
        </div>

        <!-- Tableau des promotions -->
        <div class="bg-white rounded-2xl shadow-2xl p-6 mb-8 mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="promotion-table" class="bg-white divide-y divide-gray-200">
                    @forelse($promotions as $promotion)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-gray-900">{{ $promotion->produit?->nom ?? 'Produit supprimé' }}</div>
                            <div class="text-sm text-gray-500">{{ $promotion->produit?->code ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $promotion->type === 'pourcentage' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $promotion->type === 'pourcentage' ? 'Pourcentage' : 'Montant fixe' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($promotion->type === 'pourcentage')
                                <span class="text-red-600 font-semibold">-{{ $promotion->valeur }}%</span>
                            @else
                                <span class="text-red-600 font-semibold">-{{ number_format($promotion->valeur, 0, ',', ' ') }} FCFA</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Du {{ $promotion->date_debut->format('d/m/Y') }}<br>
                            Au {{ $promotion->date_fin->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($promotion->estValide())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @elseif($promotion->date_debut > now())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    À venir
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Expirée
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('promotions.edit', $promotion) }}" 
                               class="text-indigo-600 hover:text-indigo-900 mr-3">Modifier</a>
                            <form action="{{ route('promotions.destroy', $promotion) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette promotion ?')">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-lg font-semibold">Aucune promotion trouvée</span>
                                <span class="text-sm mt-1">Essayez d'ajouter une nouvelle promotion ou de modifier vos filtres.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $promotions->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Filtrage des promotions
    function filterPromotions() {
        const search = document.getElementById('search').value.toLowerCase();
        const rows = document.querySelectorAll('#promotion-table tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(search) ? '' : 'none';
        });
    }

    // Graphique des promotions
    const ctx = document.getElementById('promotionsChart');
    if (ctx) { // Vérifier si l'élément canvas existe
        const context = ctx.getContext('2d');
        const labels = {!! json_encode($promotions->pluck('date_debut')->map(fn($date) => $date->format('d/m/Y'))->toArray() ?? []) !!};
        const data = {!! json_encode($promotions->pluck('valeur')->toArray() ?? []) !!};

        if (labels.length > 0 && data.length > 0) {
             new Chart(context, {
                 type: 'line',
                 data: {
                     labels: labels,
                     datasets: [{
                         label: 'Promotions',
                         data: data,
                         borderColor: 'rgb(79, 70, 229)',
                         tension: 0.1
                     }]
                 },
                 options: {
                     responsive: true,
                     plugins: {
                         legend: {
                             display: false
                         }
                     },
                     scales: {
                         y: {
                             beginAtZero: true
                         }
                     }
                 }
             });
         } else {
             // Afficher un message si pas de données
             const container = ctx.parentElement;
             container.innerHTML = '<div class="text-center text-gray-500 py-8">Aucune donnée de promotion pour le graphique.</div>';
         }
    }
</script>
@endpush
@endsection 