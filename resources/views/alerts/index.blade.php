@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Affichage des messages flash -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15L6.342 6.342a1.2 1.2 0 0 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.15 2.758 3.15a1.2 1.2 0 0 1 0 1.697z"/></svg>
                    </span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15L6.342 6.342a1.2 1.2 0 0 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.15 2.758 3.15a1.2 1.2 0 0 1 0 1.697z"/></svg>
                    </span>
                </div>
            @endif

            <!-- En-tête moderne -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
                <div>
                    <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Gestion des Alertes</h1>
                    <p class="mt-2 text-lg text-gray-500">Configurez et visualisez toutes les alertes système.</p>
                </div>
                <div class="flex flex-wrap gap-4">
                    {{-- Remplacer le bouton modal par un lien vers la page de création --}}
                    <a href="{{ route('alertes.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Nouvelle alerte
                    </a>
                </div>
            </div>

            {{-- Statistiques - Utiliser les statistiques du contrôleur --}}
            {{-- Assurez-vous que les statistiques sont toujours passées, même avec pagination --}}
            @if(isset($stats))
                <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                    <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                        <h3 class="text-sm font-medium text-indigo-700">Total Alertes</h3>
                        <p class="text-3xl font-bold text-indigo-900 mt-2">{{ $stats['total'] ?? $alerts->total() }}</p>
                    </div>
                    <div class="relative bg-white/60 backdrop-blur-xl border border-blue-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                        <h3 class="text-sm font-medium text-blue-700">Alertes Actives</h3>
                        <p class="text-3xl font-bold text-blue-900 mt-2">{{ $stats['active'] ?? Alert::actif()->count() }}</p>
                    </div>
                    {{-- Ajouter d'autres stats si pertinentes pour les alertes --}}
                     <div class="relative bg-white/60 backdrop-blur-xl border border-green-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                        <h3 class="text-sm font-medium text-green-700">Alertes Stock Bas</h3>
                        <p class="text-3xl font-bold text-green-900 mt-2">{{ $stats['stock_bas'] ?? Alert::parType('stock_bas')->count() }}</p>
                    </div>
                     <div class="relative bg-white/60 backdrop-blur-xl border border-purple-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                        <h3 class="text-sm font-medium text-purple-700">Alertes Péremption</h3>
                        <p class="text-3xl font-bold text-purple-900 mt-2">{{ $stats['peremption'] ?? Alert::parType('peremption')->count() }}</p>
                    </div>
                </div>
            @endif

            {{-- Filtres et recherche (si implémenté) --}}
            {{-- Actuellement non implémenté dans le contrôleur pour les alertes --}}
            {{-- <div class="bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 mb-8">
                 <form action="{{ route('alertes.index') }}" method="GET" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Rechercher une alerte..." 
                               class="w-full rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                     <div>
                        <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors">
                            Filtrer
                        </button>
                    </div>
                </form>
            </div> --}}

            <!-- Tableau des alertes -->
            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <div class="overflow-hidden shadow-2xl ring-1 ring-indigo-200 ring-opacity-40 sm:rounded-2xl bg-white/70 backdrop-blur-xl">
                            <table class="min-w-full divide-y divide-indigo-100">
                                <thead class="bg-gradient-to-tr from-indigo-100 to-purple-100">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-bold text-indigo-900 sm:pl-6">Type</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Catégorie</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Seuil</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Période</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Email</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Statut</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-indigo-50 bg-white/80">
                                    @forelse($alerts as $alert)
                                        <tr class="hover:bg-indigo-50/50 transition-colors">
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-indigo-900 sm:pl-6">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 flex-shrink-0 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                                        <i class="fas fa-bell text-indigo-600"></i> {{-- Icône alerte --}}
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-indigo-900">{{ $alertTypes[$alert->type] }}</div>
                                                        <div class="text-xs text-gray-500">Créée le {{ $alert->created_at->format('d/m/Y') }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">
                                                {{ $alert->category ? $alert->categorie->nom : 'Toutes' }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">
                                                {{ number_format($alert->seuil, 2) }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">
                                                {{ $alert->periode ? $alert->periode . ' j' : '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">
                                                {{ $alert->notification_email ?: '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                                <span @class([
                                                    'inline-flex items-center rounded-full px-3 py-1 text-xs font-medium',
                                                    'bg-green-100 text-green-800' => $alert->actif,
                                                    'bg-gray-100 text-gray-800' => !$alert->actif
                                                ])>
                                                    {{ $alert->actif ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                <div class="flex items-center justify-end space-x-3">
                                                    {{-- Lien vers la page de détails --}}
                                                     <a href="{{ route('alertes.show', $alert) }}" class="text-blue-600 hover:text-blue-900 flex items-center" title="Voir détails">
                                                        <i class="fas fa-eye mr-1"></i>
                                                        <span class="hidden sm:inline">Voir</span>
                                                    </a>
                                                    {{-- Lien vers la page d'édition --}}
                                                    <a href="{{ route('alertes.edit', $alert) }}" class="text-indigo-600 hover:text-indigo-900 flex items-center" title="Modifier">
                                                        <i class="fas fa-edit mr-1"></i>
                                                        <span class="hidden sm:inline">Modifier</span>
                                                    </a>
                                                    <form action="{{ route('alertes.destroy', $alert) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette alerte ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-900 flex items-center"
                                                                title="Supprimer">
                                                            <i class="fas fa-trash-alt mr-1"></i>
                                                            <span class="hidden sm:inline">Supprimer</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-3 py-4 text-sm text-gray-500 text-center">
                                                <div class="p-6 text-center">
                                                    <i class="fas fa-bell text-4xl text-gray-300 mb-2"></i>
                                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune alerte configurée</h3>
                                                    <p class="mt-1 text-sm text-gray-500">Commencez par créer une nouvelle alerte.</p>
                                                    <a href="{{ route('alertes.create') }}" class="mt-2 inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                                        <i class="fas fa-plus-circle mr-1"></i>
                                                        Créer une alerte
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

            <!-- Affichage de la pagination -->
            <div class="mt-6">
                {{ $alerts->links() }}
            </div>
        </div>
    </div>

    {{-- Suppression des anciens modaux qui ne sont plus utilisés --}}
    @push('scripts')
    <script>
        // Supprimer la logique JavaScript des anciens modaux si elle n'est plus nécessaire
        // (Les liens pointent maintenant vers les pages dédiées)
        // S'assurer que les messages flash sont gérés (Tailwind CSS peut le faire directement)

        // Script simple pour fermer les messages flash
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.alert [role="button"]').forEach(button => {
                button.addEventListener('click', () => {
                    button.closest('.alert').remove();
                });
            });
        });
    </script>
    @endpush
@endsection 