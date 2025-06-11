@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-blue-600 via-cyan-500 to-teal-400 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Gestion des Fournisseurs</h1>
                <p class="mt-2 text-lg text-gray-500">Gérez vos fournisseurs, leurs contacts et leurs historiques d'approvisionnement.</p>
            </div>
            <div class="flex flex-wrap gap-4 items-center">
                 <a href="{{ route('fournisseurs.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-blue-600 to-teal-500 text-white font-bold shadow-xl hover:shadow-neon-blue hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-plus"></i>
                    Nouveau Fournisseur
                </a>
                <a href="{{ route('commandes.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-purple-600 to-pink-500 text-white font-bold shadow-xl hover:shadow-neon-purple hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-shopping-cart"></i>
                    Commandes Fournisseurs
                </a>
                <a href="{{ route('receptions.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-yellow-500 to-orange-500 text-white font-bold shadow-xl hover:shadow-neon-orange hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-truck-loading"></i>
                    Réceptions Marchandises
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-8">
            <div class="relative bg-white/60 backdrop-blur-xl border border-blue-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-blue-700">Total Fournisseurs</h3>
                <p class="text-3xl font-bold text-blue-900 mt-2">{{ $totalFournisseurs ?? 0 }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-green-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-green-700">Fournisseurs Actifs</h3>
                <p class="text-3xl font-bold text-green-900 mt-2">{{ $fournisseursActifs ?? 0 }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-red-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-red-700">Fournisseurs Inactifs</h3>
                <p class="text-3xl font-bold text-red-900 mt-2">{{ $fournisseursInactifs ?? 0 }}</p>
            </div>
        </div>

        <!-- Filtres glassy -->
        <div class="relative bg-white/60 backdrop-blur-xl border border-blue-100 rounded-2xl shadow-2xl p-8 mb-8">
            <form action="{{ route('fournisseurs.index') }}" method="GET" class="space-y-4">
                 <div class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label for="search" class="block text-sm font-medium text-blue-700 mb-1">Recherche</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               class="w-full rounded-lg border border-blue-200 bg-white/70 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 sm:text-sm transition-all"
                               placeholder="Nom, code, contact, email...">
                    </div>
                     {{-- Ajouter d'autres filtres ici si nécessaire --}}
                    <div>
                        <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 h-[42px]">
                             <i class="fas fa-filter mr-2"></i>
                            Filtrer
                        </button>
                         <a href="{{ route('fournisseurs.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 h-[42px]">
                             <i class="fas fa-sync"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tableau moderne Tailwind -->
        <div class="overflow-x-auto rounded-2xl shadow-2xl bg-white/70 backdrop-blur-xl border border-blue-100">
            <table class="min-w-full divide-y divide-blue-200">
                <thead class="bg-gradient-to-tr from-blue-100 to-cyan-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Nom & Code</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Contact Principal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Téléphone</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Statut</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-blue-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white/80 divide-y divide-blue-50">
                    @forelse($fournisseurs as $fournisseur)
                            <tr class="hover:bg-blue-50/70 transition-all">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-blue-900">{{ $fournisseur->nom }}</div>
                                    <div class="text-sm text-blue-500">{{ $fournisseur->code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $fournisseur->contact_principal_nom }} {{ $fournisseur->contact_principal_prenom }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $fournisseur->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $fournisseur->telephone }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($fournisseur->actif)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Actif
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactif
                                        </span>
                                    @endif
                                </td>
                                 <td class="px-6 py-4 whitespace-nowrap text-center">
                                     <div class="flex items-center justify-center gap-2">
                                         <a href="{{ route('fournisseurs.show', $fournisseur) }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition" title="Voir">
                                             <i class="fas fa-eye"></i>
                                         </a>
                                        <a href="{{ route('fournisseurs.edit', $fournisseur) }}" 
                                           class="inline-flex items-center px-3 py-2 rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('fournisseurs.destroy', $fournisseur) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition" title="Supprimer" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                     </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-blue-400">Aucun fournisseur trouvé</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination moderne -->
                <div class="mt-6 flex justify-center">
                    {{ $fournisseurs->withQueryString()->links() }}
                </div>
            
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