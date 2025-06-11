@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-blue-600 via-cyan-500 to-teal-400 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Détails du Fournisseur</h1>
                <p class="mt-2 text-lg text-gray-500">Informations complètes et historique du fournisseur.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('fournisseurs.edit', $fournisseur) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-yellow-500 to-orange-500 text-white font-bold shadow-xl hover:shadow-neon-yellow hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-edit"></i>
                    Modifier
                </a>
                <form action="{{ route('fournisseurs.destroy', $fournisseur) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-red-600 to-rose-600 text-white font-bold shadow-xl hover:shadow-neon-red hover:-translate-y-1 transition-all duration-200" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ?')">
                        <i class="fas fa-trash"></i>
                        Supprimer
                    </button>
                </form>
                 <a href="{{ route('fournisseurs.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold shadow hover:bg-gray-200 hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la liste
                </a>
            </div>
        </div>

        <!-- Contenu principal avec design moderne -->
        <div class="bg-white/60 backdrop-blur-xl border border-blue-100 rounded-2xl shadow-2xl p-8 space-y-8">

            <!-- Informations principales -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center text-white">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    Informations Générales
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                     <div>
                        <p class="text-sm font-medium text-blue-700">Nom</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $fournisseur->nom }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-blue-700">Code</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $fournisseur->code }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-blue-700">Contact Principal</p>
                        {{-- Utiliser les nouveaux champs --}}
                        <p class="mt-1 text-sm text-gray-900">{{ $fournisseur->contacts()->where('est_principal', true)->first()->nom ?? '' }} {{ $fournisseur->contacts()->where('est_principal', true)->first()->prenom ?? '' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-blue-700">Téléphone</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $fournisseur->telephone }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-blue-700">Email</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $fournisseur->email }}</p>
                    </div>
                     <div>
                        <p class="text-sm font-medium text-blue-700">Statut</p>
                         <p class="mt-1 text-sm text-gray-900">
                            @if($fournisseur->actif)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Actif
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactif
                                </span>
                            @endif
                         </p>
                    </div>
                </div>
            </div>

            <!-- Adresse -->
             <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center gap-2">
                     <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center text-white">
                         <i class="fas fa-map-marker-alt"></i>
                     </div>
                    Adresse
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-medium text-blue-700">Adresse Complète</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $fournisseur->adresse }}</p>
                    </div>
                     <div>
                        <p class="text-sm font-medium text-blue-700">Ville</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $fournisseur->ville }}</p>
                    </div>
                     <div>
                        <p class="text-sm font-medium text-blue-700">Code Postal</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $fournisseur->code_postal }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-blue-700">Pays</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $fournisseur->pays }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-blue-700">NINEA</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $fournisseur->ninea ?? 'Non renseigné' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-blue-700">Registre de Commerce</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $fournisseur->registre_commerce ?? 'Non renseigné' }}</p>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 flex flex-col items-center hover:shadow-xl transition-all duration-300">
                    <h4 class="text-sm font-medium text-blue-700">Solde Actuel</h4>
                    <p class="text-2xl font-semibold text-blue-900 mt-2">{{ number_format($fournisseur->solde_actuel, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 flex flex-col items-center hover:shadow-xl transition-all duration-300">
                    <h4 class="text-sm font-medium text-blue-700">Total Approvisionnements</h4>
                    <p class="text-2xl font-semibold text-blue-900 mt-2">{{ number_format($fournisseur->total_approvisionnements, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 flex flex-col items-center hover:shadow-xl transition-all duration-300">
                    <h4 class="text-sm font-medium text-blue-700">Nombre d'Approvisionnements</h4>
                    <p class="text-2xl font-semibold text-blue-900 mt-2">{{ $fournisseur->nombre_approvisionnements ?? 0 }}</p>
                </div>
                <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 flex flex-col items-center hover:shadow-xl transition-all duration-300">
                    <h4 class="text-sm font-medium text-blue-700">Note Moyenne</h4>
                    <p class="text-2xl font-semibold text-blue-900 mt-2">{{ number_format($fournisseur->note_moyenne ?? 0, 1) }}/10</p>
                </div>
            </div>

            <!-- Notes -->
            @if($fournisseur->notes)
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center text-white">
                        <i class="fas fa-sticky-note"></i>
                    </div>
                    Notes
                </h3>
                <p class="text-sm text-gray-700">{{ $fournisseur->notes }}</p>
            </div>
            @endif

            <!-- Derniers approvisionnements -->
             <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center gap-2">
                     <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center text-white">
                         <i class="fas fa-boxes"></i>
                     </div>
                    Derniers Approvisionnements (5 récents)
                </h3>
                @if($fournisseur->approvisionnements->count() > 0)
                    <div class="overflow-x-auto rounded-xl border border-blue-200">
                        <table class="min-w-full divide-y divide-blue-200">
                            <thead class="bg-gradient-to-tr from-blue-100 to-cyan-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Référence</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Montant</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-blue-100">
                                @foreach($fournisseur->approvisionnements as $approvisionnement)
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $approvisionnement->date_commande->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $approvisionnement->reference }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                        {{ number_format($approvisionnement->montant_total, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($approvisionnement->statut === 'livree') bg-green-100 text-green-800
                                            @elseif($approvisionnement->statut === 'en_cours') bg-yellow-100 text-yellow-800
                                            @elseif($approvisionnement->statut === 'annulee') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($approvisionnement->statut) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-600">Aucun approvisionnement récent trouvé.</p>
                @endif
            </div>

            <!-- Dernières évaluations -->
             <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center gap-2">
                     <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center text-white">
                         <i class="fas fa-star"></i>
                     </div>
                    Dernières Évaluations (5 récentes)
                </h3>
                @if($fournisseur->evaluations->count() > 0)
                    <div class="overflow-x-auto rounded-xl border border-blue-200">
                        <table class="min-w-full divide-y divide-blue-200">
                            <thead class="bg-gradient-to-tr from-blue-100 to-cyan-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Évaluateur</th>
                                     <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Note</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Commentaire</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-blue-100">
                                @foreach($fournisseur->evaluations as $evaluation)
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $evaluation->created_at->format('d/m/Y') }}
                                    </td>
                                     <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $evaluation->user->name ?? 'N/A'}}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                        {{ $evaluation->note_globale ?? 'N/A'}}/10 {{-- Assurez-vous que note_globale est calculée ou stockée --}}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $evaluation->commentaire }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-600">Aucune évaluation récente trouvée.</p>
                @endif
            </div>
            
             <!-- Liens vers les pages de relations complètes -->
             <div class="flex justify-center gap-4 mt-8">



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
.hover\:shadow-neon-yellow:hover {
     box-shadow: 0 0 15px rgba(251, 191, 36, 0.5); /* yellow-500 */
}
.hover\:shadow-neon-red:hover {
    box-shadow: 0 0 15px rgba(239, 68, 68, 0.5); /* red-600 */
}
</style>
@endpush
@endsection 