@extends('layouts.app')

@section('title', 'Gestion des remises')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-pink-600 via-red-500 to-yellow-400 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Gestion des Remises</h1>
                <p class="mt-2 text-lg text-gray-500">Visualisez, filtrez et gérez toutes les remises disponibles pour vos clients.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                @if(Route::has('remises.create'))
                <a href="{{ route('remises.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-pink-600 to-yellow-400 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-plus"></i>
                    Nouvelle remise
                </a>
@else
    <span class="text-sm text-gray-400 italic">La création de remise est réservée à l'administration.</span>
@endif
            </div>
        </div>

        <!-- Alertes -->
        @if(session('success'))
        <div class="relative bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-md animate-fade-in-down" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 text-green-500"></i>
                <span class="font-medium">{{ session('success') }}</span>
                <button type="button" class="ml-auto text-green-500 hover:text-green-800" data-bs-dismiss="alert" aria-label="Close">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>
        </div>
        @endif
        @if(session('error'))
        <div class="relative bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md animate-fade-in-down" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>
                <span class="font-medium">{{ session('error') }}</span>
                <button type="button" class="ml-auto text-red-500 hover:text-red-800" data-bs-dismiss="alert" aria-label="Close">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>
        </div>
        @endif

        <!-- Bloc filtres/recherche -->
        <div class="bg-white/60 backdrop-blur-xl border border-pink-100 rounded-2xl shadow-2xl p-6 mb-8">
            <form action="{{ route('remises.index') }}" method="GET" class="flex flex-wrap gap-4 items-center">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher une remise..." class="flex-1 rounded-xl border-pink-200 focus:border-pink-500 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                <select name="statut" class="w-48 rounded-xl border-pink-200 focus:border-pink-500 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('statut') === 'active' ? 'selected' : '' }}>Actives</option>
                    <option value="inactive" {{ request('statut') === 'inactive' ? 'selected' : '' }}>Inactives</option>
                </select>
                <button type="submit" class="px-6 py-3 rounded-xl bg-pink-600 text-white font-bold hover:bg-pink-700 transition-colors">Filtrer</button>
            </form>
        </div>

        <!-- Tableau des remises -->
        <div class="bg-white/60 backdrop-blur-xl border border-pink-100 rounded-2xl shadow-2xl p-6 mb-8">
            <table class="min-w-full divide-y divide-pink-100">
                <thead class="bg-gradient-to-tr from-pink-100 to-yellow-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-pink-700 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-pink-700 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-pink-700 uppercase tracking-wider">Valeur</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-pink-700 uppercase tracking-wider">Période</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-pink-700 uppercase tracking-wider">Utilisation</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-pink-700 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-pink-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white/60 divide-y divide-pink-50">
                    @foreach($remises as $remise)
                    <tr class="hover:bg-pink-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $remise->code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($remise->type === 'pourcentage')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Pourcentage
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Montant Fixe
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($remise->type === 'pourcentage')
                                    {{ $remise->valeur }}%
                                @else
                                    {{ number_format($remise->valeur, 0, ',', ' ') }} FCFA
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                Du {{ $remise->date_debut->format('d/m/Y') }}<br>
                                Au {{ $remise->date_fin->format('d/m/Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($remise->utilisation_max)
                                    {{ $remise->utilisation_actuelle }}/{{ $remise->utilisation_max }}
                                @else
                                    {{ $remise->utilisation_actuelle }} (Illimité)
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('remises.toggle-status', $remise) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="focus:outline-none">
                                    @if($remise->actif)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Actif
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactif
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
    <a href="{{ route('remises.show', $remise) }}" class="text-indigo-600 hover:text-indigo-900" title="Voir détails">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('remises.edit', $remise) }}" class="text-blue-600 hover:text-blue-900" title="Modifier">
        <i class="fas fa-edit"></i>
    </a>
    <form action="{{ route('remises.destroy', $remise) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette remise ?')" title="Supprimer">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 bg-white/60 backdrop-blur-xl border border-pink-100 rounded-2xl shadow-2xl p-4">
            {{ $remises->links() }}
        </div>
    </div>
</div>
@endsection