@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold text-gray-900">Détails du mouvement de stock</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        Informations détaillées sur le mouvement de stock #{{ $stockMovement->reference_mouvement }}
                    </p>
                </div>
                <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                    <a href="{{ route('stock-movements.index') }}" class="btn-secondary">
                        Retour à la liste
                    </a>
                </div>
            </div>

            <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Produit</h4>
                            <div class="mt-1">
                                <p class="text-sm font-medium text-gray-900">{{ $stockMovement->product->nom }}</p>
                                <p class="text-sm text-gray-500">{{ $stockMovement->product->categorie->nom ?? 'Catégorie non définie' }}</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Type de mouvement</h4>
                            <div class="mt-1">
                                @if($stockMovement->type === 'entree')
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                        Entrée de stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                        Sortie de stock
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Quantité</h4>
                            <div class="mt-1">
                                <p class="text-sm text-gray-900">
                                    {{ number_format($stockMovement->quantite_avant_conditionnement, 2) }} {{ $stockMovement->product->uniteVente->symbole }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Date du mouvement</h4>
                            <div class="mt-1">
                                <p class="text-sm text-gray-900">{{ $stockMovement->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Motif</h4>
                            <div class="mt-1">
                                <p class="text-sm text-gray-900">{{ $stockMovement->motif }}</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Date de péremption</h4>
                            <div class="mt-1">
                                <p class="text-sm text-gray-900">
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
                                </p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Référence</h4>
                            <div class="mt-1">
                                <p class="text-sm text-gray-900">{{ $stockMovement->reference_mouvement }}</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Type de référence</h4>
                            <div class="mt-1">
                                <p class="text-sm text-gray-900">{{ ucfirst($stockMovement->type_reference) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 