@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Gestion des Catégories</h1>
                <p class="mt-2 text-lg text-gray-500">Visualisez et gérez toutes les catégories de produits.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Ajouter une catégorie
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-indigo-700">Total catégories</h3>
                <p class="text-3xl font-bold text-indigo-900 mt-2">{{ $stats['total'] }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-blue-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-blue-700">Catégories actives</h3>
                <p class="text-3xl font-bold text-blue-900 mt-2">{{ $stats['active'] }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-green-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-green-700">Avec produits</h3>
                <p class="text-3xl font-bold text-green-900 mt-2">{{ $stats['with_products'] }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-purple-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-purple-700">Total produits</h3>
                <p class="text-3xl font-bold text-purple-900 mt-2">{{ $stats['total_products'] }}</p>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 mb-8">
            <form action="{{ route('categories.index') }}" method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Rechercher une catégorie..." 
                           class="w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="w-48">
                    <select name="status" 
                            class="w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actives</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactives</option>
                    </select>
                </div>
                <div class="w-48">
                    <select name="sort" 
                            class="w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date de création</option>
                        <option value="nom" {{ request('sort') === 'nom' ? 'selected' : '' }}>Nom</option>
                        <option value="products_count" {{ request('sort') === 'products_count' ? 'selected' : '' }}>Nombre de produits</option>
                    </select>
                </div>
                <div class="w-48">
                    <select name="direction" 
                            class="w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Décroissant</option>
                        <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Croissant</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Tableau des catégories -->
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow-2xl ring-1 ring-indigo-200 ring-opacity-40 sm:rounded-2xl bg-white/70 backdrop-blur-xl">
                        <table class="min-w-full divide-y divide-indigo-100">
                            <thead class="bg-gradient-to-tr from-indigo-100 to-purple-100">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-bold text-indigo-900 sm:pl-6">Nom</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Description</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Nombre de produits</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Statut</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-indigo-50 bg-white/80">
                                @forelse($categories as $category)
                                    <tr class="hover:bg-indigo-50/50 transition-colors">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-indigo-900 sm:pl-6">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                                    <i class="fas fa-tag text-indigo-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-indigo-900">{{ $category->nom }}</div>
                                                    <div class="text-xs text-gray-500">Créée le {{ $category->created_at->format('d/m/Y') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-600">
                                            {{ $category->description ? Str::limit($category->description, 100) : 'Aucune description' }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-800">
                                                {{ $category->products_count }} {{ Str::plural('produit', $category->products_count) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $category->actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $category->actif ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            <div class="flex items-center justify-end space-x-3">
    <a href="{{ route('categories.show', $category) }}" class="text-indigo-600 hover:text-indigo-900 flex items-center" title="Voir">
        <i class="fas fa-eye mr-1"></i>
        
    </a>
    <a href="{{ route('categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900 flex items-center" title="Modifier">
        <i class="fas fa-edit mr-1"></i>
        
    </a>
    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" 
                class="text-red-600 hover:text-red-900 flex items-center"
                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')"
                title="Supprimer">
            <i class="fas fa-trash-alt mr-1"></i>
            
        </button>
    </form>
</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-sm text-gray-500 text-center">
                                            <div class="p-6 text-center">
                                                <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                                <p class="text-gray-500">Aucune catégorie trouvée</p>
                                                <a href="{{ route('categories.create') }}" class="mt-2 inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                                    <i class="fas fa-plus-circle mr-1"></i>
                                                    Créer une catégorie
                                                </a>
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
        <div class="mt-6">
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection