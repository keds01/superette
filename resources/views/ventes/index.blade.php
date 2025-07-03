@extends('layouts.app')

@section('title', 'Liste des Ventes')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-pink-600 via-red-500 to-yellow-400 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Liste des Ventes</h1>
                <p class="mt-2 text-lg text-gray-500">Consultez, filtrez et gérez toutes les ventes enregistrées.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('ventes.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
    Nouvelle Vente
</a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="relative bg-white/60 backdrop-blur-xl border border-pink-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-pink-700">Total ventes</h3>
                <p class="text-3xl font-bold text-pink-900 mt-2">{{ $totalVentes ?? 0 }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-yellow-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-yellow-700">Montant total</h3>
                <p class="text-3xl font-bold text-yellow-900 mt-2">{{ number_format($montantTotal ?? 0, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-green-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-green-700">Montant payé</h3>
                <p class="text-3xl font-bold text-green-900 mt-2">{{ number_format($montantPaye ?? 0, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-indigo-700">Clients distincts</h3>
                <p class="text-3xl font-bold text-indigo-900 mt-2">{{ $clientsCount ?? 0 }}</p>
            </div>
        </div>

        <!-- Filtres glassy -->
        <div class="relative bg-white/60 backdrop-blur-xl border border-pink-100 rounded-2xl shadow-2xl p-8 mb-8">
            <form action="{{ route('ventes.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
                    <div>
                        <label for="statut" class="block text-sm font-medium text-pink-700 mb-1">Statut</label>
                        <select name="statut" id="statut" class="mt-1 block w-full rounded-lg border border-pink-200 bg-white/70 shadow focus:border-pink-400 focus:ring-2 focus:ring-pink-300 sm:text-sm">
                            <option value="">Tous</option>
                            <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                            <option value="terminee" {{ request('statut') == 'terminee' ? 'selected' : '' }}>Terminée</option>
                            <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>
                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-pink-700 mb-1">Date début</label>
                        <input type="date" name="date_debut" id="date_debut" class="mt-1 block w-full rounded-lg border border-pink-200 bg-white/70 shadow focus:border-pink-400 focus:ring-2 focus:ring-pink-300 sm:text-sm" value="{{ request('date_debut') }}">
                    </div>
                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-pink-700 mb-1">Date fin</label>
                        <input type="date" name="date_fin" id="date_fin" class="mt-1 block w-full rounded-lg border border-pink-200 bg-white/70 shadow focus:border-pink-400 focus:ring-2 focus:ring-pink-300 sm:text-sm" value="{{ request('date_fin') }}">
                    </div>
                    <div>
                        <label for="employe_id" class="block text-sm font-medium text-pink-700 mb-1">Employé</label>
                        <select name="employe_id" id="employe_id" class="mt-1 block w-full rounded-lg border border-pink-200 bg-white/70 shadow focus:border-pink-400 focus:ring-2 focus:ring-pink-300 sm:text-sm">
                            <option value="">Tous</option>
                            @foreach($employes as $employe)
                                <option value="{{ $employe->id }}" {{ request('employe_id') == $employe->id ? 'selected' : '' }}>{{ $employe->nom }} {{ $employe->prenom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-pink-700 mb-1">Client</label>
                        <select name="client_id" id="client_id" class="mt-1 block w-full rounded-lg border border-pink-200 bg-white/70 shadow focus:border-pink-400 focus:ring-2 focus:ring-pink-300 sm:text-sm">
                            <option value="">Tous</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->nom }} {{ $client->prenom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="numero_vente" class="block text-sm font-medium text-pink-700 mb-1">N° Vente</label>
                        <input type="text" name="numero_vente" id="numero_vente" class="mt-1 block w-full rounded-lg border border-pink-200 bg-white/70 shadow focus:border-pink-400 focus:ring-2 focus:ring-pink-300 sm:text-sm" value="{{ request('numero_vente') }}" placeholder="Rechercher...">
                    </div>
                </div>
                <div class="flex gap-2 mt-4">
                    <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filtrer
                    </button>
                    <a href="{{ route('ventes.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </a>
                </div>
            </form>
        </div>
        <!-- Tableau moderne Tailwind -->
        <div class="overflow-x-auto rounded-2xl shadow-2xl bg-white/70 backdrop-blur-xl border border-pink-100">
            <table class="min-w-full divide-y divide-pink-200">
                <thead class="bg-gradient-to-tr from-pink-100 to-yellow-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-pink-700 uppercase tracking-wider">N° Vente</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-pink-700 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-pink-700 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-pink-700 uppercase tracking-wider">Employé</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-yellow-700 uppercase tracking-wider">Montant Total</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-green-700 uppercase tracking-wider">Montant Payé</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-pink-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white/80 divide-y divide-pink-50">
                    @forelse($ventes as $vente)
                    <tr class="hover:bg-pink-50/70 transition-all">
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-pink-900">{{ $vente->numero_vente }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $vente->date_vente->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $vente->client->nom ?? 'Client non spécifié' }} {{ $vente->client->prenom ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $vente->employe->nom ?? 'Employé non spécifié' }} {{ $vente->employe->prenom ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-teal-700 font-bold">
                            @php
                                $totalInitial = 0;
                                $totalFinal = 0;
                                foreach($vente->details as $detail) {
                                    $prixUnitaire = $detail->prix_unitaire;
                                    $prixPromo = $detail->produit->prix_promo ?? $prixUnitaire;
                                    $estEnPromo = $prixPromo < $prixUnitaire;
                                    $totalInitial += $prixUnitaire * $detail->quantite;
                                    $totalFinal += ($estEnPromo ? $prixPromo : $prixUnitaire) * $detail->quantite;
                                }
                            @endphp
                            <span class="font-bold text-teal-700">{{ number_format($totalFinal, 0, ',', ' ') }} FCFA</span>
                            @if($totalInitial > $totalFinal)
                                <span class="ml-2 text-xs text-gray-400 line-through cursor-help" title="Prix sans promotion">{{ number_format($totalInitial, 0, ',', ' ') }} FCFA</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-green-700 font-bold">{{ number_format($vente->montant_paye, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($vente->statut === 'en_cours')
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-300">
                                    En cours
                                </span>
                            @elseif($vente->statut === 'terminee')
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-300">
                                    Terminée
                                </span>
                            @elseif($vente->statut === 'annulee')
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-300">
                                    Annulée
                                </span>
                            @else
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800 border border-gray-300">
                                    {{ ucfirst($vente->statut) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('ventes.show', $vente) }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($vente->peutEtreModifiee())
                                <a href="{{ route('ventes.edit', $vente) }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                @if($vente->peutEtreAnnulee())
                                <form action="{{ route('ventes.destroy', $vente) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition" title="Annuler" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette vente ?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('ventes.facture', $vente) }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition" title="Facture" target="_blank">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-pink-400">Aucune vente trouvée</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination moderne -->
        <div class="mt-6 flex justify-center">
            {{ $ventes->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Plus besoin de select2, Tailwind gère le style des selects
</script>
@endpush 