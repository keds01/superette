@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8">
            <!-- En-tête -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-indigo-900">{{ $categorie->nom }}</h2>
                    <p class="mt-2 text-gray-600">{{ $categorie->description }}</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('categories.edit', $categorie) }}" class="text-indigo-600 hover:text-indigo-900 flex items-center px-4 py-2 rounded-xl" title="Modifier">
                        <i class="fas fa-edit mr-2"></i>
                        
                    </a>
                    <form action="{{ route('categories.destroy', $categorie) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="text-red-600 hover:text-red-900 flex items-center px-4 py-2 rounded-xl"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')"
                                title="Supprimer">
                            <i class="fas fa-trash-alt mr-2"></i>
                            
                        </button>
                    </form>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-indigo-50 rounded-xl p-6">
                    <h3 class="text-lg font-medium text-indigo-900 mb-2">Statut</h3>
                    <p class="text-2xl font-bold {{ $categorie->actif ? 'text-green-600' : 'text-red-600' }}">
                        {{ $categorie->actif ? 'Active' : 'Inactive' }}
                    </p>
                </div>
                <div class="bg-indigo-50 rounded-xl p-6">
                    <h3 class="text-lg font-medium text-indigo-900 mb-2">Produits</h3>
                    <p class="text-2xl font-bold text-indigo-600">{{ $categorie->products_count }}</p>
                </div>
                <div class="bg-indigo-50 rounded-xl p-6">
                    <h3 class="text-lg font-medium text-indigo-900 mb-2">Alertes</h3>
                    <p class="text-2xl font-bold text-indigo-600">{{ $categorie->alerts_count }}</p>
                </div>
            </div>

            <!-- Informations détaillées -->
            <div class="bg-indigo-50 rounded-xl p-6 mb-8">
                <h3 class="text-lg font-medium text-indigo-900 mb-4">Informations détaillées</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Créée le :</span><br>
                            {{ $categorie->created_at->format('d/m/Y H:i') }}
                        </p>
                        <p class="text-sm text-gray-600 mt-2">
                            <span class="font-medium">Dernière modification :</span><br>
                            {{ $categorie->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Slug :</span><br>
                            {{ $categorie->slug }}
                        </p>
                        @if($categorie->parent_id)
                            <p class="text-sm text-gray-600 mt-2">
                                <span class="font-medium">Catégorie parente :</span><br>
                                {{ $categorie->parent->nom }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Liste des produits -->
            @if($categorie->products->isNotEmpty())
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Produits de cette catégorie</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($categorie->products as $product)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $product->nom }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ number_format($product->prix_vente, 2) }} €</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $product->stock }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $product->actif ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun produit</h3>
                    <p class="mt-1 text-sm text-gray-500">Cette catégorie ne contient aucun produit pour le moment.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 