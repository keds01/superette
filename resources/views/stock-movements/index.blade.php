@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Mouvements de Stock</h1>
                <p class="mt-2 text-lg text-gray-500">Visualisez l'historique complet des entrées et sorties de stock.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('mouvements-stock.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-green-600 to-emerald-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-dolly"></i>
                    Nouveau Mouvement
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-indigo-700">Total des mouvements</h3>
                <p class="text-3xl font-bold text-indigo-900 mt-2">{{ $mouvements->total() }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-green-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-green-700">Entrées de stock</h3>
                <p class="text-3xl font-bold text-green-900 mt-2">{{ $entreesCount ?? 0 }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-red-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-red-700">Sorties de stock</h3>
                <p class="text-3xl font-bold text-red-900 mt-2">{{ $sortiesCount ?? 0 }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-yellow-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-yellow-700">Produits concernés</h3>
                <p class="text-3xl font-bold text-yellow-900 mt-2">{{ $productsCount ?? 0 }}</p>
            </div>
        </div>

        <!-- Filtres -->
        <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8 mb-8">
            <form action="{{ route('mouvements-stock.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Recherche -->
                    <div class="relative">
                        <label for="search" class="block text-sm font-medium text-indigo-700 mb-1 flex items-center gap-1">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="11" cy="11" r="8" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-2.5-2.5"/></svg>
                            Recherche
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               class="peer mt-1 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm transition-all" 
                               placeholder="Produit, référence, motif...">
                    </div>

                    <!-- Type de mouvement -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-indigo-700 mb-1">Type de mouvement</label>
                        <select name="type" id="type" class="mt-1 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm">
                            <option value="">Tous les types</option>
                            <option value="entree" {{ request('type') == 'entree' ? 'selected' : '' }}>Entrée</option>
                            <option value="sortie" {{ request('type') == 'sortie' ? 'selected' : '' }}>Sortie</option>
                        </select>
                    </div>

                    <!-- Période -->
                    <div>
                        <label for="periode" class="block text-sm font-medium text-indigo-700 mb-1">Période</label>
                        <select name="periode" id="periode" class="mt-1 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm">
                            <option value="">Toutes périodes</option>
                            <option value="today" {{ request('periode') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                            <option value="week" {{ request('periode') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                            <option value="month" {{ request('periode') == 'month' ? 'selected' : '' }}>Ce mois</option>
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
                        <a href="{{ route('mouvements-stock.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tableau des mouvements -->
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow-2xl ring-1 ring-indigo-200 ring-opacity-40 sm:rounded-2xl bg-white/70 backdrop-blur-xl">
                        <table class="min-w-full divide-y divide-indigo-100">
                            <thead class="bg-gradient-to-tr from-indigo-100 to-purple-100">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-bold text-indigo-900 sm:pl-6">Date</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Produit</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Type</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Quantité</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Motif</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Date péremption</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-indigo-50 bg-white/80">
                                @forelse($mouvements as $movement)
                                    <tr class="hover:bg-indigo-50/50 transition-colors">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6 text-left align-middle">
                                            {{ $movement->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-indigo-900">
                                            @if($movement->product)
                                                <div class="font-medium">{{ $movement->product->nom }}</div>
                                                <div class="text-gray-500">
                                                    {{ $movement->product->categorie ? $movement->product->categorie->nom : 'Catégorie inconnue' }}
                                                </div>
                                            @else
                                                <div class="font-medium text-red-600">Produit supprimé</div>
                                                <div class="text-gray-500">-</div>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            @if($movement->type === 'entree')
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                                    Entrée
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                                    Sortie
                                                </span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            @if($movement->product)
                                                {{ number_format($movement->quantite_avant_conditionnement, 2) }} {{ $movement->product->uniteVente ? $movement->product->uniteVente->symbole : 'unité' }}
                                            @else
                                                {{ number_format($movement->quantite_avant_conditionnement, 2) }} unité
                                            @endif
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500">
                                            {{ Str::limit($movement->motif, 50) }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            @if($movement->date_peremption)
                                                <span @class([
                                                    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                                    'bg-yellow-100 text-yellow-800' => $movement->date_peremption->diffInDays(now()) <= 30,
                                                    'bg-gray-100 text-gray-800' => $movement->date_peremption->diffInDays(now()) > 30
                                                ])>
                                                    {{ $movement->date_peremption->format('d/m/Y') }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('mouvements-stock.show', $movement) }}" class="text-primary-600 hover:text-primary-900">
                                                    Détails
                                                </a>
                                                <form action="{{ route('mouvements-stock.destroy', $movement) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Êtes-vous sûr de vouloir annuler ce mouvement de stock ?')">
                                                        Annuler
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-3 py-4 text-sm text-gray-500 text-center">
                                            Aucun mouvement de stock trouvé
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{ $mouvements->links() }}
            </div>
        </div>
    </div>
@endsection 