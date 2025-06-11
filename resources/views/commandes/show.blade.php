@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-bold bg-gradient-to-tr from-indigo-400 to-purple-600 bg-clip-text text-transparent tracking-tight flex items-center gap-3">
                            <svg class="w-8 h-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m4 0h-4m4 0a2 2 0 01-2 2h-4a2 2 0 01-2-2m8 0V7a2 2 0 00-2-2H8a2 2 0 00-2 2v10" />
                            </svg>
                            Détail de la commande
                        </h2>
                        <p class="mt-2 text-lg text-gray-500">N° {{ $commande->numero_commande }}</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('commandes.edit', $commande) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                            <i class="fas fa-edit"></i>
                            Modifier
                        </a>
                        <a href="{{ route('commandes.index') }}" 
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
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Date commande</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $commande->date_commande->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Date livraison prévue</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $commande->date_livraison_prevue->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Fournisseur</p>
                                <p class="mt-1 text-lg font-semibold text-indigo-600">{{ $commande->fournisseur->nom ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Statut</p>
                                <span class="inline-block mt-1 px-3 py-1 rounded-xl bg-violet-100 text-violet-700 text-lg font-semibold">{{ $commande->statut }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Devise</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $commande->devise }}</p>
                            </div>
                        </div>
                        @if(session('success'))
                            <div class="p-3 bg-green-100 text-green-800 rounded-xl">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="p-3 bg-red-100 text-red-800 rounded-xl">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                </div>
                <!-- Tableau produits -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                            </svg>
                            Produits commandés
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 rounded-xl">
                                <thead class="bg-violet-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Produit</th>
                                        <th class="px-4 py-2 text-left">Catégorie</th>
                                        <th class="px-4 py-2 text-right">Quantité</th>
                                        <th class="px-4 py-2 text-right">Prix unitaire</th>
                                        <th class="px-4 py-2 text-right">Total ligne</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($commande->details as $detail)
                                    <tr class="border-t">
                                        <td class="px-4 py-2">{{ $detail->produit->nom ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $detail->produit->categorie->nom ?? '-' }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($detail->quantite, 2, ',', ' ') }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($detail->prix_unitaire, 2, ',', ' ') }} {{ $commande->devise }}</td>
                                        <td class="px-4 py-2 text-right font-semibold">{{ number_format($detail->montant_total, 2, ',', ' ') }} {{ $commande->devise }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-violet-50 font-bold">
                                        <td colspan="4" class="px-4 py-2 text-right">Montant total</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($commande->montant_total, 2, ',', ' ') }} {{ $commande->devise }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Colonne secondaire (actions rapides, stats, badges, logs) -->
<div class="space-y-6">
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
    <a href="{{ route('commandes.edit', $commande) }}" 
       class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
        <i class="fas fa-edit"></i>
        Modifier la commande
    </a>
    @if(in_array($commande->statut, ['en_attente', 'en_cours']))
        <a href="{{ route('receptions.create', ['commande_id' => $commande->id]) }}"
           class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-tr from-green-500 to-green-700 text-white font-semibold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
            <i class="fas fa-truck"></i>
            Réceptionner la commande
        </a>
    @endif
    <a href="{{ route('commandes.index') }}" 
       class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-white border border-indigo-200 text-indigo-600 font-semibold shadow-sm hover:bg-indigo-50 transition-all duration-200">
        <i class="fas fa-arrow-left"></i>
        Retour à la liste
    </a>
</div>
        </div>
    </div>
    <!-- Bloc stats, badges, logs -->
    @include('commandes._stats_badges_logs')
</div>
        </div>
    </div>
</div>
@endsection
