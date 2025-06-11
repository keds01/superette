@extends('layouts.app')

@section('title', 'Utilisateurs')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- En-tête moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-purple-500 to-blue-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Gestion des Utilisateurs</h1>
                <p class="mt-2 text-lg text-gray-500">Visualisez et gérez tous les comptes utilisateurs de la plateforme.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-user-plus"></i>
                    Ajouter un utilisateur
                </a>
                <a href="{{ route('roles.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-green-500 to-green-400 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-users-cog"></i>
                    Gérer les rôles
                </a>
                <a href="{{ route('admin.permissions.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-purple-500 to-pink-400 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-key"></i>
                    Gérer les permissions
                </a>
            </div>
        </div>

        <!-- Statistiques (exemple, à adapter selon variables disponibles) -->
        @if(isset($stats))
        <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-indigo-700">Total utilisateurs</h3>
                <p class="text-3xl font-bold text-indigo-900 mt-2">{{ $stats['total'] ?? '?' }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-green-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-green-700">Utilisateurs actifs</h3>
                <p class="text-3xl font-bold text-green-900 mt-2">{{ $stats['active'] ?? '?' }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-purple-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-purple-700">Admins</h3>
                <p class="text-3xl font-bold text-purple-900 mt-2">{{ $stats['admins'] ?? '?' }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-pink-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <h3 class="text-sm font-medium text-pink-700">Inactifs</h3>
                <p class="text-3xl font-bold text-pink-900 mt-2">{{ $stats['inactive'] ?? '?' }}</p>
            </div>
        </div>
        @endif

        <!-- Tableau des utilisateurs -->
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow-2xl ring-1 ring-indigo-200 ring-opacity-40 sm:rounded-2xl bg-white/70 backdrop-blur-xl">
                        <table class="min-w-full divide-y divide-indigo-100">
                            <thead class="bg-gradient-to-tr from-indigo-100 to-purple-100">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-bold text-indigo-900">Nom</th>
                                    <th class="py-3 px-4 text-left text-xs font-bold text-indigo-900">Email</th>
                                    <th class="py-3 px-4 text-left text-xs font-bold text-indigo-900">Rôles</th>
                                    <th class="py-3 px-4 text-left text-xs font-bold text-indigo-900">Statut</th>
                                    <th class="py-3 px-4 text-center text-xs font-bold text-indigo-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-indigo-50 bg-white/80">
                                @forelse($users as $user)
                                <tr class="hover:bg-indigo-50/50 transition-colors">
                                    <td class="py-4 px-4 font-semibold text-indigo-900 flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 font-bold">{{ strtoupper(substr($user->name,0,1)) }}</span>
                                        {{ $user->name }}
                                    </td>
                                    <td class="py-4 px-4">{{ $user->email }}</td>
                                    <td class="py-4 px-4">
                                        @foreach($user->roles as $role)
                                            <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full mr-1">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-2 py-1 rounded-full {{ $user->actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $user->actif ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-600 hover:text-blue-900 mx-1" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900 mx-1" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block mx-1" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-500">
                                        <div class="p-6 text-center">
                                            <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                            <p class="text-gray-500">Aucun utilisateur trouvé</p>
                                            <a href="{{ route('admin.users.create') }}" class="mt-2 inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-plus-circle mr-1"></i>
                                                Créer un utilisateur
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
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
