@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-violet-600 via-indigo-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Gestion des Employés</h1>
                <p class="mt-2 text-lg text-gray-500">Visualisez et gérez tous les employés de la superette.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('employes.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-violet-600 to-indigo-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Ajouter un employé
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="relative bg-white/60 backdrop-blur-xl border border-violet-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-violet-700">Total employés</h3>
                <p class="text-3xl font-bold text-violet-900 mt-2">{{ $employes->total() }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-indigo-700">Employés actifs</h3>
                <p class="text-3xl font-bold text-indigo-900 mt-2">{{ $employes->where('actif', true)->count() }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-purple-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-purple-700">Employés inactifs</h3>
                <p class="text-3xl font-bold text-purple-900 mt-2">{{ $employes->where('actif', false)->count() }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-fuchsia-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-fuchsia-700">Nouveaux ce mois</h3>
                <p class="text-3xl font-bold text-fuchsia-900 mt-2">{{ $employes->where('created_at', '>=', now()->startOfMonth())->count() }}</p>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white/60 backdrop-blur-xl border border-violet-100 rounded-2xl shadow-2xl p-6 mb-8">
            <form action="{{ route('employes.index') }}" method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Rechercher un employé..." 
                           class="w-full rounded-xl border-violet-200 focus:border-violet-500 focus:ring focus:ring-violet-200 focus:ring-opacity-50">
                </div>
                <div class="w-48">
                    <select name="status" 
                            class="w-full rounded-xl border-violet-200 focus:border-violet-500 focus:ring focus:ring-violet-200 focus:ring-opacity-50">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actifs</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactifs</option>
                    </select>
                </div>
                <div class="w-48">
                    <select name="sort" 
                            class="w-full rounded-xl border-violet-200 focus:border-violet-500 focus:ring focus:ring-violet-200 focus:ring-opacity-50">
                        <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date d'embauche</option>
                        <option value="nom" {{ request('sort') === 'nom' ? 'selected' : '' }}>Nom</option>
                        <option value="role" {{ request('sort') === 'role' ? 'selected' : '' }}>Rôle</option>
                    </select>
                </div>
                <div class="w-48">
                    <select name="direction" 
                            class="w-full rounded-xl border-violet-200 focus:border-violet-500 focus:ring focus:ring-violet-200 focus:ring-opacity-50">
                        <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Décroissant</option>
                        <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Croissant</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-violet-600 text-white font-bold hover:bg-violet-700 transition-colors">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Tableau des employés -->
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow-2xl ring-1 ring-violet-200 ring-opacity-40 sm:rounded-2xl bg-white/70 backdrop-blur-xl">
                        <table class="min-w-full divide-y divide-violet-100">
                            <thead class="bg-gradient-to-tr from-violet-100 to-indigo-100">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-bold text-violet-900 sm:pl-6">Employé</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-violet-900">Email</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-violet-900">Rôle</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-violet-900">Date d'embauche</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-violet-900">Statut</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-violet-50 bg-white/80">
                                @forelse($employes as $employe)
                                    <tr class="hover:bg-violet-50/50 transition-colors">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-violet-900 sm:pl-6">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-violet-100 flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-violet-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-violet-900">{{ $employe->nom }} {{ $employe->prenom }}</div>
                                                    <div class="text-xs text-gray-500">ID: {{ $employe->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-600">
                                            {{ $employe->email }}
                                        </td>
                                        <td class="px-3 py-4 text-sm">
                                            <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-800">
                                                {{ $employe->role->nom ?? 'Non assigné' }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">
                                            {{ $employe->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $employe->actif ? 'bg-violet-100 text-violet-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $employe->actif ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            <div class="flex items-center justify-end space-x-3">
                                                <a href="{{ route('employes.edit', $employe) }}" class="text-violet-600 hover:text-violet-900 flex items-center" title="Modifier">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    <span class="hidden sm:inline">Modifier</span>
                                                </a>
                                                <form action="{{ route('employes.destroy', $employe) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900 flex items-center"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet employé ?')"
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
                                        <td colspan="6" class="px-3 py-4 text-sm text-gray-500 text-center">
                                            <div class="p-6 text-center">
                                                <i class="fas fa-users text-4xl text-gray-300 mb-2"></i>
                                                <p class="text-gray-500">Aucun employé trouvé</p>
                                                <a href="{{ route('employes.create') }}" class="mt-2 inline-flex items-center text-violet-600 hover:text-violet-900">
                                                    <i class="fas fa-plus-circle mr-1"></i>
                                                    Créer un employé
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
        <div class="mt-6">
            {{ $employes->links() }}
        </div>
    </div>
</div>
@endsection 