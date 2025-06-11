@extends('layouts.app')

@section('title', 'Gestion des Alertes')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Gestion des Alertes</h1>
                <p class="mt-2 text-md text-gray-600">Gérez les alertes de stock et de péremption de vos produits.</p>
            </div>
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-end gap-4">
                <div class="flex gap-4">
                    <a href="{{ route('alertes.create') }}" class="px-6 py-3 rounded-xl bg-gradient-to-tr from-green-600 to-teal-600 text-white font-bold shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-200 inline-flex items-center">
                        <i class="fas fa-edit mr-2"></i> Nouvelle alerte
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5 mb-8">
            <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-indigo-700">Total alertes</h3>
                <p class="text-3xl font-bold text-indigo-900 mt-2">{{ $alertes->count() }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-red-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-red-700 flex items-center gap-1">Alertes actives <i class="fas fa-info-circle text-red-500" data-bs-toggle="tooltip" title="Nombre d'alertes actuellement déclenchées. Nécessite une attention immédiate."></i></h3>
                <p class="text-3xl font-bold text-red-900 mt-2">{{ $alertes->where('estDeclenchee', true)->count() }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-yellow-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-yellow-700 flex items-center gap-1">Seuil minimum <i class="fas fa-info-circle text-yellow-500" data-bs-toggle="tooltip" title="Nombre d'alertes configurées pour le seuil minimum de stock."></i></h3>
                <p class="text-3xl font-bold text-yellow-900 mt-2">{{ $alertes->where('type', 'seuil_minimum')->count() }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-green-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-green-700 flex items-center gap-1">Seuil maximum <i class="fas fa-info-circle text-green-500" data-bs-toggle="tooltip" title="Nombre d'alertes configurées pour le seuil maximum de stock."></i></h3>
                <p class="text-3xl font-bold text-green-900 mt-2">{{ $alertes->where('type', 'seuil_maximum')->count() }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-orange-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-orange-700 flex items-center gap-1">Péremption <i class="fas fa-info-circle text-orange-500" data-bs-toggle="tooltip" title="Nombre d'alertes configurées pour les dates de péremption."></i></h3>
                <p class="text-3xl font-bold text-orange-900 mt-2">{{ $alertes->where('type', 'peremption')->count() }}</p>
            </div>
        </div>

        <!-- Filtres glassy -->
        <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-8 mb-8">
            <form action="{{ route('alertes.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-indigo-700 mb-1">Recherche</label>
                        <input type="text" name="search" id="search" 
                               class="mt-1 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm"
                               placeholder="Nom du produit, type d'alerte..."
                               value="{{ request('search') }}">
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-indigo-700 mb-1">Type d'alerte</label>
                        <select name="type" id="type" 
                                class="mt-1 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm">
                            <option value="">Tous</option>
                            <option value="seuil_minimum" {{ request('type') == 'seuil_minimum' ? 'selected' : '' }}>Seuil Minimum</option>
                            <option value="seuil_maximum" {{ request('type') == 'seuil_maximum' ? 'selected' : '' }}>Seuil Maximum</option>
                            <option value="peremption" {{ request('type') == 'peremption' ? 'selected' : '' }}>Date de Péremption</option>
                        </select>
                    </div>
                    <div>
                        <label for="statut" class="block text-sm font-medium text-indigo-700 mb-1">Statut</label>
                        <select name="statut" id="statut" 
                                class="mt-1 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm">
                            <option value="">Tous</option>
                            <option value="active" {{ request('statut') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="declenchee" {{ request('statut') == 'declenchee' ? 'selected' : '' }}>Déclenchée</option>
                            <option value="resolue" {{ request('statut') == 'resolue' ? 'selected' : '' }}>Résolue</option>
                        </select>
                    </div>
                    <div>
                        <label for="tri" class="block text-sm font-medium text-indigo-700 mb-1">Trier par</label>
                        <select name="tri" id="tri" 
                                class="mt-1 block w-full rounded-lg border border-indigo-200 bg-white/70 shadow focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 sm:text-sm">
                            <option value="recent" {{ request('tri') == 'recent' ? 'selected' : '' }}>Plus récentes</option>
                            <option value="ancien" {{ request('tri') == 'ancien' ? 'selected' : '' }}>Plus anciennes</option>
                            <option value="urgence" {{ request('tri') == 'urgence' ? 'selected' : '' }}>Urgence</option>
                            <option value="produit" {{ request('tri') == 'produit' ? 'selected' : '' }}>Nom du produit</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-4">
                    <a href="{{ route('alertes.index') }}" 
                       class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                        Réinitialiser
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 rounded-lg bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-semibold shadow-lg hover:shadow-neon transition-all duration-200">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste des alertes -->
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow-2xl ring-1 ring-indigo-200 ring-opacity-40 sm:rounded-2xl bg-white/70 backdrop-blur-xl">
                        <table class="min-w-full divide-y divide-indigo-100">
                            <thead class="bg-gradient-to-tr from-indigo-100 to-purple-100">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-bold text-indigo-900 sm:pl-6">Produit</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Type</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Seuil</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Stock Actuel</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-indigo-900">Statut</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-indigo-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white/80 divide-y divide-indigo-50">
                                @forelse($alertes as $alerte)
                                <tr class="hover:bg-indigo-50/70 transition-all">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                                <i class="fas fa-box text-indigo-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-indigo-900">{{ $alerte->produit?->nom ?? 'Produit inconnu' }}</div>
                                                <div class="text-sm text-gray-500">{{ $alerte->produit->categorie?->nom ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($alerte->type === 'seuil_minimum')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-arrow-down mr-1"></i>
                                                Seuil Minimum
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-arrow-up mr-1"></i>
                                                Seuil Maximum
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-indigo-700 font-bold">
                                        {{ $alerte->seuil }} {{ $alerte->produit?->unite?->symbole ?? '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-indigo-700 font-bold">
                                        @if($alerte->produit)
                                            {{ $alerte->produit->stock_actuel ?? 'N/A' }} {{ $alerte->produit->unite?->symbole ?? '' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($alerte->estDeclenchee())
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-circle mr-1"></i>
                                                Déclenchée
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                En attente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="document.getElementById('modalModifierAlerte{{ $alerte->id }}').classList.remove('hidden')"
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('alertes.destroy', $alerte) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette alerte ?')"
                                                        class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        <div class="flex flex-col items-center py-5">
                                            <i class="fas fa-bell-slash text-indigo-500 text-3xl mb-2"></i>
                                            <p>Aucune alerte configurée</p>
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
    </div>
</div>

<!-- Modal Création Alerte -->
<div id="modalCreerAlerte" class="fixed inset-0 bg-gray-600/50 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-8 border w-full max-w-md shadow-2xl rounded-2xl bg-white/90 backdrop-blur-xl">
        <div class="absolute top-4 right-4">
            <button onclick="document.getElementById('modalCreerAlerte').classList.add('hidden')" 
                    class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="mt-3">
            <h3 class="text-2xl font-bold bg-gradient-to-tr from-indigo-600 to-purple-500 bg-clip-text text-transparent mb-6">Nouvelle Alerte</h3>
            <form action="{{ route('produits.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="produit_id" class="block text-sm font-medium text-indigo-700">Produit *</label>
                        <select name="produit_id" id="produit_id" required
                                class="mt-1 block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-500">
                            <option value="">Sélectionner un produit</option>
                            @foreach($produits as $produit)
                                <option value="{{ $produit->id }}">{{ $produit->nom }} ({{ $produit->categorie?->nom ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-indigo-700">Type d'alerte *</label>
                        <select name="type" id="type" required
                                class="mt-1 block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-500"
                                onchange="toggleAlerteFields()">
                            <option value="seuil_minimum">Seuil Minimum</option>
                            <option value="seuil_maximum">Seuil Maximum</option>
                            <option value="peremption">Date de Péremption</option>
                        </select>
                    </div>

                    <div id="seuil-field">
                        <label for="seuil" class="block text-sm font-medium text-indigo-700">Seuil *</label>
                        <input type="number" name="seuil" id="seuil" min="0" step="0.01"
                               class="mt-1 block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-500">
                    </div>
                    
                    <div id="periode-field" class="hidden">
                        <label for="periode" class="block text-sm font-medium text-indigo-700">Période d'alerte (jours avant péremption) *</label>
                        <input type="number" name="periode" id="periode" min="1" step="1" value="15"
                               class="mt-1 block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-indigo-700">Message (optionnel)</label>
                        <input type="text" name="message" id="message"
                               class="mt-1 block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-500">
                    </div>
                    
                    <div class="flex justify-end gap-4 mt-8">
                        <button type="button" onclick="document.getElementById('modalCreerAlerte').classList.add('hidden')"
                                class="px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-all duration-200">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                            Créer l'alerte
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modals Modification Alertes -->
@foreach($alertes as $alerte)
<div id="modalModifierAlerte{{ $alerte->id }}" class="fixed inset-0 bg-gray-600/50 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-8 border w-full max-w-md shadow-2xl rounded-2xl bg-white/90 backdrop-blur-xl">
        <div class="absolute top-4 right-4">
            <button onclick="document.getElementById('modalModifierAlerte{{ $alerte->id }}').classList.add('hidden')" 
                    class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="mt-3">
            <h3 class="text-2xl font-bold bg-gradient-to-tr from-indigo-600 to-purple-500 bg-clip-text text-transparent mb-6">Modifier l'Alerte</h3>
            <form action="{{ route('alertes.update', $alerte) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label for="type{{ $alerte->id }}" class="block text-sm font-medium text-indigo-700">Type d'alerte *</label>
                        <select name="type" id="type{{ $alerte->id }}" required
                                class="mt-1 block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-500">
                            <option value="seuil_minimum" {{ $alerte->type === 'seuil_minimum' ? 'selected' : '' }}>Seuil Minimum</option>
                            <option value="seuil_maximum" {{ $alerte->type === 'seuil_maximum' ? 'selected' : '' }}>Seuil Maximum</option>
                        </select>
                    </div>

                    <div>
                        <label for="seuil{{ $alerte->id }}" class="block text-sm font-medium text-indigo-700">Seuil *</label>
                        <input type="number" name="seuil" id="seuil{{ $alerte->id }}" required min="0" step="0.01"
                               value="{{ $alerte->seuil }}"
                               class="mt-1 block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-500">
                    </div>

                    <div class="flex justify-end gap-4 mt-8">
                        <button type="button" onclick="document.getElementById('modalModifierAlerte{{ $alerte->id }}').classList.add('hidden')"
                                class="px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-all duration-200">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                            Mettre à jour
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
    // Initialisation des tooltips
    document.addEventListener('DOMContentLoaded', function() {
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(tooltip => {
            new bootstrap.Tooltip(tooltip);
        });
    });
</script>
@endpush
@endsection 