@extends('layouts.app')

@section('content')
    <!-- Section Notifications -->
    @if(!empty($notifications))
        <div class="mb-6 space-y-4">
            <h3 class="text-lg font-semibold text-gray-700">Alertes Personnalisées</h3>
            <div class="rounded-lg bg-yellow-50 p-4 shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.485 2.495a.5.5 0 00-.005.005L1.15 9.79a1.5 1.5 0 000 2.121l7.331 7.332a.5.5 0 00.707 0l7.332-7.331a1.5 1.5 0 000-2.121L9.202 2.5a.5.5 0 00-.707 0zM9 8a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm.904 2.744a.75.75 0 10-.816 1.21l.116-.078a.75.75 0 00-.116.078l-.816 1.21a.75.75 0 10.816 1.21l-.116.078a.75.75 0 00.116-.078z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-yellow-800">Nouvelles alertes de stock et péremption</h4>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul role="list" class="list-disc space-y-1 pl-5">
                                @foreach($notifications as $notification)
                                    <li>{{ $notification['message'] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Section Filtres -->
    <div class="mb-8 rounded-lg bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Filtrer les données</h3>
        <form action="{{ route('dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label for="date_debut" class="block text-sm font-medium text-gray-700">Date de début</label>
                <input type="date" name="date_debut" id="date_debut" 
                       value="{{ request('date_debut', now()->subDays(30)->format('Y-m-d')) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="date_fin" class="block text-sm font-medium text-gray-700">Date de fin</label>
                <input type="date" name="date_fin" id="date_fin" 
                       value="{{ request('date_fin', now()->format('Y-m-d')) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="categorie" class="block text-sm font-medium text-gray-700">Catégorie</label>
                <select name="categorie" id="categorie" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                                {{ request('categorie') == $category->id ? 'selected' : '' }}>
                            {{ $category->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Appliquer les filtres
                </button>
            </div>
        </form>
    </div>

    <!-- Actions rapides en haut -->
    <div class="mb-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('ventes.create') }}" class="bg-gradient-to-tr from-green-500 to-emerald-600 p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-white flex items-center justify-between group">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-lg">
                    <i class="fas fa-cash-register text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold">Nouvelle Vente</h3>
                    <p class="text-sm text-white/80">Enregistrer une vente</p>
                </div>
            </div>
            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
        </a>

        <a href="{{ route('produits.create') }}" class="bg-gradient-to-tr from-blue-500 to-indigo-600 p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-white flex items-center justify-between group">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-lg">
                    <i class="fas fa-box-open text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold">Ajouter Produit</h3>
                    <p class="text-sm text-white/80">Ajouter un nouveau produit au catalogue</p>
                </div>
            </div>
            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
        </a>

        <a href="{{ route('alertes.index') }}" class="bg-gradient-to-tr from-red-500 to-pink-600 p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-white flex items-center justify-between group">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-lg">
                    <i class="fas fa-bell text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold">Alertes</h3>
                    <p class="text-sm text-white/80">{{ $lowStockCount }} produits en alerte</p>
                </div>
            </div>
            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
        </a>

        <a href="{{ route('promotions.index') }}" class="bg-gradient-to-tr from-purple-500 to-pink-600 p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-white flex items-center justify-between group">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-lg">
                    <i class="fas fa-tag text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold">Promotions</h3>
                    <p class="text-sm text-white/80">Gérer les promotions</p>
                </div>
            </div>
            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>

    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Valeur totale du stock -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-700">Valeur du stock</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stockValue, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nombre total de produits -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-700">Total produits</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalProducts }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produits en alerte -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-700">En alerte</h3>
                        <p class="text-2xl font-bold text-red-600">{{ $lowStockCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produits à péremption -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-700">À péremption</h3>
                        <p class="text-2xl font-bold text-yellow-600">{{ $expiringCount }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableaux détaillés -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Produits en alerte -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                        Produits en alerte de stock
                    </h3>
                    <a href="{{ route('alertes.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Voir tout</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock actuel</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seuil d'alerte</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($lowStockProducts as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">{{ $product->nom }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $product->stock_actuel }} {{ $product->unite?->symbole }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $product->seuil_alerte }} {{ $product->unite?->symbole }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('mouvements-stock.create', ['product_id' => $product->id]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">Réapprovisionner</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Aucun produit en alerte
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Produits à péremption -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-clock text-yellow-500"></i>
                        Produits à péremption proche
                    </h3>
                    <a href="{{ route('stocks.index', ['filter' => 'expiring']) }}" class="text-sm text-indigo-600 hover:text-indigo-800">Voir tout</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date péremption</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jours restants</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($expiringProducts as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">{{ $product->nom }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $product->stock_actuel }} {{ $product->unite?->symbole }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $product->date_peremption->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm {{ $product->jours_restants <= 7 ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                            {{ $product->jours_restants }} jours
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Aucun produit à péremption proche
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Évolution des mouvements -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-line text-indigo-500"></i>
                    Évolution des mouvements (30 derniers jours)
                </h3>
                <div style="height: 400px;">
                    <canvas id="movementsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Distribution par catégorie -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-purple-500"></i>
                    Distribution par catégorie
                </h3>
                <div style="height: 400px;">
                    <canvas id="categoriesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Configuration des graphiques
        const movementsCtx = document.getElementById('movementsChart').getContext('2d');
        const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');

        // Graphique des mouvements
        new Chart(movementsCtx, {
            type: 'line',
            data: window.movementsChartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
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

        // Graphique des catégories
        new Chart(categoriesCtx, {
            type: 'doughnut',
            data: window.categoriesChartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
    </script>
    @endpush
@endsection 
