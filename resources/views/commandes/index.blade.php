@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-purple-600 via-indigo-500 to-blue-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Gestion des Commandes</h1>
                <p class="mt-2 text-lg text-gray-500">Suivez et gérez toutes vos commandes auprès des fournisseurs.</p>
            </div>
            <div class="flex flex-wrap gap-4">
    <a href="{{ route('commandes.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-500 text-white font-bold shadow-xl hover:shadow-neon-purple hover:-translate-y-1 transition-all duration-200">
        <i class="fas fa-plus"></i>
        Nouvelle Commande
    </a>
    <a href="{{ route('fournisseurs.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-green-600 to-teal-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
        <i class="fas fa-user-plus"></i>
        Nouveau Fournisseur
    </a>
    <a href="{{ route('fournisseurs.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-sky-600 to-cyan-500 text-white font-bold shadow-xl hover:shadow-neon-cyan hover:-translate-y-1 transition-all duration-200">
        <i class="fas fa-truck"></i>
        Voir les Fournisseurs
    </a>
</div>
        </div>

        <!-- Statistiques -->
        <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="relative bg-white/60 backdrop-blur-xl border border-purple-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-purple-700">Total Commandes</h3>
                <p class="text-3xl font-bold text-purple-900 mt-2">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-indigo-700">Commandes En Cours</h3>
                <p class="text-3xl font-bold text-indigo-900 mt-2">{{ $stats['en_cours'] ?? 0 }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-green-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-green-700">Commandes Terminées</h3>
                <p class="text-3xl font-bold text-green-900 mt-2">{{ $stats['terminees'] ?? 0 }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-red-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-red-700">Commandes Annulées</h3>
                <p class="text-3xl font-bold text-red-900 mt-2">{{ $stats['annulees'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white/60 backdrop-blur-xl border border-purple-100 rounded-2xl shadow-2xl p-6 mb-8">
            <form action="{{ route('commandes.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:flex-wrap md:gap-4 md:items-end">
                <div class="flex-1 min-w-[200px]">
                    <label for="search" class="block text-sm font-medium text-purple-700 mb-1">Rechercher</label>
                    <input type="text"
                           name="search"
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="N° Commande, Fournisseur..."
                           class="w-full rounded-xl border-purple-200 bg-white/70 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-all">
                </div>
                <div class="min-w-[150px]">
                    <label for="statut" class="block text-sm font-medium text-purple-700 mb-1">Statut</label>
                    <select name="statut" id="statut"
                            class="w-full rounded-xl border-purple-200 bg-white/70 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-all">
                        <option value="">Tous</option>
                        <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="validee" {{ request('statut') === 'validee' ? 'selected' : '' }}>Validée</option>
                        <option value="en_cours_livraison" {{ request('statut') === 'en_cours_livraison' ? 'selected' : '' }}>En cours de livraison</option>
                        <option value="partiellement_recue" {{ request('statut') === 'partiellement_recue' ? 'selected' : '' }}>Partiellement Reçue</option>
                        <option value="recue" {{ request('statut') === 'recue' ? 'selected' : '' }}>Reçue</option>
                        <option value="terminee" {{ request('statut') === 'terminee' ? 'selected' : '' }}>Terminée</option>
                        <option value="annulee" {{ request('statut') === 'annulee' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>
                <div class="min-w-[150px]">
                    <label for="sort" class="block text-sm font-medium text-purple-700 mb-1">Trier par</label>
                    <select name="sort" id="sort"
                            class="w-full rounded-xl border-purple-200 bg-white/70 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-all">
                        <option value="date_commande" {{ request('sort', 'date_commande') === 'date_commande' ? 'selected' : '' }}>Date de Commande</option>
                        <option value="numero_commande" {{ request('sort') === 'numero_commande' ? 'selected' : '' }}>N° Commande</option>
                        <option value="fournisseur_nom" {{ request('sort') === 'fournisseur_nom' ? 'selected' : '' }}>Fournisseur</option>
                        <option value="montant_total" {{ request('sort') === 'montant_total' ? 'selected' : '' }}>Montant Total</option>
                    </select>
                </div>
                <div class="min-w-[150px]">
                    <label for="direction" class="block text-sm font-medium text-purple-700 mb-1">Direction</label>
                    <select name="direction" id="direction"
                            class="w-full rounded-xl border-purple-200 bg-white/70 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-all">
                        <option value="desc" {{ request('direction', 'desc') === 'desc' ? 'selected' : '' }}>Décroissant</option>
                        <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Croissant</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="h-[42px] px-6 py-2 rounded-xl bg-purple-600 text-white font-semibold hover:bg-purple-700 transition-colors shadow-md focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-opacity-75">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                     <a href="{{ route('commandes.index') }}" class="ml-2 h-[42px] px-4 py-2 inline-flex justify-center items-center border border-gray-300 text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-purple-500" title="Réinitialiser les filtres">
                        <i class="fas fa-sync"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Tableau des commandes -->
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow-2xl ring-1 ring-purple-200 ring-opacity-40 sm:rounded-2xl bg-white/70 backdrop-blur-xl">
                        <table class="min-w-full divide-y divide-purple-100">
                            <thead class="bg-gradient-to-tr from-purple-100 to-indigo-100">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-bold text-purple-900 sm:pl-6">N° Commande</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-purple-900">Fournisseur</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-purple-900">Date Commande</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-purple-900">Livraison Prévue</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-purple-900">Statut</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-purple-900">Montant Total</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-center text-xs font-bold text-purple-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-purple-50 bg-white/80">
                                @forelse($commandes as $commande)
                                    <tr class="hover:bg-purple-50/50 transition-colors">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-purple-100 flex items-center justify-center mr-3 text-purple-600">
                                                    <i class="fas fa-receipt"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-purple-900">{{ $commande->numero_commande ?? 'N/A' }}</div>
                                                    <div class="text-xs text-gray-500">ID: {{ $commande->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-700">{{ $commande->fournisseur->nom ?? 'N/A' }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-700">{{ $commande->date_commande ? \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') : 'N/A' }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-700">{{ $commande->date_livraison_prevue ? \Carbon\Carbon::parse($commande->date_livraison_prevue)->format('d/m/Y') : 'N/A' }}</td>
                                        <td class="px-3 py-4 text-sm">
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold leading-5
                                                @switch($commande->statut)
                                                    @case('en_attente') bg-yellow-100 text-yellow-800 @break
                                                    @case('validee') bg-blue-100 text-blue-800 @break
                                                    @case('en_cours_livraison') bg-cyan-100 text-cyan-800 @break
                                                    @case('partiellement_recue') bg-orange-100 text-orange-800 @break
                                                    @case('recue') bg-teal-100 text-teal-800 @break
                                                    @case('terminee') bg-green-100 text-green-800 @break
                                                    @case('annulee') bg-red-100 text-red-800 @break
                                                    @default bg-gray-100 text-gray-800 @break
                                                @endswitch
                                            ">
                                                {{ Str::ucfirst(str_replace('_', ' ', $commande->statut ?? 'N/A')) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-700 text-right">{{ number_format($commande->montant_total ?? 0, 2, ',', ' ') }} {{ $commande->devise ?? 'EUR' }}</td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-center text-sm font-medium sm:pr-6">
                                            <div class="flex items-center justify-center gap-2">
    <a href="{{ route('commandes.show', $commande) }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition" title="Voir">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('commandes.edit', $commande) }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition" title="Modifier">
        <i class="fas fa-edit"></i>
    </a>
    <a href="{{ route('receptions.create', ['commande_id' => $commande->id]) }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-green-100 text-green-700 hover:bg-green-200 transition" title="Réception de commande">
        <i class="fas fa-truck-loading"></i>
    </a>
    <form action="{{ route('commandes.destroy', $commande) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ? Ceci pourrait affecter les stocks et les suivis.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition" title="Supprimer">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-3 py-12 text-center">
                                            <div class="text-center">
                                                <i class="fas fa-inbox fa-3x text-purple-300 mb-4"></i>
                                                <p class="text-xl font-semibold text-purple-700 mb-2">Aucune commande trouvée</p>
                                                <p class="text-gray-500 mb-4">Créez votre première commande fournisseur pour commencer.</p>
                                                <a href="{{ route('commandes.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-tr from-purple-500 to-indigo-500 text-white font-semibold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-150">
                                                    <i class="fas fa-plus-circle"></i>
                                                    Créer une commande
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
        @if ($commandes->hasPages())
            <div class="mt-8">
                {{ $commandes->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
.animate-fade-in-down {
    animation: fadeInDown 0.5s ease-out;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hover\:shadow-neon-purple:hover {
    box-shadow: 0 0 15px rgba(129, 140, 248, 0.6); /* approx indigo-400 */
}
</style>
@endpush

@endsection