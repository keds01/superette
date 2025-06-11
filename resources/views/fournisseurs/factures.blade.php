@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md::justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-blue-600 via-cyan-500 to-teal-400 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Factures du Fournisseur</h1>
                <p class="mt-2 text-lg text-gray-500">Liste de toutes les factures associées à {{ $fournisseur->nom }}.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                 {{-- Lien pour créer une nouvelle facture si la fonctionnalité existe --}}
                 {{-- <a href="{{ route('factures.create', $fournisseur) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-blue-600 to-teal-500 text-white font-bold shadow-xl hover:shadow-neon-blue hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-file-invoice"></i>
                    Créer une facture
                </a> --}}
                 <a href="{{ route('fournisseurs.show', $fournisseur) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold shadow hover:bg-gray-200 hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-arrow-left"></i>
                    Retour au fournisseur
                </a>
            </div>
        </div>

        <!-- Tableau moderne Tailwind pour les factures -->
        <div class="overflow-x-auto rounded-2xl shadow-2xl bg-white/70 backdrop-blur-xl border border-blue-100">
            <table class="min-w-full divide-y divide-blue-200">
                <thead class="bg-gradient-to-tr from-blue-100 to-cyan-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Date Facture</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Référence Facture</th>
                         <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Montant Total</th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Statut Paiement</th>
                         <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Référence Appro.</th>
                         {{-- <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-blue-700 uppercase tracking-wider">Actions</th> --}}{{-- Activer si fonctionnalités show/download/etc. factures existent --}}
                    </tr>
                </thead>
                <tbody class="bg-white/80 divide-y divide-blue-50">
                    @forelse($factures as $facture)
                            <tr class="hover:bg-blue-50/70 transition-all">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $facture->date_facture->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $facture->reference }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-800">
                                    {{ number_format($facture->montant_total, 0, ',', ' ') }} FCFA
                                </td>
                                 <td class="px-6 py-4 whitespace-nowrap">
                                    @if($facture->statut_paiement === 'payee')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Payée
                                        </span>
                                    @elseif($facture->statut_paiement === 'partiellement_payee')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Partiellement payée
                                        </span>
                                    @elseif($facture->statut_paiement === 'impayee')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Impayée
                                        </span>
                                    @else
                                         <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($facture->statut_paiement) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                     @if($facture->approvisionnement)
                                        <a href="{{ route('fournisseurs.approvisionnements', [$fournisseur, '#' . $facture->approvisionnement->id]) }}" class="text-blue-600 hover:underline">
                                            {{ $facture->approvisionnement->reference ?? 'N/A' }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                {{-- <td class="px-6 py-4 whitespace-nowrap text-center">
                                     <div class="flex items-center justify-center gap-2">
                                        {{-- Lien pour voir/télécharger la facture si la route existe --}}
                                        {{-- <a href="{{ route('factures.show', $facture) }}" 
                                           class="inline-flex items-center px-3 py-2 rounded-lg bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition" title="Voir/Télécharger">
                                            <i class="fas fa-file-alt"></i>
                                        </a> --}}
                                     </div>
                                </td> --}}
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-blue-400">Aucune facture trouvée pour ce fournisseur.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination moderne -->
                <div class="mt-6 flex justify-center">
                    {{ $factures->links() }}
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
