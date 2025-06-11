@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-green-600 via-teal-500 to-blue-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Gestion des Clients</h1>
                <p class="mt-2 text-lg text-gray-500">Visualisez, filtrez et gérez tous vos clients.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-green-600 to-blue-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-plus"></i>
                    Nouveau client
                </a>
            </div>
        </div>

        <!-- Bloc filtres/recherche -->
        <div class="bg-white/60 backdrop-blur-xl border border-green-100 rounded-2xl shadow-2xl p-6 mb-8">
            <form action="{{ route('clients.index') }}" method="GET" class="flex flex-wrap gap-4 items-center">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un client..." class="flex-1 rounded-xl border-green-200 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                <select name="statut" class="w-48 rounded-xl border-green-200 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                    <option value="">Tous les statuts</option>
                    <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actifs</option>
                    <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>Inactifs</option>
                </select>
                <button type="submit" class="px-6 py-3 rounded-xl bg-green-600 text-white font-bold hover:bg-green-700 transition-colors">Filtrer</button>
            </form>
        </div>

        <!-- Tableau des clients -->
        <div class="bg-white rounded-2xl shadow-2xl p-6 mb-8">
            <table class="min-w-full divide-y divide-green-100">
                <thead class="bg-gradient-to-tr from-green-100 to-blue-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-green-900 uppercase tracking-wider">Nom</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-green-900 uppercase tracking-wider">Téléphone</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-green-900 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-green-900 uppercase tracking-wider">Statut</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-green-900 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-green-50">
                                @forelse ($clients as $client)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $client->nom }} {{ $client->prenom }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $client->telephone }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $client->email ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $client->statut === 'actif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($client->statut) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('clients.show', $client->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('clients.edit', $client->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            Aucun client trouvé
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $clients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
