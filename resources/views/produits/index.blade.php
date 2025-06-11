@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Gestion des Produits</h1>
                <p class="mt-2 text-lg text-gray-500">Visualisez, filtrez et gérez tous les produits de la supérette.</p>
            </div>
            <a href="{{ route('produits.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Ajouter un produit
            </a>
        </div>

        <!-- Statistiques glassmorphism -->
        <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-indigo-700">Total produits</h3>
                <p class="text-3xl font-bold text-indigo-900 mt-2">{{ $stats['totalProducts'] }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-blue-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-blue-700">Valeur du stock</h3>
                <p class="text-3xl font-bold text-blue-900 mt-2">{{ number_format($stats['stockValue'], 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-red-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-red-700">Produits en alerte</h3>
                <p class="text-3xl font-bold text-red-900 mt-2">{{ $stats['lowStockCount'] }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-yellow-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-yellow-700">Proche péremption</h3>
                <p class="text-3xl font-bold text-yellow-900 mt-2">{{ $stats['expiringCount'] }}</p>
            </div>
        </div>

        <!-- Filtres glassy modernisés -->
        <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8 mb-8">
            <form action="{{ route('produits.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Recherche -->
                    <div class="relative">
                        <label for="search" class="block text-sm font-medium text-indigo-700 mb-1 flex items-center gap-1">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="11" cy="11" r="8" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-2.5-2.5"/></svg>
                            Recherche
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               class="peer mt-1 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                               placeholder="Nom, référence, code-barres...">
                    </div>

                    <!-- Catégorie -->
                    <div>
                        <label for="categorie_id" class="block text-sm font-medium text-indigo-700 mb-1">Catégorie</label>
                        <select name="categorie_id" id="categorie_id" class="mt-1 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $categorie)
                                <option value="{{ $categorie->id }}" {{ request('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                    {{ $categorie->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- État du stock -->
                    <div>
                        <label for="stock_status" class="block text-sm font-medium text-indigo-700 mb-1">État du stock</label>
                        <select name="stock_status" id="stock_status" class="mt-1 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm">
                            <option value="">Tous</option>
                            <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stock faible</option>
                            <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>En rupture</option>
                        </select>
                    </div>

                    <!-- Boutons de filtrage -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filtrer
                        </button>
                        <a href="{{ route('produits.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Barre d'actions -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-4">
            <h2 class="text-xl font-bold bg-gradient-to-tr from-indigo-400 to-purple-600 bg-clip-text text-transparent tracking-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Liste des Produits
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('produits.export.excel', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-tr from-green-500 to-green-400 text-white font-semibold shadow hover:scale-105 transition-all">
                    <i class="fas fa-file-excel"></i> Exporter Excel
                </a>
                <a href="{{ route('produits.export.pdf', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-tr from-red-500 to-pink-400 text-white font-semibold shadow hover:scale-105 transition-all">
                    <i class="fas fa-file-pdf"></i> Exporter PDF
                </a>
            </div>
        </div>

        <!-- Tableau des produits -->
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-x-auto shadow-2xl ring-1 ring-indigo-200 ring-opacity-40 sm:rounded-2xl bg-white/70 backdrop-blur-xl">
                        <table class="min-w-full divide-y divide-indigo-100">
                            <thead class="bg-gradient-to-tr from-indigo-100 to-purple-100">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-bold text-indigo-900 sm:pl-6">Produit</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-indigo-900">Stock</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-indigo-900">Prix vente</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-indigo-900">Prix achat</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-indigo-900">Péremption</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-indigo-900">Emplacement</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-indigo-900">Catégorie</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-indigo-50 bg-white/80">
                                @forelse($produits as $produit)
                                <tr class="hover:bg-indigo-50/50 transition-colors">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                        <div class="flex flex-col items-center">
                                            @if($produit->image)
                                                <img src="{{ Storage::url($produit->image) }}" alt="{{ $produit->nom }}" class="h-14 w-14 rounded-lg object-cover border-2 border-indigo-200 shadow mb-2">
                                            @else
                                                <div class="h-14 w-14 rounded-lg bg-indigo-100 flex items-center justify-center border-2 border-indigo-200 mb-2">
                                                    <svg class="h-8 w-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                            <span class="block text-sm font-semibold text-indigo-900 text-center">{{ $produit->nom }}</span>
                                            <span class="block text-xs text-gray-400 text-center mt-1">Ref: {{ $produit->reference ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                        @php $stockFaible = $produit->stock <= $produit->seuil_alerte; @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $stockFaible ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ number_format($produit->stock, 2) }} {{ $produit->uniteVente->symbole }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                        {{ number_format($produit->prix_vente_ttc, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center text-indigo-800">{{ number_format($produit->prix_achat_ht, 0, ',', ' ') }} FCFA</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                        @if($produit->date_peremption)
                                            <span class="{{ $produit->estProchePeremption() ? 'text-red-600 font-medium' : 'text-indigo-700' }}">
                                                {{ $produit->date_peremption->format('d/m/Y') }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center text-indigo-700">
                                        {{ $produit->emplacement_rayon }} - {{ $produit->emplacement_etagere }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center text-indigo-700">{{ $produit->categorie->nom }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <a href="{{ route('produits.edit', $produit) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            
                                            <form action="{{ route('produits.destroy', $produit) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            

                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-4 text-sm text-gray-500 text-center">Aucun produit trouvé</td>
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
            {{ $produits->links() }}
        </div>
    </div>
</div>
@endsection 