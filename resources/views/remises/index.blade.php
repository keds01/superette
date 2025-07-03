@extends('layouts.app')

@section('title', 'Gestion des remises')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-indigo-900 flex items-center gap-3">
                    <i class="fas fa-percent text-indigo-600"></i> Gestion des Remises
                </h1>
                <p class="mt-1 text-gray-500">Visualisez, filtrez et gérez toutes les remises disponibles pour vos clients.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @if(auth()->user() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                    <a href="{{ route('remises.select-vente') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-bold shadow hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-plus-circle"></i> Nouvelle remise
                    </a>
                @else
                    <span class="text-sm text-gray-400 italic">La création de remise est réservée à l'administration.</span>
                @endif
            </div>
        </div>

        <!-- Alertes -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow animate-fade-in" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2 text-green-500"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow animate-fade-in" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Bloc filtres/recherche -->
        <div class="bg-white border border-indigo-100 rounded-xl shadow-xl p-6 mb-6">
            <form action="{{ route('remises.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div class="col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="Code remise, description..." 
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" id="type" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Tous les types</option>
                        <option value="pourcentage" {{ request('type') === 'pourcentage' ? 'selected' : '' }}>Pourcentage</option>
                        <option value="montant_fixe" {{ request('type') === 'montant_fixe' ? 'selected' : '' }}>Montant fixe</option>
                    </select>
                </div>
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" id="statut" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('statut') === 'active' ? 'selected' : '' }}>Actives</option>
                        <option value="inactive" {{ request('statut') === 'inactive' ? 'selected' : '' }}>Inactives</option>
                    </select>
                </div>
                <div class="flex items-stretch gap-2">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i> Filtrer
                    </button>
                    <a href="{{ route('remises.index') }}" class="bg-gray-200 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Tableau des remises -->
        <div class="bg-white border border-indigo-100 rounded-xl shadow-xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vente</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date application</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($remises as $remise)
                        <tr class="hover:bg-indigo-50/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $remise->code_remise ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($remise->type_remise === 'pourcentage')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-percent mr-1 text-blue-600"></i> Pourcentage
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-money-bill-wave mr-1 text-green-600"></i> Montant fixe
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if($remise->type_remise === 'pourcentage')
                                    <span class="font-semibold">{{ number_format($remise->valeur_remise, 2, ',', ' ') }}%</span>
                                @else
                                    <span class="font-semibold">{{ number_format($remise->valeur_remise, 0, ',', ' ') }} FCFA</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if($remise->vente)
                                    <a href="{{ route('ventes.show', $remise->vente) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Vente #{{ $remise->vente->id }}
                                    </a>
                                @else
                                    <span class="text-gray-400 italic">Non liée</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $remise->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-indigo-700">
                                {{ number_format($remise->montant_remise, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if($remise->vente)
                                        <a href="{{ route('ventes.show', $remise->vente) }}" class="text-indigo-600 hover:text-indigo-900" title="Voir la vente">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                                        <form action="{{ route('remises.destroy', $remise) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette remise ?')" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                Aucune remise trouvée. <a href="{{ route('remises.select-vente') }}" class="text-indigo-600 hover:underline">Créer une remise</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="p-4 border-t border-gray-200">
                {{ $remises->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation pour les alertes
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 5000);
        });
    });
</script>
@endsection