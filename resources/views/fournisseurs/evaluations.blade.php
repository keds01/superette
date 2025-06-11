@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-blue-600 via-cyan-500 to-teal-400 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Évaluations du Fournisseur</h1>
                <p class="mt-2 text-lg text-gray-500">Liste de toutes les évaluations pour {{ $fournisseur->nom }}.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                 {{-- Lien pour ajouter une nouvelle évaluation si la fonctionnalité existe et est distincte de la page show --}}
                 {{-- <a href="{{ route('evaluations_fournisseurs.create', $fournisseur) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-blue-600 to-teal-500 text-white font-bold shadow-xl hover:shadow-neon-blue hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-star-half-alt"></i>
                    Ajouter une évaluation
                </a> --}}
                 <a href="{{ route('fournisseurs.show', $fournisseur) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold shadow hover:bg-gray-200 hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-arrow-left"></i>
                    Retour au fournisseur
                </a>
            </div>
        </div>

        <!-- Tableau moderne Tailwind pour les évaluations -->
        <div class="overflow-x-auto rounded-2xl shadow-2xl bg-white/70 backdrop-blur-xl border border-blue-100">
            <table class="min-w-full divide-y divide-blue-200">
                <thead class="bg-gradient-to-tr from-blue-100 to-cyan-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Évaluateur</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Qualité Produits</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Délai Livraison</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Prix Compétitifs</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Service Client</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Note Globale</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Commentaire</th>
                         {{-- <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-blue-700 uppercase tracking-wider">Actions</th> --}}{{-- Activer si fonctionnalités edit/delete évaluations existent --}}
                    </tr>
                </thead>
                <tbody class="bg-white/80 divide-y divide-blue-50">
                    @forelse($evaluations as $evaluation)
                            <tr class="hover:bg-blue-50/70 transition-all">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $evaluation->created_at->format('d/m/Y') }}</div>
                                </td>
                                 <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $evaluation->user->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $evaluation->qualite_produits }}/10
                                </td>
                                 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $evaluation->delai_livraison }}/10
                                </td>
                                 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $evaluation->prix_competitifs }}/10
                                </td>
                                 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $evaluation->service_client }}/10
                                </td>
                                 <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-800">
                                    {{ number_format(($evaluation->qualite_produits + $evaluation->delai_livraison + $evaluation->prix_competitifs + $evaluation->service_client) / 4, 1) }}/10
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $evaluation->commentaire ?? 'Aucun commentaire' }}
                                </td>
                                {{-- <td class="px-6 py-4 whitespace-nowrap text-center">
                                     <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('evaluations_fournisseurs.edit', [$fournisseur, $evaluation]) }}" 
                                           class="inline-flex items-center px-3 py-2 rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('evaluations_fournisseurs.destroy', [$fournisseur, $evaluation]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition" title="Supprimer" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette évaluation ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                     </div>
                                </td> --}}
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-8 text-blue-400">Aucune évaluation trouvée pour ce fournisseur.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination moderne -->
                <div class="mt-6 flex justify-center">
                    {{ $evaluations->links() }}
                </div>
            
            </div>
        </div>
    </div>

@push('styles')
<style>
/* Styles pour l'animation du header */
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

/* Style pour l'ombre néon (à adapter si nécessaire) */
.hover\:shadow-neon-blue:hover {
    box-shadow: 0 0 15px rgba(59, 130, 246, 0.5); /* blue-500 */
}
</style>
@endpush
@endsection 