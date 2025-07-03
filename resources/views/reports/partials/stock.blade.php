<div class="space-y-6">
    <!-- Indicateurs de stock -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                        <i class="fas fa-boxes text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-700">Produits en stock</h3>
                        <div class="mt-1 text-3xl font-semibold">
                            {{ isset($stocks) ? $stocks->total() : 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-700">En alerte</h3>
                        <div class="mt-1 text-3xl font-semibold">
                            {{ $produitsEnAlerte ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-700">Rupture</h3>
                        <div class="mt-1 text-3xl font-semibold">
                            {{ $produitsEnRupture ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-orange-100 rounded-full p-3">
                        <i class="fas fa-calendar-times text-orange-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-700">Péremption</h3>
                        <div class="mt-1 text-3xl font-semibold">
                            {{ $produitsPerimes ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Produits proche de péremption -->
    @if(isset($produitsPerimesListe) && count($produitsPerimesListe) > 0)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>
                    Produits proche de péremption
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Péremption</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jours restants</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($produitsPerimesListe as $produit)
                            @php
                                $joursRestants = $produit->date_peremption ? $produit->date_peremption->diffInDays(now()) : null;
                                $classCouleur = $joursRestants <= 7 ? 'text-red-600' : ($joursRestants <= 15 ? 'text-orange-600' : 'text-yellow-600');
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $produit->nom }}</div>
                                    <div class="text-xs text-gray-500">{{ $produit->reference }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $produit->categorie->nom ?? 'Non catégorisé' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $produit->stock }} {{ $produit->uniteVente->symbole ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm {{ $classCouleur }} font-medium">
                                        {{ $produit->date_peremption ? $produit->date_peremption->format('d/m/Y') : 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $classCouleur }} bg-{{ substr($classCouleur, 5, -6) }}-100">
                                        {{ $joursRestants }} jour(s)
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('produits.edit', $produit->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('mouvements-stock.create') }}?product_id={{ $produit->id }}" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-exchange-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    
    <!-- État du stock -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">État du stock</h2>
                <a href="{{ route('stocks.index') }}" class="px-3 py-1 text-sm text-indigo-600 hover:text-indigo-900 font-medium flex items-center">
                    Voir tous les stocks <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        
        @if(isset($stocks) && count($stocks) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock actuel</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seuil alerte</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur stock</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($stocks as $produit)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($produit->image)
                                            <div class="flex-shrink-0 h-10 w-10 mr-3">
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $produit->image) }}" alt="{{ $produit->nom }}">
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $produit->nom }}</div>
                                            <div class="text-xs text-gray-500">{{ $produit->reference }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $produit->categorie->nom ?? 'Non catégorisé' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ number_format($produit->stock, 2) }} {{ $produit->uniteVente->symbole ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ number_format($produit->seuil_alerte, 2) }} {{ $produit->uniteVente->symbole ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ number_format($produit->stock * $produit->prix_achat_ht, 2) }} FCFA
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($produit->stock <= 0)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Rupture
                                        </span>
                                    @elseif($produit->stock <= $produit->seuil_alerte)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            En alerte
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Normal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="{{ route('produits.edit', $produit->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('mouvements-stock.create') }}?product_id={{ $produit->id }}" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-exchange-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $stocks->withQueryString()->links() }}
            </div>
        @else
            <div class="p-6 text-center">
                <div class="inline-block p-3 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-box-open text-gray-400 text-4xl"></i>
                </div>
                <p class="text-gray-500 mb-2">Aucun produit en stock</p>
                <a href="{{ route('produits.create') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                    Ajouter un nouveau produit
                </a>
            </div>
        @endif
    </div>
</div> 