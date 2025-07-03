@extends('layouts.app')

@section('title', 'Détail du mouvement de stock')

@section('content')
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-exchange-alt text-indigo-500"></i>
                    Détails du mouvement de stock
                </h1>
                <a href="{{ route('mouvements-stock.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-50 text-indigo-700 font-semibold border border-indigo-200 hover:bg-indigo-100 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
                    </a>
            </div>

            <div class="bg-white/80 border border-indigo-100 rounded-2xl shadow-xl p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Bloc Produit -->
                        <div>
                        <h2 class="text-sm font-semibold text-gray-500 mb-1">Produit</h2>
                                @php
                                    if (!$stockMovement->produit && $stockMovement->produit_id) {
                                        $produitHorsRelation = \App\Models\Produit::withTrashed()->find($stockMovement->produit_id);
                                    } else {
                                        $produitHorsRelation = null;
                                    }
                                    $produitAffichage = $stockMovement->produit ?? $produitHorsRelation ?? null;
                                @endphp
                                @if($produitAffichage)
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold text-indigo-800">{{ $produitAffichage->nom }}</span>
                                        @if($produitAffichage->deleted_at)
                                    <span class="ml-2 px-2 py-0.5 rounded bg-red-100 text-red-700 text-xs font-semibold">Produit supprimé</span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-500 mb-2">{{ $produitAffichage->categorie?->nom ?? 'Catégorie non définie' }}</div>
                            @if(!$stockMovement->produit && $produitHorsRelation)
                                <div class="text-xs text-amber-600 mb-2">
                                    <a href="/fix-relation-mouvement.php" class="underline">Diagnostiquer et réparer ce problème</a>
                                </div>
                            @endif
                        @else
                            <div class="text-red-600 font-semibold">Produit manquant</div>
                            <div class="text-xs text-amber-600 mb-2">
                                L'ID du produit ({{ $stockMovement->produit_id ?? 'Manquant' }}) n'existe plus.<br>
                                <a href="/fix-mouvement-26.php" class="underline">Diagnostiquer et réparer ce problème</a>
                            </div>
                        @endif
                        </div>

                    <!-- Bloc Type de mouvement -->
                        <div>
                        <h2 class="text-sm font-semibold text-gray-500 mb-1">Type de mouvement</h2>
                                @if($stockMovement->type === 'entree')
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800 font-bold text-sm">
                                <i class="fas fa-arrow-down mr-1"></i> Entrée de stock
                                    </span>
                                @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-800 font-bold text-sm">
                                <i class="fas fa-arrow-up mr-1"></i> Sortie de stock
                                    </span>
                                @endif
                        </div>

                    <!-- Bloc Quantité -->
                        <div>
                        <h2 class="text-sm font-semibold text-gray-500 mb-1">Quantité du mouvement</h2>
                                    @php
                                        $quantiteMouvement = $stockMovement->quantite_apres_conditionnement - $stockMovement->quantite_avant_conditionnement;
                                        $produitActif = $produitAffichage ?? null;
                                    @endphp
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-bold text-gray-900">{{ number_format(abs($quantiteMouvement), 2) }}</span>
                            <span class="text-sm text-gray-500">{{ $produitActif?->uniteVente?->symbole ?? '' }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $quantiteMouvement >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }} font-semibold">
                                {{ $quantiteMouvement >= 0 ? '+' : '-' }}
                            </span>
                            </div>
                        <div class="text-xs text-gray-500 mt-1">Avant : {{ number_format($stockMovement->quantite_avant_conditionnement, 2) }} | Après : {{ number_format($stockMovement->quantite_apres_conditionnement, 2) }}</div>
                        </div>

                    <!-- Bloc Date du mouvement -->
                        <div>
                        <h2 class="text-sm font-semibold text-gray-500 mb-1">Date du mouvement</h2>
                        <div class="text-gray-900 text-base">
                            {{ $stockMovement->date_mouvement ? $stockMovement->date_mouvement->format('d/m/Y H:i') : ($stockMovement->created_at ? $stockMovement->created_at->format('d/m/Y H:i') : 'Date non définie') }}
                            </div>
                        </div>

                    <!-- Bloc Motif -->
                        <div>
                        <h2 class="text-sm font-semibold text-gray-500 mb-1">Motif</h2>
                        <div class="text-gray-900 text-base">{{ $stockMovement->motif ?: 'Aucun motif spécifié' }}</div>
                        </div>

                    <!-- Bloc Date de péremption -->
                        <div>
                        <h2 class="text-sm font-semibold text-gray-500 mb-1">Date de péremption</h2>
                        <div class="text-gray-900 text-base">
                                    @if($stockMovement->date_peremption)
                                        <span @class([
                                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                            'bg-yellow-100 text-yellow-800' => $stockMovement->date_peremption->diffInDays(now()) <= 30,
                                            'bg-gray-100 text-gray-800' => $stockMovement->date_peremption->diffInDays(now()) > 30
                                        ])>
                                            {{ $stockMovement->date_peremption->format('d/m/Y') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                            </div>
                        </div>

                    <!-- Bloc Référence -->
                        <div>
                        <h2 class="text-sm font-semibold text-gray-500 mb-1">Référence</h2>
                        <div class="text-gray-900 text-base">{{ $stockMovement->reference_mouvement ?: '-' }}</div>
                        </div>

                    <!-- Bloc Type de référence -->
                        <div>
                        <h2 class="text-sm font-semibold text-gray-500 mb-1">Type de référence</h2>
                        <div class="text-gray-900 text-base">{{ $stockMovement->type_reference ? ucfirst($stockMovement->type_reference) : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection