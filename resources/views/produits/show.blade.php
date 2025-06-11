@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-bold bg-gradient-to-tr from-indigo-400 to-purple-600 bg-clip-text text-transparent tracking-tight flex items-center gap-3">
                            <svg class="w-8 h-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Détails du Produit
                        </h2>
                        <p class="mt-2 text-lg text-gray-500">{{ $produit->nom }}</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('produits.edit', $produit) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                            <i class="fas fa-edit"></i>
                            Modifier
                        </a>
                        <a href="{{ route('produits.index') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-indigo-200 text-indigo-600 font-semibold shadow-sm hover:bg-indigo-50 transition-all duration-200">
                            <i class="fas fa-arrow-left"></i>
                            Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations principales -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Informations du Produit
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col items-center justify-center p-4 bg-indigo-50/50 rounded-xl border border-indigo-100">
                                @if($produit->image)
                                    <img src="{{ Storage::url($produit->image) }}" alt="{{ $produit->nom }}" 
                                         class="w-48 h-48 object-cover rounded-lg shadow-lg mb-4">
                                @else
                                    <div class="w-48 h-48 bg-white rounded-lg shadow-lg flex items-center justify-center mb-4">
                                        <svg class="w-24 h-24 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Nom</p>
                                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $produit->nom }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Référence</p>
                                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $produit->reference }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Catégorie</p>
                                        <p class="mt-1 text-lg font-semibold text-indigo-600">{{ $produit->categorie->nom }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Unité de vente</p>
                                        <p class="mt-1 text-lg font-semibold text-indigo-600">{{ $produit->uniteVente->nom }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Code-barres</p>
                                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $produit->code_barres ?? 'Non défini' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Description</p>
                                        <p class="mt-1 text-lg text-gray-900">{{ $produit->description ?? 'Aucune description' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Stock -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Stock
                            </h3>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Stock actuel</p>
                                        <p class="mt-1 text-2xl font-bold {{ $produit->stock <= $produit->seuil_alerte ? 'text-red-600' : 'text-green-600' }}">
                                            {{ number_format($produit->stock, 2) }} {{ $produit->uniteVente->symbole }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Seuil d'alerte</p>
                                        <p class="mt-1 text-2xl font-bold text-indigo-600">
                                            {{ number_format($produit->seuil_alerte, 2) }} {{ $produit->uniteVente->symbole }}
                                        </p>
                                    </div>
                                </div>
                                @if($produit->date_peremption)
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Date de péremption</p>
                                        <p class="mt-1 text-lg font-semibold {{ $produit->date_peremption->isPast() ? 'text-red-600' : ($produit->date_peremption->diffInDays(now()) <= 15 ? 'text-yellow-600' : 'text-green-600') }}">
                                            {{ $produit->date_peremption->format('d/m/Y') }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Prix -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Prix
                            </h3>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Prix d'achat HT</p>
                                        <p class="mt-1 text-2xl font-bold text-indigo-600">
                                            {{ number_format($produit->prix_achat_ht, 0, ',', ' ') }} FCFA
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Prix de vente HT</p>
                                        <p class="mt-1 text-2xl font-bold text-indigo-600">
                                            {{ number_format($produit->prix_vente_ht, 0, ',', ' ') }} FCFA
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Marge</p>
                                    <p class="mt-1 text-xl font-semibold text-indigo-600">
                                        {{ number_format($produit->marge, 2) }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Derniers mouvements -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Derniers mouvements de stock
                        </h3>
                        @if($produit->mouvementsStock->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-indigo-100">
                                    <thead class="bg-indigo-50/50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Quantité</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Motif</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-indigo-100">
                                        @foreach($produit->mouvementsStock as $movement)
                                            <tr class="hover:bg-indigo-50/50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $movement->created_at->format('d/m/Y H:i') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $movement->type === 'entree' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ $movement->type === 'entree' ? 'Entrée' : 'Sortie' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ number_format($movement->quantite_apres_unite, 2) }} {{ $produit->uniteVente->symbole }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $movement->motif }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-gray-500 py-4">Aucun mouvement de stock enregistré</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">
                <!-- Statistiques globales -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Statistiques du Produit
                        </h3>
                        <div class="space-y-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total des ventes</p>
                                <p class="mt-1 text-2xl font-bold text-indigo-600">
                                    {{ number_format($stats['total_ventes'], 0, ',', ' ') }} {{ $produit->uniteVente->symbole }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total des entrées</p>
                                <p class="mt-1 text-2xl font-bold text-indigo-600">
                                    {{ number_format($stats['total_entrees'], 0, ',', ' ') }} {{ $produit->uniteVente->symbole }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Valeur du stock</p>
                                <p class="mt-1 text-2xl font-bold text-indigo-600">
                                    {{ number_format($stats['valeur_stock'], 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                            @if($stats['dernier_mouvement'])
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Dernier mouvement</p>
                                    <div class="mt-1 flex items-center gap-2">
                                        <span class="text-sm text-gray-900">
                                            {{ $stats['dernier_mouvement']->created_at->format('d/m/Y H:i') }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $stats['dernier_mouvement']->type === 'entree' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $stats['dernier_mouvement']->type === 'entree' ? 'Entrée' : 'Sortie' }}
                                        </span>
                                        <span class="text-sm text-gray-900">
                                            ({{ number_format($stats['dernier_mouvement']->quantite_apres_unite, 2) }} {{ $produit->uniteVente->symbole }})
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Actions rapides
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ route('mouvements-stock.create', ['product_id' => $produit->id]) }}" 
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-tr from-green-600 to-emerald-500 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                                <i class="fas fa-plus"></i>
                                Ajouter du stock
                            </a>
                            <a href="{{ route('mouvements-stock.create', ['product_id' => $produit->id, 'type' => 'sortie']) }}" 
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-tr from-red-600 to-pink-500 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                                <i class="fas fa-minus"></i>
                                Retirer du stock
                            </a>
                            <a href="{{ route('promotions.create', ['product_id' => $produit->id]) }}" 
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-tr from-yellow-600 to-orange-500 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                                <i class="fas fa-tag"></i>
                                Créer une promotion
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 