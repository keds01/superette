@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-green-600 via-teal-500 to-blue-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Gestion des Réceptions</h1>
                <p class="mt-2 text-lg text-gray-500">Suivez et gérez toutes les réceptions de marchandises liées à vos commandes fournisseurs.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('receptions.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-green-600 to-teal-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-plus"></i>
                    Nouvelle Réception
                </a>
            </div>
        </div>

        <!-- Tableau des réceptions -->
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow-2xl ring-1 ring-green-200 ring-opacity-40 sm:rounded-2xl bg-white/70 backdrop-blur-xl">
                        <table class="min-w-full divide-y divide-green-100">
                            <thead class="bg-gradient-to-tr from-green-100 to-teal-100">
                                <tr>
                                    <th scope="col" class="py-3.5 px-3 text-left text-xs font-bold text-green-900">N° Réception</th>
                                    <th scope="col" class="py-3.5 px-3 text-left text-xs font-bold text-green-900">Commande</th>
                                    <th scope="col" class="py-3.5 px-3 text-left text-xs font-bold text-green-900">Fournisseur</th>
                                    <th scope="col" class="py-3.5 px-3 text-left text-xs font-bold text-green-900">Date</th>
                                    <th scope="col" class="py-3.5 px-3 text-left text-xs font-bold text-green-900">Statut</th>
                                    <th scope="col" class="py-3.5 px-3 text-right text-xs font-bold text-green-900">Montant</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-center text-xs font-bold text-green-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-green-50 bg-white/80">
                                @forelse($receptions as $reception)
                                    <tr>
                                        <td class="px-3 py-4 text-sm text-gray-800 font-semibold">{{ $reception->numero_reception ?? 'N/A' }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-700">
                                            @if($reception->commande)
                                                <a href="{{ route('commandes.show', $reception->commande) }}" class="text-blue-700 hover:underline">{{ $reception->commande->numero_commande ?? '-' }}</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-700">{{ $reception->fournisseur->nom ?? '-' }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-700">{{ $reception->date_reception ? \Carbon\Carbon::parse($reception->date_reception)->format('d/m/Y') : '-' }}</td>
                                        <td class="px-3 py-4 text-sm">
                                            <span class="inline-block px-3 py-1 rounded-xl bg-green-100 text-green-700 text-xs font-bold">
                                                {{ Str::ucfirst(str_replace('_', ' ', $reception->statut ?? 'N/A')) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-700 text-right">{{ number_format($reception->montant_total ?? 0, 2, ',', ' ') }} {{ $reception->devise ?? 'EUR' }}</td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-center text-sm font-medium sm:pr-6">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('receptions.show', $reception) }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-green-100 text-green-700 hover:bg-green-200 transition" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('receptions.edit', $reception) }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('receptions.destroy', $reception) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réception ?');">
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
                                                <i class="fas fa-inbox fa-3x text-green-300 mb-4"></i>
                                                <p class="text-xl font-semibold text-green-700 mb-2">Aucune réception trouvée</p>
                                                <p class="text-gray-500 mb-4">Créez votre première réception de marchandise pour commencer.</p>
                                                <a href="{{ route('receptions.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-tr from-green-500 to-teal-500 text-white font-semibold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-150">
                                                    <i class="fas fa-plus-circle"></i>
                                                    Créer une réception
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
        @if ($receptions->hasPages())
            <div class="mt-8">
                {{ $receptions->withQueryString()->links() }}
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

.hover\:shadow-neon:hover {
    box-shadow: 0 0 15px rgba(16, 185, 129, 0.5); /* approx emerald-400 */
}
</style>
@endpush
@endsection
